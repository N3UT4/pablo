<?php

require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloHorariosTatuador.php';
require_once DIR_PATH . 'app/models/ModeloUsuarios.php';

// Controlador del módulo de tatuador (rúbrica: Roles / APIs REST).
// Restringe el acceso solo a usuarios con rol 'tatuador' o admin en modo artista.
class ControladorTatuador extends ControladorBase
{
    private ModeloCitas $citaModel;
    private ModeloHorariosTatuador $horarioModel;
    private ModeloUsuarios $userModel;

    public function __construct()
    {
        $this->citaModel = new ModeloCitas();
        $this->horarioModel = new ModeloHorariosTatuador();
        $this->userModel = new ModeloUsuarios();
    }

    private function getArtistId(): int
    {
        $artistId = (int) ($_SESSION['user']['artist_id'] ?? $_SESSION['artist_id'] ?? 0);
        if ($artistId <= 0) {
            $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));
            $artistId = (int) ($artist['id'] ?? 0);
        }
        return $artistId;
    }

    private function verifyPageAccess(): ?int
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
            $this->redirect(BASE_URL . 'index.php?action=login');
            return null;
        }

        $rol = $user['rol'] ?? 'cliente';
        $artistId = $this->getArtistId();

        if ($rol === 'tatuador') {
            if ($artistId <= 0) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'No estás asociado a un tatuador.'];
                $this->redirect(BASE_URL . 'index.php?action=dashboard');
                return null;
            }
            return $artistId;
        }

        if ($rol === 'admin') {
            if (!empty($_SESSION['artist_mode']) && $artistId > 0) {
                return $artistId;
            }
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Presiona "Ver Modo Tatuador" desde el dashboard para acceder.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
            return null;
        }

        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tu rol no tiene acceso a esta sección.'];
        $this->redirect(BASE_URL . 'index.php?action=dashboard');
        return null;
    }

    private function verifyApiAccess(): ?int
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->json(false, 'Debes iniciar sesión primero.', ['redirect' => BASE_URL . 'index.php?action=login'], 401);
            return null;
        }

        $rol = $user['rol'] ?? 'cliente';
        $artistId = $this->getArtistId();

        if ($rol === 'tatuador') {
            if ($artistId <= 0) {
                $this->json(false, 'No estás asociado a un tatuador.', [], 403);
                return null;
            }
            return $artistId;
        }

        if ($rol === 'admin') {
            if (!empty($_SESSION['artist_mode']) && $artistId > 0) {
                return $artistId;
            }
            $this->json(false, 'Activa el modo tatuador desde el dashboard.', ['redirect' => BASE_URL . 'index.php?action=artist-switch-mode'], 403);
            return null;
        }

        $this->json(false, 'Tu rol no tiene acceso a esta sección.', [], 403);
        return null;
    }

    public function dashboard(): void
    {
        $artistId = $this->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));

        $this->view('tatuador-dashboard', [
            'title' => 'Panel de Tatuador — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-panel',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'artist' => $artist,
            'rol' => $_SESSION['user']['rol'] ?? 'cliente',
        ]);
    }

    public function agenda(): void
    {
        $artistId = $this->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $citas = $this->citaModel->findByArtist($artistId);

        $this->view('tatuador-agenda', [
            'title' => 'Mi Agenda — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-agenda',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'citas' => $citas,
        ]);
    }

    public function horarios(): void
    {
        $artistId = $this->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $horarios = $this->horarioModel->getByArtist($artistId);

        $this->view('tatuador-horarios', [
            'title' => 'Mis Horarios — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-horarios',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'horarios' => $horarios,
        ]);
    }

    public function perfil(): void
    {
        $artistId = $this->verifyPageAccess();
        if ($artistId === null) {
            return;
        }

        $artist = $this->userModel->findArtistByUser((int) ($_SESSION['user']['id'] ?? 0));
        $user = $this->userModel->find((int) ($_SESSION['user']['id'] ?? 0));

        $this->view('tatuador-perfil', [
            'title' => 'Mi Perfil — ITZA TATTOO STUDIO',
            'currentPage' => 'artist-perfil',
            'user' => $_SESSION['user'],
            'artistId' => $artistId,
            'artist' => $artist,
            'profile' => $user,
        ]);
    }

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

        $artistId = $this->getArtistId();
        if ($artistId <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No hay un tatuador asociado a tu cuenta.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
            return;
        }

        $_SESSION['artist_mode'] = true;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Has entrado en modo tatuador.'];
        $this->redirect(BASE_URL . 'index.php?action=artist-panel');
    }

    public function updateEstado(): void
    {
        $artistId = $this->verifyApiAccess();
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

        $cita = $this->citaModel->find($citaId);
        if (!$cita || (int) $cita['artist_id'] !== $artistId) {
            $this->json(false, 'Cita no encontrada o no asignada a ti.', [], 404);
        }

        $this->citaModel->updateEstado($citaId, $estado);
        $this->json(true, 'Estado actualizado.', ['cita_id' => $citaId, 'estado' => $estado]);
    }

    public function saveSchedule(): void
    {
        $artistId = $this->verifyApiAccess();
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

        if ($dia < 0 || $dia > 6) {
            $this->json(false, 'Día inválido.', [], 422);
        }

        $this->horarioModel->save($artistId, $dia, $horaInicio, $horaFin, $disponible);
        $this->json(true, 'Horario actualizado.', ['dia' => $dia]);
    }

    public function citasJson(): void
    {
        $artistId = $this->verifyApiAccess();
        if ($artistId === null) {
            return;
        }

        $citas = $this->citaModel->findByArtist($artistId);
        $this->json(true, 'ok', ['data' => $citas]);
    }
}
