<?php
require_once dirname(__DIR__, 3) . '/config/config.php';

$user = $_SESSION['user'] ?? null;
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ITZA TATTOO STUDIO') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=New+Rocker&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <nav>
            <a href="<?= BASE_URL ?>index.php?action=home" class="logo">
                <img src="<?= BASE_URL ?>img/itza tatto.png" alt="ITZA TATTOO Logo" class="logo-img">
                ITZA <span>TATTOO</span>
            </a>
            <div class="nav-links">
                <a href="<?= BASE_URL ?>index.php?action=home#servicios">Servicios</a>
                <a href="<?= BASE_URL ?>index.php?action=home#proceso">Cómo funciona</a>
                <a href="<?= BASE_URL ?>index.php?action=home#galeria">Galería</a>
                <a href="<?= BASE_URL ?>index.php?action=home#artistas">Artista</a>
                <a href="<?= BASE_URL ?>index.php?action=contact">Contacto</a>
                <a href="<?= BASE_URL ?>index.php?action=promotions">Promociones</a>
             </div>
             <?php if ($user): ?>
        <?php
            $rol = $user['rol'] ?? 'cliente';
            $artistMode = !empty($_SESSION['artist_mode']);
            $isArtist = ($rol === 'tatuador' || ($rol === 'admin' && $artistMode));
        ?>
        <?php if ($isArtist): ?>
            <div class="artist-header" style="display:flex; gap:12px; align-items:center;">
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
                            <a href="<?= BASE_URL ?>index.php?action=artist-switch-mode&exit=1" style="color:var(--gold);">Salir modo tatuador</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>index.php?action=logout" class="dropdown-logout">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="display:flex; gap:12px; align-items:center;">
                <?php if ($rol === 'admin'): ?>
                    <a href="<?= BASE_URL ?>index.php?action=artist-switch-mode" class="btn-ghost" style="padding:10px 14px; border-radius:999px;">Ver Modo Tatuador</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>index.php?action=dashboard" class="nav-cta">Dashboard</a>
                <a href="<?= BASE_URL ?>index.php?action=logout" class="btn-ghost" style="padding:10px 14px; border-radius:999px;">Salir</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div style="display:flex; gap:12px; align-items:center;">
            <a href="<?= BASE_URL ?>index.php?action=login" class="btn-ghost" style="padding:10px 14px; border-radius:999px;">Ingresar</a>
            <a href="<?= BASE_URL ?>index.php?action=register" class="nav-cta">Agendar cita</a>
        </div>
    <?php endif; ?>
        <div class="lang-switcher"></div>
    </nav>
    </header>

    <?php if ($flash): ?>
        <div class="container container-narrow" style="padding-top: 24px; padding-bottom: 0;">
            <div class="alert show <?= $flash['type'] === 'success' ? 'success' : '' ?>">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>
