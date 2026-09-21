<?php

require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloPagos.php';
require_once DIR_PATH . 'app/models/ModeloArtistas.php';
require_once DIR_PATH . 'app/models/ModeloServicios.php';
require_once DIR_PATH . 'app/helpers/AbonoHelper.php';

class ControladorCitas extends ControladorBase
{
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
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(false, 'Solicitud no válida.', [], 405);
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $this->json(false, 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.', [], 419);
            }

            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                $this->json(false, 'Debes iniciar sesión para agendar una cita.', ['redirect' => BASE_URL . 'index.php?action=login'], 401);
            }

            $artistId = (int) ($_POST['id_tatuador'] ?? 0);
            $servicioSlug = trim((string) ($_POST['tipo_servicio'] ?? ''));
            $tamano = strtolower(trim((string) ($_POST['tamano'] ?? '')));
            $detalle = trim((string) ($_POST['detalle_personalizado'] ?? ''));
            $observaciones = trim((string) ($_POST['observaciones'] ?? ''));
            $fecha = trim((string) ($_POST['fecha_cita'] ?? ''));
            $hora = trim((string) ($_POST['hora_cita'] ?? ''));
            $monto = (float) ($_POST['monto'] ?? 0);
            $metodo = trim((string) ($_POST['metodo_pago'] ?? ''));
            $comprobante = trim((string) ($_POST['comprobante'] ?? ''));

            if ($artistId <= 0 || $servicioSlug === '' || $tamano === '' || $fecha === '' || $hora === '' || $monto <= 0) {
                $this->json(false, 'Revisa los campos marcados en rojo antes de continuar.', [], 422);
            }

            if (!AbonoHelper::esTamanoValido($tamano)) {
                $this->json(false, "Tamaño de tatuaje inválido. Debe ser: pequeño, mediano o grande.", [], 422);
            }

            if (!in_array($metodo, ['nequi', 'transferencia', 'efectivo', 'tarjeta'], true)) {
                $this->json(false, 'Selecciona un método de pago válido.', [], 422);
            }

            if ($metodo === 'nequi' && strlen($comprobante) < 4) {
                $this->json(false, 'Ingresa el número de comprobante de la transferencia por Nequi.', [], 422);
            }

            if (!$this->artistModel->exists($artistId)) {
                $this->json(false, 'El tatuador seleccionado no existe. Revisa la tabla "artistas" en la base de datos.', [], 422);
            }

            $servicio = $this->serviceModel->findBySlug($servicioSlug);
            if (!$servicio) {
                $this->json(false, 'El estilo "' . $servicioSlug . '" no existe en la tabla "servicios". Agrégalo o ajusta el slug.', [], 422);
            }

            $servicioDetalle = $this->serviceModel->findById((int) $servicio['id']);
            if (!$servicioDetalle || !isset($servicioDetalle['precio_desde']) || $servicioDetalle['precio_desde'] <= 0) {
                $this->json(false, 'El servicio seleccionado no tiene un precio definido.', [], 422);
            }
            $precioTotal = (float) $servicioDetalle['precio_desde'];

            if (!$this->validarAnticipacionMinima($fecha, $hora)) {
                $this->json(false, 'No se permite agendar citas con menos de 72 horas (3 días) de anticipación.', [], 422);
            }

            $validacion = AbonoHelper::validarMontoAbono($monto, $tamano, $precioTotal);
            if (!$validacion['valido']) {
                $this->json(false, $validacion['mensaje'], [], 422);
            }

            $appointmentId = $this->appointmentModel->create([
                'usuario_id' => $userId,
                'artista_id' => $artistId,
                'servicio_id' => $servicio['id'],
                'fecha_cita' => $fecha,
                'hora_cita' => $hora,
                'detalle_personalizado' => $detalle,
                'observaciones' => $observaciones,
            ]);

            $this->paymentModel->create([
                'cita_id' => $appointmentId,
                'monto' => $monto,
                'metodo' => $metodo,
                'comprobante' => $comprobante,
            ]);

            $respuesta = AbonoHelper::generarRespuestaAbono($tamano, $monto, $precioTotal, 0);

            $this->json(
                true,
                $respuesta['mensaje'],
                [
                    'cita_id' => $appointmentId,
                    'abono' => $respuesta['data'],
                ],
                201
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible agendar la cita. Verifica la conexión a la base de datos.', [], 500);
        }
    }

    private function validarAnticipacionMinima(string $fecha, string $hora): bool
    {
        $fechaCita = $fecha . ' ' . $hora . ':00';
        $timestampCita = strtotime($fechaCita);
        $timestampAhora = time();
        $diferenciaSegundos = $timestampCita - $timestampAhora;

        return $diferenciaSegundos >= (72 * 3600);
    }
}