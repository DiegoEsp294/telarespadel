<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Metricas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }
        $this->load->model('Metricas_model');
    }

    public function index()
    {
        $data = [
            'hoy'             => $this->Metricas_model->resumenHoy(),
            'por_dia'         => $this->Metricas_model->visitasPorDia(14),
            'top_paginas'     => $this->Metricas_model->topPaginas(7),
            'top_acciones'    => $this->Metricas_model->topAcciones(7),
            'por_torneo'      => $this->Metricas_model->visitasPorTorneo(30),
            'general'         => $this->Metricas_model->totalGeneral(),
        ];

        $this->load->view('admin/layout/header');
        $this->load->view('admin/layout/sidebar');
        $this->load->view('admin/metricas', $data);
        $this->load->view('admin/layout/footer');
    }
}
