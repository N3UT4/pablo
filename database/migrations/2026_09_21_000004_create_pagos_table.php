<?php

/**
 * =====================================================================
 * MIGRATION: create_pagos_table
 * =====================================================================
 * DESCRIPCIÓN: Crea la tabla `pagos` (anionimamente `abonos` en el
 *   esquema original) que registra los pagos/abonos parciales o totales
 *   asociados a una cita. Cada pago tiene un monto, método de pago,
 *   comprobante opcional y un estado que rastrea su procesamiento.
 *
 *   INTEGRIDAD FINANCIERA: La creación de pagos DEBE envolverse en
 *   DB::transaction() para garantizar que el pago y cualquier actualización
 *   asociada (estado de cita, saldo) se persistan atómicamente.
 *
 *   BASE: Esquema canónico `database/database.sql` → tabla `abonos`.
 *   RELACIONES:
 *     - Un pago pertenece a una cita (belongsTo → Cita via cita_id)
 *     - Una cita tiene muchos pagos (hasMany → Pago)
 * =====================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración: crear tabla pagos.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            // Clave primaria autoincremental
            $table->id();

            // Relación con citas: cada pago está asociado a una cita específica
            // CASCADE: si la cita se elimina, sus pagos también (integridad referencial)
            $table->unsignedBigInteger('cita_id');
            $table->foreign('cita_id')
                  ->references('id')
                  ->on('citas')
                  ->onDelete('cascade');
            $table->index('cita_id', 'idx_pagos_cita');

            // Monto del pago/abono (DECIMAL para exactitud monetaria, sin riesgo de punto flotante)
            $table->decimal('monto', 12, 2)->notNull();

            // Método de pago: nequi, transferencia, efectivo o tarjeta
            $table->enum('metodo', ['nequi', 'transferencia', 'efectivo', 'tarjeta']);

            // Ruta o nombre del archivo de comprobante (opcional — no siempre hay comprobante)
            $table->string('comprobante', 255)->nullable();

            // Estado del pago: pendiente (esperando verificación), verificado (confirmado),
            // rechazado (falló la verificación), reembolsado (devuelto al cliente)
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado', 'reembolsado'])
                  ->default('pendiente');
            $table->index('estado', 'idx_pagos_estado');

            // Timestamps estándar de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración: eliminar tabla pagos.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
