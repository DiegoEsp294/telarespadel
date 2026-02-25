<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm px-3">
    <div class="ms-auto">

        <span class="me-3">
            <?= $this->session->userdata('email'); ?>
        </span>

        <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm">
            Cerrar sesión
        </a>

    </div>
</nav>
