<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Usuario (stub)
 * =====================================================================
 * DESCRIPCIÓN: Modelo base para la tabla `usuarios`. Se usa como
 *   referencia para las relaciones de Cliente, Artista y Cita.
 *   Este modelo puede extenderse con lógica de autenticación,
 *   roles y gestión de sesión según las necesidades del proyecto.
 *
 *   TABLA: usuarios
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

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
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'documento',
        'fecha_nacimiento',
        'rol',
        'artista_id',
    ];

    /**
     * Atributos ocultos en serializaciones.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Atributos casteados a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'artista_id' => 'integer',
        'fecha_nacimiento' => 'date',
        'rol' => 'string',
    ];

    /**
     * Valores de rol permitidos.
     *
     * @var array<int, string>
     */
    public const ROLES_PERMITIDOS = [
        'cliente',
        'admin',
        'tatuador',
    ];
}
