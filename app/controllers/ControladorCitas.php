<?php

require_once APP_ROOT . '/app/models/ModeloCitas.php';
require_once APP_ROOT . '/app/models/ModeloPagos.php';
require_once APP_ROOT . '/app/models/ModeloArtistas.php';
require_once APP_ROOT . '/app/models/ModeloServicios.php';

// Atiende el formulario de abono (js/abono.js): agenda la cita y registra el abono/pago.
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
                $this->json(false, 'Debes iniciar sesión para agendar una cita.', ['redirect' => 'index.php?action=login'], 401);
            }

            $artistId = (int) ($_POST['id_tatuador'] ?? 0);
            $servicioSlug = trim((string) ($_POST['tipo_servicio'] ?? ''));
            $detalle = trim((string) ($_POST['detalle_personalizado'] ?? ''));
            $observaciones = trim((string) ($_POST['observaciones'] ?? ''));
            $fecha = trim((string) ($_POST['fecha_cita'] ?? ''));
            $hora = trim((string) ($_POST['hora_cita'] ?? ''));
            $monto = (float) ($_POST['monto'] ?? 0);
            $metodo = trim((string) ($_POST['metodo_pago'] ?? ''));
            $comprobante = trim((string) ($_POST['comprobante'] ?? ''));

            if ($artistId <= 0 || $servicioSlug === '' || $fecha === '' || $hora === '' || $monto <= 0) {
                $this->json(false, 'Revisa los campos marcados en rojo antes de continuar.', [], 422);
            }

            if (!in_array($metodo, ['nequi', 'transferencia', 'efectivo', 'tarjeta'], true)) {
                $this->json(false, 'Selecciona un método de pago válido.', [], 422);
            }

            if ($metodo === 'nequi' && strlen($comprobante) < 4) {
                $this->json(false, 'Ingresa el número de comprobante de la transferencia por Nequi.', [], 422);
            }

            if (!$this->artistModel->exists($artistId)) {
                $this->json(false, 'El tatuador seleccionado no existe. Revisa la tabla "artists" en la base de datos.', [], 422);
            }

            $servicio = $this->serviceModel->findBySlug($servicioSlug);
            if (!$servicio) {
                $this->json(false, 'El estilo "' . $servicioSlug . '" no existe en la tabla "services". Agrégalo o ajusta el slug.', [], 422);
            }

            $appointmentId = $this->appointmentModel->create([
                'user_id' => $userId,
                'artist_id' => $artistId,
                'service_id' => $servicio['id'],
                'fecha_cita' => $fecha,
                'hora_cita' => $hora,
                'detalle_personalizado' => $detalle,
                'observaciones' => $observaciones,
            ]);

            $this->paymentModel->create([
                'appointment_id' => $appointmentId,
                'monto' => $monto,
                'metodo' => $metodo,
                'comprobante' => $comprobante,
            ]);

            $this->json(true, 'Tu cita quedó agendada y tu abono registrado. ¡Te esperamos!', ['appointment_id' => $appointmentId]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible agendar la cita. Verifica la conexión a la base de datos.', [], 500);
        }
    }
}