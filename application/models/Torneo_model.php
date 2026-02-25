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
        $query = $this->db
            ->select("
                torneos.*,
                STRING_AGG(c.nombre, ', ' ORDER BY c.nombre) AS categorias_label
            ")
            ->from('torneos')
            ->join('torneo_categorias tc', 'tc.torneo_id = torneos.id', 'INNER')
            ->join('categorias c', 'c.id = tc.categoria_id', 'INNER')
            ->where('torneos.estado !=', 'finalizado')
            ->group_by('torneos.id')
            ->order_by('torneos.fecha_inicio', 'ASC')
            ->get();

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
            ->select('c.nombre as categoria, i.categoria_id, COUNT(*) as cantidad')
            ->from('inscripciones i')
            ->join('categorias c', 'c.id = i.categoria_id', 'INNER')
            ->where('i.torneo_id', $torneo_id)
            ->where('i.estado', 'confirmada')
            ->group_by(['i.categoria_id', 'c.nombre'])
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

    public function obtener_por_usuario($usuario_id)
    {
        return $this->db
            ->order_by('id', 'DESC')
            ->get('torneos')
            ->result();
    }

    /**
     * Obtener torneo validando dueño
     */
    public function obtener_por_id_y_usuario($torneo_id, $usuario_id)
    {
        return $this->db
            ->where('id', $torneo_id)
            ->get('torneos')
            ->row();
    }

    public function crear($data)
    {
        $this->db->insert('torneos', $data);
        return $this->db->insert_id();
    }

    public function actualizar($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('torneos', $data);
    }

    public function eliminar($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete('torneos');
    }

    public function asignarCategoria($torneo_id, $categoria_id)
    {
        return $this->db->insert('torneo_categorias',[ 'torneo_id'=>$torneo_id, 'categoria_id'=>$categoria_id]);

    }

    public function eliminarCategorias($torneo_id)
    {
        return $this->db->where('torneo_id', $torneo_id)->delete('torneo_categorias');

    }

    public function obtenerCategorias()
    {

        $query = $this->db->get('categorias');
        return $query->result();
    }

    public function obtenerCategoriasPorTorneo($torneo_id)
    {
        return $this->db
            ->select('c.*')
            ->from('torneo_categorias tc')
            ->join('categorias c', 'c.id = tc.categoria_id')
            ->where('tc.torneo_id', $torneo_id)
            ->where('c.activo', true)
            ->order_by('c.rama', 'ASC')
            ->order_by('c.nivel', 'ASC')
            ->get()
            ->result();
    }

    public function obtenerCategoriasIds($torneo_id)
    {
        $query = $this->db
            ->where('torneo_id', $torneo_id)
            ->select('categoria_id')
            ->from('torneo_categorias')
            ->get();
        return array_column($query->result_array(), 'categoria_id');
    }

    public function guardar_inscripcion($data){
        return $this->db->insert('inscripciones', $data);
    }

    public function guardar_participante($data)
    {
        $ok = $this->db->insert('participantes', $data);

        if ($ok) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function obtener_inscripciones($id_torneo){
        $query = $this->db
            ->where('tc.torneo_id', $id_torneo)
            ->select('
                i.*, 
                p1.nombre as nombre1, p2.nombre as nombre2, 
                p1.apellido as apellido1, p2.apellido as apellido2,
                p1.telefono as telefono1, p2.telefono as telefono2,
                c.nombre as categoria'
            )
            ->from('inscripciones i')
            ->join('participantes p1','p1.id=i.participante1_id','LEFT')
            ->join('participantes p2','p2.id=i.participante2_id','LEFT')
            ->join('categorias c', 'i.categoria_id=c.id', 'LEFT')
            ->join('torneo_categorias tc', 'tc.categoria_id=i.categoria_id', 'INNER')
            ->get();
        return $query->result();  
    }

    public function eliminar_inscripcion($id_inscripcion)
    {
        return $this->db
            ->where('id', $id_inscripcion)
            ->delete('inscripciones');
    }

    public function obtenerParejas($torneo_id, $categoria_id)
    {
        return $this->db
            ->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->where('estado', 'confirmada')
            ->order_by('id', 'ASC')
            ->get('inscripciones')
            ->result();

        // var_dump($this->db->last_query());die;
    }

    public function limpiarFixture($torneo_id)
    {
        $this->db->trans_start();

        // 1️⃣ obtener zonas del torneo
        $zonas = $this->db
            ->select('id')
            ->where('torneo_id', $torneo_id)
            ->get('zonas')
            ->result();

        if (empty($zonas)) {
            $this->db->trans_complete();
            return true;
        }

        $zona_ids = array_column($zonas, 'id');

        /*
        =====================================
        2️⃣ BORRAR PARTIDOS
        =====================================
        */

        $this->db
            ->where_in('zona_id', $zona_ids)
            ->delete('partidos');

        /*
        =====================================
        3️⃣ BORRAR RELACION ZONA-PAREJAS
        =====================================
        */

        $this->db
            ->where_in('zona_id', $zona_ids)
            ->delete('zona_parejas');

        /*
        =====================================
        4️⃣ BORRAR ZONAS
        =====================================
        */

        $this->db
            ->where('torneo_id', $torneo_id)
            ->delete('zonas');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function obtenerCategoriasFixture($torneo_id)
    {
        return $this->db
            ->where('torneo_id', $torneo_id)
            ->get('torneo_categorias')
            ->result();
    }

    public function insertarZona($data)
    {
        $ok = $this->db->insert('zonas', $data);

        if ($ok) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function insertarZonaParejas($data)    {
        $ok = $this->db->insert('zona_parejas', $data);

        if ($ok) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function insertarPartidos($data)
    {
        $ok = $this->db->insert('partidos', $data);
        if ($ok) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function obtenerPartidos($partido_id)
    {
        return $this->db
            ->where('id', $partido_id)
            ->get('partidos')
            ->row();
    }

        public function obtenerZona($zona_id)
    {
        return $this->db
            ->where('id', $zona_id)
            ->get('zonas')
            ->row();
    }

    public function eliminarSetsAnteriores($partido_id){
        return $this->db
            ->where('partido_id', $partido_id)
            ->delete('partido_sets');
    }

    public function insertarSet($dataSet)
    {
        return $this->db->insert('partido_sets', $dataSet);
    }

    public function actualizarPartido($partido_id, $ganador_id)
    {
        return $this->db
            ->where('id', $partido_id)
            ->update('partidos', [
                'estado' => 'finalizado',
                'ganador_id' => $ganador_id
            ]);
    }

    public function avanzarGanador($partido_id, $campoDestino, $ganador_id)
    {
        return $this->db
            ->where('id', $partido_id)
            ->update('partidos', [
                $campoDestino => $ganador_id
            ]);
    }

    public function asignarCancha($partido_id, $cancha)
    {
        return $this->db
            ->where('id', $partido_id)
            ->update('partidos', [
                'cancha' => $cancha
            ]);
    }

    public function iniciarPartido($partido_id)
    {
        return $this->db
            ->where('id', $partido_id)
            ->update('partidos', [
                'estado' => 'en_juego'
            ]);
    }

    public function cargarSet($partido_id, $nro_set, $j1, $j2)
    {
        $existe = $this->db
            ->where([
                'partido_id' => $partido_id,
                'nro_set' => $nro_set
            ])
            ->get('partidos_sets')
            ->row();

        $data = [
            'partido_id' => $partido_id,
            'nro_set' => $nro_set,
            'juegos_pareja1' => $j1,
            'juegos_pareja2' => $j2
        ];

        if ($existe)
        {
            $this->db
                ->where('id', $existe->id)
                ->update('partidos_sets', $data);
        }
        else
        {
            $this->db->insert('partidos_sets', $data);
        }
    }

    public function calcularGanador($partido_id)
    {
        $sets = $this->db
            ->where('partido_id', $partido_id)
            ->order_by('nro_set')
            ->get('partidos_sets')
            ->result();

        $partido = $this->db
            ->where('id', $partido_id)
            ->get('partidos')
            ->row();

        $sets_p1 = 0;
        $sets_p2 = 0;

        foreach ($sets as $set)
        {
            if ($set->juegos_pareja1 > $set->juegos_pareja2)
                $sets_p1++;
            else
                $sets_p2++;
        }

        if ($sets_p1 > $sets_p2)
            return $partido->pareja1_id;

        if ($sets_p2 > $sets_p1)
            return $partido->pareja2_id;

        return null;
    }

    public function obtenerSets($partido_id)
    {
        return $this->db
            ->where('partido_id', $partido_id)
            ->order_by('nro_set')
            ->get('partidos_sets')
            ->result();
    }

    public function obtenerFixtureZonas($torneo_id, $categoria_id)
    {

        $zonas = $this->db
            ->where([
                'torneo_id' => $torneo_id,
                'categoria_id' => $categoria_id
            ])
            ->order_by('numero')
            ->get('zonas')
            ->result();

        foreach ($zonas as &$zona)
        {
            $zona->partidos = $this->db->query("
                SELECT
                    p.id,
                    p.estado,
                    p.fecha,
                    p.cancha,
                    CONCAT(p1a.apellido,' ',p1a.nombre,' / ',
                        p1b.apellido,' ',p1b.nombre) AS pareja1,
                    CONCAT(p2a.apellido,' ',p2a.nombre,' / ',
                        p2b.apellido,' ',p2b.nombre) AS pareja2
                FROM partidos p
                LEFT JOIN inscripciones i1 ON i1.id = p.pareja1_id
                LEFT JOIN participantes p1a ON p1a.id = i1.participante1_id
                LEFT JOIN participantes p1b ON p1b.id = i1.participante2_id
                LEFT JOIN inscripciones i2 ON i2.id = p.pareja2_id
                LEFT JOIN participantes p2a ON p2a.id = i2.participante1_id
                LEFT JOIN participantes p2b ON p2b.id = i2.participante2_id
                WHERE p.zona_id = ?
                ORDER BY p.id
            ", [$zona->id])->result();
        }

        return $zonas;
    }

    public function obtenerFixturePlayoff($torneo_id, $categoria_id)
    {

        $partidos = $this->db->query("
            SELECT
                p.*,
                CASE p.ronda
                    WHEN 1 THEN 'Octavos'
                    WHEN 2 THEN 'Cuartos'
                    WHEN 3 THEN 'Semifinal'
                    WHEN 4 THEN 'Final'
                END AS nombre_ronda,

                COALESCE(
                    CONCAT(p1a.apellido,' ',p1a.nombre,' / ',
                        p1b.apellido,' ',p1b.nombre),
                    p.referencia1
                ) AS pareja1,

                COALESCE(
                    CONCAT(p2a.apellido,' ',p2a.nombre,' / ',
                        p2b.apellido,' ',p2b.nombre),
                    p.referencia2
                ) AS pareja2

            FROM partidos p

            LEFT JOIN inscripciones i1 ON i1.id = p.pareja1_id
            LEFT JOIN participantes p1a ON p1a.id = i1.participante1_id
            LEFT JOIN participantes p1b ON p1b.id = i1.participante2_id

            LEFT JOIN inscripciones i2 ON i2.id = p.pareja2_id
            LEFT JOIN participantes p2a ON p2a.id = i2.participante1_id
            LEFT JOIN participantes p2b ON p2b.id = i2.participante2_id

            WHERE p.torneo_id = ?
            AND p.categoria_id = ?
            AND p.zona_id IS NULL
            ORDER BY p.ronda, p.id
        ", [$torneo_id, $categoria_id])->result();

        $agrupado = [];

        foreach ($partidos as $p) {
            $agrupado[$p->nombre_ronda][] = $p;
        }

        return $agrupado;
    }

}

