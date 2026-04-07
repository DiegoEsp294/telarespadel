<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsors extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sponsor_model');

        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }
    }

    // ─── Listado ────────────────────────────────────────────────────────────

    public function index()
    {
        $data['sponsors'] = $this->Sponsor_model->obtener_todos();
        $data['flash_success'] = $this->session->flashdata('success');
        $data['flash_error']   = $this->session->flashdata('error');

        $this->load->view('header', $data);
        $this->load->view('admin/sponsors_listado', $data);
        $this->load->view('footer');
    }

    // ─── Crear ──────────────────────────────────────────────────────────────

    public function crear()
    {
        $data['torneos'] = $this->Sponsor_model->obtener_torneos();
        $data['sponsor'] = null;

        $this->load->view('header', $data);
        $this->load->view('admin/sponsor_form', $data);
        $this->load->view('footer');
    }

    public function guardar()
    {
        $logoBase64 = null;
        if (!empty($_FILES['logo']['tmp_name'])) {
            $logoBase64 = base64_encode(file_get_contents($_FILES['logo']['tmp_name']));
        }

        $torneo_id = $this->input->post('torneo_id');

        $data = [
            'nombre'      => $this->input->post('nombre'),
            'sitio_web'   => $this->input->post('sitio_web')   ?: null,
            'instagram'   => $this->input->post('instagram')   ?: null,
            'facebook'    => $this->input->post('facebook')    ?: null,
            'whatsapp'    => $this->input->post('whatsapp')    ?: null,
            'otro_link'   => $this->input->post('otro_link')   ?: null,
            'otro_label'  => $this->input->post('otro_label')  ?: null,
            'activo'      => (bool)$this->input->post('activo'),
            'es_global'   => (bool)$this->input->post('es_global'),
            'torneo_id'   => ($torneo_id !== '' && $torneo_id !== null) ? (int)$torneo_id : null,
            'orden'       => (int)($this->input->post('orden') ?: 0),
        ];

        if ($logoBase64) {
            $data['logo'] = $logoBase64;
        }

        if (empty($data['nombre'])) {
            $this->session->set_flashdata('error', 'El nombre del sponsor es obligatorio.');
            redirect('admin/Sponsors/crear');
            return;
        }

        if ($this->Sponsor_model->crear($data)) {
            $this->session->set_flashdata('success', 'Sponsor creado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al crear el sponsor.');
        }

        redirect('admin/Sponsors/index');
    }

    // ─── Editar ─────────────────────────────────────────────────────────────

    public function editar($id)
    {
        $sponsor = $this->Sponsor_model->obtener_por_id($id);
        if (!$sponsor) {
            $this->session->set_flashdata('error', 'Sponsor no encontrado.');
            redirect('admin/Sponsors/index');
            return;
        }

        $data['sponsor']  = $sponsor;
        $data['torneos']  = $this->Sponsor_model->obtener_torneos();

        $this->load->view('header', $data);
        $this->load->view('admin/sponsor_form', $data);
        $this->load->view('footer');
    }

    public function actualizar($id)
    {
        $sponsor = $this->Sponsor_model->obtener_por_id($id);
        if (!$sponsor) {
            $this->session->set_flashdata('error', 'Sponsor no encontrado.');
            redirect('admin/Sponsors/index');
            return;
        }

        $torneo_id = $this->input->post('torneo_id');

        $data = [
            'nombre'      => $this->input->post('nombre'),
            'sitio_web'   => $this->input->post('sitio_web')   ?: null,
            'instagram'   => $this->input->post('instagram')   ?: null,
            'facebook'    => $this->input->post('facebook')    ?: null,
            'whatsapp'    => $this->input->post('whatsapp')    ?: null,
            'otro_link'   => $this->input->post('otro_link')   ?: null,
            'otro_label'  => $this->input->post('otro_label')  ?: null,
            'activo'      => (bool)$this->input->post('activo'),
            'es_global'   => (bool)$this->input->post('es_global'),
            'torneo_id'   => ($torneo_id !== '' && $torneo_id !== null) ? (int)$torneo_id : null,
            'orden'       => (int)($this->input->post('orden') ?: 0),
        ];

        if (!empty($_FILES['logo']['tmp_name'])) {
            $data['logo'] = base64_encode(file_get_contents($_FILES['logo']['tmp_name']));
        }

        if (empty($data['nombre'])) {
            $this->session->set_flashdata('error', 'El nombre del sponsor es obligatorio.');
            redirect('admin/Sponsors/editar/' . $id);
            return;
        }

        if ($this->Sponsor_model->actualizar($id, $data)) {
            $this->session->set_flashdata('success', 'Sponsor actualizado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar el sponsor.');
        }

        redirect('admin/Sponsors/index');
    }

    // ─── Toggle activo ──────────────────────────────────────────────────────

    public function toggle($id)
    {
        $this->Sponsor_model->toggle_activo($id);
        redirect('admin/Sponsors/index');
    }

    // ─── Eliminar ───────────────────────────────────────────────────────────

    public function eliminar($id)
    {
        if ($this->Sponsor_model->eliminar($id)) {
            $this->session->set_flashdata('success', 'Sponsor eliminado.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo eliminar el sponsor.');
        }
        redirect('admin/Sponsors/index');
    }
}
