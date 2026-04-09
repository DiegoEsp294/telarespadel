    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-badge">Club de Pádel · Santiago del Estero</span>
            <h2>Telares<br>Padel</h2>
            <p class="hero-tagline">Torneos de todos los niveles · Los Telares</p>
            <div class="cta-buttons">
                <a href="#torneos" class="btn" style="color: white; background-color: var(--color-naranja);">Ver Torneos</a>
                <?php if ($usuario_logueado && in_array($usuario_rol, array(1, 2, 3))): ?>
                    <a class="btn" style="color: white; background-color: var(--color-naranja);" href="<?php echo base_url('admin/Torneos/torneos'); ?>" >
                        Panel Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-scroll-hint">↓</div>
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
                        <p><?php echo ($club_info['telefono'] ? $club_info['telefono']." - 3413068291" : '+54 (XXX) XXXX-XXXX'); ?></p>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <p>Lun - Dom: 08:00 - 12:00 hs</p>
                    </div>

                    <div class="social-links">
                        <a href="https://instagram.com/<?php echo isset($club_info['instagram']) ? str_replace('@', '', $club_info['instagram']) : 'telarespadel'; ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/5493856458000" target="_blank" title="WhatsApp" style="display:inline-flex; flex-direction:column; align-items:center; gap:2px;">
                            <i class="fab fa-whatsapp"></i> Juani
                        </a>
                        <a href="https://wa.me/5493413068291" target="_blank" title="WhatsApp" style="display:inline-flex; flex-direction:column; align-items:center; gap:2px;">
                            <i class="fab fa-whatsapp"></i> Ramiro
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

                            <?php if($torneo->estado === 'proxima' && !empty($torneo->fecha_inicio) && $torneo->fecha_inicio !== '0000-00-00'): ?>
                            <div class="torneo-countdown" data-fecha="<?php echo $torneo->fecha_inicio; ?>">
                                <div class="cd-label"><i class="fas fa-clock"></i> Comienza en</div>
                                <div class="cd-boxes">
                                    <div class="cd-box"><span class="cd-num cd-dias">--</span><span class="cd-unit">días</span></div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box"><span class="cd-num cd-horas">--</span><span class="cd-unit">hs</span></div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box"><span class="cd-num cd-mins">--</span><span class="cd-unit">min</span></div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box"><span class="cd-num cd-segs">--</span><span class="cd-unit">seg</span></div>
                                </div>
                            </div>
                            <?php endif; ?>

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

                                <div class="torneo-share-row">
                                    <button onclick="compartirTorneo('<?php echo addslashes($torneo->nombre); ?>', '<?php echo base_url('home/torneo/'.$torneo->id); ?>')"
                                            class="btn-share btn-share-wpp"
                                            title="Compartir por WhatsApp">
                                        <i class="fab fa-whatsapp"></i> Compartir
                                    </button>
                                    <button onclick="copiarLink('<?php echo base_url('home/torneo/'.$torneo->id); ?>', this)"
                                            class="btn-share btn-share-copy"
                                            title="Copiar link">
                                        <i class="fas fa-link"></i> Copiar link
                                    </button>
                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-torneos">No hay torneos registrados en este momento.</p>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- Sección Torneos Finalizados -->
    <?php if (!empty($torneos_finalizados)): ?>
    <section class="torneos-section" style="background:#f7f8fa; padding-top:40px; padding-bottom:48px;">
        <div class="container">
            <h2>Torneos Finalizados</h2>
            <p class="section-subtitle">Los últimos torneos disputados</p>

            <div class="torneos-grid">
                <?php foreach($torneos_finalizados as $torneo): ?>
                <div class="torneo-card status-finalizado">

                    <div class="torneo-header">
                        <h3><?php echo $torneo->nombre; ?></h3>
                        <span class="status-badge finalizado">Finalizado</span>
                    </div>

                    <div class="torneo-body">
                        <div class="torneo-info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Fechas</strong>
                                <p><?php
                                    $fi = (!empty($torneo->fecha_inicio) && $torneo->fecha_inicio != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_inicio)) : null;
                                    $ff = (!empty($torneo->fecha_fin)    && $torneo->fecha_fin    != '0000-00-00') ? date('d/m/Y', strtotime($torneo->fecha_fin))    : null;
                                    echo ($fi || $ff) ? (($fi ?? 'a confirmar') . ' - ' . ($ff ?? 'a confirmar')) : 'a confirmar';
                                ?></p>
                            </div>
                        </div>

                        <div class="torneo-info-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Categorías</strong>
                                <p><?php echo $torneo->categorias_label; ?></p>
                            </div>
                        </div>

                        <?php if (!empty($torneo->nombre_organizador)): ?>
                        <div class="torneo-info-item">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>Organizador</strong>
                                <p><?php echo $torneo->nombre_organizador; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="torneo-image">
                        <?php if (!empty($torneo->imagen)): ?>
                            <img src="data:image/jpeg;base64,<?php echo $torneo->imagen; ?>" alt="Flyer <?php echo $torneo->nombre; ?>">
                        <?php else: ?>
                            <img src="<?php echo base_url('assets/img/torneo-default.jpg'); ?>" alt="Flyer por defecto">
                        <?php endif; ?>
                    </div>

                    <div class="torneo-footer">
                        <a href="<?php echo base_url('home/torneo/'.$torneo->id); ?>" class="btn-info">
                            Ver Resultados
                        </a>
                        <div class="torneo-share-row">
                            <button onclick="compartirTorneo('<?php echo addslashes($torneo->nombre); ?>', '<?php echo base_url('home/torneo/'.$torneo->id); ?>')"
                                    class="btn-share btn-share-wpp" title="Compartir por WhatsApp">
                                <i class="fab fa-whatsapp"></i> Compartir
                            </button>
                            <button onclick="copiarLink('<?php echo base_url('home/torneo/'.$torneo->id); ?>', this)"
                                    class="btn-share btn-share-copy" title="Copiar link">
                                <i class="fas fa-link"></i> Copiar link
                            </button>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ══ Sección Auspiciantes ══════════════════════════════════════════ -->
    <?php
    $CI_sps =& get_instance();
    if (!isset($CI_sps->Sponsor_model)) {
        $CI_sps->load->model('Sponsor_model');
    }
    $sponsors_seccion = $CI_sps->Sponsor_model->obtener_activos_para_seccion();
    if (!empty($sponsors_seccion)):
    ?>
    <section class="sponsors-section" id="auspiciantes">
        <div class="container">
            <h2 class="sponsors-section-title">Nuestros Auspiciantes</h2>
            <p class="sponsors-section-sub">Gracias a quienes hacen posible el pádel en Telares</p>
            <div class="sponsors-logos-grid">
                <?php foreach ($sponsors_seccion as $sp): ?>
                <div class="sponsor-logo-card">
                    <?php if ($sp->logo): ?>
                        <img src="data:image/png;base64,<?= $sp->logo ?>"
                             alt="<?= htmlspecialchars($sp->nombre) ?>"
                             class="sponsor-logo-grid-img">
                    <?php endif; ?>
                    <span class="sponsor-logo-name"><?= htmlspecialchars($sp->nombre) ?></span>
                    <?php
                    // Iconos de redes sociales
                    $sp_redes = [];
                    if (!empty($sp->sitio_web))   $sp_redes[] = ['url' => $sp->sitio_web, 'icon' => 'fas fa-globe', 'cls' => 'ssp-web'];
                    if (!empty($sp->instagram))   $sp_redes[] = ['url' => (strpos($sp->instagram,'http')===0)?$sp->instagram:'https://instagram.com/'.ltrim($sp->instagram,'@'), 'icon' => 'fab fa-instagram', 'cls' => 'ssp-ig'];
                    if (!empty($sp->facebook))    $sp_redes[] = ['url' => $sp->facebook, 'icon' => 'fab fa-facebook', 'cls' => 'ssp-fb'];
                    if (!empty($sp->whatsapp))    $sp_redes[] = ['url' => 'https://wa.me/'.preg_replace('/\D/','',$sp->whatsapp), 'icon' => 'fab fa-whatsapp', 'cls' => 'ssp-wa'];
                    if (!empty($sp->otro_link))   $sp_redes[] = ['url' => $sp->otro_link, 'icon' => 'fas fa-share-alt', 'cls' => 'ssp-otro'];
                    if (!empty($sp_redes)):
                    ?>
                    <div class="sponsor-social-links">
                        <?php foreach ($sp_redes as $red): ?>
                        <a href="<?= htmlspecialchars($red['url']) ?>"
                           target="_blank" rel="noopener sponsored"
                           class="ssp <?= $red['cls'] ?>">
                            <i class="<?= $red['icon'] ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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

