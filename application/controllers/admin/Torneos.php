<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Torneos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Torneo_model');

        // seguridad básica
        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }
    }

    public function torneos()
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $data['torneos'] = $this->Torneo_model
            ->obtener_por_usuario($usuario_id);

        $this->load->view('header');
        $this->load->view('admin/torneos_listado', $data);
        $this->load->view('footer');
    }

    private function validar_dueno($torneo_id)
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $torneo =
            $this->Torneo_model
                ->obtener_por_id_y_usuario($torneo_id, $usuario_id);

        if (!$torneo) {
            show_404();
        }

        return $torneo;
    }

    public function crear()
    {
        $this->load->model('Torneo_model');
        
        $data['categorias'] = $this->Torneo_model->obtenerCategorias();
        $data['categorias_torneo'] = []; // Array vacío para crear nuevo torneo

        $this->load->view('header');
        $this->load->view('admin/torneo_form', $data);
        $this->load->view('footer');
    }

    public function guardar()
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $categorias = $this->input->post('categorias') ?? [];

        $imagenBase64 = null;
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $imagenBase64 = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));
        }

        $telOrg = preg_replace('/[^0-9]/', '', $this->input->post('organizador_telefono'));

        $data = [
            'nombre'               => $this->input->post('nombre'),
            'fecha_inicio'         => $this->input->post('fecha_inicio'),
            'fecha_fin'            => $this->input->post('fecha_fin') ?: null,
            'estado'               => 'proxima',
            'categoria'            => $this->input->post('categoria'),
            'nombre_organizador'   => $this->input->post('organizador'),
            'telefono_organizador' => $telOrg ?: null,
            'precio_inscripcion'   => $this->input->post('precio_inscripcion') ?: 0,
            'premios'              => $this->input->post('premios') ?: null,
            'fecha_cierre_inscripcion' => $this->input->post('fecha_cierre_inscripcion') ?: null,
            'imagen'               => $imagenBase64,
            'visible'                  => $this->input->post('visible') ? TRUE : FALSE,
            'inscripciones_visibles'   => $this->input->post('inscripciones_visibles') ? TRUE : FALSE,
            'fixture_visible'          => $this->input->post('fixture_visible') ? TRUE : FALSE,
            'zona_visible'             => $this->input->post('zona_visible') ? TRUE : FALSE,
            'resultados_visibles'      => $this->input->post('resultados_visibles') ? TRUE : FALSE,
            'partidos_visibles'        => $this->input->post('partidos_visibles') ? TRUE : FALSE,
        ];

        log_message('debug', 'Creando torneo con datos: ' . json_encode($data));

        // Crear el torneo y obtener el ID
        $torneo_id = $this->Torneo_model->crear($data);

        if(!$torneo_id) {
            log_message('error', 'Error creando torneo - insert_id es vacio. DB Error: ' . json_encode($this->db->error()));
        } else {
            log_message('info', 'Torneo creado con ID: ' . $torneo_id);
            
            // Asignar categorías al torneo creado
            if(!empty($categorias)) {
                foreach($categorias as $cat_id){
                    $this->Torneo_model->asignarCategoria($torneo_id, $cat_id);
                }
            }
        }

        redirect('admin/Torneos/torneos');
    }

    public function editar($id)
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $data['categorias'] = $this->Torneo_model->obtenerCategorias();
        $data['categorias_torneo_ids'] =
            $this->Torneo_model->obtenerCategoriasIds($id);

        $torneo = $this->Torneo_model
            ->obtener_por_id($id);

        if(!$torneo){
            show_404();
        }

        $data['inscripciones'] = $this->Torneo_model
                                    ->obtener_inscripciones($id);
        $data['torneo'] = $torneo;
        $data['categorias_torneo'] = $this->Torneo_model->obtenerCategoriasPorTorneo($id);

        $this->load->view('header');
        $this->load->view('admin/torneo_form', $data);
        $this->load->view('footer');
    }

    public function actualizar($id)
    {

        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $torneo = $this->Torneo_model->obtener_por_id($id);

        if(!$torneo){
            show_404();
        }

        $categorias = $this->input->post('categorias');

        // Eliminar categorías existentes antes de asignar las nuevas
        $this->Torneo_model->eliminarCategorias($id);

        // Asignar las nuevas categorías
        if($categorias) {
            foreach($categorias as $cat_id){
                $this->Torneo_model->asignarCategoria($id, $cat_id);
            }
        }

        $imagenBase64 = $torneo->imagen;

        // si cargaron una nueva imagen
        if (!empty($_FILES['imagen']['tmp_name'])) {

            $fileTmpPath = $_FILES['imagen']['tmp_name'];
            $fileData = file_get_contents($fileTmpPath);

            $imagenBase64 = base64_encode($fileData);
        }

        $telOrg = preg_replace('/[^0-9]/', '', $this->input->post('organizador_telefono'));

        $data = [
            'nombre'               => $this->input->post('nombre'),
            'fecha_inicio'         => $this->input->post('fecha_inicio'),
            'fecha_fin'            => $this->input->post('fecha_fin') ?: null,
            'estado'               => $this->input->post('estado'),
            'descripcion'          => $this->input->post('descripcion'),
            'categoria'            => $this->input->post('categoria'),
            'imagen'               => $imagenBase64,
            'nombre_organizador'   => $this->input->post('organizador'),
            'telefono_organizador' => $telOrg ?: null,
            'precio_inscripcion'   => $this->input->post('precio_inscripcion') ?: 0,
            'premios'              => $this->input->post('premios') ?: null,
            'fecha_cierre_inscripcion' => $this->input->post('fecha_cierre_inscripcion') ?: null,
            'visible'                  => $this->input->post('visible') ? TRUE : FALSE,
            'inscripciones_visibles'   => $this->input->post('inscripciones_visibles') ? TRUE : FALSE,
            'fixture_visible'          => $this->input->post('fixture_visible') ? TRUE : FALSE,
            'zona_visible'             => $this->input->post('zona_visible') ? TRUE : FALSE,
            'resultados_visibles'      => $this->input->post('resultados_visibles') ? TRUE : FALSE,
            'partidos_visibles'        => $this->input->post('partidos_visibles') ? TRUE : FALSE,
        ];

        log_message('debug', 'Actualizando torneo ID: ' . $id . ' con datos: ' . json_encode($data));

        $result = $this->Torneo_model->actualizar($id, $data);
        
        if(!$result) {
            $db_error = $this->db->error();
            log_message('error', 'Error al actualizar torneo ID: ' . $id . ' - ' . json_encode($db_error));
        } else {
            log_message('info', 'Torneo ID: ' . $id . ' actualizado exitosamente');
        }

        redirect('admin/Torneos/torneos');
    }

    public function eliminar($id)
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $torneo = $this->Torneo_model->obtener_por_id($id);

        if(!$torneo){
            show_404();
        }

        $this->Torneo_model->eliminarTorneoCompleto($id);

        redirect('admin/Torneos/torneos');
    }

    public function ver($id)
    {
        $usuario_id = $this->session->userdata('usuario_id');

        $this->load->model('Torneo_model');

        $torneo = $this->Torneo_model->obtener_por_id($id);

        if(!$torneo){
            show_404();
        }

        $data['inscripciones'] = $this->Torneo_model
                            ->obtener_inscripciones($id);

        $data['torneo'] = $torneo;
        $data['categorias_torneo'] = $this->Torneo_model->obtenerCategoriasPorTorneo($id);

        $this->load->view('header');
        $this->load->view('admin/torneo_ver', $data);
        $this->load->view('footer');
    }

    public function buscar_participantes()
    {
        $this->load->model('Torneo_model');
        $q = $this->input->get('q');

        if (!$q || strlen(trim($q)) < 2) {
            echo json_encode([]);
            return;
        }

        $resultados = $this->Torneo_model->buscar_participantes(trim($q));
        echo json_encode($resultados);
    }

    public function guardar_inscripcion(){
        $this->load->model('Torneo_model');

        $id_torneo   = $this->input->post('torneo_id');
        $categoria_id = $this->input->post('categoria');

        // Participante 1: usar existente o crear nuevo
        $p1_id = (int)$this->input->post('participante1_id');
        if ($p1_id > 0) {
            $id_participante1 = $p1_id;
        } else {
            $id_participante1 = $this->Torneo_model->guardar_participante([
                'nombre'    => $this->input->post('nombre1'),
                'apellido'  => $this->input->post('apellido1'),
                'telefono'  => $this->input->post('telefono1') ?: null,
                'dni'       => $this->input->post('dni1') ?: null,
                'categoria' => $this->input->post('categoria_p1') ?: null,
            ]);
        }

        // Participante 2: usar existente o crear nuevo
        $p2_id = (int)$this->input->post('participante2_id');
        if ($p2_id > 0) {
            $id_participante2 = $p2_id;
        } else {
            $id_participante2 = $this->Torneo_model->guardar_participante([
                'nombre'    => $this->input->post('nombre2'),
                'apellido'  => $this->input->post('apellido2'),
                'telefono'  => $this->input->post('telefono2') ?: null,
                'dni'       => $this->input->post('dni2') ?: null,
                'categoria' => $this->input->post('categoria_p2') ?: null,
            ]);
        }

        if ($id_participante1 && $id_participante2) {
            $this->Torneo_model->guardar_inscripcion([
                'torneo_id'        => $id_torneo,
                'participante1_id' => $id_participante1,
                'participante2_id' => $id_participante2,
                'estado'           => 'pendiente',
                'categoria_id'     => $categoria_id,
            ]);
        }

        echo json_encode($this->Torneo_model->obtener_inscripciones($id_torneo));
    }

    public function eliminar_inscripcion(){
        $this->load->model('Torneo_model');
        $id_inscripcion = $this->input->post('id');
        $id_torneo      = $this->input->post('torneo_id');
        $this->Torneo_model->eliminar_inscripcion($id_inscripcion);
        echo json_encode($this->Torneo_model->obtener_inscripciones($id_torneo));
    }

    public function fixture($torneo_id)
    {
        $this->load->library('FixtureService');
        $this->load->model('Torneo_model');

        $categoria_id = $this->input->get('categoria_id');

        $categorias = $this->Torneo_model
            ->obtenerCategoriasPorTorneo($torneo_id);

        // si no viene por GET, usamos la primera
        if (!$categoria_id && !empty($categorias)) {
            $categoria_id = $categorias[0]->id;
        }
        
        $data['categorias'] = $categorias;
        $data['categoria_id'] = $categoria_id;

        // traer todo el fixture armado
        $data['zonas'] = $this->fixtureservice->obtenerFixtureCompleto($torneo_id, $categoria_id);

        // info del torneo
        $data['torneo'] = $this->Torneo_model->obtener_por_id($torneo_id);

        $data['playoff'] = $this->Torneo_model->obtenerPlayoffBracket($torneo_id, $categoria_id);

        // datos para la pestaña de configuración de zonas
        $data['inscripciones_zona'] = $this->Torneo_model->obtenerInscripcionesConZona($torneo_id, $categoria_id);
        $data['zonas_db']           = $this->Torneo_model->obtenerZonasPorCategoria($torneo_id, $categoria_id);
        $data['inscriptos']          = $this->Torneo_model->obtener_inscripciones_por_categoria($torneo_id, $categoria_id);
        $data['inscriptos_con_seed'] = $this->Torneo_model->obtenerInscripcionesConSeed($torneo_id, $categoria_id);
        $data['todos_partidos']      = $this->Torneo_model->obtenerTodosPartidosTorneo($torneo_id);

        $this->load->view('header');
        $this->load->view('admin/torneo_fixture', $data);
        $this->load->view('footer');
    }

    public function editar_pareja_playoff()
    {
        $this->load->model('Torneo_model');

        $partido_id  = (int)$this->input->post('partido_id');
        $pareja1_id  = $this->input->post('pareja1_id') ?: null;
        $pareja2_id  = $this->input->post('pareja2_id') ?: null;

        if (!$partido_id) {
            echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
            return;
        }

        $update = [
            'pareja1_id' => $pareja1_id ? (int)$pareja1_id : null,
            'pareja2_id' => $pareja2_id ? (int)$pareja2_id : null,
        ];

        $this->Torneo_model->actualizarPartidoDatos($partido_id, $update);
        echo json_encode(['ok' => true]);
    }

    public function guardar_zonas($torneo_id)
    {
        $this->load->model('Torneo_model');

        $categoria_id = (int)$this->input->post('categoria_id');
        $num_zonas    = (int)$this->input->post('num_zonas');
        $asignaciones = $this->input->post('zona') ?: [];

        // 1. Limpiar zonas y partidos existentes de esta categoría
        $this->Torneo_model->limpiarFixtureCategoria($torneo_id, $categoria_id);

        // 2. Crear las zonas
        $zona_ids = [];
        for ($i = 1; $i <= $num_zonas; $i++) {
            $zona_id = $this->Torneo_model->insertarZona([
                'torneo_id'    => $torneo_id,
                'categoria_id' => $categoria_id,
                'nombre'       => chr(64 + $i),
                'numero'       => $i,
            ]);
            $zona_ids[$i] = $zona_id;
        }

        // 3. Asignar parejas a zonas
        foreach ($asignaciones as $inscripcion_id => $zona_numero) {
            $zona_numero = (int)$zona_numero;
            if ($zona_numero < 1 || !isset($zona_ids[$zona_numero])) continue;
            $this->Torneo_model->insertarZonaParejas([
                'zona_id'        => $zona_ids[$zona_numero],
                'inscripcion_id' => (int)$inscripcion_id,
            ]);
        }

        redirect('admin/Torneos/fixture/' . $torneo_id . '?categoria_id=' . $categoria_id);
    }

    public function generar_partidos($torneo_id)
    {
        $this->load->library('FixtureService');
        $this->load->model('Torneo_model');

        $categoria_id = (int)$this->input->post('categoria_id');

        $this->fixtureservice->generarPartidosDesdeCofiguracion($torneo_id, $categoria_id);

        redirect('admin/Torneos/fixture/' . $torneo_id . '?categoria_id=' . $categoria_id);
    }

    public function generar_fixture($torneo_id)
    {
        // Redirige a la página de configuración manual de fixture
        redirect('admin/Torneos/fixture/' . $torneo_id);
    }

    public function obtener_partido($partido_id){
        $this->load->model('Torneo_model');

        $partido = $this->Torneo_model->obtenerPartidos($partido_id);

        if (!$partido) {
            echo json_encode(null);
            return;
        }

        // Agregar los sets ya cargados para pre-poblar el modal
        $sets = $this->db
            ->where('partido_id', $partido_id)
            ->order_by('numero_set', 'ASC')
            ->get('partido_sets')
            ->result();

        $partido->set_1 = null;
        $partido->set_2 = null;
        $partido->set_3 = null;

        foreach ($sets as $s) {
            $key = 'set_' . $s->numero_set;
            $partido->$key = $s->games_pareja1 . '-' . $s->games_pareja2;
        }

        echo json_encode($partido);
    }

    public function actualizar_partido(){
        $this->load->model('Torneo_model');
        $this->load->library('FixtureService');

        // Datos del partido
        $dia = $this->input->post('dia') ?? NULL;
        $hora = $this->input->post('hora') ?? NULL;
        $cancha = $this->input->post('cancha') ?? NULL;

        $partido_id = $this->input->post('id');

        // Actualizamos fecha, hora y cancha
        $data = [
            "fecha" => $dia,
            "hora" => $hora,
            "cancha" => $cancha
        ];
        $partido = $this->Torneo_model->actualizarPartido($partido_id, $data);

        // Obtenemos los sets y validamos el formato
        $sets_input = [
            $this->input->post('set_1'),
            $this->input->post('set_2'),
            $this->input->post('set_3')
        ];

        $sets = [];
        if($this->input->post('set_1') && $this->input->post('set_2')){
            foreach($sets_input as $index => $set) {
                if($set) {
                    if(!preg_match('/^\d{1,2}-\d{1,2}$/', $set)) {
                        echo json_encode([
                            "error" => "Formato inválido para el set ".($index+1)
                        ]);
                        return;
                    }
                    $parts = explode('-', $set);
                    $a = (int)$parts[0]; $b = (int)$parts[1];
                    $valid = (($a === 6 && $b <= 4) || ($b === 6 && $a <= 4)
                           || ($a === 7 && $b === 5) || ($a === 5 && $b === 7)
                           || ($a === 7 && $b === 6) || ($a === 6 && $b === 7));
                    if (!$valid) {
                        echo json_encode([
                            "error" => "Resultado inválido en set ".($index+1).": $set"
                        ]);
                        return;
                    }
                    $sets[] = [ $a, $b ];
                } else {
                    $sets[] = [null, null];
                }
            }

            // Llamamos a la función del modelo
            $this->fixtureservice->cargarResultadoPartido(
                $partido_id,
                $sets[0][0], $sets[0][1],
                $sets[1][0], $sets[1][1],
                $sets[2][0], $sets[2][1]
            );   
        }

        echo json_encode($partido);
    }

    public function guardar_horarios_bulk()
    {
        $this->load->model('Torneo_model');

        $json     = $this->input->post('partidos');
        $partidos = json_decode($json, true);

        if (!$partidos || !is_array($partidos)) {
            echo json_encode(['ok' => false, 'msg' => 'Sin datos']);
            return;
        }

        $actualizados = 0;
        foreach ($partidos as $p) {
            $pid = (int)($p['id'] ?? 0);
            if (!$pid) continue;
            $this->Torneo_model->actualizarPartido($pid, [
                'fecha'  => $p['fecha']  ?: null,
                'hora'   => $p['hora']   ?: null,
                'cancha' => $p['cancha'] ?: null,
            ]);
            $actualizados++;
        }

        echo json_encode(['ok' => true, 'actualizados' => $actualizados]);
    }

    public function editar_inscripcion()
    {
        $this->load->model('Torneo_model');

        $inscripcion_id = (int)$this->input->post('inscripcion_id');
        $insc = $this->Torneo_model->obtener_inscripcion($inscripcion_id);

        if (!$insc) {
            echo json_encode(['ok' => false, 'error' => 'Inscripción no encontrada']);
            return;
        }

        $this->Torneo_model->actualizar_participante($insc->participante1_id, [
            'nombre'    => $this->input->post('nombre1'),
            'apellido'  => $this->input->post('apellido1'),
            'telefono'  => $this->input->post('telefono1'),
            'dni'       => $this->input->post('dni1') ?: null,
            'categoria' => $this->input->post('categoria_p1') ?: null,
        ]);

        $this->Torneo_model->actualizar_participante($insc->participante2_id, [
            'nombre'    => $this->input->post('nombre2'),
            'apellido'  => $this->input->post('apellido2'),
            'telefono'  => $this->input->post('telefono2'),
            'dni'       => $this->input->post('dni2') ?: null,
            'categoria' => $this->input->post('categoria_p2') ?: null,
        ]);

        echo json_encode(['ok' => true]);
    }

}
