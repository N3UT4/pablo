<?php

// Controla las páginas públicas y las respuestas de error de la aplicación.
class PageController extends Controller
{
    public function home(): void
    {
        $this->view('home', [
            'title' => 'ITZA TATTOO STUDIO',
            'currentPage' => 'home',
        ]);
    }

    public function login(): void
    {
        $this->view('login', [
            'title' => 'Ingresar',
            'currentPage' => 'login',
        ]);
    }

    public function register(): void
    {
        $this->view('register', [
            'title' => 'Crear cuenta',
            'currentPage' => 'register',
        ]);
    }

    public function dashboard(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            $this->redirect('index.php?action=login');
        }

        $this->view('dashboard', [
            'title' => 'Mi dashboard',
            'currentPage' => 'dashboard',
            'user' => $user,
        ]);
    }

    public function contact(): void
    {
        $this->view('contact', [
            'title' => 'Contacto',
            'currentPage' => 'contact',
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('404', [
            'title' => 'Página no encontrada',
        ]);
    }

    public function serverError(): void
    {
        http_response_code(500);
        $this->view('500', [
            'title' => 'Error del servidor',
        ]);
    }
}
