<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroProduccion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registros_produccion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orden_produccion_id',
        'operador_id',
        'cantidad_producida',
        'cantidad_rechazada',
        'fecha_hora_registro',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_hora_registro' => 'datetime',
        'cantidad_producida' => 'integer',
        'cantidad_rechazada' => 'integer',
    ];

    /**
     * Relación: Un registro de producción pertenece a una orden de producción.
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }

    /**
     * Relación: Un registro de producción es realizado por un operador (usuario).
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }
}
