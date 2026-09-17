<?php

// Traduce la acción solicitada a un método del controlador correspondiente.
class Enrutador
{
    public static function dispatch(): void
    {
        $action = $_GET['action'] ?? 'home';
        $pageController = new ControladorPaginas();
        $authController = new ControladorAutenticacion();
        $tatuadorController = new ControladorTatuador();

        switch ($action) {
            case 'home':
                $pageController->home();
                break;
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
            case 'dashboard':
                $pageController->dashboard();
                break;
            case 'contact':
                $pageController->contact();
                break;
            case 'gallery':
                $pageController->gallery();
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
            case '404':
                $pageController->notFound();
                break;
            case '500':
                $pageController->serverError();
                break;
            case 'csrf-token':
                $pageController->csrfToken();
                break;
            case 'gallery-list':
                require_once DIR_PATH . 'app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->list();
                break;
            case 'gallery-upload':
                require_once DIR_PATH . 'app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->upload();
                break;
            case 'book-appointment':
                require_once DIR_PATH . 'app/controllers/ControladorCitas.php';
                (new ControladorCitas())->store();
                break;
            case 'submit-consent':
                require_once DIR_PATH . 'app/controllers/ControladorConsentimiento.php';
                (new ControladorConsentimiento())->store();
                break;
            case 'validate-promo':
                require_once DIR_PATH . 'app/controllers/ControladorPromociones.php';
                (new ControladorPromociones())->validateCode();
                break;
            case 'redeem-promo':
                require_once DIR_PATH . 'app/controllers/ControladorPromociones.php';
                (new ControladorPromociones())->redeem();
                break;
            case 'api':
                require_once DIR_PATH . 'app/controllers/ControladorApi.php';
                (new ControladorApi())->handle();
                break;
            case 'reporte-citas':
                require_once DIR_PATH . 'app/controllers/ControladorReportes.php';
                (new ControladorReportes())->citasCsv();
                break;
            case 'reporte-citas-json':
                require_once DIR_PATH . 'app/controllers/ControladorReportes.php';
                (new ControladorReportes())->citasJson();
                break;
            case 'logout':
                $authController->logout();
                break;
            case 'cambiar-clave':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->changeTempPassword();
                } else {
                    $pageController->changePasswordForm();
                }
                break;
            case 'delete-account':
                $authController->deleteAccount();
                break;
            // --- Módulo Tatuador ---
            case 'artist-panel':
                $tatuadorController->dashboard();
                break;
            case 'artist-agenda':
                $tatuadorController->agenda();
                break;
            case 'artist-horarios':
                $tatuadorController->horarios();
                break;
            case 'artist-perfil':
                $tatuadorController->perfil();
                break;
            case 'artist-switch-mode':
                $tatuadorController->switchMode();
                break;
            case 'artist-update-estado':
                $tatuadorController->updateEstado();
                break;
            case 'artist-save-schedule':
                $tatuadorController->saveSchedule();
                break;
            case 'artist-citas-json':
                $tatuadorController->citasJson();
                break;
            default:
                $pageController->notFound();
        }
    }
}