<style>
.torneo-countdown {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 14px 16px 12px;
    border-radius: 10px 10px 0 0;
    text-align: center;
}
.cd-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,.5);
    margin-bottom: 10px;
}
.cd-label i { margin-right: 5px; color: #FF6600; }
.cd-boxes {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.cd-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255,255,255,.08);
    border-radius: 7px;
    padding: 6px 10px;
    min-width: 48px;
}
.cd-num {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}
.cd-unit {
    font-size: 9px;
    color: rgba(255,255,255,.45);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 3px;
}
.cd-sep {
    font-size: 20px;
    font-weight: 700;
    color: #FF6600;
    line-height: 1;
    margin-bottom: 12px;
}
.cd-terminado {
    font-size: 13px;
    font-weight: 700;
    color: #FF6600;
    letter-spacing: .5px;
}
</style>

<script>
function compartirTorneo(nombre, url) {
    if (navigator.share) {
        navigator.share({ title: 'Ver torneo ' + nombre, url: url }).catch(function() {});
    } else {
        window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank');
    }
}
function copiarLink(url, btn) {
    navigator.clipboard.writeText(url).then(function() {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.style.background = '#1a7a4a';
        setTimeout(function() {
            btn.innerHTML = original;
            btn.style.background = '';
        }, 2000);
    });
}

/* ===== COUNTDOWN ===== */
function pad(n) { return String(n).padStart(2, '0'); }

function tickCountdowns() {
    document.querySelectorAll('.torneo-countdown').forEach(function(el) {
        const fecha  = el.dataset.fecha;          // "YYYY-MM-DD"
        const target = new Date(fecha + 'T16:00:00-03:00').getTime();
        const ahora  = Date.now();
        const diff   = target - ahora;

        if (diff <= 0) {
            el.innerHTML = '<div class="cd-label"><i class="fas fa-clock"></i> <span class="cd-terminado">¡Comienza hoy!</span></div>';
            return;
        }

        const dias  = Math.floor(diff / 86400000);
        const horas = Math.floor((diff % 86400000) / 3600000);
        const mins  = Math.floor((diff % 3600000)  / 60000);
        const segs  = Math.floor((diff % 60000)    / 1000);

        el.querySelector('.cd-dias').textContent = pad(dias);
        el.querySelector('.cd-horas').textContent = pad(horas);
        el.querySelector('.cd-mins').textContent  = pad(mins);
        el.querySelector('.cd-segs').textContent  = pad(segs);
    });
}

if (document.querySelector('.torneo-countdown')) {
    tickCountdowns();
    setInterval(tickCountdowns, 1000);
}
</script>
