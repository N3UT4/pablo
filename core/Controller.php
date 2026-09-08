<?php

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = APP_ROOT . '/app/views/pages/' . $template . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException('Vista no encontrada: ' . $template);
        }

        require APP_ROOT . '/app/views/layout/header.php';
        require $viewPath;
        require APP_ROOT . '/app/views/layout/footer.php';
    }

    protected function redirect(string $route): void
    {
        header('Location: ' . $route);
        exit;
    }
}
