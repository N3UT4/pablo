<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Artista (stub)
 * =====================================================================
 * DESCRIPCIÓN: Representa un tatuador/artista del estudio.
 *   Se usa como referencia en las relaciones de Cita.
 *
 *   TABLA: artistas
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artista extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'artistas';

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
        'usuario_id',
        'nombre',
        'bio',
        'foto',
        'activo',
    ];

    /**
     * Atributos casteados a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'usuario_id' => 'integer',
        'activo' => 'boolean',
    ];
}
