    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Telares Padel</h4>
                    <p>Tu lugar para jugar, competir y disfrutar del padel en buena compañía.</p>
                </div>
                <div class="footer-section">
                    <h4>Enlaces Rápidos</h4>
                    <ul>
                        <li><a href="<?php echo base_url(); ?>">Inicio</a></li>
                        <li><a href="#torneos">Torneos</a></li>
                        <li><a href="#nosotros">Nosotros</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Ubicación</h4>
                    <p><?php echo isset($club_info['ubicacion']) ? $club_info['ubicacion'] : 'Los Telares, Santiago del Estero'; ?></p>
                    <p>Tel Juani: <?php echo isset($club_info['telefono']) ? $club_info['telefono'] : '+54 (XXX) XXXX-XXXX'; ?></p>
                    <p>Tel Ramiro: 3413068291</p>

                </div>
                <div class="footer-section">
                    <h4>Síguenos</h4>
                    <div class="footer-social">
                        <a href="https://instagram.com/<?php echo isset($club_info['instagram']) ? str_replace('@', '', $club_info['instagram']) : 'telarespadel'; ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Telares Padel. Todos los derechos reservados.</p>
                <p class="footer-dev">
                    Desarrollado por <strong>Diego Espíndola</strong> &mdash;
                    <a href="mailto:diegoesp63@gmail.com" title="Contactar al desarrollador">¿Querés algo similar?</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- ====== LOADING OVERLAY ====== -->
    <div id="tp-loading">
        <div class="tp-ld-wrap">
            <div class="tp-ld-ring2"></div>
            <div class="tp-ld-ring"></div>
            <img src="<?= base_url('logo_inicio.png') ?>" alt="Cargando" class="tp-ld-logo">
        </div>
        <p class="tp-ld-text">Cargando…</p>
    </div>

    <script>
    (function () {
        var overlay = document.getElementById('tp-loading');
        var fetchCount = 0;
        var pageReady = false;

        function show() { overlay.classList.add('show'); }
        function hide() { overlay.classList.remove('show'); }

        function tryHide() {
            if (fetchCount <= 0 && pageReady) { fetchCount = 0; hide(); }
        }

        /* 1. Mostrar durante la carga inicial de la página */
        show();
        window.addEventListener('load', function () {
            pageReady = true;
            tryHide();
        });

        /* 2. Mostrar al navegar a otra página */
        window.addEventListener('beforeunload', show);

        /* 3b. Al volver con el botón atrás (bfcache) el overlay queda visible — forzar hide */
        window.addEventListener('pageshow', function (e) {
            pageReady = true;
            fetchCount = 0;
            hide();
        });

        /* 4. Interceptar todos los fetch para mostrar durante AJAX */
        var _origFetch = window.fetch;
        window.fetch = function () {
            var args = arguments;
            fetchCount++;

            /* Mostrar sólo si la petición tarda más de 300 ms (evita parpadeo en búsquedas rápidas) */
            var timer = setTimeout(function () {
                if (fetchCount > 0) show();
            }, 300);

            return _origFetch.apply(this, args).finally(function () {
                clearTimeout(timer);
                fetchCount = Math.max(0, fetchCount - 1);
                tryHide();
            });
        };
    })();
    </script>

    <script src="<?php echo base_url('assets/js/script.js'); ?>"></script>

<?php if (!$this->session->userdata('usuario_id') || $this->session->userdata('id_roles') != 1): ?>
<script>
(function() {
    var ENDPOINT = '<?= base_url("metricas/registrar") ?>';
    var isMobile = window.innerWidth < 768;

    function track(tipo, accion, extra) {
        var body = new URLSearchParams({
            tipo:         tipo,
            url:          window.location.pathname + window.location.search,
            accion:       accion || '',
            torneo_id:    (extra && extra.torneo_id)    ? extra.torneo_id    : '',
            categoria_id: (extra && extra.categoria_id) ? extra.categoria_id : '',
            es_mobile:    isMobile ? 'true' : 'false',
        });
        navigator.sendBeacon ? navigator.sendBeacon(ENDPOINT, body)
            : fetch(ENDPOINT, { method:'POST', body:body, keepalive:true }).catch(function(){});
    }

    // Page view al cargar
    window.addEventListener('load', function() {
        var m = window.location.search.match(/[?&]torneo_id=(\d+)/);
        var c = window.location.search.match(/[?&]categoria_id=(\d+)/);
        track('page_view', '', {
            torneo_id:    m ? m[1] : '',
            categoria_id: c ? c[1] : '',
        });
    });

    // Exponer función global para acciones específicas
    window.trackAccion = function(accion, extra) {
        track('accion', accion, extra || {});
    };
})();
</script>
<?php endif; ?>
</body>
<!-- ====== PWA INSTALL BANNER ====== -->
<style>
#pwa-banner {
    display: none;
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #1a1a2e;
    border-top: 2px solid #FF6600;
    padding: 14px 18px;
    z-index: 99999;
    align-items: center;
    gap: 12px;
    box-shadow: 0 -4px 20px rgba(0,0,0,.35);
}
#pwa-banner.show { display: flex; }
#pwa-banner img { width: 46px; height: 46px; border-radius: 10px; flex-shrink: 0; }
#pwa-banner .pwa-txt { flex: 1; }
#pwa-banner .pwa-txt strong { display: block; color: #fff; font-size: 14px; }
#pwa-banner .pwa-txt span { color: rgba(255,255,255,.6); font-size: 12px; }
#pwa-banner .pwa-instalar {
    background: #FF6600; color: #fff; border: none;
    padding: 9px 18px; border-radius: 8px; font-size: 13px;
    font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;
}
#pwa-banner .pwa-cerrar {
    background: none; border: none; color: rgba(255,255,255,.45);
    font-size: 20px; cursor: pointer; padding: 4px 6px; flex-shrink: 0; line-height: 1;
}
</style>

