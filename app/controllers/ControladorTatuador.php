<?php
// =====================================================================
// FILE: app/controllers/ControladorTatuador.php
// =====================================================================
// DESCRIPCIÓN: Controlador del módulo de tatuador. Gestiona el panel del tatuador (dashboard), la agenda de citas, los horarios de trabajo, el perfil del artista, y las APIs REST para actualizar el estado de citas, guardar horarios y obtener citas en formato JSON.
// UBICACIÓN MVC: Controller
// ¿POR QUÉ EXISTE? Proporciona una interfaz dedicada al tatuador para gestionar su agenda, horarios y perfil. Requiere que el usuario tenga rol 'tatuador' (o 'admin' con modo tatuador activado).
// CÓMO SE USA: El Enrutador despacha las acciones artist-panel, artist-agenda, artist-horarios, artist-perfil, artist-update-estado, artist-save-schedule, artist-citas-json y artist-switch-mode a los métodos de este controlador.
// DEPENDENCIAS:
//   - Models: ModeloCitas, ModeloHorariosTatuador, ModeloUsuarios
//   - Helper: AuthHelper (verificación de acceso de página y API)
// MÉTODOS CLAVE:
//   - dashboard(): carga el artista asociado y muestra el panel principal.
//   - agenda(): lista las citas asignadas al tatuador (ModeloCitas::findByArtist).
//   - horarios(): muestra los horarios de trabajo (ModeloHorariosTatuador::getByArtist).
//   - perfil(): muestra el perfil del artista y del usuario.
//   - switchMode(): activa/desactiva el "modo tatuador" para administradores.
//   - updateEstado(): API REST — actualiza el estado de una cita (valida CSRF, método POST, pertenencia al artista).
//   - saveSchedule(): API REST — guarda/actualiza un horario semanal (valida CSRF, día 0-6).
//   - citasJson(): API REST — retorna las citas del tatuador en JSON.
// AUTORIZACIÓN: verifyPageAccess() para vistas (redirige si no autorizado), verifyApiAccess() para APIs (retorna JSON 401/403 si no autorizado).
// SEGURIDAD: verify_csrf_token() en updateEstado y saveSchedule. Verificación de que la cita pertenece al artista antes de modificar.
// =====================================================================

// Controlador del módulo de tatuador. Gestiona el panel del tatuador,
// agenda, horarios, perfil, y la API REST para el modo tatuador.
// Usa AuthHelper para la verificación de acceso.
require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloHorariosTatuador.php';
require_once DIR_PATH . 'app/models/ModeloUsuarios.php';
require_once DIR_PATH . 'app/helpers/AuthHelper.php';

class ControladorTatuador extends ControladorBase
{
    private ModeloCitas $citaModel;
    private ModeloHorariosTatuador $horarioModel;
    private ModeloUsuarios $userModel;
    private AuthHelper $authHelper;

    public function __construct()
    {
        $this->citaModel = new ModeloCitas();
        $this->horarioModel = new ModeloHorariosTatuador();
        $this->userModel = new ModeloUsuarios();
        $this->authHelper = new AuthHelper($this->userModel);
    }

