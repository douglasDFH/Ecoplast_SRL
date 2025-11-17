<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMaterial extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipos_materiales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Identificación
        'codigo',
        'nombre',

        // Clasificación
        'clasificacion',

        // Información Técnica
        'descripcion',
        'densidad_min',
        'densidad_max',
        'temperatura_procesamiento_min',
        'temperatura_procesamiento_max',
        'tiempo_degradacion_min',
        'tiempo_degradacion_max',

        // Certificaciones
        'certificaciones_aplicables',

        // UI/UX
        'color_referencia',
        'icono',
        'orden_visualizacion',

        // Control
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'densidad_min' => 'decimal:3',
        'densidad_max' => 'decimal:3',
        'temperatura_procesamiento_min' => 'decimal:1',
        'temperatura_procesamiento_max' => 'decimal:1',
        'tiempo_degradacion_min' => 'integer',
        'tiempo_degradacion_max' => 'integer',
        'orden_visualizacion' => 'integer',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the insumos that use this tipo de material.
     */
    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class, 'tipo_material_id');
    }

    /**
     * Scope para filtrar solo tipos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por clasificación.
     */
    public function scopePorClasificacion($query, string $clasificacion)
    {
        return $query->where('clasificacion', $clasificacion);
    }

    /**
     * Scope para ordenar por orden de visualización.
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden_visualizacion', 'asc')->orderBy('nombre', 'asc');
    }

    /**
     * Obtener el rango de densidad como string formateado.
     */
    public function getRangoDensidadAttribute(): ?string
    {
        if ($this->densidad_min && $this->densidad_max) {
            return "{$this->densidad_min} - {$this->densidad_max} g/cm³";
        }
        return null;
    }

    /**
     * Obtener el rango de temperatura como string formateado.
     */
    public function getRangoTemperaturaAttribute(): ?string
    {
        if ($this->temperatura_procesamiento_min && $this->temperatura_procesamiento_max) {
            return "{$this->temperatura_procesamiento_min} - {$this->temperatura_procesamiento_max} °C";
        }
        return null;
    }

    /**
     * Obtener el rango de degradación como string formateado.
     */
    public function getRangoDegradacionAttribute(): ?string
    {
        if ($this->tiempo_degradacion_min && $this->tiempo_degradacion_max) {
            return "{$this->tiempo_degradacion_min} - {$this->tiempo_degradacion_max} días";
        }
        return null;
    }

    /**
     * Obtener si es un polímero biodegradable.
     */
    public function getEsBiodegradableAttribute(): bool
    {
        return $this->clasificacion === 'Polímero Biodegradable';
    }
}
