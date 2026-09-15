<?php
// Punto de entrada de la aplicación MVC.
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/ControladorBase.php';
require_once __DIR__ . '/core/Enrutador.php';
require_once __DIR__ . '/app/controllers/ControladorPaginas.php';
require_once __DIR__ . '/app/controllers/ControladorAutenticacion.php';

// Registra el error sin exponer detalles técnicos y muestra una respuesta 500 segura.
set_exception_handler(static function (Throwable $exception): void {
    error_log((string) $exception);
    (new ControladorPaginas())->serverError();
});

Enrutador::dispatch();