    // Dashboard principal del tatuador.
    public function dashboard(): void
    {
        $artistId = $this->authHelper->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $artist = null;
        try {
            $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));
        } catch (Throwable $e) {
            error_log('[Tatuador] Error cargando artista: ' . $e->getMessage());
        }

        $this->view('tatuador-dashboard', [
            'title' => 'Panel de Tatuador — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-panel',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'artist' => $artist,
            'rol' => $_SESSION['user']['rol'] ?? 'cliente',
        ]);
    }

    // Agenda de citas del tatuador.
    public function agenda(): void
    {
        $artistId = $this->authHelper->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $citas = [];
        try {
            $citas = $this->citaModel->findByArtist($artistId);
        } catch (Throwable $e) {
            error_log('[Tatuador] Error cargando agenda: ' . $e->getMessage());
        }

        $this->view('tatuador-agenda', [
            'title' => 'Mi Agenda — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-agenda',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'citas' => $citas,
        ]);
    }

    // Horarios de trabajo del tatuador.
    public function horarios(): void
    {
        $artistId = $this->authHelper->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $horarios = [];
        try {
            $horarios = $this->horarioModel->getByArtist($artistId);
        } catch (Throwable $e) {
            error_log('[Tatuador] Error cargando horarios: ' . $e->getMessage());
        }

        $this->view('tatuador-horarios', [
            'title' => 'Mis Horarios — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-horarios',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'horarios' => $horarios,
        ]);
    }

    // Perfil del tatuador.
    public function perfil(): void
    {
        $artistId = $this->authHelper->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $artist = null;
        $user = null;
        try {
            $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));
            $user = $this->userModel->find((int) ($_SESSION['user']['id'] ?? 0));
        } catch (Throwable $e) {
            error_log('[Tatuador] Error cargando perfil: ' . $e->getMessage());
        }

        $this->view('tatuador-perfil', [
            'title' => 'Mi Perfil — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-perfil',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'artist' => $artist,
            'profile' => $user,
        ]);
    }

    // Activa o desactiva el modo tatuador (para admins).
    public function switchMode(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || ($user['rol'] !== 'admin' && $user['rol'] !== 'tatuador')) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Acceso denegado.'];
            $this->redirect(BASE_URL . 'index.php?action=home');
            return;
        }

        $exit = (string) ($_GET['exit'] ?? '');
        if ($exit === '1') {
            unset($_SESSION['artist_mode']);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Has salido del modo tatuador.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
            return;
        }

        $artistId = $this->authHelper->getArtistId();
        if ($artistId <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No hay un tatuador asociado a tu cuenta.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
            return;
        }

        $_SESSION['artist_mode'] = true;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Has entrado en modo tatuador.'];
        $this->redirect(BASE_URL . 'index.php?action=artist-panel');
    }

    // API: Actualiza el estado de una cita (pendiente, confirmada, completada, cancelada).
    public function updateEstado(): void
    {
        $artistId = $this->authHelper->verifyApiAccess();
        if ($artistId === null) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(false, 'Método no permitido.', [], 405);
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $this->json(false, 'La sesión expiró. Recarga la página.', [], 419);
        }

        $citaId = (int) ($_POST['cita_id'] ?? 0);
        $estado = strtolower(trim((string) ($_POST['estado'] ?? '')));
        $allowed = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        if ($citaId <= 0 || !in_array($estado, $allowed, true)) {
            $this->json(false, 'Datos inválidos.', [], 422);
        }

        // Verifica que la cita exista y pertenezca al artista autenticado (protege contra acceso a otras citas)
        $cita = $this->citaModel->find($citaId);
        if (!$cita || (int) $cita['artist_id'] !== $artistId) {
            $this->json(false, 'Cita no encontrada o no asignada a ti.', [], 404);
        }

        $this->citaModel->updateEstado($citaId, $estado);
        $this->json(true, 'Estado actualizado.', ['cita_id' => $citaId, 'estado' => $estado]);
    }

    // API: Guarda/actualiza los horarios del tatuador para un día.
    public function saveSchedule(): void
    {
        $artistId = $this->authHelper->verifyApiAccess();
        if ($artistId === null) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(false, 'Método no permitido.', [], 405);
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $this->json(false, 'La sesión expiró. Recarga la página.', [], 419);
        }

        $dia = (int) ($_POST['dia'] ?? -1);
        $horaInicio = (string) ($_POST['hora_inicio'] ?? '09:00:00');
        $horaFin = (string) ($_POST['hora_fin'] ?? '17:00:00');
        $disponible = (int) ($_POST['disponible'] ?? 1) === 1;

        // Valida que el día esté en el rango 0-6 (0=domingo, 6=sábado)
        if ($dia < 0 || $dia > 6) {
            $this->json(false, 'Día inválido.', [], 422);
        }

        $this->horarioModel->save($artistId, $dia, $horaInicio, $horaFin, $disponible);
        $this->json(true, 'Horario actualizado.', ['dia' => $dia]);
    }

    // API: Retorna las citas del tatuador en formato JSON.
    public function citasJson(): void
    {
        $artistId = $this->authHelper->verifyApiAccess();
        if ($artistId === null) {
            return;
        }

        $citas = $this->citaModel->findByArtist($artistId);
        $this->json(true, 'ok', ['data' => $citas]);
    }
}
