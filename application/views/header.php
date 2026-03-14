<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Telares Padel">
    <meta name="theme-color" content="#FF6600">
    <link rel="apple-touch-icon" href="<?= base_url('logo_inicio.png') ?>">

    <?php
        $seo_title       = isset($seo_title)       ? $seo_title       : (isset($club_nombre) ? $club_nombre : 'Telares Padel');
        $seo_description = isset($seo_description) ? $seo_description : 'Torneos de pádel en Los Telares, Santiago del Estero. Seguí los fixtures, resultados y cruces en tiempo real.';
        $seo_image       = isset($seo_image)       ? $seo_image       : base_url('logo_inicio.png');
        $seo_url         = isset($seo_url)         ? $seo_url         : current_url();
        $seo_robots      = isset($seo_robots)      ? $seo_robots      : 'index, follow';
        $page_title      = ($seo_title === 'Telares Padel') ? 'Telares Padel' : $seo_title . ' | Telares Padel';
    ?>

    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description"    content="<?= htmlspecialchars($seo_description) ?>">
    <meta name="robots"         content="<?= $seo_robots ?>">
    <link rel="canonical"       href="<?= $seo_url ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Telares Padel">
    <meta property="og:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo_description) ?>">
    <meta property="og:image"       content="<?= $seo_image ?>">
    <meta property="og:url"         content="<?= $seo_url ?>">
    <meta property="og:locale"      content="es_AR">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo_description) ?>">
    <meta name="twitter:image"       content="<?= $seo_image ?>">

    <!-- JSON-LD: Organización -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SportsOrganization",
        "name": "Telares Padel",
        "sport": "Padel",
        "url": "<?= base_url() ?>",
        "logo": "<?= base_url('logo_inicio.png') ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Los Telares",
            "addressRegion": "Santiago del Estero",
            "addressCountry": "AR"
        }
    }
    </script>

    <link rel="icon" type="image/png" href="<?= base_url('logo_inicio.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('logo_inicio.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v='.time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">

    <?php 
        $usuario_logueado = $this->session->userdata('usuario_id');
        $usuario_rol = $this->session->userdata('id_roles');
        $usuario_nombre = $this->session->userdata('usuario_nombre');
    ?>

    <style>
        /* ====== LOADING OVERLAY ====== */
        #tp-loading {
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 35, 0.97);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            opacity: 0;
            visibility: hidden;
            transition: opacity .22s ease, visibility .22s ease;
            backdrop-filter: blur(3px);
        }
        #tp-loading.show {
            opacity: 1;
            visibility: visible;
        }
        .tp-ld-wrap {
            position: relative;
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tp-ld-logo {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            object-fit: cover;
            animation: tp-pulse 1.8s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }
        .tp-ld-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #FF6600;
            animation: tp-spin 1s linear infinite;
        }
        .tp-ld-ring2 {
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: rgba(255,102,0,.3);
            animation: tp-spin 1.8s linear infinite reverse;
        }
        .tp-ld-text {
            margin-top: 22px;
            color: rgba(255,255,255,.45);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            animation: tp-fade 1.6s ease-in-out infinite;
        }
        @keyframes tp-spin  { to { transform: rotate(360deg); } }
        @keyframes tp-pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.8; transform:scale(.94); } }
        @keyframes tp-fade  { 0%,100% { opacity:.3; } 50% { opacity:.9; } }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #0d0c0c;
            cursor: pointer;
            padding: 10px;
            transition: all 0.3s ease;
        }
        
        .menu-toggle:hover {
            color: var(--color-naranja);
        }
        
        .nav-auth-item {
            display: flex !important;
            gap: 10px;
            align-items: center;
            list-style: none;
            padding: 8px 15px;
            margin: 0;
        }
        
        .nav-auth-item a,
        .nav-auth-item button {
            text-decoration: none;
            color: #0d0c0c;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .nav-auth-item .btn-login {
            border: 2px solid #FF6600;
            color: #FF6600;
            background: transparent;
        }
        
        .nav-auth-item .btn-login:hover {
            background: #FF6600;
            color: white;
        }
        
        .nav-auth-item .btn-registro {
            background: #FF6600;
            color: white;
        }
        
        .nav-auth-item .btn-registro:hover {
            background: #FF8533;
        }
        
        .nav-auth-item .usuario-info {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .nav-auth-item .usuario-info span {
            color: #FF6600;
            font-weight: 600;
        }
        
        .nav-auth-item .btn-logout {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
        }
        
        .nav-auth-item .btn-logout:hover {
            background: #c82333;
        }

        .nav-separator {
            border-top: 1px solid #eee;
            margin: 10px 0;
            padding: 0 !important;
            height: 0 !important;
        }

        /* Responsive navbar */
        @media (max-width: 768px) {
            .container-nav {
                position: relative;
            }
            
            .menu-toggle {
                display: block;
                order: 2;
            }
            
            .nav-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                gap: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                z-index: 999;
                margin-top: 15px;
                padding: 0;
                list-style: none;
            }
            
            .nav-menu.active {
                max-height: 600px;
            }
            
            .nav-menu li {
                border-bottom: 1px solid #eee;
                margin: 0;
                padding: 0;
            }
            
            .nav-menu li:last-child {
                border-bottom: none;
            }
            
            .nav-menu a {
                display: block;
                padding: 15px 20px !important;
                margin: 0 !important;
            }

            .nav-auth-item {
                padding: 15px 20px !important;
                flex-direction: column;
                align-items: flex-start !important;
            }

            .nav-auth-item a,
            .nav-auth-item button {
                width: 100%;
                text-align: center;
                padding: 10px 15px !important;
                margin-bottom: 10px;
            }

            .nav-auth-item a:last-child,
            .nav-auth-item button:last-child {
                margin-bottom: 0;
            }

            .nav-separator {
                display: block !important;
                border-top: 2px solid #eee;
                margin: 10px 0 !important;
                padding: 0 !important;
                list-style: none !important;
                height: 0 !important;
            }
        }

        @media (max-width: 480px) {
            .nav-menu {
                max-height: 0;
            }
            
            .nav-menu.active {
                max-height: 800px;
            }

            .nav-auth-item {
                flex-direction: column;
                width: 100%;
            }
            
            .nav-auth-item a,
            .nav-auth-item button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-nav">
            <div class="logo-section">
                <img src="<?php echo base_url('logo_inicio.png'); ?>" alt="Telares Padel" class="logo">
                <div class="club-title">
                    <h1>TELARES PADEL</h1>
                </div>
            </div>
            
            <!-- Botón de menú hamburguesa para móvil -->
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo base_url(); ?>" class="nav-link active">Inicio</a></li>
                <li><a href="#torneos" class="nav-link">Torneos</a></li>
                <li><a href="#nosotros" class="nav-link">Nosotros</a></li>
                
                <!-- Separador -->
                <li class="nav-separator"></li>
                
                <!-- Autenticación dentro del menú -->
                <li class="nav-auth-item">
                    <?php if (isset($usuario_logueado) && $usuario_logueado): ?>
                        <div class="usuario-info">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo isset($usuario_nombre) ? $usuario_nombre : 'Usuario'; ?></span>
                        </div>
                        <a href="<?php echo site_url('auth/perfil'); ?>" class="nav-link" title="Mi Perfil">
                            <i class="fas fa-cog"></i> Mi Perfil
                        </a>
                        <form method="POST" action="<?php echo site_url('auth/logout'); ?>" style="display:block; width: 100%;">
                            <button type="submit" class="btn-logout" style="width: 100%;">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo site_url('auth/login'); ?>" class="btn-login" style="display: block; text-align: center;">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                        <a href="<?php echo site_url('auth/registro'); ?>" class="btn-registro" style="display: block; text-align: center;">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        // Debug: Verificar estado de login
        console.log('usuario_logueado:', '<?php echo isset($usuario_logueado) && $usuario_logueado ? "SI" : "NO"; ?>');
        console.log('usuario_nombre:', '<?php echo isset($usuario_nombre) ? $usuario_nombre : "VACIO"; ?>');
        console.log('usuario_rol:', '<?php echo isset($usuario_rol) ? $usuario_rol : "VACIO"; ?>');
        
        // Mostrar alert en desarrollo
        <?php if ($this->input->get('debug')): ?>
            alert('DEBUG LOGIN:\n\nLogueado: <?php echo isset($usuario_logueado) && $usuario_logueado ? "SÍ ✅" : "NO ❌"; ?>\nNombre: <?php echo isset($usuario_nombre) ? $usuario_nombre : "(vacío)"; ?>\nRol: <?php echo isset($usuario_rol) ? $usuario_rol : "(vacío)"; ?>');
        <?php endif; ?>
        
        // Toggle del menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
            }
            
            // Cerrar menú al hacer clic en un enlace
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navMenu.classList.remove('active');
                });
            });

            // Cerrar menú al hacer clic en botón logout
            const logoutButtons = navMenu.querySelectorAll('button');
            logoutButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Solo cerrar el menú si no es de logout
                    if (!button.classList.contains('btn-logout')) {
                        navMenu.classList.remove('active');
                    }
                });
            });
            
            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.navbar')) {
                    navMenu.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
