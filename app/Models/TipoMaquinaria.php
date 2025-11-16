<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMaquinaria extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipos_maquina';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_tipo',
        'descripcion',
    ];

    /**
     * Relación: Un tipo de maquinaria tiene muchas máquinas.
     */
    public function maquinarias(): HasMany
    {
        return $this->hasMany(Maquinaria::class, 'tipo_id');
    }
}
