<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Metricas_model extends CI_Model
{
    public function registrar($data)
    {
        $this->db->insert('metricas', $data);
    }

    public function resumenHoy()
    {
        return $this->db->query("
            SELECT
                COUNT(*)                                        AS total_visitas,
                COUNT(DISTINCT sesion_id)                       AS sesiones_unicas,
                SUM(CASE WHEN es_mobile THEN 1 ELSE 0 END)     AS mobile,
                SUM(CASE WHEN tipo = 'page_view' THEN 1 ELSE 0 END) AS page_views
            FROM metricas
            WHERE created_at >= CURRENT_DATE
        ")->row();
    }

    public function visitasPorDia($dias = 14)
    {
        return $this->db->query("
            SELECT
                DATE(created_at)            AS dia,
                COUNT(*)                    AS total,
                COUNT(DISTINCT sesion_id)   AS sesiones
            FROM metricas
            WHERE created_at >= NOW() - INTERVAL '{$dias} days'
              AND tipo = 'page_view'
            GROUP BY DATE(created_at)
            ORDER BY dia ASC
        ")->result();
    }

    public function topPaginas($dias = 7)
    {
        return $this->db->query("
            SELECT url, COUNT(*) AS vistas
            FROM metricas
            WHERE tipo = 'page_view'
              AND created_at >= NOW() - INTERVAL '{$dias} days'
              AND url IS NOT NULL
            GROUP BY url
            ORDER BY vistas DESC
            LIMIT 10
        ")->result();
    }

    public function topAcciones($dias = 7)
    {
        return $this->db->query("
            SELECT accion, COUNT(*) AS cantidad
            FROM metricas
            WHERE tipo = 'accion'
              AND created_at >= NOW() - INTERVAL '{$dias} days'
              AND accion IS NOT NULL
            GROUP BY accion
            ORDER BY cantidad DESC
            LIMIT 15
        ")->result();
    }

    public function visitasPorTorneo($dias = 30)
    {
        return $this->db->query("
            SELECT
                m.torneo_id,
                t.nombre AS torneo_nombre,
                COUNT(*) AS visitas,
                COUNT(DISTINCT m.sesion_id) AS sesiones
            FROM metricas m
            LEFT JOIN torneos t ON t.id = m.torneo_id
            WHERE m.tipo = 'page_view'
              AND m.torneo_id IS NOT NULL
              AND m.created_at >= NOW() - INTERVAL '{$dias} days'
            GROUP BY m.torneo_id, t.nombre
            ORDER BY visitas DESC
            LIMIT 10
        ")->result();
    }

    public function totalGeneral()
    {
        return $this->db->query("
            SELECT
                COUNT(*)                        AS total_eventos,
                COUNT(DISTINCT sesion_id)       AS sesiones_totales,
                MIN(created_at)                 AS primer_evento,
                SUM(CASE WHEN es_mobile THEN 1 ELSE 0 END) * 100 / NULLIF(COUNT(*),0) AS pct_mobile
            FROM metricas
        ")->row();
    }
}
