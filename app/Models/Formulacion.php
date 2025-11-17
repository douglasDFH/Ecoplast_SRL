<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Formulacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formulaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo_formula',
        'nombre_formula',
        'descripcion',
        'version',
        'tipo_producto_destino',
        'temperatura_procesamiento_min',
        'temperatura_procesamiento_max',
        'tiempo_degradacion_estimado',
        'certificaciones',
        'aprobado',
        'fecha_aprobacion',
        'usuario_aprueba_id',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activo' => 'boolean',
        'aprobado' => 'boolean',
        'fecha_aprobacion' => 'datetime',
        'temperatura_procesamiento_min' => 'decimal:1',
        'temperatura_procesamiento_max' => 'decimal:1',
    ];

    /**
     * Relación: Una formulación tiene muchos componentes (insumos).
     */
    public function componentes()
    {
        return $this->hasMany(FormulacionInsumo::class, 'formulacion_id');
    }

    /**
     * Alias para componentes
     */
    public function insumos()
    {
        return $this->componentes();
    }

    /**
     * Relación: Órdenes de producción que usan esta formulación
     */
    public function ordenesProduccion()
    {
        return $this->hasMany(OrdenProduccion::class, 'formulacion_id');
    }

    /**
     * Relación: Usuario que aprobó la formulación
     */
    public function usuarioAprueba(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_aprueba_id');
    }

    /**
     * Scope: Solo formulaciones activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Solo formulaciones aprobadas.
     */
    public function scopeAprobadas($query)
    {
        return $query->where('aprobado', true);
    }
}
