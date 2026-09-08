<?php
// Punto de entrada de la aplicación MVC.
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/app/controllers/PageController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

// Registra el error sin exponer detalles técnicos y muestra una respuesta 500 segura.
set_exception_handler(static function (Throwable $exception): void {
    error_log((string) $exception);
    (new PageController())->serverError();
});

Router::dispatch();
