<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Todos los sponsors (para el admin).
     */
    public function obtener_todos()
    {
        $this->db->select('s.*, t.nombre AS torneo_nombre');
        $this->db->from('sponsors s');
        $this->db->join('torneos t', 't.id = s.torneo_id', 'left');
        $this->db->order_by('s.orden', 'ASC');
        $this->db->order_by('s.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Sponsors activos globales para el ticker (todas las páginas).
     */
    public function obtener_activos_global()
    {
        $this->db->where('activo', true);
        $this->db->where('es_global', true);
        $this->db->order_by('orden', 'ASC');
        $this->db->order_by('nombre', 'ASC');
        return $this->db->get('sponsors')->result();
    }

    /**
     * Todos los sponsors activos (globales + los del torneo indicado).
     */
    public function obtener_activos_para_seccion($torneo_id = null)
    {
        $this->db->where('activo', true);
        if ($torneo_id) {
            $this->db->group_start();
                $this->db->where('es_global', true);
                $this->db->or_where('torneo_id', (int)$torneo_id);
            $this->db->group_end();
        } else {
            $this->db->where('es_global', true);
        }
        $this->db->order_by('orden', 'ASC');
        $this->db->order_by('nombre', 'ASC');
        return $this->db->get('sponsors')->result();
    }

    /**
     * Un sponsor por ID.
     */
    public function obtener_por_id($id)
    {
        return $this->db->where('id', (int)$id)->get('sponsors')->row();
    }

    /**
     * Crear sponsor.
     */
    public function crear($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('sponsors', $data);
    }

    /**
     * Actualizar sponsor.
     */
    public function actualizar($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', (int)$id)->update('sponsors', $data);
    }

    /**
     * Eliminar sponsor.
     */
    public function eliminar($id)
    {
        return $this->db->where('id', (int)$id)->delete('sponsors');
    }

    /**
     * Alternar estado activo/inactivo.
     */
    public function toggle_activo($id)
    {
        $sponsor = $this->obtener_por_id($id);
        if (!$sponsor) return false;
        $nuevo = $sponsor->activo ? false : true;
        return $this->db->where('id', (int)$id)->update('sponsors', [
            'activo'     => $nuevo,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Lista de torneos disponibles (para el select del formulario).
     */
    public function obtener_torneos()
    {
        $this->db->select('id, nombre');
        $this->db->order_by('fecha_inicio', 'DESC');
        return $this->db->get('torneos')->result();
    }
}
