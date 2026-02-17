<!-- Perfil Usuario Section -->
<style>
    .perfil-container {
        padding: 40px 20px;
        min-height: calc(100vh - 300px);
        background-color: #f5f5f5;
    }

    .perfil-card {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .perfil-header {
        background: linear-gradient(135deg, #003366 0%, #004d80 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .perfil-header h1 {
        margin: 0 0 10px;
        font-size: 28px;
    }

    .perfil-header .avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 102, 0, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 40px;
    }

    .perfil-body {
        padding: 30px;
    }

    .perfil-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 5px;
        border-left: 4px solid #FF6600;
    }

    .info-item label {
        display: block;
        color: #003366;
        font-weight: 600;
        font-size: 12px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .info-item p {
        margin: 0;
        color: #333;
        font-size: 16px;
    }

    .perfil-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 14px;
    }

    .btn-primary {
        background-color: #FF6600;
        color: white;
    }

    .btn-primary:hover {
        background-color: #FF8533;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #e9ecef;
        color: #333;
    }

    .btn-secondary:hover {
        background-color: #dee2e6;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .estadisticas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .stat-box {
        background: linear-gradient(135deg, #003366 0%, #004d80 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-box .numero {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-box .label {
        font-size: 12px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    @media (max-width: 600px) {
        .perfil-info {
            grid-template-columns: 1fr;
        }

        .perfil-actions {
            flex-direction: column;
        }

        .perfil-header h1 {
            font-size: 24px;
        }
    }
</style>

<section class="perfil-container">
    <div class="perfil-card">
        <div class="perfil-header">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1><?php echo $usuario->nombre . ' ' . $usuario->apellido; ?></h1>
            <p><?php echo $usuario->email; ?></p>
        </div>

        <div class="perfil-body">
            <div class="perfil-info">
                <div class="info-item">
                    <label>Nombre Completo</label>
                    <p><?php echo $usuario->nombre . ' ' . $usuario->apellido; ?></p>
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <p><?php echo $usuario->email; ?></p>
                </div>

                <div class="info-item">
                    <label>Teléfono</label>
                    <p><?php echo $usuario->telefono ?? 'No especificado'; ?></p>
                </div>

                <div class="info-item">
                    <label>Categoría</label>
                    <p><?php 
                        echo ($usuario->categoria) ? $usuario->categoria : 'Sin categorizar';
                    ?></p>
                </div>

                <div class="info-item">
                    <label>Miembro Desde</label>
                    <p><?php echo date('d/m/Y', strtotime($usuario->fecha_registro)); ?></p>
                </div>

                <div class="info-item">
                    <label>Último Acceso</label>
                    <p><?php 
                        if ($usuario->ultimo_acceso) {
                            echo date('d/m/Y H:i', strtotime($usuario->ultimo_acceso));
                        } else {
                            echo 'Primera vez';
                        }
                    ?></p>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="estadisticas">
                <div class="stat-box">
                    <div class="numero">0</div>
                    <div class="label">Torneos Ganados</div>
                </div>
                <div class="stat-box">
                    <div class="numero">0</div>
                    <div class="label">Participaciones</div>
                </div>
                <div class="stat-box">
                    <div class="numero"><?php echo ($usuario->categoria) ? $usuario->categoria : '-'; ?></div>
                    <div class="label">Categoría</div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="perfil-actions">
                <a href="<?php echo site_url('auth/perfil'); ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
                <a href="<?php echo base_url(); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
                <form method="POST" action="<?php echo site_url('auth/logout'); ?>" style="flex: 1;">
                    <button type="submit" class="btn btn-danger" style="width: 100%;">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
