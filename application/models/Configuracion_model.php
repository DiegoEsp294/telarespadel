<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuracion_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener configuración del club
     * @return array Retorna array con los datos de configuración
     */
    public function obtener_configuracion()
    {
        $query = $this->db->get('configuracion_club', 1);
        
        if ($query->num_rows() > 0) {
            $config = $query->row();
            return array(
                'ubicacion' => $config->ubicacion,
                'telefono' => $config->telefono,
                'email' => $config->email,
                'facebook' => $config->facebook,
                'instagram' => $config->instagram
            );
        } else {
            // Retornar valores por defecto si no existe configuración
            return array(
                'ubicacion' => 'Los Telares, Santiago del Estero',
                'telefono' => '3855555555',
                'email' => 'telarespadel@gmail.com',
                'facebook' => 'telarespadel',
                'instagram' => '@telarespadel'
            );
        }
    }

    /**
     * Actualizar configuración del club
     * @param array $data Datos a actualizar
     * @return boolean
     */
    public function actualizar_configuracion($data)
    {
        return $this->db->set($data)->update('configuracion_club');
    }

    /**
     * Obtener dato específico de configuración
     * @param string $clave
     * @return string
     */
    public function obtener_dato($clave)
    {
        $query = $this->db->select($clave)->get('configuracion_club', 1);
        
        if ($query->num_rows() > 0) {
            $resultado = $query->row();
            return $resultado->$clave;
        }
        return null;
    }
}
