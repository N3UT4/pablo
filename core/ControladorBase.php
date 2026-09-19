<?php
// Clase base abstracta para todos los controladores.
// Proporciona métodos utilitarios compartidos: renderizado de vistas,
// redirecciones y respuestas JSON para endpoints AJAX.
abstract class ControladorBase
{
    // Renderiza una vista dentro del encabezado y pie compartidos.
    // Extrae el array $data para que las variables estén disponibles en la vista.
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = DIR_PATH . 'app/views/pages/' . $template . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException('Vista no encontrada: ' . $template);
        }

        require DIR_PATH . 'app/views/layout/encabezado.php';
        require $viewPath;
        require DIR_PATH . 'app/views/layout/pie.php';
    }

    // Centraliza las redirecciones para terminar la petición inmediatamente.
    // Soporta URLs absolutas y relativas.
    protected function redirect(string $route): void
    {
        $isAbsolute = preg_match('/^https?:\/\//i', $route) === 1 || strpos($route, '//') === 0;
        $url = $isAbsolute || strpos($route, '/') === 0
            ? $route
            : BASE_URL . ltrim($route, '/');
        header('Location: ' . $url);
        exit;
    }

    // Respuesta JSON estándar para los endpoints AJAX (formularios estáticos en /js).
    // Siempre usa código 200 para evitar que Apache dispare ErrorDocument y
    // reemplace el cuerpo JSON con una página HTML de error.
    protected function json(bool $ok, string $message, array $extra = [], int $statusCode = 200): void
    {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(
            ['ok' => $ok, 'message' => $message, 'errorCode' => $statusCode],
            $extra
        ));
        exit;
    }
}
