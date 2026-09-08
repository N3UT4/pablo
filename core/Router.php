<?php

// Traduce la acción solicitada a un método del controlador correspondiente.
class Router
{
    public static function dispatch(): void
    {
        $action = $_GET['action'] ?? 'home';
        $pageController = new PageController();
        $authController = new AuthController();

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
