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

    <script src="<?php echo base_url('assets/js/script.js'); ?>"></script>
</body>
</html>
