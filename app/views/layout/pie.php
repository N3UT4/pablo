<footer class="site-footer">
    <div class="wrap footer-inner">
        <!-- Redes sociales del estudio -->
        <div class="footer-connect footer-col-left">
            <span class="footer-nav-title" data-i18n="home.follow">Síguenos</span>
            <div class="social-links" aria-label="Redes sociales del estudio">
                <a href="<?= htmlspecialchars(STUDIO_FACEBOOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i><span>Facebook</span>
                </a>
                <a href="<?= htmlspecialchars(STUDIO_INSTAGRAM) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i><span>Instagram</span>
                </a>
                <a href="<?= htmlspecialchars(STUDIO_TIKTOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                    <i class="fa-brands fa-tiktok" aria-hidden="true"></i><span>TikTok</span>
                </a>
                <a href="<?= htmlspecialchars(STUDIO_MAPS) ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                    <i class="fa-brands fa-youtube" aria-hidden="true"></i><span>YouTube</span>
                </a>
            </div>
        </div>

        <!-- Enlaces legales y de navegación -->
        <nav class="footer-nav footer-col-center" aria-label="Enlaces legales">
            <span class="footer-nav-title" data-i18n="home.legal">Legal</span>
            <a href="<?= BASE_URL ?>index.php?action=home#privacidad" data-i18n="home.privacy">Privacidad</a>
            <a href="<?= BASE_URL ?>index.php?action=home#terminos" data-i18n="home.terms">Términos</a>
            <a href="<?= BASE_URL ?>index.php?action=home#cookies" data-i18n="home.cookies">Cookies</a>
            <a href="<?= BASE_URL ?>index.php?action=home#proceso">Cómo funciona</a>
            <a href="<?= BASE_URL ?>index.php?action=promotions">Promociones</a>
        </nav>

        <!-- Ubicación del estudio -->
        <div class="footer-location footer-col-right">
            <span class="footer-nav-title">Ubicación</span>
            <address>
                <span class="city-line">Estudio de Bogotá, La Victoria</span><br>
                <?= htmlspecialchars(STUDIO_ADDRESS) ?>
            </address>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="wrap">© <?= date('Y') ?> ITZA TATTOO. Todos los derechos reservados.</div>
    </div>
</footer>

<?php if ($user ?? false): ?>
    </main>
    </div>
<?php else: ?>
    </main>
<?php endif; ?>

<!-- Scripts del sitio -->
<script src="<?= BASE_URL ?>js/internacionalizacion.php"></script>
<script src="<?= BASE_URL ?>js/swal-tema.php"></script>
<script src="<?= BASE_URL ?>js/formularios-fx.php"></script>
<script src="<?= BASE_URL ?>js/animaciones.php"></script>
<?php if (($currentPage ?? '') === 'home'): ?>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
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
