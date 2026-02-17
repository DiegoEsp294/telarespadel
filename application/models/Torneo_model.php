<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Torneo_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function obtener_todos()
    {
        $query = $this->db->order_by('fecha_inicio', 'DESC')->get('torneos');
        return $query->result();
    }

    public function obtener_proximos()
    {
        $this->db->where('estado !=', 'finalizado');
        $query = $this->db->order_by('fecha_inicio', 'ASC')->get('torneos');
        return $query->result();
    }

    public function obtener_por_id($id)
    {
        $query = $this->db->where('id', $id)->get('torneos');
        return $query->row();
    }

    public function obtener_inscriptos($torneo_id)
    {
        $query = $this->db
            ->where('i.torneo_id', $torneo_id)
            ->where('i.estado !=', 'cancelada')
            ->select('i.*, p1.nombre as nombre_p1, p1.apellido as apellido_p1, p2.nombre as nombre_p2, p2.apellido as apellido_p2')
            ->from('inscripciones i')
            ->join('participantes p1', 'i.participante1_id = p1.id', 'left')
            ->join('participantes p2', 'i.participante2_id = p2.id', 'left')
            ->order_by('i.fecha_inscripcion', 'ASC')
            ->get();
        return $query->result();
    }

    public function obtener_inscriptos_por_categoria($torneo_id)
    {
        $query = $this->db
            ->where('torneo_id', $torneo_id)
            ->where('estado', 'confirmada')
            ->select('categoria, COUNT(*) as cantidad')
            ->from('inscripciones')
            ->group_by('categoria')
            ->get();
        return $query->result();
    }

    public function contar_inscriptos($torneo_id, $estado = 'confirmada')
    {
        return $this->db
            ->where('torneo_id', $torneo_id)
            ->where('estado', $estado)
            ->count_all_results('inscripciones');
    }

    public function obtener_categorias_disponibles($torneo_id)
    {
        $query = $this->db
            ->select('DISTINCT categoria')
            ->where('torneo_id', $torneo_id)
            ->from('inscripciones')
            ->get();
        return $query->result();
    }

    public function crear_solicitud_inscripcion($data)
    {
        return $this->db->insert('solicitudes_inscripcion', $data);
    }

    public function obtener_solicitudes($torneo_id)
    {
        $query = $this->db
            ->where('torneo_id', $torneo_id)
            ->where('estado', 'pendiente')
            ->order_by('fecha_solicitud', 'ASC')
            ->get('solicitudes_inscripcion');
        return $query->result();
    }

}

