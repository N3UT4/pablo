<?php

require_once APP_ROOT . '/app/models/ModeloCitas.php';
require_once APP_ROOT . '/app/models/ModeloUsuarios.php';
require_once APP_ROOT . '/app/models/ModeloServicios.php';
require_once APP_ROOT . '/app/models/ModeloArtistas.php';

// API REST del proyecto (rúbrica: APIs REST, Formato JSON).
// Expone el recurso "citas" (appointments) con los métodos HTTP:
//   GET    index.php?action=api&recurso=citas          -> listar
//   GET    index.php?action=api&recurso=citas&id=1     -> ver una
//   POST   index.php?action=api&recurso=citas          -> crear (JSON body)
//   PUT    index.php?action=api&recurso=citas&id=1     -> actualizar (JSON body)
//   DELETE index.php?action=api&recurso=citas&id=1     -> eliminar
// Todas las respuestas son JSON: { ok, message, data }.
class ControladorApi extends ControladorBase
{
    private ModeloCitas $appointmentModel;
    private ModeloUsuarios $userModel;
    private ModeloServicios $serviceModel;
    private ModeloArtistas $artistModel;

    public function __construct()
    {
        $this->appointmentModel = new ModeloCitas();
        $this->userModel = new ModeloUsuarios();
        $this->serviceModel = new ModeloServicios();
        $this->artistModel = new ModeloArtistas();
    }

    // Punto de entrada único de la API.
    public function handle(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $recurso = strtolower(trim((string) ($_GET['recurso'] ?? '')));

        try {
            switch ($recurso) {
                case 'citas':
                    $this->citas($method);
                    break;
                case 'servicios':
                    $this->servicios($method);
                    break;
                case 'artistas':
                    $this->artistas($method);
                    break;
                default:
                    $this->json(false, 'Recurso no encontrado. Usa: citas, servicios o artistas.', [], 404);
            }
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'Error interno del servidor.', [], 500);
        }
    }

    // Lee el cuerpo JSON de la petición (para POST/PUT).
    private function body(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    // --- Recurso: citas (appointments) ---
    private function citas(string $method): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        switch ($method) {
            case 'GET':
                if ($id > 0) {
                    $cita = $this->appointmentModel->find($id);
                    if (!$cita) {
                        $this->json(false, 'Cita no encontrada.', [], 404);
                    }
                    $this->json(true, 'ok', ['data' => $cita]);
                }
                $this->json(true, 'ok', ['data' => $this->appointmentModel->all()]);
                break;

            case 'POST':
                $data = $this->body();
                $userId = (int) ($data['user_id'] ?? 0);
                $artistId = (int) ($data['artist_id'] ?? 0);
                $serviceId = (int) ($data['service_id'] ?? 0);
                $fecha = trim((string) ($data['fecha_cita'] ?? ''));
                $hora = trim((string) ($data['hora_cita'] ?? ''));

                if ($userId <= 0 || $artistId <= 0 || $serviceId <= 0 || $fecha === '' || $hora === '') {
                    $this->json(false, 'Faltan campos obligatorios: user_id, artist_id, service_id, fecha_cita, hora_cita.', [], 422);
                }

                $newId = $this->appointmentModel->create([
                    'user_id' => $userId,
                    'artist_id' => $artistId,
                    'service_id' => $serviceId,
                    'fecha_cita' => $fecha,
                    'hora_cita' => $hora,
                    'detalle_personalizado' => trim((string) ($data['detalle_personalizado'] ?? '')),
                    'observaciones' => trim((string) ($data['observaciones'] ?? '')),
                ]);
                $this->json(true, 'Cita creada.', ['data' => $this->appointmentModel->find($newId)], 201);
                break;

            case 'PUT':
                if ($id <= 0) {
                    $this->json(false, 'Debes indicar el id de la cita a actualizar.', [], 422);
                }
                if (!$this->appointmentModel->find($id)) {
                    $this->json(false, 'Cita no encontrada.', [], 404);
                }
                $this->appointmentModel->update($id, $this->body());
                $this->json(true, 'Cita actualizada.', ['data' => $this->appointmentModel->find($id)]);
                break;

            case 'DELETE':
                if ($id <= 0) {
                    $this->json(false, 'Debes indicar el id de la cita a eliminar.', [], 422);
                }
                if (!$this->appointmentModel->find($id)) {
                    $this->json(false, 'Cita no encontrada.', [], 404);
                }
                $this->appointmentModel->delete($id);
                $this->json(true, 'Cita eliminada.', ['id' => $id]);
                break;

            default:
                $this->json(false, 'Método no permitido.', [], 405);
        }
    }

    // --- Recurso: servicios (solo lectura) ---
    private function servicios(string $method): void
    {
        if ($method !== 'GET') {
            $this->json(false, 'Método no permitido para servicios.', [], 405);
        }
        $this->json(true, 'ok', ['data' => $this->serviceModel->listActive()]);
    }

    // --- Recurso: artistas (solo lectura) ---
    private function artistas(string $method): void
    {
        if ($method !== 'GET') {
            $this->json(false, 'Método no permitido para artistas.', [], 405);
        }
        $this->json(true, 'ok', ['data' => $this->artistModel->listActive()]);
    }
}
