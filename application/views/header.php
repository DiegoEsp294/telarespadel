<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($club_nombre) ? $club_nombre : 'Telares Padel'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
                <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Telares Padel" class="logo">
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
                <li><a href="#contacto" class="nav-link">Contacto</a></li>
                
                <!-- Separador -->
                <li class="nav-separator"></li>
                
                <!-- Autenticación dentro del menú -->
                <li class="nav-auth-item">
                    <?php if ($this->session->userdata('usuario_id')): ?>
                        <div class="usuario-info">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo $this->session->userdata('usuario_nombre'); ?></span>
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
