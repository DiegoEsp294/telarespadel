<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Push_model');
        if (!$this->session->userdata('usuario_id')) redirect('auth/login');
    }

    /* ===== GUARDAR suscripción (llamado desde el navegador) ===== */
    public function suscribir()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (empty($data['endpoint']) || empty($data['keys']['p256dh']) || empty($data['keys']['auth'])) {
            http_response_code(400);
            echo json_encode(['ok' => false]);
            return;
        }

        $ok = $this->Push_model->guardar_suscripcion(
            $data['endpoint'],
            $data['keys']['p256dh'],
            $data['keys']['auth']
        );

        echo json_encode(['ok' => (bool)$ok]);
    }

    /* ===== ELIMINAR suscripción ===== */
    public function desuscribir()
    {
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!empty($data['endpoint'])) {
            $this->Push_model->eliminar_suscripcion($data['endpoint']);
        }
        echo json_encode(['ok' => true]);
    }

    /* ===== PANTALLA ADMIN ===== */
    public function index()
    {
        $this->load->model('Torneo_model');
        $data['total_suscriptores'] = $this->Push_model->total();
        $data['torneos'] = $this->Torneo_model->obtener_por_usuario($this->session->userdata('usuario_id'));
        $this->load->view('header');
        $this->load->view('admin/push_notificaciones', $data);
        $this->load->view('footer');
    }

    /* ===== ENVIAR notificación ===== */
    public function enviar()
    {
        $titulo = $this->input->post('titulo') ?: 'Telares Padel';
        $cuerpo = $this->input->post('cuerpo') ?: '';
        $url    = $this->input->post('url')    ?: base_url();

        require_once FCPATH . 'vendor/autoload.php';

        $auth = [
            'VAPID' => [
                'subject'    => getenv('VAPID_SUBJECT') ?: 'mailto:admin@telarespadel.com.ar',
                'publicKey'  => getenv('VAPID_PUBLIC'),
                'privateKey' => getenv('VAPID_PRIVATE'),
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $subs    = $this->Push_model->obtener_todas();
        $payload = json_encode(['title' => $titulo, 'body' => $cuerpo, 'url' => $url]);

        $enviados = 0;
        $fallidos = [];

        foreach ($subs as $s) {
            $subscription = \Minishlink\WebPush\Subscription::create([
                'endpoint' => $s->endpoint,
                'keys'     => ['p256dh' => $s->p256dh, 'auth' => $s->auth],
            ]);
            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $enviados++;
            } else {
                // suscripción expirada — limpiar
                if ($report->isSubscriptionExpired()) {
                    $this->Push_model->eliminar_suscripcion($report->getRequest()->getUri()->__toString());
                }
                $fallidos[] = $report->getReason();
            }
        }

        $this->session->set_flashdata('push_ok', "Notificación enviada a $enviados dispositivo(s).");
        if (!empty($fallidos)) {
            $this->session->set_flashdata('push_err', count($fallidos) . ' envío(s) fallaron.');
        }
        redirect('admin/Push/index');
    }
}
