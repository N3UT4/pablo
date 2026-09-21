<?php

/**
 * =====================================================================
 * MIGRATION: create_clientes_table
 * =====================================================================
 * DESCRIPCIÓN: Crea la tabla `clientes` que almacena el perfil de
 *   cada cliente vinculado a un usuario del sistema (`usuarios.id`).
 *   Un usuario puede ser cliente, tatuador o admin; esta tabla
 *   extiende el registro únicamente para quienes tienen rol 'cliente'.
 *
 *   BASE: Esquema canónico `database/database.sql`.
 *   RELACIONES:
 *     - clientes.id_usuario → usuarios.id (UNIQUE, FK, CASCADE)
 *     - Un cliente tiene muchas citas (hasMany → Cita)
 * =====================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración: crear tabla clientes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            // Clave primaria autoincremental
            $table->id();

            // Relación con la tabla usuarios: cada cliente pertenece a un usuario
            // UNIQUE garantiza que un usuario no puede ser registrado dos veces como cliente
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
            $table->unique('usuario_id', 'uq_cliente_usuario');

            // Documento de identidad del cliente (único a nivel global)
            $table->string('documento', 50)->unique();

            // Fecha de nacimiento (nullable — no siempre disponible)
            $table->date('fecha_nacimiento')->nullable();

            // Timestamps estándar de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración: eliminar tabla clientes.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
