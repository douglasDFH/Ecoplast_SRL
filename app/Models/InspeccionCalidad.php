<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspeccionCalidad extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inspecciones_calidad';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orden_produccion_id',
        'inspector_id',
        'fecha_inspeccion',
        'resultado', // 'Aprobado', 'Rechazado', 'Aprobado con Observaciones'
        'observaciones',
        'lote_inspeccionado',
        'cantidad_muestreada',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inspeccion' => 'datetime',
        'cantidad_muestreada' => 'integer',
    ];

    /**
     * Relación: Una inspección pertenece a una orden de producción.
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }

    /**
     * Relación: Una inspección es realizada por un inspector (usuario).
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Scope: Solo inspecciones aprobadas.
     */
    public function scopeAprobadas($query)
    {
        return $query->where('resultado', 'Aprobado');
    }

    /**
     * Scope: Solo inspecciones rechazadas.
     */
    public function scopeRechazadas($query)
    {
        return $query->where('resultado', 'Rechazado');
    }
}
