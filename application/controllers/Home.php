<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Torneo_model');
        $this->load->model('Configuracion_model');
    }

    public function index()
    {
        // Datos del club desde la BD
        $data['club_nombre'] = 'Telares Padel';
        $data['club_descripcion'] = 'Club de Padel de excelencia en tu localidad';
        $data['club_info'] = $this->Configuracion_model->obtener_configuracion();

        // Cargar torneos desde la base de datos
        $data['torneos'] = $this->Torneo_model->obtener_proximos();

        // Si no hay torneos, mostrar array vacío
        if (empty($data['torneos'])) {
            $data['torneos'] = array();
        }

        // Cargar vista
        $this->load->view('header', $data);
        $this->load->view('inicio', $data);
        $this->load->view('footer');
    }

    public function torneo($id)
    {
        // Obtener datos del torneo
        $torneo = $this->Torneo_model->obtener_por_id($id);

        // Si el torneo no existe, redirigir a inicio
        if (!$torneo) {
            redirect('/');
        }

        // Datos para la vista
        $data['torneo'] = $torneo;
        $data['inscriptos'] = $this->Torneo_model->obtener_inscriptos($id);
        $data['total_inscriptos'] = $this->Torneo_model->contar_inscriptos($id);
        $data['inscriptos_por_categoria'] = $this->Torneo_model->obtener_inscriptos_por_categoria($id);
        $data['solicitudes'] = $this->Torneo_model->obtener_solicitudes($id);

        // Cargar vista
        $this->load->view('header', array('club_nombre' => 'Telares Padel'));
        $this->load->view('detalle_torneo', $data);
        $this->load->view('footer');
    }

    public function solicitar_inscripcion()
    {
        $torneo_id = $this->input->post('torneo_id');
        $nombre = $this->input->post('nombre');
        $apellido = $this->input->post('apellido');
        $email = $this->input->post('email');
        $telefono = $this->input->post('telefono');
        $categoria = $this->input->post('categoria');
        $compañero = $this->input->post('compañero');

        if (empty($torneo_id) || empty($nombre) || empty($email)) {
            $this->session->set_flashdata('error', 'Completa los datos requeridos');
            redirect('home/torneo/'.$torneo_id);
            return;
        }

        $data = array(
            'torneo_id' => $torneo_id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'telefono' => $telefono,
            'categoria' => $categoria,
            'compañero' => $compañero,
            'estado' => 'pendiente',
            'fecha_solicitud' => date('Y-m-d H:i:s')
        );

        if ($this->Torneo_model->crear_solicitud_inscripcion($data)) {
            $this->session->set_flashdata('success', '¡Solicitud enviada! Nos contactaremos pronto.');
        } else {
            $this->session->set_flashdata('error', 'Error al enviar la solicitud. Intenta de nuevo.');
        }

        redirect('home/torneo/'.$torneo_id);
    }

}
