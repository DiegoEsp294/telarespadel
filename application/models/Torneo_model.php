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

    public function obtener_proximos($solo_visibles = TRUE)
    {
        $this->db
            ->select("
                torneos.*,
                STRING_AGG(c.nombre, ', ' ORDER BY c.nombre) AS categorias_label
            ")
            ->from('torneos')
            ->join('torneo_categorias tc', 'tc.torneo_id = torneos.id', 'INNER')
            ->join('categorias c', 'c.id = tc.categoria_id', 'INNER')
            ->where('torneos.estado !=', 'finalizado')
            ->group_by('torneos.id')
            ->order_by('torneos.fecha_inicio', 'ASC');

        if ($solo_visibles) {
            $this->db->where('torneos.visible', TRUE);
        }

        return $this->db->get()->result();
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
            // ->where('i.estado', 'confirmada')
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

    public function eliminarTorneoCompleto($torneo_id)
    {
        $this->db->trans_start();

        $this->db->query("
            DELETE FROM resultados_partido
            WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ?
            )
        ", [$torneo_id]);

        $this->db->query("
            DELETE FROM partido_sets
            WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ?
            )
        ", [$torneo_id]);

        $this->db->delete('partidos', ['torneo_id' => $torneo_id]);
        $this->db->delete('inscripciones', ['torneo_id' => $torneo_id]);
        $this->db->delete('zonas', ['torneo_id' => $torneo_id]);
        $this->db->delete('torneos', ['id' => $torneo_id]);

        $this->db->trans_complete();
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
            ->where('i.torneo_id', $id_torneo)
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
            ->get();
        return $query->result();
    }

    public function eliminar_inscripcion($id_inscripcion)
    {
        return $this->db
            ->where('id', $id_inscripcion)
            ->delete('inscripciones');
    }

    public function obtener_inscripcion($id)
    {
        return $this->db
            ->select('i.*, i.participante1_id, i.participante2_id,
                p1.nombre as nombre1, p1.apellido as apellido1, p1.telefono as telefono1,
                p2.nombre as nombre2, p2.apellido as apellido2, p2.telefono as telefono2')
            ->from('inscripciones i')
            ->join('participantes p1', 'p1.id = i.participante1_id', 'LEFT')
            ->join('participantes p2', 'p2.id = i.participante2_id', 'LEFT')
            ->where('i.id', $id)
            ->get()
            ->row();
    }

    public function actualizar_participante($id, $data)
    {
        return $this->db->where('id', $id)->update('participantes', $data);
    }

    public function obtener_inscripciones_por_categoria($torneo_id, $categoria_id)
    {
        return $this->db
            ->select('i.id, i.participante1_id, i.participante2_id, i.estado,
                p1.nombre as nombre1, p1.apellido as apellido1, p1.telefono as telefono1,
                p2.nombre as nombre2, p2.apellido as apellido2, p2.telefono as telefono2')
            ->from('inscripciones i')
            ->join('participantes p1', 'p1.id = i.participante1_id', 'LEFT')
            ->join('participantes p2', 'p2.id = i.participante2_id', 'LEFT')
            ->where('i.torneo_id', $torneo_id)
            ->where('i.categoria_id', $categoria_id)
            ->order_by('i.id', 'ASC')
            ->get()
            ->result();
    }

    public function obtenerParejas($torneo_id, $categoria_id)
    {
        return $this->db
            ->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            // ->where('estado', 'confirmada')
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

    public function limpiarFixtureCategoria($torneo_id, $categoria_id)
    {
        $this->db->trans_start();

        $zonas = $this->db
            ->select('id')
            ->where(['torneo_id' => $torneo_id, 'categoria_id' => $categoria_id])
            ->get('zonas')
            ->result();

        if (!empty($zonas)) {
            $zona_ids = array_map('intval', array_column($zonas, 'id'));
            $in = implode(',', $zona_ids);

            $this->db->query("DELETE FROM partido_sets WHERE partido_id IN (SELECT id FROM partidos WHERE zona_id IN ($in))");
            $this->db->query("DELETE FROM resultados_partido WHERE partido_id IN (SELECT id FROM partidos WHERE zona_id IN ($in))");
            $this->db->where_in('zona_id', $zona_ids)->delete('partidos');
            $this->db->where_in('zona_id', $zona_ids)->delete('tabla_posiciones');
            $this->db->where_in('zona_id', $zona_ids)->delete('zona_parejas');
            $this->db->where('torneo_id', $torneo_id)->where('categoria_id', $categoria_id)->delete('zonas');
        }

        $this->db->query("
            DELETE FROM partido_sets WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ? AND categoria_id = ? AND zona_id IS NULL
            )
        ", [$torneo_id, $categoria_id]);

        $this->db->query("
            DELETE FROM resultados_partido WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ? AND categoria_id = ? AND zona_id IS NULL
            )
        ", [$torneo_id, $categoria_id]);

        $this->db->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->where('zona_id IS NULL', null, false)
            ->delete('partidos');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function obtenerZonasPorCategoria($torneo_id, $categoria_id)
    {
        return $this->db
            ->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->order_by('numero', 'ASC')
            ->get('zonas')
            ->result();
    }

    public function obtenerInscripcionesConZona($torneo_id, $categoria_id)
    {
        return $this->db->query("
            SELECT
                i.id,
                CONCAT(p1.apellido, ' ', p1.nombre, ' / ', p2.apellido, ' ', p2.nombre) AS pareja_nombre,
                z.id   AS zona_id,
                z.numero AS zona_numero
            FROM inscripciones i
            LEFT JOIN participantes p1 ON p1.id = i.participante1_id
            LEFT JOIN participantes p2 ON p2.id = i.participante2_id
            LEFT JOIN zona_parejas zp ON zp.inscripcion_id = i.id
                AND zp.zona_id IN (SELECT id FROM zonas WHERE torneo_id = ? AND categoria_id = ?)
            LEFT JOIN zonas z ON z.id = zp.zona_id
            WHERE i.torneo_id  = ?
              AND i.categoria_id = ?
            ORDER BY i.id ASC
        ", [$torneo_id, $categoria_id, $torneo_id, $categoria_id])->result();
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

    public function actualizarPartidos($partido_id, $data)
    {
        $ok = $this->db->update('partidos', $data);
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

    public function actualizarPartido($partido_id, $data)
    {
        return $this->db
            ->where('id', $partido_id)
            ->update('partidos', $data);
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
                'numero_set' => $nro_set
            ])
            ->get('partido_sets')
            ->row();

        $data = [
            'partido_id' => $partido_id,
            'numero_set' => $nro_set,
            'juegos_pareja1' => $j1,
            'juegos_pareja2' => $j2
        ];

        if ($existe)
        {
            $this->db
                ->where('id', $existe->id)
                ->update('partido_sets', $data);
        }
        else
        {
            $this->db->insert('partido_sets', $data);
        }
    }

    public function calcularGanador($partido_id)
    {
        $sets = $this->db
            ->where('partido_id', $partido_id)
            ->order_by('numero_set')
            ->get('partido_sets')
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
            ->order_by('numero_set')
            ->get('partido_sets')
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

    public function eliminarResultadosAnteriores($partido_id)
    {
        $this->db
            ->where('partido_id', $partido_id)
            ->delete('resultados_partido');
    }

    public function insertarResultado(array $data)
    {
        /*
            $data esperado:
            [
                'partido_id' => int,
                'set1_p1'    => int,
                'set1_p2'    => int,
                'set2_p1'    => int,
                'set2_p2'    => int,
                'set3_p1'    => int|null,
                'set3_p2'    => int|null,
                'ganador_id' => int
            ]
        */
        $this->db->insert('resultados_partido', $data);
    }

    public function obtenerPartidosPorZona($zona_id)
    {
        return $this->db
            ->select('id, pareja1_id, pareja2_id, ganador_id, torneo_id, categoria_id')
            ->from('partidos')
            ->where('zona_id', $zona_id)
            ->get()
            ->result();
    }

    public function actualizarTablaZona($torneo_id, $categoria_id, $zona_id, $inscripcion_id, $datos)
    {
        $existe = $this->db
            ->from('tabla_posiciones')
            ->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->where('zona_id', $zona_id)
            ->where('inscripcion_id', $inscripcion_id)
            ->count_all_results();

        if ($existe) {

            $camposActualizar = [
                'pj'           => $datos['pj'],
                'pg'           => $datos['pg'],
                'pp'           => $datos['pp'],
                'sets_favor'   => $datos['sets_favor'],
                'sets_contra'  => $datos['sets_contra'],
                'games_favor'  => $datos['games_favor'],
                'games_contra' => $datos['games_contra'],
                'diferencia_games' => $datos['diferencia_games'],
                'posicion'     => $datos['posicion'] ?? null,
            ];
            $this->db->where('torneo_id', $torneo_id)
                ->where('categoria_id', $categoria_id)
                ->where('zona_id', $zona_id)
                ->where('inscripcion_id', $inscripcion_id)
                ->update('tabla_posiciones', $camposActualizar);
        } else {
            $datos['torneo_id'] = $torneo_id;
            $datos['categoria_id'] = $categoria_id;
            $datos['zona_id'] = $zona_id;
            $datos['inscripcion_id'] = $inscripcion_id;
            $this->db->insert('tabla_posiciones', $datos);
        }
    }

    public function obtenerTablaZona($zona_id)
    {
        return $this->db
            ->from('tabla_posiciones tp')
            ->join('inscripciones i', 'i.id = tp.inscripcion_id')
            ->join('participantes p1', 'p1.id = i.participante1_id')
            ->join('participantes p2', 'p2.id = i.participante2_id')
            ->select("tp.*, CONCAT(p1.nombre, ' ', p1.apellido, ' - ', p2.nombre, ' ', p2.apellido) as pareja_nombre")
            ->where('tp.zona_id', $zona_id)
            ->order_by('tp.pg', 'DESC')      // partidos ganados
            ->order_by('tp.diferencia_games', 'DESC') // diferencia de games
            ->get()
            ->result();
    }

    public function buscarPartidosPorReferencia($ganadorTag, $perdedorTag)
    {
        return $this->db
            ->from('partidos')
            ->group_start()
                ->where('referencia1', $ganadorTag)
                ->or_where('referencia2', $ganadorTag)
                ->or_where('referencia1', $perdedorTag)
                ->or_where('referencia2', $perdedorTag)
            ->group_end()
            ->get()
            ->result();
    }

    public function guardarSeed($data)
    {
        /*
            $data esperado:
            [
                'codigo'         => '1A', '2B', etc
                'inscripcion_id' => int,
                'torneo_id'      => int,
                'categoria_id'   => int
            ]
        */

        $existe = $this->db
            ->where('torneo_id',   $data['torneo_id'])
            ->where('categoria_id',$data['categoria_id'])
            ->where('codigo',      $data['codigo'])
            ->get('seeds')
            ->row();

        if ($existe) {
            $this->db
                ->where('id', $existe->id)
                ->update('seeds', ['inscripcion_id' => $data['inscripcion_id']]);
        } else {
            $this->db->insert('seeds', $data);
        }
    }

    public function buscarPartidosPorSeed($codigo)
    {
        return $this->db
            ->from('partidos')
            ->where('referencia1', $codigo)
            ->or_where('referencia2', $codigo)
            ->get()
            ->result();
    }

    public function actualizarPartidoDatos($partido_id, $data)
    {
        /*
            $data puede contener:
            [
                'pareja1_id' => int,
                'pareja2_id' => int,
                'ganador_id' => int,
                'estado'     => 'pendiente'|'finalizado',
                'referencia1'=> string,
                'referencia2'=> string
            ]d
        */
        $this->db->where('id', $partido_id)
                ->update('partidos', $data);
    }

    public function partidosListosParaJugar()
    {
        return $this->db
            ->select('*')
            ->from('partidos')
            ->where('estado', 'pendiente')
            ->where('pareja1_id IS NOT NULL', null, false)
            ->where('pareja2_id IS NOT NULL', null, false)
            ->get()
            ->result();
    }

    public function activarPartido($partido_id)
    {
        $data = [
            'estado' => 'jugando',
            'fecha' => date('Y-m-d H:i:s') // opcional, si querés registrar cuándo se activa
        ];

        $this->db->where('id', $partido_id)
                ->update('partidos', $data);

        return $this->db->affected_rows() > 0;
    }

    public function obtener_clasificados_playoff($torneo_id, $categoria_id, $clasifican = 2)
    {
        return $this->db
            ->select('
                tp.torneo_id,
                tp.categoria_id,
                tp.zona_id,
                z.numero AS zona_numero,

                tp.inscripcion_id,
                tp.posicion,
                tp.puntos,
                tp.pg,
                tp.pj,

                p1.nombre || \' \' || p1.apellido || \' / \' ||
                p2.nombre || \' \' || p2.apellido AS pareja_nombre
            ')
            ->from('tabla_posiciones tp')

            ->join('zonas z', 'z.id = tp.zona_id')

            ->join('inscripciones i', 'i.id = tp.inscripcion_id')

            ->join('participantes p1', 'p1.id = i.participante1_id')
            ->join('participantes p2', 'p2.id = i.participante2_id')

            ->where('tp.torneo_id', $torneo_id)
            ->where('tp.categoria_id', $categoria_id)
            ->where('tp.posicion <=', $clasifican)

            ->order_by('z.numero', 'ASC')
            ->order_by('tp.posicion', 'ASC')
            ->get()
            ->result();
    }

    public function guardarTablaPosicion($data)
    {
        $this->db->where('zona_id', $data['zona_id']);
        $this->db->where('inscripcion_id', $data['inscripcion_id']);

        $existe = $this->db->get('tabla_posiciones')->row();

        if ($existe){
            $camposActualizar = [
                'pj'           => $data['pj'],
                'pg'           => $data['pg'],
                'pp'           => $data['pp'],
                'sets_favor'   => $data['sets_favor'],
                'sets_contra'  => $data['sets_contra'],
                'games_favor'  => $data['games_favor'],
                'games_contra' => $data['games_contra'],
                'diferencia_games' => $data['diferencia_games'],
                'posicion'     => $data['posicion'] ?? null
            ];
            $this->db->where('id', $existe->id)->update('tabla_posiciones', $camposActualizar);
        } else {
            $this->db->insert('tabla_posiciones', $data);
        }
    }

    public function obtenerPartidosFinalizadosZona($zona_id)
    {
        return $this->db
            ->select('p.id, p.zona_id, p.pareja1_id, p.pareja2_id, p.ganador_id')
            ->from('partidos p')
            ->where('p.zona_id', $zona_id)
            ->where('p.estado', 'finalizado')
            ->order_by('p.id', 'ASC')
            ->get()
            ->result();
    }

    public function obtenerSetsPartido($partido_id)
    {
        return $this->db
            ->select('
                numero_set,
                games_pareja1,
                games_pareja2
            ')
            ->from('partido_sets')
            ->where('partido_id', $partido_id)
            ->order_by('numero_set', 'ASC')
            ->get()
            ->result();
    }

    public function ObtenerResultados($torneo_id)
    {

    }

    public function obtenerPlayoffBracket($torneo_id, $categoria_id)
    {
        $partidos = $this->db->query("
            SELECT
                p.id,
                p.ronda,
                p.estado,
                p.pareja1_id,
                p.pareja2_id,
                p.ganador_id,
                p.referencia1,
                p.referencia2,
                p.cancha,
                p.hora,
                p.fecha,
                COALESCE(
                    CONCAT(p1a.apellido, ' ', p1a.nombre, ' / ', p1b.apellido, ' ', p1b.nombre),
                    p.referencia1
                ) AS pareja1_nombre,
                COALESCE(
                    CONCAT(p2a.apellido, ' ', p2a.nombre, ' / ', p2b.apellido, ' ', p2b.nombre),
                    p.referencia2
                ) AS pareja2_nombre,
                s1.games_pareja1 AS set1_p1, s1.games_pareja2 AS set1_p2,
                s2.games_pareja1 AS set2_p1, s2.games_pareja2 AS set2_p2,
                s3.games_pareja1 AS set3_p1, s3.games_pareja2 AS set3_p2
            FROM partidos p
            LEFT JOIN inscripciones i1  ON i1.id  = p.pareja1_id
            LEFT JOIN participantes p1a ON p1a.id = i1.participante1_id
            LEFT JOIN participantes p1b ON p1b.id = i1.participante2_id
            LEFT JOIN inscripciones i2  ON i2.id  = p.pareja2_id
            LEFT JOIN participantes p2a ON p2a.id = i2.participante1_id
            LEFT JOIN participantes p2b ON p2b.id = i2.participante2_id
            LEFT JOIN partido_sets s1 ON s1.partido_id = p.id AND s1.numero_set = 1
            LEFT JOIN partido_sets s2 ON s2.partido_id = p.id AND s2.numero_set = 2
            LEFT JOIN partido_sets s3 ON s3.partido_id = p.id AND s3.numero_set = 3
            WHERE p.torneo_id    = ?
              AND p.categoria_id = ?
              AND p.zona_id      IS NULL
              AND p.fase         = 'playoff'
            ORDER BY p.ronda ASC, p.id ASC
        ", [$torneo_id, $categoria_id])->result();

        $nombres = [
            1 => 'Reclasificación',
            2 => 'Cuartos',
            3 => 'Semifinal',
            4 => 'Final',
        ];

        $bracket = [];
        foreach ($partidos as $p) {
            $r = $p->ronda;
            if (!isset($bracket[$r])) {
                $bracket[$r] = [
                    'nombre'   => $nombres[$r] ?? "Ronda $r",
                    'partidos' => [],
                ];
            }
            $bracket[$r]['partidos'][] = $p;
        }

        return $bracket;
    }

}