<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <a href="<?= BASE_URL ?>index.php?action=home" class="footer-logo">ITZA <span>TATTOO</span></a>
            <p><?= htmlspecialchars(STUDIO_CITY) ?></p>
        </div>
        <div class="social-links" aria-label="Redes sociales del estudio">
            <a href="<?= htmlspecialchars(STUDIO_INSTAGRAM) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i><span>Instagram</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_FACEBOOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i><span>Facebook</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_TIKTOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                <i class="fa-brands fa-tiktok" aria-hidden="true"></i><span>TikTok</span>
            </a>
            <a href="https://wa.me/<?= STUDIO_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>WhatsApp</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_MAPS) ?>" target="_blank" rel="noopener noreferrer" aria-label="Google Maps">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>Ubicación</span>
            </a>
        </div>
    </div>
</footer>
    <script src="<?= BASE_URL ?>js/internacionalizacion.php"></script>
    <script src="<?= BASE_URL ?>js/swal-tema.php"></script>
    <script src="<?= BASE_URL ?>js/formularios-fx.php"></script>
    <script src="<?= BASE_URL ?>js/animaciones.php"></script>
    <?php if (($currentPage ?? '') === 'home'): ?>
        <script src="<?= BASE_URL ?>js/galeria-carrusel.php"></script>
    <?php elseif (($currentPage ?? '') === 'login'): ?>
        <script src="<?= BASE_URL ?>js/autenticacion.php"></script>
    <?php elseif (($currentPage ?? '') === 'register'): ?>
        <script src="<?= BASE_URL ?>js/registro.php"></script>
    <?php elseif (($currentPage ?? '') === 'gallery'): ?>
        <script src="<?= BASE_URL ?>js/api.php"></script>
        <script src="<?= BASE_URL ?>js/galeria.php"></script>
    <?php elseif (($currentPage ?? '') === 'booking'): ?>
        <script src="<?= BASE_URL ?>js/api.php"></script>
        <script src="<?= BASE_URL ?>js/abono.php"></script>
    <?php elseif (($currentPage ?? '') === 'consent'): ?>
        <script src="<?= BASE_URL ?>js/api.php"></script>
        <script src="<?= BASE_URL ?>js/consentimiento.php"></script>
    <?php elseif (($currentPage ?? '') === 'profile'): ?>
        <script src="<?= BASE_URL ?>js/perfil.php"></script>
    <?php elseif (($currentPage ?? '') === 'promotions'): ?>
        <script src="<?= BASE_URL ?>js/api.php"></script>
        <script src="<?= BASE_URL ?>js/promociones.php"></script>
    <?php elseif (in_array(($currentPage ?? ''), ['artist-panel', 'artist-agenda', 'artist-horarios', 'artist-perfil'])): ?>
        <script src="<?= BASE_URL ?>js/api.php"></script>
        <script src="<?= BASE_URL ?>js/tatuador.php"></script>
    <?php endif; ?>
</body>
</html>
