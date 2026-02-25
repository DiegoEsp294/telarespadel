<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FixtureService
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Torneo_model');
    }

    /* ======================================
       FUNCIÓN PRINCIPAL
    ====================================== */

    public function generarFixture($torneo_id)
    {
        $this->CI->db->trans_start();

        $this->CI->Torneo_model->limpiarFixture($torneo_id);

        $categorias = $this->CI->Torneo_model->obtenerCategoriasFixture($torneo_id);
        foreach ($categorias as $categoria)
        {

            $parejas = $this->CI->Torneo_model->obtenerParejas(
                $torneo_id,
                $categoria->categoria_id
            );

            if (empty($parejas)) {
                continue;
            }

            if (count($parejas) < 3) {
                continue;
            }


            $config = $this->calcularZonasAPA(count($parejas));

            $zonas = $this->crearZonas(
                $torneo_id,
                $categoria->categoria_id,
                $config['zonas']
            );

            $zonasConParejas =
                $this->distribuirParejasSnake($zonas, $parejas);


            foreach ($zonasConParejas as $zona)
            {
                $total = count($zona['parejas']);

                if ($total == 3) {
                    $this->generarRoundRobinZona($zona['zona_id'], $zona['parejas']);
                }
                elseif ($total == 4) {
                    $this->generarZonaAPA4($zona['zona_id'], $zona['parejas']);
                }
            }
            // generar estructura de playoffs (solo estructura)
            $this->generarPlayoffsAPA(
                $torneo_id,
                $categoria->categoria_id,
                $config
            );
        }

            $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }


    /* ======================================
       APA — CALCULO ZONAS
    ====================================== */

    private function calcularZonasAPA($cantidad)
    {
        if ($cantidad >= 6 && $cantidad <= 8) {
            return ['zonas'=>2, 'fase'=>'semifinal'];
        }

        if ($cantidad >= 9 && $cantidad <= 11) {
            return ['zonas'=>3, 'fase'=>'mixto_3zonas'];
        }

        if ($cantidad >= 12 && $cantidad <= 14) {
            return ['zonas'=>4, 'fase'=>'cuartos_4zonas'];
        }

        if ($cantidad >= 15 && $cantidad <= 17) {
            return ['zonas'=>5, 'fase'=>'apa_5zonas'];
        }

        if ($cantidad >= 18 && $cantidad <= 20) {
            return ['zonas'=>6, 'fase'=>'octavos_6zonas'];
        }

        if ($cantidad >= 21 && $cantidad <= 23) {
            return ['zonas'=>8, 'fase'=>'octavos_8zonas'];
        }

        throw new Exception("Cantidad APA no soportada");
    }

    /* ======================================
       CREAR ZONAS
    ====================================== */

    private function crearZonas($torneo_id, $categoria_id, $cantidad)
    {
        $zonas = [];

        for ($i = 0; $i < $cantidad; $i++)
        {
            $nombre = chr(65 + $i);

            $data_zonas = [
                'torneo_id' => $torneo_id,
                'categoria_id' => $categoria_id,
                'nombre' => $nombre,
                'numero' => $i + 1
            ];
            $zona_id = $this->CI->Torneo_model->insertarZona($data_zonas);

            $zonas[] = [
                'zona_id' => $zona_id,
                'parejas' => []
            ];
        }

        return $zonas;
    }

    /* ======================================
       DISTRIBUCIÓN SNAKE
    ====================================== */

    private function distribuirParejasSnake($zonas, $parejas)
    {
        $direccion = 1;
        $indexZona = 0;
        $totalZonas = count($zonas);

        foreach ($parejas as $pareja)
        {
            $zonas[$indexZona]['parejas'][] = $pareja;

            $dataZonaParejas = [
                'zona_id' => $zonas[$indexZona]['zona_id'],
                'inscripcion_id' => $pareja->id
            ];
            $this->CI->Torneo_model->insertarZonaParejas($dataZonaParejas);

            $indexZona += $direccion;

            if ($indexZona >= $totalZonas) {
                $direccion = -1;
                $indexZona = $totalZonas - 1;
            }

            if ($indexZona < 0) {
                $direccion = 1;
                $indexZona = 0;
            }
        }

        return $zonas;
    }

    /* ======================================
       ROUND ROBIN
    ====================================== */

    private function generarRoundRobinZona($zona_id, $parejas)
    {
        $total = count($parejas);

        for ($i = 0; $i < $total; $i++)
        {
            for ($j = $i + 1; $j < $total; $j++)
            {
                $data_partidos = [
                    'torneo_id' => $parejas[$i]->torneo_id,
                    'zona_id' => $zona_id,
                    'categoria_id' => $parejas[$i]->categoria_id,
                    'pareja1_id' => $parejas[$i]->id,
                    'pareja2_id' => $parejas[$j]->id,
                    'estado' => 'pendiente'
                ];
                $this->CI->Torneo_model->insertarPartidos($data_partidos);
            }
        }
    }

    private function generarPlayoffsAPA($torneo_id, $categoria_id, $config)
    {
        switch ($config['fase'])
        {

            /*
            =====================================================
            6–8 PAREJAS
            2 zonas → semifinal directa
            =====================================================
            */
            case 'semifinal':

                $this->cruzar([
                    ['1A','2B'],
                    ['1B','2A'],
                ], 'semifinal');

            break;


            /*
            =====================================================
            9–11 PAREJAS
            3 zonas (APA real)
            1A y 1B pasan directo
            =====================================================
            */
            case 'mixto_3zonas':

                // CUARTOS
                $this->cruzar([
                    ['2B','2C'],
                    ['2A','1C'],
                ], 'cuartos');

                // SEMIS (esperan)
                $this->clasificadosDirectos([
                    '1A',
                    '1B'
                ], 'semifinal');

            break;


            /*
            =====================================================
            12–14 PAREJAS
            4 zonas → cuartos completos
            =====================================================
            */
            case 'cuartos_4zonas':

                $this->cruzar([
                    ['1A','2C'],
                    ['1C','2A'],
                    ['1B','2D'],
                    ['1D','2B'],
                ], 'cuartos');

            break;


            /*
            =====================================================
            15–17 PAREJAS
            5 zonas (estructura APA híbrida)
            =====================================================
            */
            case 'apa_5zonas':

                // REPECHAJE / PRE-CUARTOS
                $this->cruzar([
                    ['2B','2C'],
                    ['2A','2D'],
                    ['2E','1C'],
                ], 'repechaje');

                // DIRECTOS A SEMI
                $this->clasificadosDirectos([
                    '1A',
                    '1B',
                    '1D',
                    '1E'
                ], 'cuartos');

            break;


            /*
            =====================================================
            18–20 PAREJAS
            6 zonas → octavos APA
            =====================================================
            */
            case 'octavos_6zonas':

                $this->cruzar([

                    ['2F','2C'], // play-in
                    ['2A','1F'],
                    ['2E','2D'],

                ], 'reclasificacion');

                $this->cruzar([
                    ['1A','GANADOR_1'],
                    ['1E','2B'],
                    ['1D','GANADOR_2'],
                    ['1B','GANADOR_3'],
                ], 'cuartos');

            break;


            /*
            =====================================================
            21–23 PAREJAS
            8 zonas → octavos completos
            =====================================================
            */
            case 'octavos_8zonas':

                $this->cruzar([
                    ['1A','2F'],
                    ['1E','2C'],
                    ['1D','2B'],
                    ['1C','2A'],
                    ['1F','2D'],
                    ['1G','2E'],
                    ['1B','2H'],
                    ['1H','2G'],
                ], 'octavos');

            break;


            default:
                throw new Exception('Formato APA no soportado');
        }
    }

    private function cruzar($reglas, $fase)
    {
        // luego creamos los partidos eliminatorios
    }

    private function clasificadosDirectos($equipos, $fase)
    {
        // guardar seeds que esperan rival
    }

    private function generarZonaAPA4($zona_id, $parejas)
    {
        if (count($parejas) != 4) {
            throw new Exception('Zona APA4 requiere exactamente 4 parejas');
        }

        // mezclar para sorteo aleatorio
        // usort($parejas, fn($a,$b)=> $a->seed <=> $b->seed);

        $A = $parejas[0];
        $B = $parejas[1];
        $C = $parejas[2];
        $D = $parejas[3];

        /*
        =========================
        PARTIDOS INICIALES
        =========================
        */

        $p1 = $this->crearPartido($zona_id, $A, $B, 1);
        $p2 = $this->crearPartido($zona_id, $C, $D, 1);

        /*
        =========================
        CRUCES APA
        =========================

        Ganador P1 vs Perdedor P2
        Ganador P2 vs Perdedor P1

        (se resuelven luego al cargar resultados)
        */

        $this->crearPartidoCondicional(
            $zona_id,
            'GANADOR_'.$p1,
            'PERDEDOR_'.$p2,
            2
        );

        $this->crearPartidoCondicional(
            $zona_id,
            'GANADOR_'.$p2,
            'PERDEDOR_'.$p1,
            2
        );
    }

    private function crearPartido($zona_id, $p1, $p2, $ronda)
    {
        $data = [
            'zona_id' => $zona_id,
            'torneo_id' => $p1->torneo_id,
            'categoria_id' => $p1->categoria_id,
            'pareja1_id' => $p1->id,
            'pareja2_id' => $p2->id,
            'ronda' => $ronda,
            'estado' => 'pendiente'
        ];

        return $this->CI->Torneo_model->insertarPartidos($data);
    }

    private function crearPartidoCondicional($zona_id, $p1, $p2, $ronda)
    {
        $zona = $this->CI->Torneo_model->obtenerZona($zona_id);

        $data = [
            'torneo_id' => $zona->torneo_id,
            'categoria_id' => $zona->categoria_id,
            'zona_id' => $zona_id,
            'referencia1' => $p1,
            'referencia2' => $p2,
            'ronda' => $ronda,
            'estado' => 'pendiente'
        ];

        $this->CI->Torneo_model->insertarPartidos($data);
    }

    public function generarRoundRobin(array $jugadores)
    {
        $cantidad = count($jugadores);

        // Si es impar agregamos BYE
        if ($cantidad % 2 != 0) {
            $jugadores[] = null;
            $cantidad++;
        }

        $fechas = [];
        $rondas = $cantidad - 1;
        $mitad = $cantidad / 2;

        for ($r = 0; $r < $rondas; $r++) {

            $partidos = [];

            for ($i = 0; $i < $mitad; $i++) {

                $local = $jugadores[$i];
                $visitante = $jugadores[$cantidad - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $partidos[] = [
                        'local' => $local,
                        'visitante' => $visitante
                    ];
                }
            }

            $fechas[] = [
                'fecha' => $r + 1,
                'partidos' => $partidos
            ];

            // rotación (menos el primero)
            $ultimo = array_pop($jugadores);
            array_splice($jugadores, 1, 0, [$ultimo]);
        }

        return $fechas;
    }

    public function cerrarPartido($partido_id, $sets)
    {
        /*
            $sets esperado:

            [
                ['p1' => 6, 'p2' => 4],
                ['p1' => 3, 'p2' => 6],
                ['p1' => 6, 'p2' => 2],
            ]
        */

        $this->CI->db->trans_start();

        // ============================
        // 1. Obtener partido
        // ============================

        $partido = $this->CI->Torneo_model->obtenerPartidos($partido_id);

        if (!$partido) {
            throw new Exception('Partido inexistente');
        }

        // ============================
        // 2. Borrar sets anteriores
        // ============================


        $this->CI->Torneo_model->eliminarSetsAnteriores($partido_id);

        // ============================
        // 3. Guardar sets nuevos
        // ============================

        $setsGanadosP1 = 0;
        $setsGanadosP2 = 0;
        $numeroSet = 1;

        foreach ($sets as $set)
        {
            $dataSet = [
                'partido_id'     => $partido_id,
                'numero_set'     => $numeroSet,
                'games_pareja1'  => $set['p1'],
                'games_pareja2'  => $set['p2'],
            ];

            $this->CI->Torneo_model->insertarSet($dataSet);

            // contar sets ganados
            if ($set['p1'] > $set['p2']) {
                $setsGanadosP1++;
            } else {
                $setsGanadosP2++;
            }

            $numeroSet++;
        }

        $this->recalcularTablaZona($partido->zona_id);
        $this->recalcularTablaZona($partido->zona_id);

        // ============================
        // 4. Determinar ganador
        // ============================

        if ($setsGanadosP1 == $setsGanadosP2) {
            throw new Exception('No se puede empatar un partido');
        }

        $ganador_id =
            ($setsGanadosP1 > $setsGanadosP2)
            ? $partido->pareja1_id
            : $partido->pareja2_id;

        // ============================
        // 5. Cerrar partido
        // ============================

        $this->CI->Torneo_model->actualizarPartido($partido_id, $ganador_id);

        // ============================
        // 6. Avanzar ganador (PLAYOFF)
        // ============================

        if ($partido->partido_siguiente_id)
        {
            $campoDestino =
                ($partido->slot_siguiente == 1)
                ? 'pareja1_id'
                : 'pareja2_id';

            $this->CI->Torneo_model->avanzarGanador($partido->partido_siguiente_id,'pareja1_id', $ganador_id);

        }

        // ============================
        // 7. Resolver referencias APA
        // ============================

        $this->resolverReferencias($partido_id);

        // ============================
        // 8. Commit
        // ============================

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }

    private function resolverReferenciasPendientes($partido_id)
    {
        $partido = $this->CI->Torneo_model->obtenerPartidos($partido_id);

        if (!$partido || !$partido->ganador_id) {
            return;
        }

        $ganadorTag  = 'GANADOR_'.$partido_id;
        $perdedorTag = 'PERDEDOR_'.$partido_id;

        $perdedor_id =
            ($partido->ganador_id == $partido->pareja1_id)
            ? $partido->pareja2_id
            : $partido->pareja1_id;

        $pendientes = $this->CI->Torneo_model
            ->buscarPartidosPorReferencia($ganadorTag, $perdedorTag);

        foreach ($pendientes as $p)
        {
            $update = [];

            if ($p->referencia1 === $ganadorTag)
                $update['pareja1_id'] = $ganador_id = $partido->ganador_id;

            if ($p->referencia2 === $ganadorTag)
                $update['pareja2_id'] = $ganador_id;

            if ($p->referencia1 === $perdedorTag)
                $update['pareja1_id'] = $perdedor_id;

            if ($p->referencia2 === $perdedorTag)
                $update['pareja2_id'] = $perdedor_id;

            $this->CI->Torneo_model->actualizarPartidoDatos($p->id, $update);
        }
    }

    private function resolverReferencias($partido_id)
    {
        // completa partidos dependientes
        $this->resolverReferenciasPendientes($partido_id);

        // ahora verificamos si alguno quedó listo
        $listos = $this->CI->Torneo_model->partidosListosParaJugar();

        foreach ($listos as $p)
        {
            if ($p->estado === 'pendiente'
                && $p->pareja1_id
                && $p->pareja2_id)
            {
                $this->CI->Torneo_model->activarPartido($p->id);
            }
        }
    }

    private function filaBase()
    {
        return [
            'pj'=>0,
            'pg'=>0,
            'pp'=>0,
            'sets_favor'=>0,
            'sets_contra'=>0,
            'games_favor'=>0,
            'games_contra'=>0,
            'dif_games'=>0
        ];
    }

    private function procesarSets(&$p1, &$p2, $sets)
    {
        $setsP1 = 0;
        $setsP2 = 0;

        foreach ($sets as $set)
        {
            $p1['games_favor'] += $set->games_pareja1;
            $p1['games_contra'] += $set->games_pareja2;

            $p2['games_favor'] += $set->games_pareja2;
            $p2['games_contra'] += $set->games_pareja1;

            if ($set->games_pareja1 > $set->games_pareja2)
                $setsP1++;
            else
                $setsP2++;
        }

        $p1['pj']++;
        $p2['pj']++;

        if ($setsP1 > $setsP2) {
            $p1['pg']++;
            $p2['pp']++;
        } else {
            $p2['pg']++;
            $p1['pp']++;
        }

        $p1['sets_favor'] += $setsP1;
        $p1['sets_contra'] += $setsP2;

        $p2['sets_favor'] += $setsP2;
        $p2['sets_contra'] += $setsP1;

        $p1['dif_games'] = $p1['games_favor'] - $p1['games_contra'];
        $p2['dif_games'] = $p2['games_favor'] - $p2['games_contra'];
    }

    private function verificarClasificadosZona($zona_id)
    {
        $tabla = $this->CI->Torneo_model->obtenerTablaZona($zona_id);

        foreach ($tabla as $fila)
        {
            if ($fila->posicion == 1)
                $this->registrarSeed($zona_id,'1',$fila->inscripcion_id);

            if ($fila->posicion == 2)
                $this->registrarSeed($zona_id,'2',$fila->inscripcion_id);
        }
    }

    private function registrarSeed($zona_id,$puesto,$inscripcion_id)
    {
        $zona = $this->CI->Torneo_model->obtenerZona($zona_id);

        $codigo = $puesto.$zona->nombre; // 1A, 2B, etc

        $this->CI->Torneo_model->guardarSeed([
            'codigo' => $codigo,
            'inscripcion_id' => $inscripcion_id
        ]);

        // intenta completar cruces
        $this->resolverReferenciasSeed($codigo,$inscripcion_id);
    }

    private function resolverReferenciasSeed($codigo, $inscripcion_id)
    {
        $pendientes = $this->CI->Torneo_model
            ->buscarPartidosPorSeed($codigo);

        foreach ($pendientes as $p)
        {
            $update = [];

            if ($p->referencia1 === $codigo)
                $update['pareja1_id'] = $inscripcion_id;

            if ($p->referencia2 === $codigo)
                $update['pareja2_id'] = $inscripcion_id;

            $this->CI->Torneo_model
                ->actualizarPartidoDatos($p->id, $update);
        }
    }


    public function obtenerFixtureCompleto($torneo_id, $categoria_id)
    {
        $this->CI->db->select("
            z.id as zona_id,
            z.numero as grupo,

            p.id as partido_id,
            p.fecha,
            p.cancha,

            p.pareja1_id,
            p.pareja2_id,

            CONCAT(p1a.apellido,' ',p1a.nombre,' - ',p1b.apellido,' ',p1b.nombre) as pareja1_nombre,
            CONCAT(p2a.apellido,' ',p2a.nombre,' - ',p2b.apellido,' ',p2b.nombre) as pareja2_nombre
        ");

        $this->CI->db->from('partidos p');

        $this->CI->db->join('zonas z','z.id = p.zona_id');

        // pareja 1
        $this->CI->db->join('inscripciones ins1','ins1.id = p.pareja1_id','left');
        $this->CI->db->join('participantes p1a','p1a.id = ins1.participante1_id','left');
        $this->CI->db->join('participantes p1b','p1b.id = ins1.participante2_id','left');

        // pareja 2
        $this->CI->db->join('inscripciones ins2','ins2.id = p.pareja2_id','left');
        $this->CI->db->join('participantes p2a','p2a.id = ins2.participante1_id','left');
        $this->CI->db->join('participantes p2b','p2b.id = ins2.participante2_id','left');

        $this->CI->db->where('p.torneo_id', $torneo_id);
        $this->CI->db->where('z.categoria_id', $categoria_id);
        
        $this->CI->db->order_by('z.numero','ASC');
        $this->CI->db->order_by('p.id','ASC');

        $query = $this->CI->db->get()->result();

        /*
        =====================================
        ARMADO DE ESTRUCTURA PARA LA VISTA
        =====================================
        */

        $zonas = [];

        foreach ($query as $row)
        {
            $zona_id = $row->zona_id;

            // crear zona si no existe
            if (!isset($zonas[$zona_id]))
            {
                $zonas[$zona_id] = [
                    'grupo' => $row->grupo,
                    'parejas' => [],
                    'partidos' => [],
                    '_mapParejas' => [] // interno para numerar
                ];
            }

            /*
            ============================
            REGISTRAR PAREJAS (numeradas)
            ============================
            */

            if ($row->pareja1_id && !isset($zonas[$zona_id]['_mapParejas'][$row->pareja1_id]))
            {
                $numero = count($zonas[$zona_id]['parejas']) + 1;

                $zonas[$zona_id]['_mapParejas'][$row->pareja1_id] = $numero;

                $zonas[$zona_id]['parejas'][] = [
                    'numero' => $numero,
                    'nombre' => strtoupper($row->pareja1_nombre)
                ];
            }

            if ($row->pareja2_id && !isset($zonas[$zona_id]['_mapParejas'][$row->pareja2_id]))
            {
                $numero = count($zonas[$zona_id]['parejas']) + 1;

                $zonas[$zona_id]['_mapParejas'][$row->pareja2_id] = $numero;

                $zonas[$zona_id]['parejas'][] = [
                    'numero' => $numero,
                    'nombre' => strtoupper($row->pareja2_nombre)
                ];
            }

            /*
            ============================
            PARTIDOS (DUELOS)
            ============================
            */

            $n1 = $zonas[$zona_id]['_mapParejas'][$row->pareja1_id] ?? '?';
            $n2 = $zonas[$zona_id]['_mapParejas'][$row->pareja2_id] ?? '?';

            $zonas[$zona_id]['partidos'][] = [
                'duelo' => "{$n1} VS {$n2}",
                'dia'   => $this->formatearDia($row->fecha),
                'hora'  => $this->formatearHora($row->fecha),
                'cancha'=> $row->cancha
            ];
        }

        // limpiar mapa interno
        foreach ($zonas as &$z)
        {
            unset($z['_mapParejas']);
        }

        return array_values($zonas);
    }

    private function formatearDia($fecha)
    {
        if (!$fecha) return '-';

        return strtoupper(strftime('%A', strtotime($fecha)));
    }

    private function formatearHora($fecha)
    {
        if (!$fecha) return '-';

        return date('H\H\S', strtotime($fecha));
    }
}