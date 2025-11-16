<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ParadaProduccion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paradas_produccion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orden_produccion_id',
        'maquina_id',
        'motivo_parada_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'descripcion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
    ];

    /**
     * Relación: Una parada de producción pertenece a una orden de producción.
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }

    /**
     * Relación: Una parada de producción ocurre en una máquina.
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    /**
     * Relación: Una parada de producción tiene un motivo.
     */
    public function motivoParada(): BelongsTo
    {
        return $this->belongsTo(MotivoParada::class, 'motivo_parada_id');
    }

    /**
     * Atributo dinámico: Calcula la duración de la parada en minutos.
     */
    public function getDuracionMinutosAttribute(): ?int
    {
        if ($this->fecha_hora_fin) {
            return $this->fecha_hora_inicio->diffInMinutes($this->fecha_hora_fin);
        }
        return null;
    }
}
