<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FixtureService
{
    private $CI;
    private $_torneo_id;
    private $_categoria_id;

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

    /* ======================================
       ZONA APA 4 PAREJAS
       Ronda 1: 1v3  y  2v4
       Ronda 2: G(M1)vP(M2)  y  G(M2)vP(M1)
       1°=ganador cruce1, 2°=ganador cruce2
    ====================================== */

    private function generarZonaAPA4($zona_id, $parejas)
    {
        $tid = $parejas[0]->torneo_id;
        $cid = $parejas[0]->categoria_id;

        // Ronda 1
        $m1 = $this->CI->Torneo_model->insertarPartidos([
            'torneo_id'    => $tid,
            'zona_id'      => $zona_id,
            'categoria_id' => $cid,
            'pareja1_id'   => $parejas[0]->id,  // P1
            'pareja2_id'   => $parejas[2]->id,  // P3
            'estado'       => 'pendiente',
            'ronda'        => 1,
        ]);

        $m2 = $this->CI->Torneo_model->insertarPartidos([
            'torneo_id'    => $tid,
            'zona_id'      => $zona_id,
            'categoria_id' => $cid,
            'pareja1_id'   => $parejas[1]->id,  // P2
            'pareja2_id'   => $parejas[3]->id,  // P4
            'estado'       => 'pendiente',
            'ronda'        => 1,
        ]);

        // Ronda 2 (cruces, sin parejas por ahora)
        $m3 = $this->CI->Torneo_model->insertarPartidos([
            'torneo_id'    => $tid,
            'zona_id'      => $zona_id,
            'categoria_id' => $cid,
            'estado'       => 'pendiente',
            'ronda'        => 2,
        ]);

        $m4 = $this->CI->Torneo_model->insertarPartidos([
            'torneo_id'    => $tid,
            'zona_id'      => $zona_id,
            'categoria_id' => $cid,
            'estado'       => 'pendiente',
            'ronda'        => 2,
        ]);

        // M1: ganador → M3(slot 1), perdedor → M4(slot 2)
        $this->CI->Torneo_model->actualizarPartido($m1, [
            'partido_siguiente_id'            => $m3,
            'slot_siguiente'                  => 1,
            'partido_siguiente_perdedor_id'   => $m4,
            'slot_siguiente_perdedor'         => 2,
        ]);

        // M2: ganador → M4(slot 1), perdedor → M3(slot 2)
        $this->CI->Torneo_model->actualizarPartido($m2, [
            'partido_siguiente_id'            => $m4,
            'slot_siguiente'                  => 1,
            'partido_siguiente_perdedor_id'   => $m3,
            'slot_siguiente_perdedor'         => 2,
        ]);
    }

    private function generarPlayoffsAPA($torneo_id, $categoria_id, $config)
    {
        $this->_torneo_id    = $torneo_id;
        $this->_categoria_id = $categoria_id;

        switch ($config['fase'])
        {

            /*
            =====================================================
            6–8 PAREJAS — 2 zonas → semifinal directa
            SF1: 1A vs 2B  |  SF2: 1B vs 2A
            =====================================================
            */
            case 'semifinal':

                [$sf1, $sf2] = $this->cruzar([
                    ['1A','2B'],
                    ['1B','2A'],
                ], 3);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1,  ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2,  ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            /*
            =====================================================
            9–11 PAREJAS — 3 zonas
            Cuartos: 2B vs 2C  |  2A vs 1C
            1A y 1B pasan directo a semis
            SF1: 1A vs GanadorQ1  |  SF2: 1B vs GanadorQ2
            =====================================================
            */
            case 'mixto_3zonas':

                [$q1, $q2] = $this->cruzar([
                    ['2B','2C'],
                    ['2A','1C'],
                ], 2);

                $sf1 = $this->crearPartidoPlayoff('1A', null, 3);
                $sf2 = $this->crearPartidoPlayoff('1B', null, 3);

                $this->CI->Torneo_model->actualizarPartido($q1, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($q2, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 2]);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1, ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2, ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            /*
            =====================================================
            12–14 PAREJAS — 4 zonas → cuartos completos
            Q1: 1A vs 2C  |  Q2: 1C vs 2A
            Q3: 1B vs 2D  |  Q4: 1D vs 2B
            SF1: GQ1 vs GQ2  |  SF2: GQ3 vs GQ4
            =====================================================
            */
            case 'cuartos_4zonas':

                [$q1, $q2, $q3, $q4] = $this->cruzar([
                    ['1A','2C'],
                    ['1C','2A'],
                    ['1B','2D'],
                    ['1D','2B'],
                ], 2);

                $sf1 = $this->crearPartidoPlayoff(null, null, 3);
                $sf2 = $this->crearPartidoPlayoff(null, null, 3);

                $this->CI->Torneo_model->actualizarPartido($q1, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q2, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($q3, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q4, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 2]);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1, ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2, ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            /*
            =====================================================
            15–17 PAREJAS — 5 zonas (APA híbrida)
            Reclasificación: 2B vs 2C | 2A vs 2D
            Cuartos: 1A vs G(r1) | 1E vs 1D | 1C vs 2E | 1B vs G(r2)
            Semis:   G(Q1) vs G(Q2) | G(Q3) vs G(Q4)
            Final:   G(SF1) vs G(SF2)
            =====================================================
            */
            case 'apa_5zonas':

                [$r1, $r2] = $this->cruzar([
                    ['2B','2C'],
                    ['2A','2D'],
                ], 1);

                // Q1: 1A vs G(r1)
                $q1 = $this->crearPartidoPlayoff('1A', null, 2);
                $this->CI->Torneo_model->actualizarPartido($r1, ['partido_siguiente_id' => $q1, 'slot_siguiente' => 2]);

                // Q2: 1E vs 1D (directo)
                $q2 = $this->crearPartidoPlayoff('1E', '1D', 2);

                // Q3: 1C vs 2E (directo)
                $q3 = $this->crearPartidoPlayoff('1C', '2E', 2);

                // Q4: 1B vs G(r2)
                $q4 = $this->crearPartidoPlayoff('1B', null, 2);
                $this->CI->Torneo_model->actualizarPartido($r2, ['partido_siguiente_id' => $q4, 'slot_siguiente' => 2]);

                $sf1 = $this->crearPartidoPlayoff(null, null, 3);
                $sf2 = $this->crearPartidoPlayoff(null, null, 3);

                $this->CI->Torneo_model->actualizarPartido($q1, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q2, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($q3, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q4, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 2]);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1, ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2, ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            /*
            =====================================================
            18–20 PAREJAS — 6 zonas
            Octavos:  2F vs 2C | 1E vs 2B | 2A vs 1F | 2E vs 2D
            Cuartos:  1A vs G(R1) | G(R2) vs 1D | 1C vs G(R3) | G(R4) vs 1B
            Semis:    G(Q1) vs G(Q2) | G(Q3) vs G(Q4)
            =====================================================
            */
            case 'octavos_6zonas':

                [$r1, $r2, $r3, $r4] = $this->cruzar([
                    ['2F','2C'],
                    ['1E','2B'],
                    ['2A','1F'],
                    ['2E','2D'],
                ], 1);

                // Q1: 1A vs G(R1)
                $q1 = $this->crearPartidoPlayoff('1A', null, 2);
                $this->CI->Torneo_model->actualizarPartido($r1, ['partido_siguiente_id' => $q1, 'slot_siguiente' => 2]);

                // Q2: G(R2) vs 1D
                $q2 = $this->crearPartidoPlayoff(null, '1D', 2);
                $this->CI->Torneo_model->actualizarPartido($r2, ['partido_siguiente_id' => $q2, 'slot_siguiente' => 1]);

                // Q3: 1C vs G(R3)
                $q3 = $this->crearPartidoPlayoff('1C', null, 2);
                $this->CI->Torneo_model->actualizarPartido($r3, ['partido_siguiente_id' => $q3, 'slot_siguiente' => 2]);

                // Q4: G(R4) vs 1B
                $q4 = $this->crearPartidoPlayoff(null, '1B', 2);
                $this->CI->Torneo_model->actualizarPartido($r4, ['partido_siguiente_id' => $q4, 'slot_siguiente' => 1]);

                $sf1 = $this->crearPartidoPlayoff(null, null, 3);
                $sf2 = $this->crearPartidoPlayoff(null, null, 3);

                $this->CI->Torneo_model->actualizarPartido($q1, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q2, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($q3, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q4, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 2]);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1, ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2, ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            /*
            =====================================================
            21–23 PAREJAS — 8 zonas → octavos completos
            =====================================================
            */
            case 'octavos_8zonas':

                [$o1,$o2,$o3,$o4,$o5,$o6,$o7,$o8] = $this->cruzar([
                    ['1A','2F'],
                    ['1E','2C'],
                    ['1D','2B'],
                    ['1C','2A'],
                    ['1F','2D'],
                    ['1G','2E'],
                    ['1B','2H'],
                    ['1H','2G'],
                ], 1);

                $q1 = $this->crearPartidoPlayoff(null, null, 2);
                $q2 = $this->crearPartidoPlayoff(null, null, 2);
                $q3 = $this->crearPartidoPlayoff(null, null, 2);
                $q4 = $this->crearPartidoPlayoff(null, null, 2);

                $this->CI->Torneo_model->actualizarPartido($o1, ['partido_siguiente_id' => $q1, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($o2, ['partido_siguiente_id' => $q1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($o3, ['partido_siguiente_id' => $q2, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($o4, ['partido_siguiente_id' => $q2, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($o5, ['partido_siguiente_id' => $q3, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($o6, ['partido_siguiente_id' => $q3, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($o7, ['partido_siguiente_id' => $q4, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($o8, ['partido_siguiente_id' => $q4, 'slot_siguiente' => 2]);

                $sf1 = $this->crearPartidoPlayoff(null, null, 3);
                $sf2 = $this->crearPartidoPlayoff(null, null, 3);

                $this->CI->Torneo_model->actualizarPartido($q1, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q2, ['partido_siguiente_id' => $sf1, 'slot_siguiente' => 2]);
                $this->CI->Torneo_model->actualizarPartido($q3, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($q4, ['partido_siguiente_id' => $sf2, 'slot_siguiente' => 2]);

                $final = $this->crearPartidoPlayoff(null, null, 4);

                $this->CI->Torneo_model->actualizarPartido($sf1, ['partido_siguiente_id' => $final, 'slot_siguiente' => 1]);
                $this->CI->Torneo_model->actualizarPartido($sf2, ['partido_siguiente_id' => $final, 'slot_siguiente' => 2]);

            break;


            default:
                throw new Exception('Formato APA no soportado');
        }
    }

    /*
     * Crea partidos de playoff con referencias seed (ej: '1A', '2B')
     * Devuelve array de IDs en el mismo orden que $reglas.
     */
    private function cruzar($reglas, $ronda)
    {
        $ids = [];
        foreach ($reglas as $regla) {
            $ids[] = $this->crearPartidoPlayoff($regla[0], $regla[1], $ronda);
        }
        return $ids;
    }

    /*
     * Inserta un partido de playoff.
     * $ref1 / $ref2 pueden ser seed codes ('1A', '2B') o null.
     */
    private function crearPartidoPlayoff($ref1, $ref2, $ronda)
    {
        $data = [
            'torneo_id'    => $this->_torneo_id,
            'categoria_id' => $this->_categoria_id,
            'referencia1'  => $ref1,
            'referencia2'  => $ref2,
            'ronda'        => $ronda,
            'fase'         => 'playoff',
            'estado'       => 'pendiente',
        ];
        return $this->CI->Torneo_model->insertarPartidos($data);
    }

    private function clasificadosDirectos($equipos, $fase)
    {
        // Lógica integrada en cada case de generarPlayoffsAPA
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

        $this->CI->Torneo_model->actualizarPartido($partido_id, ["ganador_id" => $ganador_id, "estado" => "finalizado"]);

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
        $ganador_id = $partido->ganador_id;
        $perdedor_id = $partido->pareja1_id + $partido->pareja2_id - $ganador_id;

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
                $update['pareja1_id'] = $ganador_id;

            if ($p->referencia2 === $ganadorTag)
                $update['pareja2_id'] = $ganador_id;

            if ($p->referencia1 === $perdedorTag)
                $update['pareja1_id'] = $perdedor_id;

            if ($p->referencia2 === $perdedorTag)
                $update['pareja2_id'] = $perdedor_id;

            if($update){
                $this->CI->Torneo_model->actualizarPartidoDatos($p->id, $update);
            }
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
            'diferencia_games'=>0
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

        $p1['diferencia_games'] = $p1['games_favor'] - $p1['games_contra'];
        $p2['diferencia_games'] = $p2['games_favor'] - $p2['games_contra'];
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
            'inscripcion_id' => $inscripcion_id,
            'torneo_id' => $zona->torneo_id,
            'categoria_id' => $zona->categoria_id
        ]);

        // Los cruces se asignan manualmente desde el admin — no resolución automática
    }

    private function resolverReferenciasSeed($codigo, $inscripcion_id, $torneo_id, $categoria_id)
    {
        $pendientes = $this->CI->Torneo_model
            ->buscarPartidosPorSeed($codigo, $torneo_id, $categoria_id);

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
            p.hora,
            p.ronda,

            p.pareja1_id,
            p.pareja2_id,

            CONCAT(p1a.apellido,' ',p1a.nombre,' - ',p1b.apellido,' ',p1b.nombre) as pareja1_nombre,
            CONCAT(p2a.apellido,' ',p2a.nombre,' - ',p2b.apellido,' ',p2b.nombre) as pareja2_nombre,

            s1.games_pareja1 as set1_p1,
            s1.games_pareja2 as set1_p2,
            s2.games_pareja1 as set2_p1,
            s2.games_pareja2 as set2_p2,
            s3.games_pareja1 as set3_p1,
            s3.games_pareja2 as set3_p2

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

        // sets
        $this->CI->db->join('partido_sets s1', 's1.partido_id = p.id AND s1.numero_set = 1', 'left');
        $this->CI->db->join('partido_sets s2', 's2.partido_id = p.id AND s2.numero_set = 2', 'left');
        $this->CI->db->join('partido_sets s3', 's3.partido_id = p.id AND s3.numero_set = 3', 'left');

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

        // Agrupar filas por zona
        $filasPorZona = [];
        foreach ($query as $row)
        {
            $filasPorZona[$row->zona_id][] = $row;
        }

        foreach ($filasPorZona as $zona_id => $filas)
        {
            $zonas[$zona_id] = [
                'grupo'       => $filas[0]->grupo,
                'parejas'     => [],
                'partidos'    => [],
                '_mapParejas' => []
            ];

            /*
            ============================
            REGISTRAR PAREJAS (numeradas)
            Paso 1: todas las pareja1 primero
            Paso 2: todas las pareja2 que aún no tengan número
            Esto garantiza que en zonas de 4 (bracket) quede 1 VS 3 y 2 VS 4
            ============================
            */

            foreach ($filas as $row)
            {
                if ($row->pareja1_id && !isset($zonas[$zona_id]['_mapParejas'][$row->pareja1_id]))
                {
                    $numero = count($zonas[$zona_id]['parejas']) + 1;
                    $zonas[$zona_id]['_mapParejas'][$row->pareja1_id] = $numero;
                    $zonas[$zona_id]['parejas'][] = [
                        'numero' => $numero,
                        'nombre' => strtoupper($row->pareja1_nombre)
                    ];
                }
            }

            foreach ($filas as $row)
            {
                if ($row->pareja2_id && !isset($zonas[$zona_id]['_mapParejas'][$row->pareja2_id]))
                {
                    $numero = count($zonas[$zona_id]['parejas']) + 1;
                    $zonas[$zona_id]['_mapParejas'][$row->pareja2_id] = $numero;
                    $zonas[$zona_id]['parejas'][] = [
                        'numero' => $numero,
                        'nombre' => strtoupper($row->pareja2_nombre)
                    ];
                }
            }

            /*
            ============================
            PARTIDOS (DUELOS)
            Cuando ambas parejas son null (no decididas aún),
            se muestran etiquetas G/P en lugar de ?
            ============================
            */

            $contadorNulos = 0;
            foreach ($filas as $row)
            {
                if (!$row->pareja1_id && !$row->pareja2_id)
                {
                    $contadorNulos++;
                    $duelo = $contadorNulos === 1 ? 'G1 VS P2' : 'G2 VS P1';
                }
                else
                {
                    $n1 = ($row->pareja1_id && isset($zonas[$zona_id]['_mapParejas'][$row->pareja1_id]))
                        ? $zonas[$zona_id]['_mapParejas'][$row->pareja1_id] : '?';
                    $n2 = ($row->pareja2_id && isset($zonas[$zona_id]['_mapParejas'][$row->pareja2_id]))
                        ? $zonas[$zona_id]['_mapParejas'][$row->pareja2_id] : '?';
                    $duelo = "{$n1} VS {$n2}";
                }

                $zonas[$zona_id]['partidos'][] = [
                    'duelo'      => $duelo,
                    'ronda'      => $row->ronda,
                    'dia'        => $this->formatearDia($row->fecha),
                    'fecha'      => $row->fecha,
                    'hora'       => $this->formatearHora($row->hora),
                    'cancha'     => $row->cancha,
                    'partido_id' => $row->partido_id,

                    // SETS
                    'set1_p1' => $row->set1_p1,
                    'set1_p2' => $row->set1_p2,
                    'set2_p1' => $row->set2_p1,
                    'set2_p2' => $row->set2_p2,
                    'set3_p1' => $row->set3_p1,
                    'set3_p2' => $row->set3_p2,
                ];
            }
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

        $dias = [
            'Sunday' => 'DOMINGO',
            'Monday' => 'LUNES',
            'Tuesday' => 'MARTES',
            'Wednesday' => 'MIÉRCOLES',
            'Thursday' => 'JUEVES',
            'Friday' => 'VIERNES',
            'Saturday' => 'SÁBADO'
        ];

        $diaIngles = date('l', strtotime($fecha));

        return $dias[$diaIngles] ?? '-';
    }

    private function formatearHora($hora)
    {
        if (!$hora) return '-';

        return date('H:i', strtotime($hora));
    }

    public function cargarResultadoPartido($partido_id, $set1_p1, $set1_p2, $set2_p1, $set2_p2, $set3_p1 = null, $set3_p2 = null)
    {
        $this->CI->db->trans_start();

        // ============================
        // 1. Obtener partido
        // ============================
        $partido = $this->CI->Torneo_model->obtenerPartidos($partido_id);
        if (!$partido) {
            throw new Exception('Partido inexistente');
        }

        $pareja1 = $partido->pareja1_id;
        $pareja2 = $partido->pareja2_id;
        $fase = $partido->fase;

        // ============================
        // 2. Borrar sets anteriores
        // ============================
        $this->CI->Torneo_model->eliminarSetsAnteriores($partido_id);

        // ============================
        // 3. Insertar sets nuevos
        // ============================
        $sets = [
            ['p1' => $set1_p1, 'p2' => $set1_p2],
            ['p1' => $set2_p1, 'p2' => $set2_p2]
        ];
        if ($set3_p1 !== null && $set3_p2 !== null) {
            $sets[] = ['p1' => $set3_p1, 'p2' => $set3_p2];
        }

        $setsGanadosP1 = 0;
        $setsGanadosP2 = 0;
        $numeroSet = 1;

        foreach ($sets as $set) {
            $dataSet = [
                'partido_id'    => $partido_id,
                'numero_set'    => $numeroSet,
                'games_pareja1' => $set['p1'],
                'games_pareja2' => $set['p2'],
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

        // ============================
        // 4. Determinar ganador
        // ============================
        if ($setsGanadosP1 == $setsGanadosP2) {
            throw new Exception('No se puede empatar un partido');
        }

        $ganador_id = ($setsGanadosP1 > $setsGanadosP2) ? $pareja1 : $pareja2;

        // ============================
        // 5. Guardar resumen resultado
        // ============================
        $this->CI->Torneo_model->eliminarResultadosAnteriores($partido_id);

        $this->CI->Torneo_model->insertarResultado([
            'partido_id' => $partido_id,
            'set1_p1'    => $set1_p1,
            'set1_p2'    => $set1_p2,
            'set2_p1'    => $set2_p1,
            'set2_p2'    => $set2_p2,
            'set3_p1'    => $set3_p1,
            'set3_p2'    => $set3_p2,
            'ganador_id' => $ganador_id
        ]);

        // ============================
        // 6. Actualizar partido
        // ============================
        $this->CI->Torneo_model->actualizarPartido($partido_id, [
            'estado'     => 'finalizado',
            'ganador_id' => $ganador_id
        ]);

        // ============================
        // 7. Actualizar tabla de posiciones si es fase zona
        // ============================
        if ($fase === 'zona') {
            $this->recalcularTablaZona($partido->zona_id);
        }

        // ============================
        // 8. Avanzar ganador en eliminación
        // ============================
        if ($partido->partido_siguiente_id) {
            $campoDestino = ($partido->slot_siguiente == 1) ? 'pareja1_id' : 'pareja2_id';
            $this->CI->Torneo_model->avanzarGanador(
                $partido->partido_siguiente_id,
                $campoDestino,
                $ganador_id
            );
        }

        // ============================
        // 8b. Avanzar perdedor (zona APA 4 parejas)
        // ============================
        if (!empty($partido->partido_siguiente_perdedor_id)) {
            $perdedor_id  = ($ganador_id == $pareja1) ? $pareja2 : $pareja1;
            $campoDestino = ($partido->slot_siguiente_perdedor == 1) ? 'pareja1_id' : 'pareja2_id';
            $this->CI->Torneo_model->avanzarGanador(
                $partido->partido_siguiente_perdedor_id,
                $campoDestino,
                $perdedor_id
            );
        }

        // ============================
        // 9. Resolver referencias APA
        // ============================
        $this->resolverReferencias($partido_id);

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }

    // private function recalcularTablaZona($zona_id)
    // {
    //     // Obtener todos los partidos de la zona
    //     $partidos = $this->CI->Torneo_model->obtenerPartidosPorZona($zona_id);

    //     // Inicializar tabla de posiciones
    //     $tabla = [];

    //     foreach ($partidos as $partido) {
    //         if (!$partido->ganador_id) continue;

    //         // Inicializar filas si no existen
    //         if (!isset($tabla[$partido->pareja1_id])) $tabla[$partido->pareja1_id] = $this->filaBase();
    //         if (!isset($tabla[$partido->pareja2_id])) $tabla[$partido->pareja2_id] = $this->filaBase();

    //         $tabla[$partido->pareja1_id]['torneo_id'] = $partido->torneo_id;
    //         $tabla[$partido->pareja1_id]['categoria_id'] = $partido->categoria_id;
    //         $tabla[$partido->pareja2_id]['torneo_id'] = $partido->torneo_id;
    //         $tabla[$partido->pareja2_id]['categoria_id'] = $partido->categoria_id;

    //         // Obtener sets del partido
    //         $sets = $this->CI->Torneo_model->obtenerSets($partido->id);

    //         // Actualizar estadísticas
    //         $this->procesarSets($tabla[$partido->pareja1_id], $tabla[$partido->pareja2_id], $sets);
    //     }

    //     // Guardar tabla actualizada en la BD
    //     foreach ($tabla as $inscripcion_id => $fila) {
    //         $this->CI->Torneo_model->actualizarTablaZona($zona_id, $inscripcion_id, $fila);
    //     }

    //     // Registrar seeds (1° y 2°)
    //     $this->verificarClasificadosZona($zona_id);
    // }

    private function recalcularTablaZona($zona_id)
    {
        // Zonas de 4 parejas usan el formato APA (cruces), no round-robin
        $num_parejas = $this->CI->db
            ->where('zona_id', $zona_id)
            ->count_all_results('zona_parejas');

        if ($num_parejas == 4) {
            $this->recalcularTablaZonaAPA4($zona_id);
            return;
        }

        // 1️⃣ obtener todos los partidos finalizados de la zona
        $partidos = $this->CI->Torneo_model->obtenerPartidosFinalizadosZona($zona_id);

        // 2️⃣ inicializar tabla
        $tabla = [];

        foreach ($partidos as $p)
        {
            if (!isset($tabla[$p->pareja1_id]))
                $tabla[$p->pareja1_id] = $this->filaBase();

            if (!isset($tabla[$p->pareja2_id]))
                $tabla[$p->pareja2_id] = $this->filaBase();

            $sets = $this->CI->Torneo_model->obtenerSetsPartido($p->id);

            $this->procesarSets(
                $tabla[$p->pareja1_id],
                $tabla[$p->pareja2_id],
                $sets
            );
        }

        /*
        ============================
        3️⃣ ORDENAR TABLA (CLAVE)
        ============================
        */

        uasort($tabla, function($a, $b) {

            // PG
            if ($a['pg'] != $b['pg'])
                return $b['pg'] <=> $a['pg'];

            // diferencia sets
            $dsA = $a['sets_favor'] - $a['sets_contra'];
            $dsB = $b['sets_favor'] - $b['sets_contra'];

            if ($dsA != $dsB)
                return $dsB <=> $dsA;

            // diferencia games
            if ($a['diferencia_games'] != $b['diferencia_games'])
                return $b['diferencia_games'] <=> $a['diferencia_games'];

            // games favor
            return $b['games_favor'] <=> $a['games_favor'];
        });

        /*
        ============================
        4️⃣ GUARDAR POSICIONES
        ============================
        */

        $posicion = 1;
        $zona = $this->CI->Torneo_model->obtenerZona($zona_id);

        foreach ($tabla as $inscripcion_id => $stats)
        {
            $this->CI->Torneo_model->guardarTablaPosicion([
                'zona_id'        => $zona_id,
                'inscripcion_id' => $inscripcion_id,
                'torneo_id' => $zona->torneo_id,
                'categoria_id' => $zona->categoria_id,
                'pj' => $stats['pj'],
                'pg' => $stats['pg'],
                'pp' => $stats['pp'],
                'sets_favor' => $stats['sets_favor'],
                'sets_contra' => $stats['sets_contra'],
                'games_favor' => $stats['games_favor'],
                'games_contra' => $stats['games_contra'],
                'diferencia_games' => $stats['diferencia_games'],
                'posicion' => $posicion
            ]);

            $posicion++;
        }

        $this->verificarClasificadosZona($zona_id);
    }

    /* ======================================
       RECALCULO TABLA — ZONA APA 4 PAREJAS
    ====================================== */

    private function recalcularTablaZonaAPA4($zona_id)
    {
        // 1. Inicializar stats para las 4 parejas de la zona
        $zona_parejas = $this->CI->db
            ->select('inscripcion_id')
            ->from('zona_parejas')
            ->where('zona_id', $zona_id)
            ->get()
            ->result();

        $tabla = [];
        foreach ($zona_parejas as $zp) {
            $tabla[$zp->inscripcion_id] = $this->filaBase();
        }

        // 2. Acumular stats de todos los partidos finalizados (ronda 1 y 2)
        $partidos = $this->CI->Torneo_model->obtenerPartidosFinalizadosZona($zona_id);
        foreach ($partidos as $p) {
            if (!isset($tabla[$p->pareja1_id])) $tabla[$p->pareja1_id] = $this->filaBase();
            if (!isset($tabla[$p->pareja2_id])) $tabla[$p->pareja2_id] = $this->filaBase();
            $sets = $this->CI->Torneo_model->obtenerSetsPartido($p->id);
            $this->procesarSets($tabla[$p->pareja1_id], $tabla[$p->pareja2_id], $sets);
        }

        // 3. Determinar posiciones según resultados de ronda 2
        $posiciones = [];

        $cruces = $this->CI->db
            ->select('id, pareja1_id, pareja2_id, ganador_id')
            ->from('partidos')
            ->where('zona_id', $zona_id)
            ->where('ronda', 2)
            ->where('estado', 'finalizado')
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        $idx = 0;
        foreach ($cruces as $c) {
            $perdedor = ($c->ganador_id == $c->pareja1_id) ? $c->pareja2_id : $c->pareja1_id;
            if ($idx === 0) {
                $posiciones[$c->ganador_id] = 1;
                $posiciones[$perdedor]      = 3;
            } else {
                $posiciones[$c->ganador_id] = 2;
                $posiciones[$perdedor]      = 4;
            }
            $idx++;
        }

        // 4. Guardar tabla
        $zona = $this->CI->Torneo_model->obtenerZona($zona_id);
        foreach ($tabla as $inscripcion_id => $stats) {
            $this->CI->Torneo_model->guardarTablaPosicion([
                'zona_id'          => $zona_id,
                'inscripcion_id'   => $inscripcion_id,
                'torneo_id'        => $zona->torneo_id,
                'categoria_id'     => $zona->categoria_id,
                'pj'               => $stats['pj'],
                'pg'               => $stats['pg'],
                'pp'               => $stats['pp'],
                'sets_favor'       => $stats['sets_favor'],
                'sets_contra'      => $stats['sets_contra'],
                'games_favor'      => $stats['games_favor'],
                'games_contra'     => $stats['games_contra'],
                'diferencia_games' => $stats['diferencia_games'],
                'posicion'         => $posiciones[$inscripcion_id] ?? null,
            ]);
        }

        $this->verificarClasificadosZona($zona_id);
    }

    /* ======================================
       GENERACIÓN MANUAL DESDE CONFIGURACIÓN
    ====================================== */

    public function generarPartidosDesdeCofiguracion($torneo_id, $categoria_id)
    {
        $this->CI->db->trans_start();

        $this->_torneo_id    = $torneo_id;
        $this->_categoria_id = $categoria_id;

        // 1. Borrar partidos existentes (conserva zonas y zona_parejas)
        $this->limpiarSoloMatchesCategoria($torneo_id, $categoria_id);

        // 2. Leer zonas y sus parejas asignadas
        $zonas_db = $this->CI->Torneo_model->obtenerZonasPorCategoria($torneo_id, $categoria_id);

        if (empty($zonas_db)) {
            $this->CI->db->trans_complete();
            return false;
        }

        // 3. Generar partidos por zona según cantidad de parejas
        foreach ($zonas_db as $zona)
        {
            $parejas = $this->CI->db
                ->select('i.*')
                ->from('zona_parejas zp')
                ->join('inscripciones i', 'i.id = zp.inscripcion_id')
                ->where('zp.zona_id', $zona->id)
                ->get()
                ->result();

            $n = count($parejas);
            if ($n < 2) continue;

            if ($n == 4) {
                $this->generarZonaAPA4($zona->id, $parejas);
            } else {
                $this->generarRoundRobinZona($zona->id, $parejas);
            }
        }

        // 4. Generar estructura de playoffs según cantidad de zonas
        $num_zonas = count($zonas_db);
        try {
            $config = $this->determinarConfigPorZonas($num_zonas);
            $this->generarPlayoffsAPA($torneo_id, $categoria_id, $config);
        } catch (Exception $e) {
            log_message('error', 'generarPartidosDesdeCofiguracion: ' . $e->getMessage());
        }

        $this->CI->db->trans_complete();
        return $this->CI->db->trans_status();
    }

    private function determinarConfigPorZonas($num_zonas)
    {
        $mapa = [
            2 => ['zonas' => 2, 'fase' => 'semifinal'],
            3 => ['zonas' => 3, 'fase' => 'mixto_3zonas'],
            4 => ['zonas' => 4, 'fase' => 'cuartos_4zonas'],
            5 => ['zonas' => 5, 'fase' => 'apa_5zonas'],
            6 => ['zonas' => 6, 'fase' => 'octavos_6zonas'],
            8 => ['zonas' => 8, 'fase' => 'octavos_8zonas'],
        ];

        if (!isset($mapa[$num_zonas])) {
            throw new Exception("Número de zonas no soportado: $num_zonas");
        }

        return $mapa[$num_zonas];
    }

    private function limpiarSoloMatchesCategoria($torneo_id, $categoria_id)
    {
        $this->CI->db->query("
            DELETE FROM partido_sets WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ? AND categoria_id = ?
            )
        ", [$torneo_id, $categoria_id]);

        $this->CI->db->query("
            DELETE FROM resultados_partido WHERE partido_id IN (
                SELECT id FROM partidos WHERE torneo_id = ? AND categoria_id = ?
            )
        ", [$torneo_id, $categoria_id]);

        $this->CI->db->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->delete('tabla_posiciones');

        $this->CI->db->where('torneo_id', $torneo_id)
            ->where('categoria_id', $categoria_id)
            ->delete('partidos');
    }
}