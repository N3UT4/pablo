<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Cliente
 * =====================================================================
 * DESCRIPCIÓN: Representa un cliente del estudio ITZA TATTOO. Extiende
 *   el modelo base Eloquent y define la relación con la tabla `clientes`
 *   así como con las citas que tiene asociadas.
 *
 *   TABLA: clientes
 *   RELACIONES:
 *     - hasMany Cita     (un cliente tiene muchas citas, vía usuario_id)
 *     - belongsTo Usuario (un cliente es un usuario del sistema)
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'clientes';

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
     * La tabla clientes incluye created_at y updated_at.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Atributos que pueden ser asignados masivamente (fillable).
     * Solo estos campos pueden ser rellenados via create() o update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'documento',
        'fecha_nacimiento',
    ];

    /**
     * Atributos que deben ser ocultos en serializaciones (JSON, arrays).
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Atributos que deben ser casteados a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usuario_id' => 'integer',
        'id' => 'integer',
        'fecha_nacimiento' => 'date',
    ];

    /**
     * VALIDACIÓN: Un cliente pertenece a un usuario del sistema.
     * RELACIÓN: belongsTo → App\Models\Usuario
     * Columna foránea: usuario_id
     * Clave local: id (en la tabla usuarios)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * RELACIÓN: Un cliente tiene muchas citas.
     * HAS MANY → App\Models\Cita
     * Columna foránea en citas: usuario_id (que apunta al usuario del cliente)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'usuario_id', 'usuario_id');
    }
}
