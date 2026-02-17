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
</style>

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
                        <strong>Categoría</strong>
                        <p><?php echo $torneo->categoria; ?></p>
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

        <!-- Contenido Principal -->
        <div class="content-grid">
            <div>
                <!-- Descripción del Torneo -->
                <div class="card">
                    <h2>Información del Torneo</h2>
                    <p class="descripcion"><?php echo nl2br($torneo->descripcion); ?></p>

                    <!-- Estadísticas -->
                    <h3>Estadísticas</h3>
                    <div class="estadisticas">
                        <div class="stat-box">
                            <div class="numero"><?php echo $total_inscriptos; ?></div>
                            <div class="label">Inscriptos</div>
                        </div>
                        <div class="stat-box">
                            <div class="numero"><?php echo $torneo->participantes; ?></div>
                            <div class="label">Cupo Total</div>
                        </div>
                    </div>

                    <!-- Inscriptos por Categoría -->
                    <?php if (!empty($inscriptos_por_categoria)): ?>
                        <h3>Inscriptos por Categoría</h3>
                        <div class="estadisticas">
                            <?php foreach ($inscriptos_por_categoria as $cat): ?>
                                <div class="stat-box">
                                    <div class="numero"><?php echo $cat->cantidad; ?></div>
                                    <div class="label"><?php echo $cat->categoria; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Listado de Inscriptos -->
                    <h3>Listado de Inscriptos</h3>
                    <?php if (!empty($inscriptos)): ?>
                        <table class="tabla-inscriptos">
                            <thead>
                                <tr>
                                    <th>Pareja</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscriptos as $inscripto): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                                $pareja = '';
                                                if (!empty($inscripto->nombre_p1)) {
                                                    $pareja .= $inscripto->nombre_p1 . ' ' . $inscripto->apellido_p1;
                                                }
                                                if (!empty($inscripto->nombre_p2)) {
                                                    if (!empty($pareja)) $pareja .= ' - ';
                                                    $pareja .= $inscripto->nombre_p2 . ' ' . $inscripto->apellido_p2;
                                                }
                                                echo !empty($pareja) ? $pareja : 'Pareja no especificada';
                                            ?>
                                        </td>
                                        <td><span class="categoria-badge"><?php echo $inscripto->categoria ?? 'Sin categoría'; ?></span></td>
                                        <td>
                                            <?php 
                                                $estados_badge = array(
                                                    'confirmada' => '<span style="color: #28a745;">✓ Confirmada</span>',
                                                    'pendiente' => '<span style="color: #ffc107;">⧖ Pendiente</span>',
                                                    'cancelada' => '<span style="color: #dc3545;">✗ Cancelada</span>'
                                                );
                                                echo $estados_badge[$inscripto->estado] ?? $inscripto->estado;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="sin-inscriptos">
                            <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
                            <p>Aún no hay inscriptos confirmados en este torneo.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Formulario de Inscripción -->
            <div>
                <div class="formulario-inscripcion">
                    <h3><i class="fas fa-edit"></i> Solicitar Inscripción</h3>
                    
                    <div class="cupo-disponible">
                        <strong>Cupo disponible:</strong> 
                        <?php 
                            $cupo_text = (strpos($torneo->participantes, 'Sin') === false) ? 
                                $torneo->participantes : 'Sin límite';
                            echo $cupo_text;
                        ?>
                    </div>

                    <form method="POST" action="<?php echo site_url('home/solicitar_inscripcion'); ?>">
                        <input type="hidden" name="torneo_id" value="<?php echo $torneo->id; ?>">

                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido">
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="+54 (XXX) XXXX-XXXX">
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <select id="categoria" name="categoria">
                                <option value="">Seleccionar...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Mixto">Mixto</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="compañero">Nombre del Compañero/a</label>
                            <textarea id="compañero" name="compañero" rows="2" placeholder="Nombre y teléfono de tu pareja de juego"></textarea>
                        </div>

                        <button type="submit" class="btn-enviar">
                            <i class="fas fa-paper-plane"></i> Enviar Solicitud
                        </button>

                        <p style="font-size: 12px; color: #999; margin-top: 15px; text-align: center;">
                            * Campos requeridos. Nos contactaremos pronto para confirmar tu inscripción.
                        </p>
                    </form>
                </div>

                <!-- Solicitudes Pendientes (para admin) -->
                <?php if (!empty($solicitudes)): ?>
                    <div class="card">
                        <h2>Solicitudes Pendientes</h2>
                        <p style="font-size: 12px; color: #999;">Total: <?php echo count($solicitudes); ?> solicitud(es)</p>
                        
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <div style="padding: 15px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px;">
                                <p><strong><?php echo $solicitud->nombre . ' ' . $solicitud->apellido; ?></strong></p>
                                <p><i class="fas fa-envelope"></i> <?php echo $solicitud->email; ?></p>
                                <p><i class="fas fa-phone"></i> <?php echo $solicitud->telefono; ?></p>
                                <p><small style="color: #999;">Solicitud: <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)); ?></small></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón Volver -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('/'); ?>" class="btn btn-secondary" style="text-decoration: none; display: inline-block; padding: 12px 30px; background-color: #003366; color: white; border-radius: 5px;">
                <i class="fas fa-arrow-left"></i> Volver a Torneos
            </a>
        </div>
    </div>
</section>
