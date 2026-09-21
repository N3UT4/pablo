<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Pago
 * =====================================================================
 * DESCRIPCIÓN: Representa un pago o abono asociado a una cita.
 *   Registra el monto, método de pago, comprobante y estado del pago.
 *
 *   INTEGRIDAD FINANCIERA: La creación de pagos debe realizarse dentro
 *   de una transacción DB::transaction() para garantizar que el pago
 *   y las actualizaciones asociadas (estado de cita, cálculo de saldo)
 *   se persistan de forma atómica. Cualquier fallo revierte todo.
 *
 *   TABLA: pagos (originalmente `abonos` en el esquema canónico)
 *   RELACIONES:
 *     - belongsTo Cita (vía cita_id → citas.id)
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Throwable;

class Pago extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     * Nota: En el esquema original la tabla se llama 'abonos'.
     * Se usa 'pagos' por convención Eloquent/Laravel.
     *
     * @var string
     */
    protected $table = 'pagos';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la clave primaria es auto-incremental.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Tipo de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe gestionar timestamps.
     * La tabla pagos incluye created_at (timestamp del pago).
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Atributos que pueden ser asignados masivamente (fillable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cita_id',
        'monto',
        'metodo',
        'comprobante',
        'estado',
    ];

    /**
     * Atributos que deben ser casteados a tipos nativos.
     * DECIMAL(12,2) para el monto garantiza precisión monetaria.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'cita_id' => 'integer',
        'monto' => 'decimal:2',
        'estado' => 'string',
    ];

    /**
     * VALIDACIÓN: Un pago pertenece a una cita.
     * RELACIÓN: belongsTo → App\Models\Cita
     * Columna foránea en pagos: cita_id → referencia a citas.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    /**
     * REGISTRA UN PAGO DENTRO DE UNA TRANSACCIÓN.
     * ===================================================================
     * Crea un nuevo pago/abono y verifica la integridad financiera
     * envolviendo toda la operación en DB::transaction().
     *
     * FLUJO:
     *   1. Inicia transacción
     *   2. Verifica que la cita exista
     *   3. Inserta el registro de pago
     *   4. (Extensible: actualizar estado de cita, calcular saldo, etc.)
     *   5. Confirmar (commit) o revocar (rollback) ante cualquier error
     *
     * @param array<string, mixed> $datos Datos del pago: cita_id, monto, metodo, comprobante(opcional)
     * @return int ID del pago recién creado
     * @throws \InvalidArgumentException Si la cita no existe o faltan datos obligatorios
     * @throws \Throwable Si ocurre cualquier error durante la transacción
     */
    public function crearConTransaccion(array $datos): int
    {
        // Validación previa: campos obligatorios
        $citaId = (int) ($datos['cita_id'] ?? 0);
        $monto = (float) ($datos['monto'] ?? 0);
        $metodo = trim((string) ($datos['metodo'] ?? ''));

        if ($citaId <= 0) {
            throw new \InvalidArgumentException('El campo cita_id es obligatorio.');
        }

        if ($monto <= 0) {
            throw new \InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        if (!in_array($metodo, ['nequi', 'transferencia', 'efectivo', 'tarjeta'], true)) {
            throw new \InvalidArgumentException('El método de pago no es válido.');
        }

        // Variable para capturar el ID del pago creado dentro del closure
        $pagoId = 0;

        // TRANSACCIÓN: Garantiza integridad financiera
        // Si cualquier paso falla, TODOS los cambios se revierten automáticamente
        DB::transaction(function () use ($citaId, $monto, $metodo, $datos, &$pagoId) {
            // Paso 1: Verificar que la cita existe (integridad referencial aplicada en PHP)
            $cita = Cita::find($citaId);
            if ($cita === null) {
                throw new \InvalidArgumentException(
                    "La cita con ID {$citaId} no existe. No se puede registrar el pago."
                );
            }

            // Paso 2: Crear el registro de pago dentro de la transacción
            $pago = new static();
            $pago->cita_id = $citaId;
            $pago->monto = $monto;
            $pago->metodo = $metodo;
            $pago->comprobante = trim((string) ($datos['comprobante'] ?? '')) ?: null;
            $pago->estado = 'pendiente';
            $pago->save();

            // Capturar el ID generado
            $pagoId = (int) $pago->id;

            // NOTA: Este punto es extensible. Ejemplos de operaciones atómicas
            // adicionales que podrían incluirse aquí:
            //   - Actualizar el estado de la cita a 'confirmada' si era 'pendiente'
            //   - Recalcular el saldo pendiente de la cita
            //   - Registrar el pago en un historial financiero
            // Todo lo anterior se revierte si alguno falla, gracias a la transacción.
        });

        return $pagoId;
    }
}
