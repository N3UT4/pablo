<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(
        string $message = 'Operación exitosa.',
        array|null $data = null,
        int $statusCode = 200
    ): void {
        self::respond(true, $message, $data, $statusCode);
    }

    public static function created(
        string $message = 'Recurso creado correctamente.',
        array|null $data = null
    ): void {
        self::respond(true, $message, $data, 201);
    }

    public static function error(
        string $message = 'Error en la solicitud.',
        array|null $data = null,
        int $statusCode = 400
    ): void {
        self::respond(false, $message, $data, $statusCode);
    }

    public static function validationError(
        string $message = 'Datos de entrada inválidos.',
        array|null $errors = null,
        int $statusCode = 422
    ): void {
        $data = $errors !== null ? ['errors' => $errors] : null;
        self::respond(false, $message, $data, $statusCode);
    }

    public static function unauthorized(
        string $message = 'No autorizado. Inicia sesión para continuar.',
        array|null $data = null
    ): void {
        self::respond(false, $message, $data, 401);
    }

    public static function forbidden(
        string $message = 'Acceso denegado. No tienes permisos para realizar esta acción.',
        array|null $data = null
    ): void {
        self::respond(false, $message, $data, 403);
    }

    public static function notFound(
        string $message = 'Recurso no encontrado.',
        array|null $data = null
    ): void {
        self::respond(false, $message, $data, 404);
    }

    public static function methodNotAllowed(
        string $message = 'Método HTTP no permitido.',
        array|null $data = null
    ): void {
        self::respond(false, $message, $data, 405);
    }

    public static function serverError(
        string $message = 'Error interno del servidor.',
        array|null $data = null
    ): void {
        self::respond(false, $message, $data, 500);
    }

    public static function csrfExpired(
        string $message = 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.'
    ): void {
        self::respond(false, $message, ['redirect' => BASE_URL . 'index.php?action=login'], 419);
    }

    private static function respond(bool $status, string $message, array|null $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}