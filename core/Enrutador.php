<?php
// =====================================================================
// FILE: core/Enrutador.php
// =====================================================================
// DESCRIPCIÓN: Enrutador central de la aplicación. Lee el parámetro ?action= de la URL, verifica los permisos de acceso (roles y sesión) y despacha la solicitud al método del controlador correspondiente. También maneja respuestas JSON para endpoints AJAX y peticiones REST.
// UBICACIÓN MVC: Router
// ¿POR QUÉ EXISTE? Reemplaza el enrutamiento tradicional de un framework completo. En una aplicación MVC sin un router externo, este archivo es el encargado de mapear URLs a controladores y métodos, validando que el usuario tenga permiso para acceder a cada ruta.
// CÓMO SE USA: Llamado desde index.php mediante Enrutador::dispatch(). El método dispatch() es estático y utiliza un switch sobre el valor de $_GET['action'].
// ESTRUCTURA DE ROUTAS:
//   - Públicas: home, contact, galeria, booking, consent, profile, promotions, recomendaciones, 404, 500, csrf-token
//   - Autenticación: login, register, logout, cambiar-clave, delete-account
//   - Dashboard cliente/admin: dashboard, cliente-citas, cliente-abonos, cliente-consentimiento, cliente-agendar
//   - Dashboard admin: admin, admin-usuarios, admin-servicios, admin-transacciones, admin-save-user, admin-save-service, admin-delete-service
//   - Panel tatuador: artist-panel, artist-agenda, artist-horarios, artist-perfil, artist-update-estado, artist-save-schedule, artist-citas-json, artist-switch-mode
//   - API REST: galeria-list, galeria-upload, book-appointment, submit-consent, validate-promo, redeem-promo, reporte-citas, reporte-citas-json, api
// SEGURIDAD: Cada ruta verifica $_SESSION['user'] y el rol antes de ejecutar el controlador. Los endpoints AJAX devuelven JSON con código 401 si no hay sesión.
// =====================================================================

