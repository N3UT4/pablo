<?php

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, 'export ') === 0) {
            $line = trim(substr($line, 7));
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $name = trim($parts[0]);
        $value = trim($parts[1]);
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            continue;
        }
        if (strlen($value) >= 2 && $value[0] === $value[strlen($value) - 1] && in_array($value[0], ['"', "'"], true)) {
            $value = substr($value, 1, -1);
        } else {
            $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
        }
        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

function itza_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

$forwardedProtocol = isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
    ? strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO'])
    : '';
$requestProtocol = isset($_SERVER['REQUEST_SCHEME'])
    ? strtolower((string) $_SERVER['REQUEST_SCHEME'])
    : '';
$serverPort = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 0;
$isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || $serverPort === 443
    || strpos($forwardedProtocol, 'https') === 0
    || $requestProtocol === 'https';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $isHttps,
    ]);
    session_start();
}

$host = trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost'));
if ($host === '') {
    $host = 'localhost';
}
$hostHasPort = preg_match('/:\d+$/', $host) === 1;
if (!$hostHasPort && $serverPort > 0 && !in_array($serverPort, [80, 443], true)) {
    $host .= ':' . $serverPort;
}

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDirectory = trim(dirname($scriptName), '/\\');
$projectFolder = '';
if ($scriptDirectory !== '' && $scriptDirectory !== '.') {
    $scriptSegments = array_values(array_filter(
        explode('/', $scriptDirectory),
        static function (string $segment): bool {
            return $segment !== '';
        }
    ));
    $projectFolder = $scriptSegments[0] ?? '';
}
$baseUrlPath = $projectFolder === '' ? '' : '/' . $projectFolder;
$protocol = $isHttps ? 'https://' : 'http://';

if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $host . $baseUrlPath . '/');
}
if (!defined('DIR_PATH')) {
    define('DIR_PATH', rtrim(dirname(__DIR__), '/\\') . DIRECTORY_SEPARATOR);
}
if (!defined('APP_ROOT')) {
    define('APP_ROOT', rtrim(DIR_PATH, '/\\'));
}
if (!defined('APP_URL')) {
    $configuredAppUrl = itza_env('APP_URL');
    define('APP_URL', $configuredAppUrl !== null && preg_match('/^https?:\/\//i', $configuredAppUrl) === 1
        ? $configuredAppUrl
        : BASE_URL);
}
if (!defined('APP_ENV')) {
    define('APP_ENV', itza_env('APP_ENV', 'development'));
}

if (!defined('DB_HOST')) {
    define('DB_HOST', itza_env('DB_HOST', '127.0.0.1'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', itza_env('DB_PORT', '3306'));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', itza_env('DB_NAME', 'itza_tattoo'));
}
if (!defined('DB_USER')) {
    define('DB_USER', itza_env('DB_USER', 'root'));
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', itza_env('DB_PASSWORD', itza_env('DB_PASS', '')));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', DB_PASSWORD);
}

if (!defined('STUDIO_CITY')) {
    define('STUDIO_CITY', 'Bogotá, La Victoria - 20 de Julio');
}
if (!defined('STUDIO_ADDRESS')) {
    define('STUDIO_ADDRESS', 'Calle 42 A Sur # 3C - 65 Este');
}
if (!defined('STUDIO_PHONE')) {
    define('STUDIO_PHONE', '301 400 3006');
}
if (!defined('STUDIO_WHATSAPP')) {
    define('STUDIO_WHATSAPP', '573014003006');
}
if (!defined('STUDIO_INSTAGRAM')) {
    define('STUDIO_INSTAGRAM', 'https://www.instagram.com/itza.tattoo');
}
if (!defined('STUDIO_FACEBOOK')) {
    define('STUDIO_FACEBOOK', 'https://www.facebook.com/share/1CfAtuh5Bx/');
}
if (!defined('STUDIO_TIKTOK')) {
    define('STUDIO_TIKTOK', 'https://www.tiktok.com/@itza_tattoo');
}
if (!defined('STUDIO_MAPS')) {
    define('STUDIO_MAPS', 'https://maps.app.goo.gl/drjEamHAYwjngxh57');
}
if (!defined('STAFF_ACCESS_CODE')) {
    define('STAFF_ACCESS_CODE', itza_env('STAFF_ACCESS_CODE', 'ITZA-STAFF-2026'));
}

if (!defined('GALLERY_UPLOAD_PATH')) {
    define('GALLERY_UPLOAD_PATH', 'img/gallery');
}
if (!defined('GALLERY_UPLOAD_DIR')) {
    define('GALLERY_UPLOAD_DIR', DIR_PATH . 'img/gallery');
}
if (!defined('GALLERY_UPLOAD_URL')) {
    define('GALLERY_UPLOAD_URL', BASE_URL . ltrim(GALLERY_UPLOAD_PATH, '/'));
}

define('STUDIO_HOURS', [
    '9:00 a. m. - 11:00 a. m.',
    '1:00 p. m. - 4:00 p. m.',
    '5:00 p. m. - 9:00 p. m.',
]);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function estado_cita(string $estado): string
{
    $map = [
        'pendiente' => 'Pendiente por revisar',
        'confirmada' => 'Confirmada',
        'completada' => 'Completada',
        'cancelada' => 'Rechazada',
    ];
    return $map[$estado] ?? $estado;
}
