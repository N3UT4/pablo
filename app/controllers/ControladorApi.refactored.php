<?php

require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloUsuarios.php';
require_once DIR_PATH . 'app/models/ModeloServicios.php';
require_once DIR_PATH . 'app/models/ModeloArtistas.php';
require_once DIR_PATH . 'app/Requests/StorePagoRequest.php';
require_once DIR_PATH . 'app/Traits/ApiResponseTrait.php';

class ControladorApi extends ControladorBase
{
    use ApiResponseTrait;

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

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            if (empty($_SESSION['user']['id'])) {
                $this->unauthorized('Autenticación requerida.');
                return;
            }
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
                    $this->notFound('Recurso no encontrado. Usa: citas, servicios o artistas.');
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->serverError('Error interno del servidor.');
        }
    }

    private function body(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    private function citas(string $method): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        switch ($method) {
            case 'GET':
                if ($id > 0) {
                    $cita = $this->appointmentModel->find($id);
                    if (!$cita) {
                        $this->notFound('Cita no encontrada.');
                        return;
                    }
                    $this->success('ok', ['data' => $cita]);
                }
                $this->success('ok', ['data' => $this->appointmentModel->all()]);
                break;

            case 'POST':
                $request = StorePagoRequest::fromGlobals();
                if (!$request->validate()) {
                    $this->validationError('Faltan campos obligatorios o son inválidos.', $request->getErrors());
                    return;
                }

                $data = $request->all();

                $newId = $this->appointmentModel->create([
                    'usuario_id' => $data['cita_id'],
                    'artista_id' => $data['artista_id'] ?? 0,
                    'servicio_id' => $data['servicio_id'] ?? 0,
                    'fecha_cita' => $data['fecha_cita'] ?? '',
                    'hora_cita' => $data['hora_cita'] ?? '',
                    'detalle_personalizado' => $data['detalle_personalizado'] ?? '',
                    'observaciones' => $data['observaciones'] ?? '',
                ]);
                $this->created('Cita creada.', ['data' => $this->appointmentModel->find($newId)]);
                break;

            case 'PUT':
                if ($id <= 0) {
                    $this->error('Debes indicar el id de la cita a actualizar.', [], 422);
                    return;
                }
                if (!$this->appointmentModel->find($id)) {
                    $this->notFound('Cita no encontrada.');
                    return;
                }
                $this->appointmentModel->update($id, $this->body());
                $this->success('Cita actualizada.', ['data' => $this->appointmentModel->find($id)]);
                break;

            case 'DELETE':
                if ($id <= 0) {
                    $this->error('Debes indicar el id de la cita a eliminar.', [], 422);
                    return;
                }
                if (!$this->appointmentModel->find($id)) {
                    $this->notFound('Cita no encontrada.');
                    return;
                }
                $this->appointmentModel->delete($id);
                $this->success('Cita eliminada.', ['id' => $id]);
                break;

            default:
                $this->methodNotAllowed('Método no permitido.');
        }
    }

    private function servicios(string $method): void
    {
        if ($method !== 'GET') {
            $this->methodNotAllowed('Método no permitido para servicios.');
            return;
        }
        $this->success('ok', ['data' => $this->serviceModel->listActive()]);
    }

    private function artistas(string $method): void
    {
        if ($method !== 'GET') {
            $this->methodNotAllowed('Método no permitido para artistas.');
            return;
        }
        $this->success('ok', ['data' => $this->artistModel->listActive()]);
    }
}