<div id="pwa-banner">
    <img src="<?= base_url('logo_inicio.png') ?>" alt="Telares Padel">
    <div class="pwa-txt">
        <strong>Telares Padel</strong>
        <span>Instalá la app y seguí los torneos</span>
    </div>
    <button class="pwa-instalar" id="pwa-btn-instalar">Instalar</button>
    <button class="pwa-cerrar" id="pwa-btn-cerrar" title="Cerrar">&times;</button>
</div>

<script>
(function() {
    const VAPID_PUBLIC    = '<?= getenv("VAPID_PUBLIC") ?>';
    const URL_SUSCRIBIR   = '<?= base_url("admin/Push/suscribir") ?>';

    /* ===== REGISTRAR SERVICE WORKER ===== */
    if ('serviceWorker' in navigator) {
        // Sin scope explícito: el browser lo infiere desde la ubicación del archivo sw.js
        navigator.serviceWorker.register('<?= base_url("sw.js") ?>')
            .then(reg => {
                <?php if ($this->session->userdata('usuario_id')): ?>
                solicitarPush(reg);
                <?php endif; ?>
            })
            .catch(err => console.warn('SW error:', err));
    }

    /* ===== INSTALAR PWA ===== */
    let deferredPrompt = null;
    const banner      = document.getElementById('pwa-banner');
    const btnInstalar = document.getElementById('pwa-btn-instalar');
    const btnCerrar   = document.getElementById('pwa-btn-cerrar');

    // No mostrar si ya fue instalada
    if (window.matchMedia('(display-mode: standalone)').matches) {
        // ya instalada, nada que hacer
    } else {
        // Limpiar dismissed si pasaron más de 7 días
        const ts = localStorage.getItem('pwa-dismissed-ts');
        if (ts && Date.now() - parseInt(ts) > 7 * 24 * 3600 * 1000) {
            localStorage.removeItem('pwa-dismissed');
            localStorage.removeItem('pwa-dismissed-ts');
        }

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            if (!localStorage.getItem('pwa-dismissed')) {
                banner.classList.add('show');
            }
        });

        btnInstalar.addEventListener('click', function() {
            if (!deferredPrompt) return;
            banner.classList.remove('show');
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function() { deferredPrompt = null; });
        });

        btnCerrar.addEventListener('click', function() {
            banner.classList.remove('show');
            localStorage.setItem('pwa-dismissed', '1');
            localStorage.setItem('pwa-dismissed-ts', Date.now().toString());
        });
    }

    /* ===== SUSCRIPCIÓN PUSH ===== */
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = window.atob(base64);
        return new Uint8Array([...raw].map(c => c.charCodeAt(0)));
    }

    function solicitarPush(reg) {
        if (!('PushManager' in window)) return;
        if (Notification.permission === 'denied') return;

        reg.pushManager.getSubscription().then(sub => {
            if (sub) return; // ya suscripto

            Notification.requestPermission().then(perm => {
                if (perm !== 'granted') return;

                reg.pushManager.subscribe({
                    userVisibleOnly:      true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC)
                }).then(newSub => {
                    fetch(URL_SUSCRIBIR, {
                        method: 'POST',
                        body:   JSON.stringify(newSub),
                        headers: { 'Content-Type': 'application/json' }
                    });
                }).catch(err => console.warn('Push subscribe error:', err));
            });
        });
    }
})();
</script>
</html>
