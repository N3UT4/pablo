<?php

require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloPagos.php';
require_once DIR_PATH . 'app/helpers/AbonoHelper.php';

class ControladorPagos extends ControladorBase
{
    private ModeloCitas $appointmentModel;
    private ModeloPagos $paymentModel;

    public function __construct()
    {
        $this->appointmentModel = new ModeloCitas();
        $this->paymentModel = new ModeloPagos();
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
                $this->json(false, 'Debes iniciar sesión para registrar un abono.', ['redirect' => BASE_URL . 'index.php?action=login'], 401);
            }

            $citaId = (int) ($_POST['cita_id'] ?? 0);
            $monto = (float) ($_POST['monto'] ?? 0);
            $metodo = trim((string) ($_POST['metodo_pago'] ?? ''));
            $comprobante = trim((string) ($_POST['comprobante'] ?? ''));

            if ($citaId <= 0 || $monto <= 0) {
                $this->json(false, 'Datos inválidos: cita_id y monto son obligatorios.', [], 422);
            }

            if (!in_array($metodo, ['nequi', 'transferencia', 'efectivo', 'tarjeta'], true)) {
                $this->json(false, 'Selecciona un método de pago válido.', [], 422);
            }

            if ($metodo === 'nequi' && strlen($comprobante) < 4) {
                $this->json(false, 'Ingresa el número de comprobante de la transferencia por Nequi.', [], 422);
            }

            $cita = $this->appointmentModel->find($citaId);
            if (!$cita) {
                $this->json(false, 'La cita no existe.', [], 404);
            }

            if ((int) $cita['usuario_id'] !== $userId) {
                $this->json(false, 'No tienes permiso para realizar abonos en esta cita.', [], 403);
            }

            $servicio = $this->obtenerServicioPorCita($citaId);
            if (!$servicio || !isset($servicio['precio_desde']) || $servicio['precio_desde'] <= 0) {
                $this->json(false, 'El servicio asociado no tiene un precio definido.', [], 422);
            }
            $precioTotal = (float) $servicio['precio_desde'];

            $tamano = $this->obtenerTamanoPorCita($citaId);
            if (!$tamano || !AbonoHelper::esTamanoValido($tamano)) {
                $this->json(false, 'No se pudo determinar el tamaño del tatuaje para validar el abono.', [], 422);
            }

            $totalAbonadoAnterior = $this->obtenerTotalAbonado($citaId);
            $nuevoTotalAbonado = round($totalAbonadoAnterior + $monto, 2);

            if ($nuevoTotalAbonado > $precioTotal) {
                $this->json(false, "El total abonado ($nuevoTotalAbonado COP) no puede exceder el precio total del tatuaje ($precioTotal COP).", [], 422);
            }

            $validacion = AbonoHelper::validarMontoAbono($monto, $tamano, $precioTotal);
            if (!$validacion['valido']) {
                $this->json(false, $validacion['mensaje'], [], 422);
            }

            $pagoId = $this->paymentModel->create([
                'cita_id' => $citaId,
                'monto' => $monto,
                'metodo' => $metodo,
                'comprobante' => $comprobante,
            ]);

            $respuesta = AbonoHelper::generarRespuestaAbono($tamano, $monto, $precioTotal, $totalAbonadoAnterior);

            $this->json(
                true,
                $respuesta['mensaje'],
                [
                    'pago_id' => $pagoId,
                    'cita_id' => $citaId,
                    'abono' => $respuesta['data'],
                ],
                201
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible registrar el abono. Verifica la conexión a la base de datos.', [], 500);
        }
    }

    public function list(int $citaId = 0): void
    {
        try {
            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                $this->json(false, 'Debes iniciar sesión.', [], 401);
            }

            if ($citaId <= 0) {
                $citaId = (int) ($_GET['cita_id'] ?? 0);
            }

            if ($citaId <= 0) {
                $this->json(false, 'ID de cita requerido.', [], 422);
            }

            $cita = $this->appointmentModel->find($citaId);
            if (!$cita) {
                $this->json(false, 'La cita no existe.', [], 404);
            }

            if ((int) $cita['usuario_id'] !== $userId && ($cita['artista_id'] ?? 0) !== $userId) {
                $this->json(false, 'No tienes permiso para ver los pagos de esta cita.', [], 403);
            }

            $pagos = $this->obtenerPagosPorCita($citaId);
            $totalAbonado = $this->obtenerTotalAbonado($citaId);

            $this->json(true, 'ok', [
                'data' => [
                    'pagos' => $pagos,
                    'total_abonado' => $totalAbonado,
                ],
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'Error al obtener los pagos.', [], 500);
        }
    }

    public function updateEstado(int $pagoId = 0): void
    {
        try {
            $user = $_SESSION['user'] ?? null;
            if (!$user || !in_array($user['rol'] ?? '', ['tatuador', 'admin'], true)) {
                $this->json(false, 'Acceso denegado. Solo personal autorizado.', [], 403);
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $this->json(false, 'Método no permitido.', [], 405);
            }

            $pagoId = $pagoId ?: (int) ($_POST['pago_id'] ?? $_GET['id'] ?? 0);
            $estado = trim((string) ($_POST['estado'] ?? ''));

            if ($pagoId <= 0) {
                $this->json(false, 'ID de pago requerido.', [], 422);
            }

            if (!in_array($estado, ['pendiente', 'verificado', 'rechazado'], true)) {
                $this->json(false, 'Estado inválido. Valores permitidos: pendiente, verificado, rechazado.', [], 422);
            }

            $this->paymentModel->updateEstado($pagoId, $estado);

            $this->json(true, 'Estado del pago actualizado.', ['pago_id' => $pagoId, 'estado' => $estado]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'Error al actualizar el estado del pago.', [], 500);
        }
    }

    private function obtenerServicioPorCita(int $citaId): ?array
    {
        $statement = $this->appointmentModel->execute(
            'SELECT s.precio_desde, s.nombre FROM servicios s
             JOIN citas c ON c.servicio_id = s.id
             WHERE c.id = :id LIMIT 1',
            ['id' => $citaId]
        );
        return $statement->fetch() ?: null;
    }

    private function obtenerTamanoPorCita(int $citaId): ?string
    {
        $statement = $this->appointmentModel->execute(
            'SELECT detalle_personalizado FROM citas WHERE id = :id LIMIT 1',
            ['id' => $citaId]
        );
        $row = $statement->fetch();
        return $row ? strtolower(trim($row['detalle_personalizado'])) : null;
    }

    private function obtenerTotalAbonado(int $citaId): float
    {
        $statement = $this->paymentModel->execute(
            'SELECT COALESCE(SUM(monto), 0) AS total FROM abonos
             WHERE cita_id = :cita_id AND estado IN ("pendiente", "verificado")',
            ['cita_id' => $citaId]
        );
        $row = $statement->fetch();
        return (float) ($row['total'] ?? 0);
    }

    private function obtenerPagosPorCita(int $citaId): array
    {
        $statement = $this->paymentModel->execute(
            'SELECT id, monto, metodo, comprobante, estado, created_at
             FROM abonos WHERE cita_id = :cita_id ORDER BY created_at DESC',
            ['cita_id' => $citaId]
        );
        return $statement->fetchAll();
    }
}