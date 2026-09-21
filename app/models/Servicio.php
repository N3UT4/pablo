<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Servicio
 * =====================================================================
 * DESCRIPCIÓN: Representa un servicio de tatuaje ofrecido por el estudio
 *   ITZA TATTOO (Blackwork, Realismo, Fine Line, etc.). Define la
 *   relación con las citas que utilizan cada servicio.
 *
 *   TABLA: servicios
 *   RELACIONES:
 *     - hasMany Cita  (un servicio aparece en muchas citas)
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'servicios';

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
     * La tabla servicios incluye created_at.
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
        'nombre',
        'slug',
        'descripcion',
        'precio_desde',
        'activo',
    ];

    /**
     * Atributos que deben ser casteados a tipos nativos para garantizar
     * tipado estricto en las operaciones del modelo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'precio_desde' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * RELACIÓN: Un servicio tiene muchas citas asociadas.
     * HAS MANY → App\Models\Cita
     * Columna foránea en citas: servicio_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'servicio_id');
    }
}
