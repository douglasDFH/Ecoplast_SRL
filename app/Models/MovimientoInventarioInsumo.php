<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventarioInsumo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimientos_inventario_insumos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'insumo_id',
        'tipo_movimiento',
        'cantidad',
        'fecha_movimiento',
        'usuario_id',
        'orden_produccion_id',
        'motivo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    /**
     * Relación: Un movimiento pertenece a un insumo.
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    /**
     * Relación: Un movimiento es realizado por un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Un movimiento puede estar asociado a una orden de producción.
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }
}
