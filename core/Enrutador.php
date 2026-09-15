<?php

// Traduce la acción solicitada a un método del controlador correspondiente.
class Enrutador
{
    public static function dispatch(): void
    {
        $action = $_GET['action'] ?? 'home';
        $pageController = new ControladorPaginas();
        $authController = new ControladorAutenticacion();

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
                require_once APP_ROOT . '/app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->list();
                break;
            case 'gallery-upload':
                require_once APP_ROOT . '/app/controllers/ControladorGaleria.php';
                (new ControladorGaleria())->upload();
                break;
            case 'book-appointment':
                require_once APP_ROOT . '/app/controllers/ControladorCitas.php';
                (new ControladorCitas())->store();
                break;
            case 'submit-consent':
                require_once APP_ROOT . '/app/controllers/ControladorConsentimiento.php';
                (new ControladorConsentimiento())->store();
                break;
            case 'validate-promo':
                require_once APP_ROOT . '/app/controllers/ControladorPromociones.php';
                (new ControladorPromociones())->validateCode();
                break;
            case 'redeem-promo':
                require_once APP_ROOT . '/app/controllers/ControladorPromociones.php';
                (new ControladorPromociones())->redeem();
                break;
            case 'api':
                require_once APP_ROOT . '/app/controllers/ControladorApi.php';
                (new ControladorApi())->handle();
                break;
            case 'reporte-citas':
                require_once APP_ROOT . '/app/controllers/ControladorReportes.php';
                (new ControladorReportes())->citasCsv();
                break;
            case 'reporte-citas-json':
                require_once APP_ROOT . '/app/controllers/ControladorReportes.php';
                (new ControladorReportes())->citasJson();
                break;
            case 'logout':
                $authController->logout();
                break;
            case 'delete-account':
                $authController->deleteAccount();
                break;
            default:
                $pageController->notFound();
        }
    }
}