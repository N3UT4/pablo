<?php
// =====================================================================
// FILE: app/views/layout/encabezado.php
// =====================================================================
// DESCRIPCIÓN: Layout superior (header) de la aplicación. Incluye el head HTML, el sidebar para usuarios autenticados o el header para visitantes, los mensajes flash y los scripts de navegación. Se carga automáticamente al inicio de cada vista renderizada por ControladorBase::view().
// UBICACIÓN MVC: View (layout)
// ¿POR QUÉ EXISTE? Proporciona la estructura HTML común a todas las páginas: etiquetas <head>, metadatos, carga de CSS, navegación y apertura del contenedor principal. Evita repetir este código en cada vista.
// CÓMO SE USA: Requerido automáticamente por ControladorBase::view() con:
//   require DIR_PATH . 'app/views/layout/encabezado.php';
// Se cierra con require DIR_PATH . 'app/views/layout/pie.php';
// VARIABLES DISPONIBLES (definidas o esperadas):
//   - $user: datos del usuario de sesión (null si no está logueado).
//   - $flash: mensaje temporal para mostrar al usuario (se elimina de la sesión después de leer).
//   - $rol: rol del usuario ('cliente', 'tatuador', 'admin').
//   - $artistMode: booleano — true si el admin ha activado el modo tatuador.
//   - $isArtist: true si el usuario es tatuador o admin en modo tatuador.
//   - $currentPage: string — nombre de la página actual (usado para resaltar navegación y cargar scripts específicos en pie.php).
//   - $currentAction: string — acción GET actual.
// PARTIALS CARGADOS:
//   - head.php: etiquetas <head>, meta, CSS, favicon.
//   - sidebar.php: navegación lateral para usuarios autenticados.
//   - header-visitor.php: encabezado para visitantes no autenticados.
//   - flash.php: muestra mensajes flash de sesión.
//   - nav-scripts.php: scripts JS globales y configuración.
// NOTA: Se requiere config.php de nuevo aquí porque este layout puede usarse de forma independiente.
// =====================================================================

// Encabezado/layout superior de la aplicación.
// Se compone de partials en app/views/partials/
require_once dirname(__DIR__, 3) . '/config/config.php';

// Recupera el usuario de la sesión (null si no está logueado)
$user = $_SESSION['user'] ?? null;
// Recupera el mensaje flash de la sesión y lo elimina para que no se muestre de nuevo
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Determina el rol del usuario: 'cliente' por defecto si no está logueado
$rol = $user['rol'] ?? 'cliente';
// Verifica si el admin ha activado el modo tatuador (permite acceder a funciones de tatuador)
$artistMode = !empty($_SESSION['artist_mode']);
// Un usuario es "artista" si es tatuador directamente, o admin con modo tatuador activado
$isArtist = ($rol === 'tatuador' || ($rol === 'admin' && $artistMode));
// Página actual: se pasa desde el controlador via view() como variable extraída
$currentPage = $currentPage ?? '';
$currentAction = $_GET['action'] ?? '';

// Carga el <head> HTML con metadatos, CSS y favicon
require_once DIR_PATH . 'app/views/partials/head.php';

// Si el usuario está logueado: muestra el sidebar de navegación
if ($user):
    require_once DIR_PATH . 'app/views/partials/sidebar.php';
else:
    // Si no está logueado: muestra el header para visitantes
    require_once DIR_PATH . 'app/views/partials/header-visitor.php';
endif;

// Muestra mensajes flash (éxito/error) almacenados en sesión
require_once DIR_PATH . 'app/views/partials/flash.php';
// Carga scripts JS globales y datos de configuración (nav-scripts)
require_once DIR_PATH . 'app/views/partials/nav-scripts.php';
