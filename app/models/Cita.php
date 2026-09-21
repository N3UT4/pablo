<?php

/**
 * =====================================================================
 * MODELO ELOQUENT: Cita
 * =====================================================================
 * DESCRIPCIÓN: Representa una cita/agendamiento de tatuaje. Vincula
 *   a un cliente (usuario), un artista/tatuador y un servicio. Cada
 *   cita puede tener uno o más pagos asociados.
 *
 *   TABLA: citas
 *   RELACIONES:
 *     - belongsTo Cliente  (vía usuario_id → clientes.usuario_id)
 *     - belongsTo Artista  (vía artista_id → artistas.id)
 *     - belongsTo Servicio (vía servicio_id → servicios.id)
 *     - hasMany Pago       (vía cita_id → pagos.cita_id)
 * =====================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cita extends Model
{
    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'citas';

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
     * La tabla citas incluye created_at.
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
        'usuario_id',
        'artista_id',
        'servicio_id',
        'fecha_cita',
        'hora_cita',
        'detalle_personalizado',
        'observaciones',
        'estado',
    ];

    /**
     * Atributos que deben ser casteados a tipos nativos.
     * Garantiza tipado estricto para fechas, enteros y estados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'usuario_id' => 'integer',
        'artista_id' => 'integer',
        'servicio_id' => 'integer',
        'fecha_cita' => 'date',
        'hora_cita' => 'datetime:H:i:s',
        'estado' => 'string',
    ];

    /**
     * Valores permitidos para el campo estado.
     * Usado para validación interna del modelo.
     *
     * @var array<int, string>
     */
    public const ESTADOS_PERMITIDOS = [
        'pendiente',
        'confirmada',
        'completada',
        'cancelada',
    ];

    /**
     * VALIDACIÓN: Una cita pertenece a un cliente.
     * RELACIÓN: belongsTo → App\Models\Cliente
     * Columna foránea en citas: usuario_id → referencia a clientes.usuario_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'usuario_id', 'usuario_id');
    }

    /**
     * VALIDACIÓN: Una cita pertenece a un artista (tatuador).
     * RELACIÓN: belongsTo → App\Models\Artista
     * Columna foránea en citas: artista_id → referencia a artistas.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function artista(): BelongsTo
    {
        return $this->belongsTo(Artista::class, 'artista_id');
    }

    /**
     * VALIDACIÓN: Una cita pertenece a un servicio.
     * RELACIÓN: belongsTo → App\Models\Servicio
     * Columna foránea en citas: servicio_id → referencia a servicios.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    /**
     * RELACIÓN: Una cita tiene muchos pagos/abonos.
     * HAS MANY → App\Models\Pago
     * Columna foránea en pagos: cita_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'cita_id');
    }

    /**
     * Cambia el estado de la cita validando que el valor sea permitido.
     *
     * @param string $estado Nuevo estado de la cita
     * @return bool True si se actualizó, false si el estado es inválido
     */
    public function cambiarEstado(string $estado): bool
    {
        if (!in_array($estado, self::ESTADOS_PERMITIDOS, true)) {
            return false;
        }

        $this->estado = $estado;
        $this->save();

        return true;
    }
}
