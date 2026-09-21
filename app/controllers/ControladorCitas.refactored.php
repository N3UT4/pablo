<?php

require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloPagos.php';
require_once DIR_PATH . 'app/models/ModeloArtistas.php';
require_once DIR_PATH . 'app/models/ModeloServicios.php';
require_once DIR_PATH . 'app/helpers/AbonoHelper.php';
require_once DIR_PATH . 'app/Requests/StoreCitaRequest.php';
require_once DIR_PATH . 'app/Traits/ApiResponseTrait.php';

class ControladorCitas extends ControladorBase
{
    use ApiResponseTrait;

    private ModeloCitas $appointmentModel;
    private ModeloPagos $paymentModel;
    private ModeloArtistas $artistModel;
    private ModeloServicios $serviceModel;

    public function __construct()
    {
        $this->appointmentModel = new ModeloCitas();
        $this->paymentModel = new ModeloPagos();
        $this->artistModel = new ModeloArtistas();
        $this->serviceModel = new ModeloServicios();
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->methodNotAllowed('Solicitud no válida. Solo se permite POST.');
            return;
        }

        $request = StoreCitaRequest::fromGlobals();

        if (!$request->validate()) {
            $this->validationError(
                'Revisa los campos marcados en rojo antes de continuar.',
                $request->getErrors()
            );
            return;
        }

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $this->csrfExpired();
            return;
        }

        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        if ($userId <= 0) {
            $this->unauthorized('Debes iniciar sesión para agendar una cita.', ['redirect' => BASE_URL . 'index.php?action=login']);
            return;
        }

        $data = $request->all();

        if (!$this->artistModel->exists($data['id_tatuador'])) {
            $this->error('El tatuador seleccionado no existe.', [], 422);
            return;
        }

        $servicio = $this->serviceModel->findBySlug($data['tipo_servicio']);
        if (!$servicio) {
            $this->error('El estilo "' . $data['tipo_servicio'] . '" no existe en la tabla "servicios".', [], 422);
            return;
        }

        $servicioDetalle = $this->serviceModel->findById((int) $servicio['id']);
        if (!$servicioDetalle || !isset($servicioDetalle['precio_desde']) || $servicioDetalle['precio_desde'] <= 0) {
            $this->error('El servicio seleccionado no tiene un precio definido.', [], 422);
            return;
        }

        $precioTotal = (float) $servicioDetalle['precio_desde'];

        $validacion = AbonoHelper::validarMontoAbono($data['monto'], $data['tamano'], $precioTotal);
        if (!$validacion['valido']) {
            $this->error($validacion['mensaje'], [], 422);
            return;
        }

        try {
            $appointmentId = $this->appointmentModel->create([
                'usuario_id' => $userId,
                'artista_id' => $data['id_tatuador'],
                'servicio_id' => $servicio['id'],
                'fecha_cita' => $data['fecha_cita'],
                'hora_cita' => $data['hora_cita'],
                'detalle_personalizado' => $data['detalle_personalizado'],
                'observaciones' => $data['observaciones'],
            ]);

            $this->paymentModel->create([
                'cita_id' => $appointmentId,
                'monto' => $data['monto'],
                'metodo' => $data['metodo_pago'],
                'comprobante' => $data['comprobante'],
            ]);

            $respuesta = AbonoHelper::generarRespuestaAbono($data['tamano'], $data['monto'], $precioTotal, 0);

            $this->created(
                $respuesta['mensaje'],
                [
                    'cita_id' => $appointmentId,
                    'abono' => $respuesta['data'],
                ]
            );
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->serverError('No fue posible agendar la cita. Verifica la conexión a la base de datos.');
        }
    }
}