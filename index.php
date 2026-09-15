<?php
// === CONFIGURACIÓN AUTOMÁTICA DE BASE DE DATOS (ITZA TATTOO) ===
$auto_host = "127.0.0.1";
$auto_user = "root";
$auto_pass = ""; 
$auto_dbname = "itza_tattoo"; 
$auto_sql = __DIR__ . "/database/database.sql"; 

// Conectar a MySQL en XAMPP
$auto_conn = new mysqli($auto_host, $auto_user, $auto_pass);
if (!$auto_conn->connect_error) {
    // Asegurar que la variable no esté vacía antes de ejecutar la consulta
    if (!empty($auto_dbname)) {
        if ($auto_conn->query("CREATE DATABASE IF NOT EXISTS `$auto_dbname`")) {
            $auto_conn->select_db($auto_dbname);
            
            // Verificar si la base de datos está vacía para importar las tablas
            $auto_check = $auto_conn->query("SHOW TABLES");
            if ($auto_check && $auto_check->num_rows == 0 && file_exists($auto_sql)) {
                $auto_query = file_get_contents($auto_sql);
                if ($auto_conn->multi_query($auto_query)) {
                    do {
                        if ($auto_result = $auto_conn->store_result()) { $auto_result->free(); }
                    } while ($auto_conn->more_results() && $auto_conn->next_result());
                }
            }
        }
    }
    $auto_conn->close();
}
// === FIN DE LA CONFIGURACIÓN AUTOMÁTICA ===

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
