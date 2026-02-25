public function torneos()
{
    $usuario_id = $this->session->userdata('usuario_id');

    $data['torneos'] = $this->db
        ->where('usuario_id', $usuario_id)
        ->get('torneos')
        ->result();

    $this->load->view('header');
    $this->load->view('admin/torneos_listado', $data);
    $this->load->view('footer');
}
