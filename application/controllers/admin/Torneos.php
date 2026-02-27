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

        $data = [
            'nombre' => $this->input->post('nombre'),
            'fecha_inicio' => $this->input->post('fecha_inicio'),
            'fecha_fin' => $this->input->post('fecha_fin'),
            'estado' => 'proxima',
            'categoria' => $this->input->post('categoria'),
            'nombre_organizador' => $this->input->post('organizador'),
            'telefono_organizador' => $this->input->post('organizador_telefono'),
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

        $data = [
            'nombre' => $this->input->post('nombre'),
            'fecha_inicio' => $this->input->post('fecha_inicio'),
            'fecha_fin' => $this->input->post('fecha_fin'),
            'estado' => $this->input->post('estado'),
            'descripcion' => $this->input->post('descripcion'),
            'categoria' => $this->input->post('categoria'),
            'imagen' => $imagenBase64,
            'nombre_organizador' => $this->input->post('organizador'),
            'telefono_organizador' => $this->input->post('organizador_telefono'),
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

    public function guardar_inscripcion(){
        $this->load->model('Torneo_model');

        $data_participante1 = [
            "nombre" => $this->input->post('nombre1'),
            "apellido" => $this->input->post('apellido1'),
            "telefono" => $this->input->post('telefono1'),
        ];

        $id_participante1 = $this->Torneo_model->guardar_participante($data_participante1);

        $data_participante2 = [
            "nombre" => $this->input->post('nombre2'),
            "apellido" => $this->input->post('apellido2'),
            "telefono" => $this->input->post('telefono2'),
        ];

        $id_participante2 = $this->Torneo_model->guardar_participante($data_participante2);
        $id_torneo = $this->input->post('torneo_id');
        $categoria_id = $this->input->post('categoria');

        if($id_participante1 && $id_participante2){
            $data = [
                "torneo_id" => $id_torneo,
                "participante1_id"  => $id_participante1,
                "participante2_id"  => $id_participante2,
                "estado" => "pendiente",
                "categoria_id" => $categoria_id
            ];

            $this->Torneo_model->guardar_inscripcion($data);

        }

        $inscripciones = $this->Torneo_model->obtener_inscripciones($id_torneo);

        echo json_encode($inscripciones);

    }

    public function eliminar_inscripcion(){
        $this->load->model('Torneo_model');
        $id_inscripcion = $this->input->post('id');
        $this->Torneo_model->eliminar_inscripcion($id_inscripcion);
        echo json_encode("ok");
    }

    public function fixture($torneo_id)
    {
        $this->load->library('FixtureService');
        $this->load->model('Torneo_model');

        $categoria_id = $this->input->get('categoria_id');

        $categorias = $this->Torneo_model
            ->obtenerCategorias($torneo_id);

        // si no viene por GET, usamos la primera
        if (!$categoria_id && !empty($categorias)) {
            $categoria_id = $categorias[0]->id;
        }
        
        $data['categorias'] = $categorias;
        $data['categoria_id'] = $categoria_id;

        // traer todo el fixture armado
        $data['zonas'] = $this->fixtureservice->obtenerFixtureCompleto($torneo_id, $categoria_id);

        // info del torneo (categoria, etc)
        $data['torneo'] = $this->Torneo_model->obtener_por_id($torneo_id);

        $playoff = $this->Torneo_model
            ->obtener_clasificados_playoff($torneo_id, $categoria_id);
        
        $data['playoff'] = $playoff;

        $this->load->view('header');
        $this->load->view('admin/torneo_fixture', $data);
        $this->load->view('footer');
    }

    public function generar_fixture($torneo_id)
    {
        $this->load->library('FixtureService');
        $this->load->model('Torneo_model');

        $this->fixtureservice->generarFixture($torneo_id);
        redirect('admin/Torneos/torneos');
    }

    public function obtener_partido($partido_id){
        $this->load->model('Torneo_model');

        $partido = $this->Torneo_model->obtenerPartidos($partido_id);

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
                    $sets[] = [ (int)$parts[0], (int)$parts[1] ];
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

}
