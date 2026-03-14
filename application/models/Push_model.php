<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push_model extends CI_Model {

    public function guardar_suscripcion($endpoint, $p256dh, $auth)
    {
        $existe = $this->db->where('endpoint', $endpoint)->count_all_results('push_subscriptions');
        if ($existe) return true;
        return $this->db->insert('push_subscriptions', [
            'endpoint' => $endpoint,
            'p256dh'   => $p256dh,
            'auth'     => $auth,
        ]);
    }

    public function eliminar_suscripcion($endpoint)
    {
        return $this->db->where('endpoint', $endpoint)->delete('push_subscriptions');
    }

    public function obtener_todas()
    {
        return $this->db->get('push_subscriptions')->result();
    }

    public function total()
    {
        return $this->db->count_all('push_subscriptions');
    }
}
