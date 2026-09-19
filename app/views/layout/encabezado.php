<?php
// Encabezado/layout superior de la aplicación.
// Se compone de partials en app/views/partials/
require_once dirname(__DIR__, 3) . '/config/config.php';

$user = $_SESSION['user'] ?? null;
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$rol = $user['rol'] ?? 'cliente';
$artistMode = !empty($_SESSION['artist_mode']);
$isArtist = ($rol === 'tatuador' || ($rol === 'admin' && $artistMode));
$currentPage = $currentPage ?? '';
$currentAction = $_GET['action'] ?? '';

require_once DIR_PATH . 'app/views/partials/head.php';

if ($user):
    require_once DIR_PATH . 'app/views/partials/sidebar.php';
else:
    require_once DIR_PATH . 'app/views/partials/header-visitor.php';
endif;

require_once DIR_PATH . 'app/views/partials/flash.php';
require_once DIR_PATH . 'app/views/partials/nav-scripts.php';
