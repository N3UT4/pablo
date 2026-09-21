<?php

/**
 * =====================================================================
 * MIGRATION: create_servicios_table
 * =====================================================================
 * DESCRIPCIÓN: Crea la tabla `servicios` que almacena los tipos de
 *   tatuaje ofrecidos por el estudio (Blackwork, Realismo, etc.).
 *   Cada servicio tiene un nombre único, un slug URL-friendly y
 *   un precio base desde el cual se calcula el costo final.
 *
 *   BASE: Esquema canónico `database/database.sql`.
 *   RELACIONES:
 *     - Un servicio tiene muchas citas (hasMany → Cita)
 * =====================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración: crear tabla servicios.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            // Clave primaria autoincremental
            $table->id();

            // Nombre legible del servicio (ej: 'Blackwork'). Único.
            $table->string('nombre', 100)->unique();

            // Slug URL-friendly para formularios y rutas (ej: 'blackwork'). Único.
            $table->string('slug', 100)->unique();

            // Descripción opcional del servicio
            $table->text('descripcion')->nullable();

            // Precio base del servicio (DECIMAL para exactitud monetaria)
            $table->decimal('precio_desde', 12, 2)->nullable();

            // Flag de activación: 1 = activo, 0 = oculto
            $table->tinyInteger('activo')->default(1);

            // Timestamps estándar de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración: eliminar tabla servicios.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
