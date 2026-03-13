<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Participantes extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Torneo_model');
        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $data['participantes'] = $this->Torneo_model->obtener_todos_participantes();
        $data['categorias']    = $this->Torneo_model->obtenerCategorias();

        $this->load->view('header');
        $this->load->view('admin/participantes', $data);
        $this->load->view('footer');
    }

    // Devuelve lista filtrada como JSON (para búsqueda AJAX en la página)
    public function listar()
    {
        $search = $this->input->get('q') ?? '';
        echo json_encode($this->Torneo_model->obtener_todos_participantes(trim($search)));
    }

    public function guardar()
    {
        $id = $this->Torneo_model->guardar_participante([
            'nombre'    => $this->input->post('nombre'),
            'apellido'  => $this->input->post('apellido'),
            'dni'       => $this->input->post('dni') ?: null,
            'telefono'  => $this->input->post('telefono') ?: null,
            'categoria' => $this->input->post('categoria') ?: null,
        ]);

        echo json_encode(['ok' => (bool)$id, 'id' => $id]);
    }

    public function actualizar($id)
    {
        $ok = $this->Torneo_model->actualizar_participante($id, [
            'nombre'    => $this->input->post('nombre'),
            'apellido'  => $this->input->post('apellido'),
            'dni'       => $this->input->post('dni') ?: null,
            'telefono'  => $this->input->post('telefono') ?: null,
            'categoria' => $this->input->post('categoria') ?: null,
        ]);

        echo json_encode(['ok' => (bool)$ok]);
    }

    public function eliminar($id)
    {
        $en_uso = $this->db
            ->group_start()
                ->where('participante1_id', $id)
                ->or_where('participante2_id', $id)
            ->group_end()
            ->count_all_results('inscripciones');

        if ($en_uso > 0) {
            echo json_encode(['ok' => false, 'error' => 'Este participante está inscripto en un torneo y no puede eliminarse.']);
            return;
        }

        $this->Torneo_model->eliminar_participante($id);
        echo json_encode(['ok' => true]);
    }

}
