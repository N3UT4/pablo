<?php
// Controlador de páginas públicas y respuestas de error.
// Gestiona todas las páginas que no requieren autenticación o lógica de negocio compleja.
class ControladorPaginas extends ControladorBase
{
    // Verifica si el usuario tiene sesión activa.
    private function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    // Redirige a login con mensaje flash si no hay sesión.
    private function requireAuth(string $redirectAction = 'login'): void
    {
        if (!$this->isAuthenticated()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Inicia sesión para continuar.'
            ];
            $this->redirect(BASE_URL . 'index.php?action=' . $redirectAction);
        }
    }

    // Muestra la página de inicio (landing page).
    public function home(): void
    {
        $isAuthenticated = $this->isAuthenticated();
        $this->view('inicio', [
            'title' => 'ITZA TATTOO STUDIO',
            'currentPage' => 'home',
            'isAuthenticated' => $isAuthenticated,
        ]);
    }

    // Muestra la página de inicio de sesión.
    public function login(): void
    {
        $this->view('ingresar', [
            'title' => 'Ingresar',
            'currentPage' => 'login',
        ]);
    }

    // Muestra la página de registro de nueva cuenta.
    public function register(): void
    {
        $this->view('registro', [
            'title' => 'Crear cuenta',
            'currentPage' => 'register',
        ]);
    }

    // Muestra el dashboard del usuario (requiere sesión).
    // Redirige a tatuador o admin según corresponda.
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

    // Muestra la página de contacto. Si es POST, procesa el formulario.
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

    // Muestra la página de galería de tatuajes (requiere autenticación para galería completa).
    public function galeria(): void
    {
        $this->requireAuth('login');
        $this->view('galeria', [
            'title' => 'Galería — ITZA TATTOO STUDIO',
            'currentPage' => 'galeria',
        ]);
    }

    // Muestra la página de agendar cita y abonar (requiere autenticación).
    public function booking(): void
    {
        $this->requireAuth('login');
        $this->view('abono', [
            'title' => 'Agendar y abonar — ITZA TATTOO STUDIO',
            'currentPage' => 'booking',
        ]);
    }

    // Muestra la página de consentimiento informado (requiere autenticación).
    public function consent(): void
    {
        $this->requireAuth('login');
        $this->view('consentimiento', [
            'title' => 'Consentimiento informado — ITZA TATTOO STUDIO',
            'currentPage' => 'consent',
        ]);
    }

    // Muestra el perfil del usuario (requiere sesión).
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

    // Muestra la página de promociones.
    public function promotions(): void
    {
        $this->view('promociones', [
            'title' => 'Promociones — ITZA TATTOO STUDIO',
            'currentPage' => 'promotions',
        ]);
    }

    // Muestra el formulario para cambiar contraseña.
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

    // Muestra la página de error 404.
    public function notFound(): void
    {
        $this->view('error404', [
            'title' => 'Página no encontrada',
        ]);
    }

    // Muestra la página de error 500 (error del servidor).
    public function serverError(): void
    {
        $this->view('error500', [
            'title' => 'Error del servidor',
        ]);
    }
}
