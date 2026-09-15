<?php

abstract class ControladorBase
{
    // Renderiza una vista dentro del encabezado y pie compartidos.
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = APP_ROOT . '/app/views/pages/' . $template . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException('Vista no encontrada: ' . $template);
        }

        require APP_ROOT . '/app/views/layout/encabezado.php';
        require $viewPath;
        require APP_ROOT . '/app/views/layout/pie.php';
    }

    // Centraliza las redirecciones para terminar la petición inmediatamente.
    protected function redirect(string $route): void
    {
        header('Location: ' . $route);
        exit;
    }

    // Respuesta JSON estándar para los endpoints AJAX (formularios estáticos en /js).
    protected function json(bool $ok, string $message, array $extra = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
        exit;
    }
}