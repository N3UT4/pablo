<?php

/**
 * =====================================================================
 * SERVICIO: BookingService
 * =====================================================================
 * DESCRIPCIÓN: Servicio de negocio que orquesta el flujo completo
 *   de agendar una cita + registrar el abono/pago inicial.
 *
 *   INTEGRIDAD FINANCIERA: Toda la operación se ejecuta dentro de
 *   una única transacción DB::transaction(). Si la creación de la
 *   cita o del pago fallan en cualquier punto, AMBAS operaciones
 *   se revierten automáticamente, dejando la base de datos en
 *   un estado consistente.
 *
 *   FLUJO TRANSACCIONAL:
 *     1. Validar datos de entrada
 *     2. INICIO TRANSACCIÓN
 *        a. Verificar existencia del servicio
 *        b. Crear la cita (INSERT INTO citas)
 *        c. Crear el pago/abono (INSERT INTO pagos)
 *     3. COMMIT si todo fue exitoso
 *     4. ROLLBACK si ocurrió cualquier error
 *
 *   USO:
 *     $service = new BookingService();
 *     try {
 *         $resultado = $service->agendarYpagar($datos);
 *     } catch (\Throwable $e) {
 *         // La BD está en estado consistente gracias a la transacción
 *     }
 * =====================================================================
 */

namespace App\Services;

use App\Models\Cita;
use App\Models\Pago;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookingService
{
    /**
     * Agendar una cita y registrar el abono/pago en una única transacción.
     *
     * @param array<string, mixed> $datos Datos completos de la cita y pago:
     *   - usuario_id:  int    ID del cliente (usuario)
     *   - artista_id: int    ID del tatuador asignado
     *   - servicio_id: int   ID del servicio contratado
     *   - fecha_cita:  string Fecha de la cita (formato Y-m-d)
     *   - hora_cita:   string Hora de la cita (formato H:i:s)
     *   - detalle_personalizado: string|null
     *   - observaciones: string|null
     *   - monto:       float  Monto del abono/pago
     *   - metodo:      string Método de pago (nequi|transferencia|efectivo|tarjeta)
     *   - comprobante: string|null Comprobante del pago
     *
     * @return array{cita_id: int, pago_id: int, estado_cita: string}
     *
     * @throws Throwable Si ocurre cualquier error (la transacción se revierte automáticamente)
     */
    public function agendarYpagar(array $datos): array
    {
        // Validaciones previas fuera de la transacción (mensajes de error claros)
        $this->validarDatosEntrada($datos);

        $citaId = 0;
        $pagoId = 0;

        // TRANSACCIÓN: Toda la operación es atómica
        // Si algo falla, NO se crea ni la cita ni el pago
        DB::transaction(function () use ($datos, &$citaId, &$pagoId) {

            // Paso 1: Verificar que el servicio existe y tiene precio definido
            $servicio = Servicio::find((int) $datos['servicio_id']);
            if ($servicio === null) {
                throw new \InvalidArgumentException(
                    "El servicio con ID {$datos['servicio_id']} no existe."
                );
            }

            if (is_null($servicio->precio_desde) || $servicio->precio_desde <= 0) {
                throw new \InvalidArgumentException(
                    'El servicio seleccionado no tiene un precio definido.'
                );
            }

            // Paso 2: Crear la cita (INSERT INTO citas)
            $cita = Cita::create([
                'usuario_id' => (int) $datos['usuario_id'],
                'artista_id' => (int) $datos['artista_id'],
                'servicio_id' => (int) $datos['servicio_id'],
                'fecha_cita' => $datos['fecha_cita'],
                'hora_cita' => $datos['hora_cita'],
                'detalle_personalizado' => $datos['detalle_personalizado'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => 'pendiente',
            ]);

            $citaId = (int) $cita->id;

            // Paso 3: Crear el pago/abono asociado a la cita (INSERT INTO pagos)
            $pago = Pago::create([
                'cita_id' => $citaId,
                'monto' => (float) $datos['monto'],
                'metodo' => $datos['metodo'],
                'comprobante' => $datos['comprobante'] ?? null,
                'estado' => 'pendiente',
            ]);

            $pagoId = (int) $pago->id;

            // NOTA: Este punto es extensible. Ejemplos de operaciones
            // atómicas adicionales que podrían incluirse aquí dentro
            // de la misma transacción:
            //   - Actualizar el estado de la cita a 'confirmada'
            //   - Acumular el abono en la cita
            //   - Enviar notificación al tatuador (cola de eventos)
            // Todo se revierte si alguno falla, garantizando integridad financiera.
        });

        return [
            'cita_id' => $citaId,
            'pago_id' => $pagoId,
            'estado_cita' => 'pendiente',
        ];
    }

    /**
     * Valida los datos de entrada antes de iniciar la transacción.
     * Lanza excepciones con mensajes descriptivos si hay errores.
     *
     * @param array<string, mixed> $datos
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validarDatosEntrada(array $datos): void
    {
        $camposObligatorios = [
            'usuario_id', 'artista_id', 'servicio_id',
            'fecha_cita', 'hora_cita', 'monto', 'metodo',
        ];

        foreach ($camposObligatorios as $campo) {
            if (!array_key_exists($campo, $datos) || $datos[$campo] === null || $datos[$campo] === '') {
                throw new \InvalidArgumentException("El campo '{$campo}' es obligatorio.");
            }
        }

        if ((int) $datos['usuario_id'] <= 0) {
            throw new \InvalidArgumentException('El usuario_id no es válido.');
        }

        if ((int) $datos['artista_id'] <= 0) {
            throw new \InvalidArgumentException('El artista_id no es válido.');
        }

        if ((float) $datos['monto'] <= 0) {
            throw new \InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        if (!in_array($datos['metodo'], ['nequi', 'transferencia', 'efectivo', 'tarjeta'], true)) {
            throw new \InvalidArgumentException('El método de pago no es válido.');
        }
    }
}
