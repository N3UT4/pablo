<?php
// =====================================================================
// FILE: app/views/layout/pie.php
// =====================================================================
// DESCRIPCIÓN: Layout inferior (footer) de la aplicación. Renderiza el pie de página con redes sociales, enlaces legales, ubicación del estudio, cierre de etiquetas HTML (<main>, <div>) y carga los scripts JavaScript específicos de cada página según $currentPage.
// UBICACIÓN MVC: View (layout)
// ¿POR QUÉ EXISTE? Proporciona la estructura HTML común al final de todas las páginas: footer, cierre de contenedores y scripts JS condicionales. Evita repetir este código en cada vista.
// CÓMO SE USA: Requerido automáticamente por ControladorBase::view() con:
//   require DIR_PATH . 'app/views/layout/pie.php';
// VARIABLES DISPONIBLES (definidas en encabezado.php o esperadas):
//   - $user: datos del usuario de sesión (null si no está logueado).
//   - $currentPage: string — nombre de la página actual, usado para cargar scripts específicos.
// SCRIPTS CARGADOS POR PÁGINA:
//   - home: Swiper (carrusel de galería).
//   - login: autenticacion.js.
//   - register: registro.js.
//   - gallery: api.js + galeria.js.
//   - booking: api.js + abono.js.
//   - consent: api.js + consentimiento.js.
//   - profile: perfil.js.
//   - promotions: api.js + promociones.js.
//   - artist-panel/agenda/horarios/perfil: api.js + tatuador.js.
// SCRIPTS GLOBALES: internacionalizacion.php, swal-tema.php, formularios-fx.php, animaciones.php.
// SEGURIDAD: htmlspecialchars() en URLs de redes sociales y dirección para prevenir XSS.
// =====================================================================
?>
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

<!-- Cierre de contenedores HTML: main y div (depende de si el usuario está logueado) -->
<?php if ($user ?? false): ?>
    </main>
    </div>
<?php else: ?>
    </main>
<?php endif; ?>

<!-- Scripts del sitio -->
<!-- Scripts globales cargados en todas las páginas -->
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
    <!-- Scripts para la página de reservas: API + abonos -->
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
    <!-- Scripts para el panel de tatuador: API + lógica de tatuador -->
    <script src="<?= BASE_URL ?>js/api.php"></script>
    <script src="<?= BASE_URL ?>js/tatuador.php"></script>
<?php endif; ?>
</body>
</html>
