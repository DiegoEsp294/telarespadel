<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->library('form_validation');
    }

    public function login()
    {
        // Si ya está logueado, redirige al inicio
        if ($this->session->userdata('usuario_id')) {
            redirect('/');
        }

        $data['error'] = '';

        if ($this->input->method() === 'post') {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Debug: Registrar el intento
            log_message('info', 'Intento de login con email: ' . $email);

            // Validación
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $data['error'] = validation_errors();
                log_message('error', 'Errores de validación: ' . $data['error']);
            } else {
                // Buscar usuario
                $usuario = $this->Usuario_model->obtener_por_email($email);

                if ($usuario) {
                    log_message('info', 'Usuario encontrado: ' . $usuario->email . ' - rol: ' . $usuario->id_roles);
                    
                    if ($this->Usuario_model->verificar_contraseña($password, $usuario->password)) {
                        // Login exitoso
                        log_message('info', 'Contraseña válida para: ' . $usuario->email);
                        
                        $this->Usuario_model->registrar_acceso($usuario->id);
                        
                        // Setear datos en sesión
                        $this->session->set_userdata([
                            'usuario_id'      => $usuario->id,
                            'usuario_nombre'  => $usuario->nombre,
                            'usuario_email'   => $usuario->email,
                            'id_roles'        => $usuario->id_roles
                        ]);

                        redirect('/');

                    } else {
                        log_message('error', 'Contraseña inválida para: ' . $email);
                        $data['error'] = 'Email o contraseña incorrecta.';
                    }
                } else {
                    log_message('error', 'Usuario NO encontrado: ' . $email);
                    $data['error'] = 'Email o contraseña incorrecta.';
                }
            }
        }

        // Cargar datos de sesión para el header
        $data['club_nombre'] = 'Telares Padel';
        $data['usuario_logueado'] = $this->session->userdata('usuario_id');
        $data['usuario_rol'] = $this->session->userdata('id_roles');
        $data['usuario_nombre'] = $this->session->userdata('usuario_nombre');


        $this->load->view('header', $data);
        $this->load->view('login', $data);
        $this->load->view('footer');

    }

    public function registro()
    {
        // Si ya está logueado, redirige al inicio
        if ($this->session->userdata('usuario_id')) {
            redirect('/');
        }

        if ($this->input->method() === 'post') {
            $nombre = $this->input->post('nombre');
            $apellido = $this->input->post('apellido');
            $email = $this->input->post('email');
            $telefono = $this->input->post('telefono');
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');

            // Validación
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|min_length[2]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');
            $this->form_validation->set_rules('password_confirm', 'Confirmar Contraseña', 'required|matches[password]');

            if ($this->form_validation->run() == FALSE) {
                $data['error'] = validation_errors();
            } else {
                // Verificar si el email ya existe
                if ($this->Usuario_model->verificar_email($email)) {
                    $data['error'] = 'El email ya está registrado.';
                } else {
                    // Crear usuario
                    $usuario_data = array(
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'email' => $email,
                        'telefono' => $telefono,
                        'password' => $password,
                        'categoria' => NULL,
                        'estado' => 'activo',
                        'id_roles' => 1
                    );

                    if ($this->Usuario_model->crear_usuario($usuario_data)) {
                        $this->session->set_flashdata('success', '¡Registro exitoso! Inicia sesión con tu nueva cuenta.');
                        redirect('auth/login');
                    } else {
                        $data['error'] = 'Error al registrar. Intenta de nuevo.';
                    }
                }
            }
        }

        // Cargar datos de sesión para el header
        $data['club_nombre'] = 'Telares Padel';
        $data['usuario_logueado'] = $this->session->userdata('usuario_id');
        $data['usuario_rol'] = $this->session->userdata('id_roles');
        $data['usuario_nombre'] = $this->session->userdata('usuario_nombre');

        $this->load->view('header', $data);
        $this->load->view('registro', $data);
        $this->load->view('footer');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', '¡Sesión cerrada exitosamente!');
        redirect('/');
    }

    public function perfil()
    {
        if (!$this->session->userdata('usuario_id')) {
            redirect('auth/login');
        }

        $usuario_id = $this->session->userdata('usuario_id');
        $data['usuario'] = $this->Usuario_model->obtener_por_id($usuario_id);
        $data['club_nombre'] = 'Telares Padel';
        
        // Cargar datos de sesión para el header
        $data['usuario_logueado'] = $this->session->userdata('usuario_id');
        $data['usuario_rol'] = $this->session->userdata('id_roles');
        $data['usuario_nombre'] = $this->session->userdata('usuario_nombre');

        $this->load->view('header', $data);
        $this->load->view('perfil_usuario', $data);
        $this->load->view('footer');
    }

}
