<?php
// Partial: Header para visitantes (no logueados).
// Requiere: $user, $rol, $artistMode, $isArtist, $currentAction, $currentPage
?>
<header class="site-header">
    <nav class="nav-shell" aria-label="Navegación principal">
        <a href="<?= BASE_URL ?>index.php?action=home" class="brand" aria-label="ITZA TATTOO, inicio">
            <img src="<?= BASE_URL ?>img/itza tatto.png?v=2" alt="" class="brand-logo" aria-hidden="true" style="width:28px;height:28px;object-fit:contain;">
            <span class="brand-copy">ITZA <span>TATTOO</span></span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="Abrir menú">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
        <div class="nav-menu" id="primary-menu">
            <div class="nav-links">
                <a href="<?= BASE_URL ?>index.php?action=home#servicios">Servicios</a>
                <a href="<?= BASE_URL ?>index.php?action=home#galeria">Galería</a>
                <div class="dropdown">
                    <button type="button" class="dropdown-btn" aria-expanded="false" aria-haspopup="true">
                        Explorar <span class="caret" aria-hidden="true">▾</span>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="<?= BASE_URL ?>index.php?action=home#proceso" role="menuitem">Cómo funciona</a>
                        <a href="<?= BASE_URL ?>index.php?action=home#artistas" role="menuitem">Artista</a>
                        <a href="<?= BASE_URL ?>index.php?action=promotions" role="menuitem">Promociones</a>
                        <a href="<?= BASE_URL ?>index.php?action=contact" role="menuitem">Contacto</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="nav-actions">
            <?php if ($user): ?>
            <?php
                $artistMode = !empty($_SESSION['artist_mode']);
                $isArtist = ($rol === 'tatuador' || ($rol === 'admin' && $artistMode));
            ?>
            <?php if ($isArtist): ?>
                <div class="artist-header">
                    <div class="artist-dropdown">
                        <button type="button" class="artist-dropdown-btn" aria-expanded="false">
                            Panel de Tatuador <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="artist-dropdown-menu">
                            <a href="<?= BASE_URL ?>index.php?action=artist-panel">Mi Agenda</a>
                            <a href="<?= BASE_URL ?>index.php?action=artist-horarios">Mis Horarios</a>
                            <a href="<?= BASE_URL ?>index.php?action=artist-perfil">Mi Perfil</a>
                            <a href="<?= BASE_URL ?>index.php?action=artist-agenda">Citas Asignadas</a>
                            <?php if ($rol === 'admin' && $artistMode): ?>
                                <a href="<?= BASE_URL ?>index.php?action=artist-switch-mode&exit=1" class="artist-exit">Salir modo tatuador</a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>index.php?action=logout" class="dropdown-logout">Cerrar sesión</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-group">
                    <?php if ($rol === 'admin'): ?>
                        <a href="<?= BASE_URL ?>index.php?action=artist-switch-mode" class="btn-auth">Ver Modo Tatuador</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>index.php?action=dashboard" class="btn-cta">Dashboard</a>
                    <a href="<?= BASE_URL ?>index.php?action=logout" class="btn-auth">Salir</a>
                </div>
            <?php endif; ?>
            <?php else: ?>
                <div class="auth-group">
                    <a href="<?= BASE_URL ?>index.php?action=login" class="btn-auth nav-login">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                        <span>Ingresar</span>
                    </a>
                    <a href="<?= BASE_URL ?>index.php?action=login" class="btn-cta nav-booking" data-i18n="home.book_header">Agendar Cita</a>
                </div>
            <?php endif; ?>
            <div class="lang-switcher" aria-label="Selector de idioma"></div>
        </div>
    </nav>
</header>
<div class="studio-subtitle">
    <span class="studio-subtitle-inner">ESTUDIO DE TATUAJES • BOGOTÁ, LA VICTORIA • 20 DE JULIO</span>
</div>
<main>
