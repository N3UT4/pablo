<?php
// Partial: Head HTML con meta, fonts, CSS y librerías.
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
    <?php
    // Carga CSS exclusivo del panel de administración cuando la acción lo requiere.
    $adminActions = ['admin', 'admin-usuarios', 'admin-servicios', 'admin-transacciones', 'admin-dashboard'];
    $isAdmin = in_array($currentAction ?? '', $adminActions, true);
    if ($isAdmin): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>css/admin.css">
    <?php endif; ?>
    <?php if (($currentAction ?? '') === 'admin-dashboard'): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard-admin.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="<?= $isAdmin ? 'admin-page' : '' ?>">