<?php

/**
 * =====================================================================
 * MIGRATION: create_citas_table
 * =====================================================================
 * DESCRIPCIÓN: Crea la tabla `citas` que almacena las citas/agendamientos
 *   de tatuaje solicitados por los clientes. Cada cita vincula un cliente
 *   (usuario_id), un artista/tatuador (artista_id) y un servicio (servicio_id).
 *
 *   BASE: Esquema canónico `database/database.sql`.
 *   RELACIONES:
 *     - Una cita pertenece a un cliente (belongsTo → Cliente via usuario_id)
 *     - Una cita pertenece a un artista (belongsTo → Artista via artista_id)
 *     - Una cita pertenece a un servicio (belongsTo → Servicio via servicio_id)
 *     - Una cita tiene muchos pagos (hasMany → Pago)
 * =====================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración: crear tabla citas.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            // Clave primaria autoincremental
            $table->id();

            // Relación con usuarios: el cliente que solicita la cita
            // CASCADE: si el usuario se elimina, sus citas también
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
            $table->index('usuario_id', 'idx_citas_usuario');

            // Relación con artistas: el tatuador asignado a la cita
            // NO ACTION por defecto (el artista no se elimina típicamente)
            $table->unsignedBigInteger('artista_id');
            $table->foreign('artista_id')
                  ->references('id')
                  ->on('artistas');
            $table->index('artista_id', 'idx_citas_artista');

            // Relación con servicios: el tipo de tatuaje contratado
            // NO ACTION por defecto
            $table->unsignedBigInteger('servicio_id');
            $table->foreign('servicio_id')
                  ->references('id')
                  ->on('servicios');
            $table->index('servicio_id', 'idx_citas_servicio');

            // Fecha y hora programadas para la cita (separadas para facilitar consultas por fecha)
            $table->date('fecha_cita');
            $table->time('hora_cita');

            // Detalle personalizado solicitado por el cliente (ej: diseño específico)
            $table->text('detalle_personalizado')->nullable();

            // Observaciones internas para el equipo del estudio
            $table->text('observaciones')->nullable();

            // Estado de la cita con sus valores permitidos
            // pendiente → confirmada → completada | cancelada
            $table->enum('estado', ['pendiente', 'confirmada', 'completada', 'cancelada'])
                  ->default('pendiente');
            $table->index('estado', 'idx_citas_estado');

            // Timestamps estándar de Laravel
            $table->timestamps();

            // Índice compuesto para consultas por fecha y hora (panel de tatuador)
            $table->index(['fecha_cita', 'hora_cita'], 'idx_citas_date_hora');
        });
    }

    /**
     * Revertir la migración: eliminar tabla citas.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
