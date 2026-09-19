<?php
// Helper de autenticación y control de acceso.
// Extrae la lógica de verificación de acceso que estaba duplicada
// en ControladorTatuador y ControladorDashboard.
class AuthHelper
{
    private ModeloUsuarios $userModel;

    public function __construct(ModeloUsuarios $userModel)
    {
        $this->userModel = $userModel;
    }

    // Obtiene el ID del artista asociado a la sesión actual.
    public function getArtistId(): int
    {
        $artistId = (int) ($_SESSION['user']['artist_id'] ?? $_SESSION['artist_id'] ?? 0);
        if ($artistId <= 0) {
            $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));
            $artistId = (int) ($artist['id'] ?? 0);
        }
        return $artistId;
    }

    // Verifica acceso a páginas del panel de tatuador.
    // Retorna el artistId o redirige si no tiene acceso.
    public function verifyPageAccess(string $redirectAction = 'login'): ?int
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            header('Location: ' . BASE_URL . 'index.php?action=' . $redirectAction);
            exit;
        }

        $rol = $user['rol'] ?? 'cliente';
        $artistId = $this->getArtistId();

        if ($rol === 'tatuador') {
            if ($artistId <= 0) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'No estás asociado a un tatuador.'];
                header('Location: ' . BASE_URL . 'index.php?action=' . $redirectAction);
                exit;
            }
            return $artistId;
        }

        if ($rol === 'admin') {
            if (!empty($_SESSION['artist_mode']) && $artistId > 0) {
                return $artistId;
            }
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Presiona "Ver Modo Tatuador" desde el dashboard para acceder.'];
            header('Location: ' . BASE_URL . 'index.php?action=' . $redirectAction);
            exit;
        }

        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tu rol no tiene acceso a esta sección.'];
        header('Location: ' . BASE_URL . 'index.php?action=' . $redirectAction);
        exit;
    }

    // Verifica acceso a la API REST del tatuador.
    // Retorna el artistId o envía respuesta JSON 401/403.
    public function verifyApiAccess(string $redirectAction = 'login'): ?int
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'message' => 'Debes iniciar sesión primero.', ['redirect' => BASE_URL . 'index.php?action=' . $redirectAction]]);
            exit;
        }

        $rol = $user['rol'] ?? 'cliente';
        $artistId = $this->getArtistId();

        if ($rol === 'tatuador') {
            if ($artistId <= 0) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'message' => 'No estás asociado a un tatuador.']);
                exit;
            }
            return $artistId;
        }

        if ($rol === 'admin') {
            if (!empty($_SESSION['artist_mode']) && $artistId > 0) {
                return $artistId;
            }
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'message' => 'Activa el modo tatuador desde el dashboard.', ['redirect' => BASE_URL . 'index.php?action=artist-switch-mode']]);
            exit;
        }

        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Tu rol no tiene acceso a esta sección.']);
        exit;
    }

    // Verifica que el usuario tenga sesión y el rol requerido.
    // Si no cumple, redirige al login o muestra error de acceso.
    public function guard(array|string $requiredRol, string $redirect): bool
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            header('Location: ' . BASE_URL . 'index.php?action=' . $redirect);
            exit;
            return false;
        }

        $rol = $user['rol'] ?? '';
        $allowed = is_array($requiredRol) ? $requiredRol : [$requiredRol];
        if (!in_array($rol, $allowed, true)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tu rol no tiene acceso a esta sección.'];
            header('Location: ' . BASE_URL . 'index.php?action=' . $redirect);
            exit;
            return false;
        }
        return true;
    }
}
