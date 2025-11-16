<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alertas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_alerta', // 'Stock Bajo', 'Produccion Retrasada', 'Mantenimiento Requerido'
        'mensaje',
        'nivel_urgencia', // 'Bajo', 'Medio', 'Alto', 'Crítico'
        'leida',
        'fecha_lectura',
        'usuario_destino_id',
        'relacion_id', // ID del modelo relacionado (Insumo, OrdenProduccion, etc.)
        'relacion_tipo', // Nombre del modelo relacionado
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'leida' => 'boolean',
        'fecha_lectura' => 'datetime',
    ];

    /**
     * Relación: Una alerta está destinada a un usuario.
     */
    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_destino_id');
    }

    /**
     * Relación polimórfica para el objeto relacionado con la alerta.
     */
    public function relacionado()
    {
        return $this->morphTo(__FUNCTION__, 'relacion_tipo', 'relacion_id');
    }

    /**
     * Scope: Solo alertas no leídas.
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    /**
     * Scope: Solo alertas de un nivel de urgencia específico.
     */
    public function scopeDeUrgencia($query, $nivel)
    {
        return $query->where('nivel_urgencia', $nivel);
    }

    /**
     * Marca la alerta como leída.
     */
    public function marcarComoLeida()
    {
        $this->leida = true;
        $this->fecha_lectura = now();
        $this->save();
    }
}
