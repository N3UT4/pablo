<?php
// =====================================================================
// FILE: index.php
// =====================================================================
// DESCRIPCIÓN: Punto de entrada principal de la aplicación ITZA TATTOO. Inicializa la base de datos automáticamente (crea la BD si no existe e importa las tablas desde el script SQL), ejecuta migraciones ligeras para agregar columnas relacionadas al módulo de tatuador, carga el autoload de componentes del framework MVC y despacha la petición al enrutador.
// UBICACIÓN MVC: Entry Point / Router
// ¿POR QUÉ EXISTE? Es el único script al que Apache dirige todas las peticiones (gracias a .htaccess con mod_rewrite). Coordina la inicialización del sistema y delega la lógica al Enrutador.
// CÓMO SE USA: Se accede mediante index.php?action=<nombre>. El parámetro ?action= determina qué controlador y método se ejecuta. La primera vez que se ejecuta, crea la base de datos y las tablas automáticamente.
// VARIABLES CLAVE:
//   - $auto_host, $auto_user, $auto_pass, $auto_dbname: credenciales de conexión a MySQL.
//   - $auto_sql: ruta al script SQL inicial (database/database.sql).
//   - $auto_conn: conexión mysqli usada para la creación y migración de la BD.
// =====================================================================

// === CONFIGURACIÓN AUTOMÁTICA DE BASE DE DATOS (ITZA TATTOO) ===
// Conecta a MySQL en XAMPP y crea la base de datos si no existe,
// luego importa las tablas desde database/database.sql si la BD está vacía.
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
                $auto_query = preg_replace('/DELIMITER\s+\S+/', '', $auto_query);
                $auto_query = str_replace('$$', ';', $auto_query);
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

// === MIGRACIÓN LEVE: tablas/columnas nuevas sin tocar datos existentes ===
$auto_conn = new mysqli($auto_host, $auto_user, $auto_pass, $auto_dbname);
if (!$auto_conn->connect_error) {
    // Agrega user_id a artists si falta
    $r = $auto_conn->query("SHOW COLUMNS FROM artists LIKE 'user_id'");
    if (!$r || $r->num_rows === 0) {
        $auto_conn->query("ALTER TABLE artists ADD COLUMN user_id INT UNSIGNED DEFAULT NULL AFTER id");
        $auto_conn->query("ALTER TABLE artists ADD INDEX idx_artists_user (user_id)");
    }

    // Crea artist_schedules si falta (horarios del tatuador)
    $auto_conn->query("CREATE TABLE IF NOT EXISTS artist_schedules (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        artist_id INT UNSIGNED NOT NULL,
        dia_semana TINYINT(1) NOT NULL DEFAULT 0,
        hora_inicio TIME NOT NULL DEFAULT '09:00:00',
        hora_fin TIME NOT NULL DEFAULT '17:00:00',
        disponible TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_schedule_artist FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
        UNIQUE KEY uq_artist_schedule (artist_id, dia_semana)
    ) ENGINE=InnoDB");
    $auto_conn->close();
}

// === MIGRACIÓN LEVE: agrega artist_id a users si falta ===
$auto_conn = new mysqli($auto_host, $auto_user, $auto_pass, $auto_dbname);
if (!$auto_conn->connect_error) {
    $r = $auto_conn->query("SHOW COLUMNS FROM users LIKE 'artist_id'");
    if (!$r || $r->num_rows === 0) {
        $auto_conn->query("ALTER TABLE users ADD COLUMN artist_id INT UNSIGNED DEFAULT NULL AFTER rol");
        $auto_conn->query("ALTER TABLE users ADD INDEX idx_users_artist (artist_id)");
    }
    $auto_conn->close();
}
// === FIN DE LA MIGRACIÓN LEVE ===

// Punto de entrada de la aplicación MVC.
require_once __DIR__ . '/config/config.php';
require_once DIR_PATH . 'core/ControladorBase.php';
require_once DIR_PATH . 'core/Enrutador.php';
require_once DIR_PATH . 'app/controllers/ControladorPaginas.php';
require_once DIR_PATH . 'app/controllers/ControladorAutenticacion.php';
require_once DIR_PATH . 'app/controllers/ControladorTatuador.php';
require_once DIR_PATH . 'app/controllers/ControladorDashboard.php';

// Registra el error sin exponer detalles técnicos y muestra una respuesta 500 segura.
set_exception_handler(static function (Throwable $exception): void {
    error_log((string) $exception);
    (new ControladorPaginas())->serverError();
});

// Despacha la petición: lee ?action= de la URL y ejecuta el método correspondiente del controlador
Enrutador::dispatch();
