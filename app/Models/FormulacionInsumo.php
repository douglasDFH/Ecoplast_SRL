<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla pivote que relaciona Formulaciones con Insumos
 * 
 * Almacena la relación entre una formulación de producto y sus insumos componentes,
 * incluyendo porcentajes y cantidades por lote.
 */
class FormulacionInsumo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formulacion_insumos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'formulacion_id',
        'insumo_id',
        'porcentaje',
        'cantidad_kg_por_lote',
        'notas',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'porcentaje' => 'decimal:2',
        'cantidad_kg_por_lote' => 'decimal:3',
    ];

    /**
     * Relación: Pertenece a una formulación
     */
    public function formulacion(): BelongsTo
    {
        return $this->belongsTo(Formulacion::class, 'formulacion_id');
    }

    /**
     * Relación: Pertenece a un insumo
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
