<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Avisos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('usuario_id')) {
            echo json_encode(['ok' => false, 'error' => 'no auth']); exit;
        }
        $this->load->model('Aviso_model');
        $this->load->model('Push_model');
        $this->load->model('Torneo_model');
    }

    /* POST — crear aviso + push */
    public function crear()
    {
        $torneo_id = (int)$this->input->post('torneo_id');
        $cancha    = $this->input->post('cancha');
        $mensaje   = trim($this->input->post('mensaje'));
        $horas     = (float)$this->input->post('horas') ?: 1;

        if (!$torneo_id || !$mensaje) {
            echo json_encode(['ok' => false, 'error' => 'Datos incompletos']); return;
        }

        $this->Aviso_model->crear($torneo_id, $cancha, $mensaje, $horas);

        // Push notification a todos los suscriptores
        try {
            $torneo = $this->Torneo_model->obtener_por_id($torneo_id);
            $titulo = '⚠️ Aviso' . ($cancha ? ' · Cancha ' . $cancha : '') . ' — ' . ($torneo->nombre ?? 'Torneo');
            $url    = site_url('home/torneo/' . $torneo_id . '?tab=listado');
            $this->_enviar_push($titulo, $mensaje, $url);
        } catch (Exception $e) {
            // Push falló pero el aviso ya se guardó, continuamos
        }

        $avisos = $this->Aviso_model->obtener_todos($torneo_id);
        echo json_encode(['ok' => true, 'avisos' => $avisos]);
    }

    /* POST — eliminar aviso */
    public function eliminar()
    {
        $id        = (int)$this->input->post('id');
        $torneo_id = (int)$this->input->post('torneo_id');
        $this->Aviso_model->eliminar($id);
        $avisos = $this->Aviso_model->obtener_todos($torneo_id);
        echo json_encode(['ok' => true, 'avisos' => $avisos]);
    }

    /* GET — avisos activos (para polling público) */
    public function activos($torneo_id)
    {
        $this->load->model('Aviso_model');
        $avisos = $this->Aviso_model->obtener_activos($torneo_id);
        header('Content-Type: application/json');
        echo json_encode($avisos);
    }

    /* ── Helpers ── */
    private function _enviar_push($titulo, $cuerpo, $url)
    {
        if (!file_exists(FCPATH . 'vendor/autoload.php')) return;
        require_once FCPATH . 'vendor/autoload.php';

        $vapidPublic  = getenv('VAPID_PUBLIC');
        $vapidPrivate = getenv('VAPID_PRIVATE');
        if (!$vapidPublic || !$vapidPrivate) return;

        $auth = [
            'VAPID' => [
                'subject'    => getenv('VAPID_SUBJECT') ?: 'mailto:admin@telarespadel.com.ar',
                'publicKey'  => $vapidPublic,
                'privateKey' => $vapidPrivate,
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $subs    = $this->Push_model->obtener_todas();
        $payload = json_encode(['title' => $titulo, 'body' => $cuerpo, 'url' => $url]);

        foreach ($subs as $s) {
            $sub = \Minishlink\WebPush\Subscription::create([
                'endpoint' => $s->endpoint,
                'keys'     => ['p256dh' => $s->p256dh, 'auth' => $s->auth],
            ]);
            $webPush->queueNotification($sub, $payload);
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                $this->Push_model->eliminar_suscripcion(
                    $report->getRequest()->getUri()->__toString()
                );
            }
        }
    }
}
