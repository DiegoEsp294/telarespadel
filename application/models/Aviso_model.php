<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aviso_model extends CI_Model {

    public function crear($torneo_id, $cancha, $mensaje, $horas)
    {
        return $this->db->insert('avisos_torneo', [
            'torneo_id' => $torneo_id,
            'cancha'    => $cancha ?: null,
            'mensaje'   => $mensaje,
            'expira_at' => date('Y-m-d H:i:s', strtotime("+{$horas} hours")),
            'creado_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function eliminar($id)
    {
        return $this->db->where('id', $id)->delete('avisos_torneo');
    }

    public function obtener_activos($torneo_id)
    {
        return $this->db->query("
            SELECT * FROM avisos_torneo
            WHERE torneo_id = ? AND expira_at > NOW()
            ORDER BY creado_at DESC
        ", [$torneo_id])->result();
    }

    public function obtener_todos($torneo_id)
    {
        return $this->db->query("
            SELECT *, expira_at > NOW() AS activo
            FROM avisos_torneo
            WHERE torneo_id = ?
            ORDER BY creado_at DESC
            LIMIT 20
        ", [$torneo_id])->result();
    }
}
