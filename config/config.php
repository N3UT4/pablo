<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $isHttps,
    ]);
    session_start();
}

define('APP_ROOT', dirname(__DIR__));
define('APP_URL', getenv('APP_URL') ?: '/');

define('APP_ENV', 'development');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'itza_tattoo');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

define('STUDIO_CITY', 'Bogotá, La Victoria - 20 de Julio');
define('STUDIO_ADDRESS', 'Calle 42 A Sur # 3C - 65 Este');
define('STUDIO_PHONE', '301 400 3006');
define('STUDIO_WHATSAPP', '573014003006');
define('STUDIO_INSTAGRAM', 'https://www.instagram.com/itza.tattoo');
define('STUDIO_FACEBOOK', 'https://www.facebook.com/share/1CfAtuh5Bx/');
define('STUDIO_TIKTOK', 'https://www.tiktok.com/@itza_tattoo');
define('STUDIO_MAPS', 'https://maps.app.goo.gl/drjEamhAYwjngxh57');
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
