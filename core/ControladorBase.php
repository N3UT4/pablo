<?php
// =====================================================================
// FILE: core/ControladorBase.php
// =====================================================================
// DESCRIPCIÓN: Clase abstracta base de la que heredan todos los controladores del proyecto. Proporciona tres métodos utilitarios esenciales: view() para renderizar vistas con layout compartido, redirect() para redirecciones HTTP con terminación inmediata, y json() para respuestas JSON estándar en endpoints AJAX.
// UBICACIÓN MVC: Controller (base)
// ¿POR QUÉ EXISTE? Evita la duplicación de código en cada controlador. Todas las operaciones comunes de presentación, redirección y respuestas API se centralizan aquí.
// CÓMO SE USA: Los controladores (ControladorAutenticacion, ControladorDashboard, ControladorTatuador, etc.) extienden esta clase y usan $this->view(), $this->redirect() y $this->json() directamente.
// MÉTODOS CLAVE:
//   - view($template, $data): extrae $data con extract(), carga encabezado.php + la vista + pie.php. Lanza RuntimeException si la vista no existe.
//   - redirect($route): envía header Location. Detecta URLs absolutas (con protocolo) vs relativas. Usa BASE_URL para las relativas.
//   - json($ok, $message, $extra, $statusCode): devuelve JSON con estructura {ok, message, errorCode, ...extra}. Siempre usa código 200 para evitar que Apache dispare ErrorDocument.
// VARIABLES DE ENTORNO: DIR_PATH, BASE_URL (definidas en config/config.php)
// =====================================================================

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
