<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Endpoint público para recibir eventos de tracking.
 * No requiere autenticación.
 */
class Metricas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Metricas_model');
    }

    public function registrar()
    {
        // Solo aceptar POST
        if ($this->input->method() !== 'post') {
            return;
        }

        $torneo_id    = (int)$this->input->post('torneo_id')    ?: null;
        $categoria_id = (int)$this->input->post('categoria_id') ?: null;

        $data = [
            'tipo'         => substr($this->input->post('tipo')   ?? 'page_view', 0, 50),
            'url'          => substr($this->input->post('url')    ?? '', 0, 500),
            'accion'       => substr($this->input->post('accion') ?? '', 0, 200) ?: null,
            'torneo_id'    => $torneo_id,
            'categoria_id' => $categoria_id,
            'sesion_id'    => session_id() ?: substr(md5($this->input->ip_address() . $_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 32),
            'ip_hash'      => hash('sha256', $this->input->ip_address()),
            'es_mobile'    => $this->input->post('es_mobile') === 'true',
        ];

        $this->Metricas_model->registrar($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true]));
    }
}
