<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function obtener_por_email($email)
    {
        $query = $this->db->where('email', $email)->where('estado', 'activo')->get('usuarios');
        return $query->row();
    }

    public function obtener_por_id($id)
    {
        $query = $this->db->where('id', $id)->where('estado', 'activo')->get('usuarios');
        return $query->row();
    }

    public function crear_usuario($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->db->insert('usuarios', $data);
    }

    public function actualizar_usuario($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $this->db->where('id', $id)->update('usuarios', $data);
    }

    public function registrar_acceso($id)
    {
        $data = array('ultimo_acceso' => date('Y-m-d H:i:s'));
        return $this->db->where('id', $id)->update('usuarios', $data);
    }

    public function verificar_email($email)
    {
        $query = $this->db->where('email', $email)->count_all_results('usuarios');
        return $query > 0;
    }

    public function verificar_contraseña($password, $hash)
    {
        return password_verify($password, $hash);
    }

}
