<?php
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
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <nav>
            <a href="index.php?action=home" class="logo">
                <img src="img/itza tatto.png" alt="ITZA TATTOO Logo" class="logo-img">
                ITZA <span>TATTOO</span>
            </a>
            <div class="nav-links">
                <a href="index.php?action=home#servicios">Servicios</a>
                <a href="index.php?action=home#proceso">Cómo funciona</a>
                <a href="index.php?action=home#artistas">Artista</a>
                <a href="index.php?action=contact">Contacto</a>
            </div>
            <div class="lang-switcher"></div>
            <?php if ($user): ?>
                <div style="display:flex; gap:12px; align-items:center;">
                    <a href="index.php?action=dashboard" class="nav-cta">Dashboard</a>
                    <a href="index.php?action=logout" class="btn-ghost" style="padding:10px 14px; border-radius:999px;">Salir</a>
                </div>
            <?php else: ?>
                <div style="display:flex; gap:12px; align-items:center;">
                    <a href="index.php?action=login" class="btn-ghost" style="padding:10px 14px; border-radius:999px;">Ingresar</a>
                    <a href="index.php?action=register" class="nav-cta">Agendar cita</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($flash): ?>
        <div class="container container-narrow" style="padding-top: 24px; padding-bottom: 0;">
            <div class="alert show <?= $flash['type'] === 'success' ? 'success' : '' ?>">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>
