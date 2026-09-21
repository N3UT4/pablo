<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function success(
        string $message = 'Operación exitosa.',
        array|null $data = null,
        int $statusCode = 200
    ): void {
        $this->respond(true, $message, $data, $statusCode);
    }

    protected function created(
        string $message = 'Recurso creado correctamente.',
        array|null $data = null
    ): void {
        $this->respond(true, $message, $data, 201);
    }

    protected function error(
        string $message = 'Error en la solicitud.',
        array|null $data = null,
        int $statusCode = 400
    ): void {
        $this->respond(false, $message, $data, $statusCode);
    }

    protected function validationError(
        string $message = 'Datos de entrada inválidos.',
        array|null $errors = null,
        int $statusCode = 422
    ): void {
        $data = $errors !== null ? ['errors' => $errors] : null;
        $this->respond(false, $message, $data, $statusCode);
    }

    protected function unauthorized(
        string $message = 'No autorizado. Inicia sesión para continuar.',
        array|null $data = null
    ): void {
        $this->respond(false, $message, $data, 401);
    }

    protected function forbidden(
        string $message = 'Acceso denegado. No tienes permisos para realizar esta acción.',
        array|null $data = null
    ): void {
        $this->respond(false, $message, $data, 403);
    }

    protected function notFound(
        string $message = 'Recurso no encontrado.',
        array|null $data = null
    ): void {
        $this->respond(false, $message, $data, 404);
    }

    protected function methodNotAllowed(
        string $message = 'Método HTTP no permitido.',
        array|null $data = null
    ): void {
        $this->respond(false, $message, $data, 405);
    }

    protected function serverError(
        string $message = 'Error interno del servidor.',
        array|null $data = null
    ): void {
        $this->respond(false, $message, $data, 500);
    }

    protected function csrfExpired(
        string $message = 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.'
    ): void {
        $this->respond(false, $message, ['redirect' => BASE_URL . 'index.php?action=login'], 419);
    }

    private function respond(bool $status, string $message, array|null $data, int $statusCode): void
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