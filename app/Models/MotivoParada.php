<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MotivoParada extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'motivos_parada';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo', // 'Planificada', 'No Planificada'
    ];

    /**
     * Relación: Un motivo de parada puede estar en muchas paradas de producción.
     */
    public function paradasProduccion(): HasMany
    {
        return $this->hasMany(ParadaProduccion::class, 'motivo_parada_id');
    }
}
