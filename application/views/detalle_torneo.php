<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
.detalle-torneo {
    padding: 40px 20px;
    background-color: #f5f5f5;
    min-height: calc(100vh - 200px);
}

.torneo-info-header {
    background: linear-gradient(135deg, #003366 0%, #004d80 100%);
    color: white;
    padding: 40px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.torneo-info-header h1 {
    margin: 0 0 20px 0;
    font-size: 32px;
}

.torneo-info-header .info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.torneo-info-header .info-item {
    display: flex;
    gap: 10px;
    align-items: center;
}

.torneo-info-header .info-item i {
    font-size: 20px;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.card h2 {
    color: #003366;
    margin-top: 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #FF6600;
    padding-bottom: 10px;
}

.card h3 {
    color: #003366;
    margin-top: 20px;
    margin-bottom: 15px;
}

.descripcion {
    line-height: 1.8;
    color: #444;
}

/* Tabla de inscriptos */
.tabla-inscriptos {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.tabla-inscriptos thead {
    background-color: #003366;
    color: white;
}

.tabla-inscriptos th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #FF6600;
}

.tabla-inscriptos td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.tabla-inscriptos tbody tr:hover {
    background-color: #f9f9f9;
}

.tabla-inscriptos tbody tr:last-child td {
    border-bottom: none;
}

.categoria-badge {
    display: inline-block;
    background-color: #FF6600;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Estadísticas */
.estadisticas {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-box {
    background: linear-gradient(135deg, #003366 0%, #004d80 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-box .numero {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-box .label {
    font-size: 14px;
    opacity: 0.9;
}

/* Formulario */
.formulario-inscripcion {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 20px;
}

.formulario-inscripcion h3 {
    color: #003366;
    margin-top: 0;
}

.form-group {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

.form-group label {
    color: #003366;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #FF6600;
    box-shadow: 0 0 5px rgba(255, 102, 0, 0.2);
}

.btn-enviar {
    width: 100%;
    padding: 12px;
    background-color: #FF6600;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
}

.btn-enviar:hover {
    background-color: #FF8533;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
}

.alerta {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    border-left: 4px solid;
}

.alerta-success {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alerta-error {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.sin-inscriptos {
    text-align: center;
    color: #999;
    padding: 40px 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.cupo-disponible {
    font-size: 14px;
    margin-top: 10px;
    padding: 10px;
    background-color: #e8f4f8;
    border-radius: 5px;
    color: #003366;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .formulario-inscripcion {
        position: relative;
        top: 0;
    }

    .torneo-info-header .info-row {
        grid-template-columns: 1fr;
    }

    .estadisticas {
        grid-template-columns: 1fr;
    }
}

    .info-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
        gap:15px;
        margin-bottom:20px;
    }

    .info-grid div{
        background:#f8f9fb;
        padding:12px;
        border-radius:6px;
        border-left:4px solid #FF6600;
    }
</style>

<?php
    $mensaje_wpp = urlencode(
        "Hola! Quiero consultar por el torneo '{$torneo->nombre}'. ¿Me podés pasar info?"
    );

    $link_wpp = "https://wa.me/".$torneo->telefono_organizador."?text=".$mensaje_wpp;
?>

<section class="detalle-torneo">
    <div class="container">
        <!-- Mostrar alertas -->
        <?php if($this->session && $this->session->flashdata('success')): ?>
            <div class="alerta alerta-success">
                ✓ <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session && $this->session->flashdata('error')): ?>
            <div class="alerta alerta-error">
                ✗ <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Encabezado del Torneo -->
        <div class="torneo-info-header">
            <h1><?php echo $torneo->nombre; ?></h1>
            
            <div class="info-row">
                <div style="margin-top:25px;">
                    <a href="<?php echo $link_wpp; ?>" 
                    target="_blank"
                    class="btn-enviar"
                    style="display:inline-block; width:auto; padding:12px 25px;">
                        <i class="fab fa-whatsapp"></i> Consultar por WhatsApp
                    </a>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <strong>Fechas</strong>
                        <p><?php 
                            $fecha_inicio = (!empty($torneo->fecha_inicio) && $torneo->fecha_inicio != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_inicio)) : null;
                            $fecha_fin = (!empty($torneo->fecha_fin) && $torneo->fecha_fin != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_fin)) : null;
                            
                            if ($fecha_inicio || $fecha_fin) {
                                $inicio = $fecha_inicio ?? 'a confirmar';
                                $fin = $fecha_fin ?? 'a confirmar';
                                echo $inicio . ' - ' . $fin;
                            } else {
                                echo 'a confirmar';
                            }
                        ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <strong>Categorías</strong>
                        <p><?php echo $categorias_label; ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-badge"></i>
                    <div>
                        <strong>Estado</strong>
                        <p><?php 
                            $estados = array(
                                'proxima' => 'Próximo',
                                'en_curso' => 'En Curso',
                                'finalizado' => 'Finalizado'
                            );
                            echo $estados[$torneo->estado] ?? ucfirst($torneo->estado);
                        ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!empty($torneo->imagen)): ?>
            <div class="card" style="padding:15px;">
                <h2>Flyer del Torneo</h2>

                <div style="text-align:center;">
                    <img 
                        src="data:image/jpeg;base64,<?php echo $torneo->imagen; ?>"
                        alt="Flyer <?php echo $torneo->nombre; ?>"
                        style="
                            max-width:100%;
                            border-radius:10px;
                            box-shadow:0 5px 20px rgba(0,0,0,0.15);
                        "
                    >
                </div>
            </div>
        <?php endif; ?>

        <!-- Contenido Principal -->
        <div class="">
            <div>
                <!-- Descripción del Torneo -->
                <div class="card">
                    <h2>Información del Torneo</h2>
                    <p class="descripcion"><?php echo nl2br($torneo->descripcion); ?></p>

                    <!-- Estadísticas -->

                    <!-- Inscriptos por Categoría -->
                    <?php if (!empty($inscriptos_por_categoria)): ?>
                        <h3>Inscriptos por Categoría</h3>
                        <div class="">
                            <?php foreach ($inscriptos_por_categoria as $cat): ?>
                                <div class="stat-box">
                                    <div class="numero"><?php echo $cat->cantidad; ?></div>
                                    <div class="label"><?php echo $cat->categoria; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>


                    <div class="info-extra">
                        <h3>Datos del Torneo</h3>

                        <div class="info-grid">
                            <?php if(!empty($torneo->sede)): ?>
                                <div><strong>📍 Sede:</strong> <?php echo $torneo->sede; ?></div>
                            <?php endif; ?>

                            <?php if(!empty($torneo->precio_inscripcion)): ?>
                                <div><strong>💰 Inscripción:</strong> $<?php echo $torneo->precio_inscripcion.' por integrante'; ?></div>
                            <?php endif; ?>

                            <?php if(!empty($torneo->fecha_cierre_inscripcion)): ?>
                                <div>
                                    <strong>⏳ Cierre inscripción:</strong>
                                    <?php echo date('d/m/Y', strtotime($torneo->fecha_cierre_inscripcion)); ?>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($torneo->premios)): ?>
                                <div><strong>🏆 Premios:</strong> <?php echo $torneo->premios; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card" style="text-align:center;">
            <h2>¿Querés participar?</h2>

            <p>Contactá al organizador o asegurá tu lugar ahora.</p>

            <a href="<?php echo $link_wpp; ?>" target="_blank" class="btn-enviar">
                <i class="fab fa-whatsapp"></i> Consultar disponibilidad
            </a>
        </div>

        
        <?php 
            $this->load->view('fixture', $fixture); 
        ?>

        <!-- Botón Volver -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('/'); ?>" class="btn btn-secondary" style="text-decoration: none; display: inline-block; padding: 12px 30px; background-color: #003366; color: white; border-radius: 5px;">
                <i class="fas fa-arrow-left"></i> Volver a Torneos
            </a>
        </div>
    </div>
</section>
