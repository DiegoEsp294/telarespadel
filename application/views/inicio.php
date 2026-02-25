    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2>Bienvenidos a Telares Padel</h2>
            <div class="cta-buttons">
                <a href="#torneos" class="btn btn-primary">Ver Torneos</a>
                
                <?php 
                // Mostrar botón de administración solo para admin (1), organizador (2) y moderador (3)
                if ($usuario_logueado && in_array($usuario_rol, array(1, 2, 3))): 
                ?>
                    <a href="<?php echo base_url('admin/Torneos/torneos'); ?>" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Panel de Administración
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Sección de Información del Club -->
    <section id="nosotros" class="info-club">
        <div class="container">
            <div class="info-content">
                <div class="info-text">
                    <h2>Sobre Nosotros</h2>
                    <p>Bienvenidos a Telares Padel, Club de Padel de excelencia, un lugar para jugar y disfrutar del padel en buena compañía. Somos un club accesible donde jugadores de todos los niveles y localidades vienen a competir, mejorar y pasar momentos inolvidables. Aquí el padel es para todos, y la pasión por el deporte es lo que nos une.</p>
                    
                    <div class="club-stats">
                        <div class="stat">
                            <h3>3</h3>
                            <p>Canchas</p>
                        </div>
                        <div class="stat">
                            <h3>+6</h3>
                            <p>Torneos anuales</p>
                            <p>torneos particulares y avalados por APA</p>
                        </div>
                    </div>
                </div>
                <div class="info-contact">
                    <h3>Información del Club</h3>

                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>
                            General Paz, S/N.<br>
                            B° Santa Teresa
                        </p>
                    </div>

                    <div class="contact-item map-link">
                        <i class="fas fa-map"></i>
                        <p>
                            <a href="https://maps.app.goo.gl/GWugdNAaGAFqtPuL7" target="_blank">
                                Ver ubicación en Google Maps
                            </a>
                        </p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p><?php echo $club_info['telefono'] ?? '+54 (XXX) XXXX-XXXX'; ?></p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <p>Lun - Dom: 08:00 - 22:00</p>
                    </div>

                    <div class="social-links">
                        <a href="<?php echo $club_info['instagram'] ?? '#'; ?>" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/549XXXXXXXXXX" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sección de Torneos -->
    <section id="torneos" class="torneos-section">
        <div class="container">
            <h2>Nuestros Torneos</h2>
            <p class="section-subtitle">Próximos eventos y competiciones</p>
            
            <div class="torneos-grid">
                <?php if(isset($torneos) && !empty($torneos)): ?>
                    <?php foreach($torneos as $torneo): ?>

                        <div class="torneo-card <?php echo 'status-' . $torneo->estado; ?>">

                            <div class="torneo-header">
                                <h3><?php echo $torneo->nombre; ?></h3>
                                <span class="status-badge <?php echo $torneo->estado; ?>">
                                    <?php 
                                        switch($torneo->estado) {
                                            case 'proxima':
                                                echo 'Próximo';
                                                break;
                                            case 'en_curso':
                                                echo 'En Curso';
                                                break;
                                            case 'finalizado':
                                                echo 'Finalizado';
                                                break;
                                            default:
                                                echo ucfirst(str_replace('_', ' ', $torneo->estado));
                                        }
                                    ?>
                                </span>
                            </div>

                            <div class="torneo-body">
                                <div class="torneo-info-item">
                                    <i class="fas fa-calendar"></i>
                                    <div>
                                        <strong>Fechas</strong>
                                        <p>
                                            <?php 
                                            $fecha_inicio = (!empty($torneo->fecha_inicio) && $torneo->fecha_inicio != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_inicio)) : null;
                                            $fecha_fin = (!empty($torneo->fecha_fin) && $torneo->fecha_fin != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_fin)) : null;

                                            if ($fecha_inicio || $fecha_fin) {
                                                $inicio = $fecha_inicio ?? 'a confirmar';
                                                $fin = $fecha_fin ?? 'a confirmar';
                                                echo $inicio . ' - ' . $fin;
                                            } else {
                                                echo 'a confirmar';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="torneo-info-item">
                                    <i class="fas fa-users"></i>
                                    <div>
                                        <strong>Categorías</strong>
                                        <p><?php echo $torneo->categorias_label; ?></p>
                                    </div>
                                </div>

                                <div class="torneo-info-item">
                                    <i class="fas fa-user"></i>
                                    <div>
                                        <strong>Organizador</strong>
                                        <p><?php echo $torneo->nombre_organizador; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- ✅ IMAGEN DEL FLYER -->
                            <div class="torneo-image">
                                <?php if (!empty($torneo->imagen)) : ?>
                                    <img 
                                        src="data:image/jpeg;base64,<?php echo $torneo->imagen; ?>"
                                        alt="Flyer del torneo <?php echo $torneo->nombre; ?>">
                                <?php else : ?>
                                    <img 
                                        src="<?php echo base_url('assets/img/torneo-default.jpg'); ?>"
                                        alt="Flyer por defecto">
                                <?php endif; ?>
                            </div>

                            <div class="torneo-footer">
                                <a href="<?php echo base_url('home/torneo/'.$torneo->id); ?>" class="btn-info">
                                    Más Información
                                </a>
                            </div>

                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-torneos">No hay torneos registrados en este momento.</p>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- Sección de Servicios -->
    <section class="servicios-section">
        <div class="container">
            <h2>Nuestros Servicios</h2>
            <p class="section-subtitle">Todo lo que ofrecemos para tu experiencia</p>
            
            <div class="servicios-grid">
                <div class="servicio-card">
                    <div class="servicio-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Torneos Regulares</h3>
                    <p>Competiciones mensuales con diferentes categorías y niveles de dificultad.</p>
                </div>
                <div class="servicio-card">
                    <div class="servicio-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Clases Particulares</h3>
                    <p>Entrenamientos personalizados de padel.</p>
                </div>
                <div class="servicio-card">
                    <div class="servicio-icon">
                        <i class="fas fa-shop"></i>
                    </div>
                    <h3>Tienda</h3>
                    <p>Acceso a equipamiento y accesorios de padel de las mejores marcas.</p>
                </div>
                <div class="servicio-card">
                    <div class="servicio-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Comunidad</h3>
                    <p>Únete a una comunidad de jugadores apasionados y eventos sociales.</p>
                </div>
            </div>
        </div>
    </section>
