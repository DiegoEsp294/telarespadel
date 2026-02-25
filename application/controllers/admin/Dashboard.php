<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // seguridad básica
        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $this->load->view('admin/layout/header');
        $this->load->view('admin/layout/sidebar');
        $this->load->view('admin/dashboard');
        $this->load->view('admin/layout/footer');
    }
    
}
