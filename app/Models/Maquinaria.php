<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maquinaria extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'maquinas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo_maquina',
        'nombre_maquina',
        'tipo_maquina_id',
        'marca',
        'modelo',
        'año_fabricacion',
        'capacidad_produccion',
        'unidad_capacidad',
        'consumo_energia_kwh',
        'temp_min_operacion',
        'temp_max_operacion',
        'presion_max_bar',
        'velocidad_max_rpm',
        'fuerza_cierre_ton',
        'diametro_husillo_mm',
        'fecha_instalacion',
        'vida_util_años',
        'ubicacion',
        'estado_actual',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'año_fabricacion' => 'integer',
        'capacidad_produccion' => 'decimal:2',
        'consumo_energia_kwh' => 'decimal:2',
        'temp_min_operacion' => 'decimal:1',
        'temp_max_operacion' => 'decimal:1',
        'presion_max_bar' => 'decimal:2',
        'velocidad_max_rpm' => 'decimal:2',
        'fuerza_cierre_ton' => 'decimal:2',
        'diametro_husillo_mm' => 'decimal:2',
        'fecha_instalacion' => 'date',
        'vida_util_años' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Una máquina pertenece a un tipo de maquinaria.
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquina_id');
    }

    /**
     * Relación: Una máquina tiene muchos registros de mantenimiento.
     */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'maquina_id');
    }

    /**
     * Relación: Una máquina puede estar asignada a muchas órdenes de producción.
     */
    public function ordenesProduccion(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'maquina_id');
    }

    /**
     * Scope: Solo máquinas operativas.
     */
    public function scopeOperativas($query)
    {
        return $query->where('estado_actual', 'operativa');
    }

    /**
     * Scope: Solo máquinas en mantenimiento.
     */
    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado_actual', 'mantenimiento');
    }

    /**
     * Scope: Solo máquinas fuera de servicio.
     */
    public function scopeFueraDeServicio($query)
    {
        return $query->where('estado_actual', 'parada');
    }

    /**
     * Scope: Solo máquinas averiadas.
     */
    public function scopeAveriadas($query)
    {
        return $query->where('estado_actual', 'averia');
    }

    /**
     * Scope: Solo máquinas activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // ==================== MÉTODOS DE AYUDA ====================

    /**
     * Calcular OEE (Overall Equipment Effectiveness) - Eficacia General del Equipo.
     * OEE = Disponibilidad × Rendimiento × Calidad
     */
    public function calcularOEE(): float
    {
        // Lógica simplificada - en producción debería calcularse con datos reales
        // OEE = (Tiempo productivo / Tiempo disponible) × (Unidades producidas / Unidades planificadas) × (Unidades buenas / Unidades producidas)

        // Por ahora, una estimación básica basada en el estado
        switch ($this->estado_actual) {
            case 'operativa':
                return 0.85; // 85% OEE típico
            case 'mantenimiento':
                return 0.70; // 70% durante mantenimiento
            case 'parada':
                return 0.50; // 50% cuando está parada
            case 'averia':
                return 0.20; // 20% cuando está averiada
            default:
                return 0.00;
        }
    }

    /**
     * Verificar si la máquina necesita mantenimiento preventivo.
     */
    public function necesitaMantenimiento(): bool
    {
        // Lógica simplificada - en producción debería basarse en horas de uso, calendario, etc.
        // Por ahora, asumimos que necesita mantenimiento si está en estado 'mantenimiento'
        return $this->estado_actual === 'mantenimiento';
    }

    /**
     * Calcular consumo energético por hora.
     */
    public function getConsumoEnergetico(): ?float
    {
        return $this->consumo_energia_kwh;
    }

    /**
     * Verificar si la máquina está dentro de los rangos operativos de temperatura.
     */
    public function estaEnRangoTemperatura(float $temperaturaActual): bool
    {
        if (!$this->temp_min_operacion || !$this->temp_max_operacion) {
            return true; // Si no hay límites definidos, asumimos que está bien
        }

        return $temperaturaActual >= $this->temp_min_operacion && $temperaturaActual <= $this->temp_max_operacion;
    }
}
