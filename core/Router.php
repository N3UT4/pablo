<?php

// Traduce la acción solicitada a un método del controlador correspondiente.
class Router
{
    public static function dispatch(): void
    {
        $action = $_GET['action'] ?? 'home';
        $pageController = new PageController();

        switch ($action) {
            case 'home':
                $pageController->home();
                break;
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    (new AuthController())->login();
                } else {
                    $pageController->login();
                }
                break;
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    (new AuthController())->register();
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
            case 'logout':
                (new AuthController())->logout();
                break;
            case 'delete-account':
                (new AuthController())->deleteAccount();
                break;
            default:
                $pageController->notFound();
        }
    }
}