// Punto de entrada del enrutador MVC.
// Traduce la acción solicitada (parámetro ?action=) al método correspondiente
// del controlador. Es el único archivo que decide qué controlador ejecutar.
class Enrutador
{
    public static function dispatch(): void
    {
        // Obtiene la acción de la URL, por defecto 'home'
        $action = $_GET['action'] ?? 'home';
        $pageController = new ControladorPaginas();
        $authController = new ControladorAutenticacion();
        $tatuadorController = new ControladorTatuador();
        $dashboardController = new ControladorDashboard();

        switch ($action) {
            // --- Página principal ---
            case 'home':
                $pageController->home();
                break;
            // --- Autenticación ---
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->login();
                } else {
                    $pageController->login();
                }
                break;
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->register();
                } else {
                    $pageController->register();
                }
                break;
            // --- Dashboard (requiere sesión) ---
            case 'dashboard':
                $user = $_SESSION['user'] ?? null;
                if (!$user) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                $rol = $user['rol'] ?? 'cliente';
                // Redirige según el rol del usuario
                if ($rol === 'tatuador') {
                    $pageController->redirect(BASE_URL . 'index.php?action=artist-panel');
                    return;
                }
                if ($rol === 'admin') {
                    $dashboardController->adminMetrics();
                    return;
                }
                $dashboardController->clienteCitas();
                break;
            // --- Panel Cliente ---
            case 'cliente-citas':
            case 'cliente-abonos':
            case 'cliente-consentimiento':
            case 'cliente-agendar':
                $user = $_SESSION['user'] ?? null;
                if (!$user) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Inicia sesión para continuar.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                // Call the appropriate method
                if ($action === 'cliente-citas') {
                    $dashboardController->clienteCitas();
                } elseif ($action === 'cliente-abonos') {
                    $dashboardController->clienteAbonos();
                } elseif ($action === 'cliente-consentimiento') {
                    $dashboardController->clienteConsentimiento();
                } elseif ($action === 'cliente-agendar') {
                    $dashboardController->clienteAgendar();
                }
                break;
            // --- Panel Administrador ---
            case 'admin':
            case 'admin-usuarios':
            case 'admin-servicios':
            case 'admin-transacciones':
            case 'admin-save-user':
            case 'admin-save-service':
            case 'admin-delete-service':
                // Verificación de rol: solo usuarios con rol 'admin' pueden acceder
                $user = $_SESSION['user'] ?? null;
                if (!$user || ($user['rol'] ?? '') !== 'admin') {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Inicia sesión para continuar.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                if ($action === 'admin') {
                    $dashboardController->adminMetrics();
                } elseif ($action === 'admin-usuarios') {
                    $dashboardController->adminUsuarios();
                } elseif ($action === 'admin-servicios') {
                    $dashboardController->adminServicios();
                } elseif ($action === 'admin-transacciones') {
                    $dashboardController->adminTransacciones();
                } elseif ($action === 'admin-save-user') {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $dashboardController->adminSaveUser();
                    } else {
                        $dashboardController->adminUsuarios();
                    }
                } elseif ($action === 'admin-save-service') {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $dashboardController->adminSaveService();
                    } else {
                        $dashboardController->adminServicios();
                    }
                } elseif ($action === 'admin-delete-service') {
                    $dashboardController->adminDeleteService();
                } elseif ($action === 'admin-dashboard') {
                    $dashboardController->adminDashboard();
                } elseif ($action === 'admin-update-estado') {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $dashboardController->ajaxUpdateEstado();
                    } else {
                        $dashboardController->adminDashboard();
                    }
                } elseif ($action === 'admin-registrar-pago') {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $dashboardController->ajaxRegistrarPago();
                    } else {
                        $dashboardController->adminDashboard();
                    }
                }
                break;
            // --- Páginas públicas ---
            case 'contact':
                $pageController->contact();
                break;
            case 'galeria':
                $pageController->galeria();
                break;
            case 'booking':
                $pageController->booking();
                break;
            case 'consent':
                $pageController->consent();
                break;
            case 'profile':
                $pageController->profile();
                break;
            case 'promotions':
                $pageController->promotions();
                break;
            case 'recomendaciones':
                $pageController->recomendaciones();
                break;
            case 'demo-404':
                header("HTTP/1.0 404 Not Found");
                $pageController->notFound();
                break;
            case 'demo-500':
                throw new Exception("Simulación de error del servidor para demostración");
                break;
            // --- Errores ---
            case '404':
                $pageController->notFound();
                break;
            case '500':
                $pageController->serverError();
                break;
            case 'csrf-token':
                $pageController->csrfToken();
                break;
            // --- Galería (API) ---
            case 'galeria-list':
                require_once DIR_PATH . 'app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->list();
                break;
            case 'galeria-upload':
                $user = $_SESSION['user'] ?? null;
                if (!$user || !in_array($user['rol'] ?? '', ['tatuador', 'admin'], true)) {
                    http_response_code(403);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'message' => 'Acceso denegado. Solo personal autorizado.']);
                    exit;
                }
                require_once DIR_PATH . 'app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->upload();
                break;
            // --- Citas (abono) ---
            case 'book-appointment':
                require_once DIR_PATH . 'app/controllers/ControladorCitas.php';
                (new ControladorCitas())->store();
                break;
            // --- Pagos/Abonos adicionales ---
            case 'registrar-abono':
                require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                (new ControladorPagos())->store();
                break;
            case 'listar-abonos':
                require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                (new ControladorPagos())->list();
                break;
            case 'actualizar-estado-pago':
                require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                (new ControladorPagos())->updateEstado();
                break;
            // --- Consentimiento ---
            case 'submit-consent':
                require_once DIR_PATH . 'app/controllers/ControladorConsentimiento.php';
                (new ControladorConsentimiento())->store();
                break;
            // --- API / Acciones que requieren sesión ---
            case 'book-appointment':
            case 'submit-consent':
            case 'validate-promo':
            case 'redeem-promo':
            case 'reporte-citas':
            case 'reporte-citas-json':
            case 'registrar-abono':
            case 'listar-abonos':
                $user = $_SESSION['user'] ?? null;
                if (!$user) {
                    // Para endpoints AJAX, devolver JSON en lugar de redirigir
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        http_response_code(401);
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['ok' => false, 'message' => 'Inicia sesión para continuar.', 'redirect' => BASE_URL . 'index.php?action=login']);
                        exit;
                    }
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Inicia sesión para continuar.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                if ($action === 'book-appointment') {
                    require_once DIR_PATH . 'app/controllers/ControladorCitas.php';
                    (new ControladorCitas())->store();
                } elseif ($action === 'submit-consent') {
                    require_once DIR_PATH . 'app/controllers/ControladorConsentimiento.php';
                    (new ControladorConsentimiento())->store();
                } elseif ($action === 'validate-promo') {
                    require_once DIR_PATH . 'app/controllers/ControladorPromociones.php';
                    (new ControladorPromociones())->validateCode();
                } elseif ($action === 'redeem-promo') {
                    require_once DIR_PATH . 'app/controllers/ControladorPromociones.php';
                    (new ControladorPromociones())->redeem();
                } elseif ($action === 'reporte-citas') {
                    require_once DIR_PATH . 'app/controllers/ControladorReportes.php';
                    (new ControladorReportes())->citasCsv();
                } elseif ($action === 'reporte-citas-json') {
                    require_once DIR_PATH . 'app/controllers/ControladorReportes.php';
                    (new ControladorReportes())->citasJson();
                } elseif ($action === 'registrar-abono') {
                    require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                    (new ControladorPagos())->store();
                } elseif ($action === 'listar-abonos') {
                    require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                    (new ControladorPagos())->list();
                }
                break;
            // --- Cerrar sesión ---
            case 'logout':
                $authController->logout();
                break;
            case 'cambiar-clave':
            case 'delete-account':
            case 'profile':
                $user = $_SESSION['user'] ?? null;
                if (!$user) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Inicia sesión para continuar.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                if ($action === 'cambiar-clave') {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $authController->changeTempPassword();
                    } else {
                        $pageController->changePasswordForm();
                    }
                } elseif ($action === 'delete-account') {
                    $authController->deleteAccount();
                } elseif ($action === 'profile') {
                    $pageController->profile();
                }
                break;
            // --- Módulo Tatuador ---
            case 'artist-panel':
            case 'artist-agenda':
            case 'artist-horarios':
            case 'artist-perfil':
            case 'artist-update-estado':
            case 'artist-save-schedule':
            case 'artist-citas-json':
            case 'actualizar-estado-pago':
                // Verificación de rol: requiere 'tatuador' o 'admin' con modo activado
                $user = $_SESSION['user'] ?? null;
                if (!$user || !in_array($user['rol'] ?? '', ['tatuador', 'admin'], true)) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Inicia sesión para continuar.'];
                    $pageController->redirect(BASE_URL . 'index.php?action=login');
                    return;
                }
                if ($action === 'artist-panel') {
                    $tatuadorController->dashboard();
                } elseif ($action === 'artist-agenda') {
                    $tatuadorController->agenda();
                } elseif ($action === 'artist-horarios') {
                    $tatuadorController->horarios();
                } elseif ($action === 'artist-perfil') {
                    $tatuadorController->perfil();
                } elseif ($action === 'artist-update-estado') {
                    $tatuadorController->updateEstado();
                } elseif ($action === 'artist-save-schedule') {
                    $tatuadorController->saveSchedule();
                } elseif ($action === 'artist-citas-json') {
                    $tatuadorController->citasJson();
                } elseif ($action === 'actualizar-estado-pago') {
                    require_once DIR_PATH . 'app/controllers/ControladorPagos.php';
                    (new ControladorPagos())->updateEstado();
                }
                break;
            case 'artist-switch-mode':
                $tatuadorController->switchMode();
                break;
            // --- API REST ---
            case 'api':
                $user = $_SESSION['user'] ?? null;
                if (!$user) {
                    http_response_code(401);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'message' => 'Inicia sesión para continuar.', 'redirect' => BASE_URL . 'index.php?action=login']);
                    exit;
                }
                require_once DIR_PATH . 'app/controllers/ControladorApi.php';
                (new ControladorApi())->handle();
                break;
            default:
                $pageController->notFound();
        }
    }
}
