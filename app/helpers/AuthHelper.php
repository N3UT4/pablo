<?php
// =====================================================================
// FILE: app/helpers/AuthHelper.php
// =====================================================================
// DESCRIPCIÓN: Helper de autenticación y control de acceso. Centraliza la lógica de verificación de permisos (roles, artista asociado, modo tatuador) que antes estaba duplicada en los controladores. Ofrece tres tipos de verificación: acceso a páginas (redirección), acceso a APIs (respuesta JSON) y guardia de roles simple.
// UBICACIÓN MVC: Helper (capa de servicio de autorización)
// ¿POR QUÉ EXISTE? Evita la duplicación de chequeos de rol/sesión en cada controlador. Proporciona un punto único de verdad para la lógica de autorización.
// CÓMO SE USA: Instanciado por ControladorDashboard y ControladorTatuador en sus constructores, recibiendo un ModeloUsuarios como dependencia. Los métodos se llaman al inicio de cada acción para bloquear el acceso no autorizado.
// MÉTODOS CLAVE:
//   - getArtistId(): obtiene el ID del artista asociado al usuario de sesión. Si no está en sesión, lo busca en BD vía ModeloUsuarios::findArtistByUser().
//   - verifyPageAccess($redirectAction): verifica acceso a páginas del panel tatuador. Retorna artistId o redirige (302) si no autorizado. Requiere rol 'tatuador' o 'admin' con modo tatuador activado.
//   - verifyApiAccess($redirectAction): verifica acceso a APIs REST. Retorna artistId o envía JSON 401/403 si no autorizado.
//   - guard($requiredRol, $redirect): verifica que el usuario esté logueado y tenga alguno de los roles requeridos. Redirige si no. Devuelve true si pass.
// LÓGICA DE AUTORIZACIÓN:
//   - Rol 'tatuador': accede si tiene un artist_id asociado.
//   - Rol 'admin': accede solo si $_SESSION['artist_mode'] está activado y tiene artist_id (modo "ver como tatuador").
//   - Otros roles: acceso denegado.
// SEGURIDAD: Usa hash_equals() indirectamente (via verify_csrf_token en config). Las respuestas JSON usan http_response_code() apropiado (401/403).
// RECURSOS: ModeloUsuarios (inyectado en constructor).
// =====================================================================

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
    // Primero verifica en sesión ($_SESSION['user']['artist_id'] o $_SESSION['artist_id']).
    // Si no está, lo busca en BD vía ModeloUsuarios::findArtistByUser().
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
    // Retorna el artistId o redirige (302) al login si no tiene acceso.
    // Rol 'tatuador': accede si tiene artist_id asociado.
    // Rol 'admin': accede solo si artist_mode está activado y tiene artist_id.
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
    // Retorna el artistId o envía respuesta JSON 401/403 si no tiene acceso.
    // Mismo criterio de roles que verifyPageAccess, pero responde JSON en lugar de redirect.
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
    // $requiredRol puede ser string o array de roles permitidos.
    // NOTA: el código después de exit; (return false) es dead code, pero se mantiene por seguridad.
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
