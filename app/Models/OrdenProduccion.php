<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenProduccion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ordenes_produccion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_orden',
        'producto_id',
        'cantidad_planificada',
        'cantidad_producida',
        'cantidad_conforme',
        'cantidad_defectuosa',
        'formulacion_id',
        'maquina_id',
        'turno_id',
        'fecha_programada',
        'fecha_inicio',
        'fecha_fin',
        'operador_id',
        'supervisor_id',
        'estado',
        'prioridad',
        'notas_produccion',
        'observaciones_calidad',
        'creado_por',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'prioridad' => 'string',
        'cantidad_planificada' => 'integer',
        'cantidad_producida' => 'integer',
        'cantidad_conforme' => 'integer',
        'cantidad_defectuosa' => 'integer',
    ];

    // ==================== RELACIONES ====================

    public function productoTerminado(): BelongsTo
    {
        return $this->belongsTo(ProductoTerminado::class, 'producto_id');
    }

    public function formulacion(): BelongsTo
    {
        return $this->belongsTo(Formulacion::class, 'formulacion_id');
    }

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function registrosProduccion(): HasMany
    {
        return $this->hasMany(RegistroProduccion::class, 'orden_produccion_id');
    }

    public function paradasProduccion(): HasMany
    {
        return $this->hasMany(ParadaProduccion::class, 'orden_produccion_id');
    }

    public function inspeccionesCalidad(): HasMany
    {
        return $this->hasMany(InspeccionCalidad::class, 'orden_produccion_id');
    }

    public function movimientosInsumos(): HasMany
    {
        return $this->hasMany(MovimientoInventarioInsumo::class, 'orden_produccion_id');
    }

    public function movimientosProductos(): HasMany
    {
        return $this->hasMany(MovimientoInventarioProducto::class, 'orden_produccion_id');
    }

    // ==================== SCOPES ====================

    public function scopePendientes($query)
    {
        return $query->where('estado', 'Pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'En Progreso');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado', 'Finalizada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'Cancelada');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', '>', 5); // Asumiendo una escala de prioridad
    }
}
