<?php

// Controla las páginas públicas y las respuestas de error de la aplicación.
class ControladorPaginas extends ControladorBase
{
    public function home(): void
    {
        $this->view('inicio', [
            'title' => 'ITZA TATTOO STUDIO',
            'currentPage' => 'home',
        ]);
    }

    public function login(): void
    {
        $this->view('ingresar', [
            'title' => 'Ingresar',
            'currentPage' => 'login',
        ]);
    }

    public function register(): void
    {
        $this->view('registro', [
            'title' => 'Crear cuenta',
            'currentPage' => 'register',
        ]);
    }

    public function dashboard(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            $this->redirect(BASE_URL . 'index.php?action=login');
        }

        if (($user['rol'] ?? '') === 'tatuador') {
            $this->redirect(BASE_URL . 'index.php?action=artist-panel');
        }

        $this->view('panel', [
            'title' => 'Mi dashboard',
            'currentPage' => 'dashboard',
            'user' => $user,
        ]);
    }

    public function contact(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once DIR_PATH . 'app/controllers/ControladorContacto.php';
            (new ControladorContacto())->store();
            return;
        }

        $this->view('contacto', [
            'title' => 'Contacto',
            'currentPage' => 'contact',
        ]);
    }

    public function gallery(): void
    {
        $this->view('galeria', [
            'title' => 'Galería — ITZA TATTOO STUDIO',
            'currentPage' => 'gallery',
        ]);
    }

    public function booking(): void
    {
        $this->view('abono', [
            'title' => 'Agendar y abonar — ITZA TATTOO STUDIO',
            'currentPage' => 'booking',
        ]);
    }

    public function consent(): void
    {
        $this->view('consentimiento', [
            'title' => 'Consentimiento informado — ITZA TATTOO STUDIO',
            'currentPage' => 'consent',
        ]);
    }

    public function profile(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            $this->redirect(BASE_URL . 'index.php?action=login');
        }

        $this->view('perfil', [
            'title' => 'Mi perfil — ITZA TATTOO STUDIO',
            'currentPage' => 'profile',
            'user' => $user,
        ]);
    }

    public function promotions(): void
    {
        $this->view('promociones', [
            'title' => 'Promociones — ITZA TATTOO STUDIO',
            'currentPage' => 'promotions',
        ]);
    }

    public function changePasswordForm(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            $this->redirect(BASE_URL . 'index.php?action=login');
        }

        $this->view('cambiar_clave', [
            'title' => 'Cambiar clave — ITZA TATTOO STUDIO',
            'currentPage' => 'cambiar_clave',
            'user' => $user,
        ]);
    }

    // Endpoint liviano para que los formularios estáticos (galeria.html, abono.html, etc.)
    // obtengan un csrf_token válido antes de enviar su POST por fetch().
    public function csrfToken(): void
    {
        $this->json(true, 'ok', ['csrf_token' => csrf_token()]);
    }

    public function notFound(): void
    {
        $this->view('error404', [
            'title' => 'Página no encontrada',
        ]);
    }

    public function serverError(): void
    {
        $this->view('error500', [
            'title' => 'Error del servidor',
        ]);
    }
}