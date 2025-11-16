<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Maquina - Maquinaria Industrial
 *
 * Gestiona máquinas de producción de plásticos biodegradables
 * (Extrusoras, Inyectoras, Termoformadoras, etc.)
 * con parámetros técnicos completos para cálculo de OEE.
 *
 * @property int $id
 * @property string $codigo_maquina
 * @property string $nombre_maquina
 * @property int $tipo_maquina_id
 * @property string|null $marca
 * @property string|null $modelo
 * @property int|null $año_fabricacion
 * @property float|null $capacidad_produccion unidades o kg por hora
 * @property string $unidad_capacidad
 * @property float|null $consumo_energia_kwh
 * @property float|null $temp_min_operacion °C
 * @property float|null $temp_max_operacion °C
 * @property float|null $presion_max_bar Bar
 * @property float|null $velocidad_max_rpm RPM
 * @property float|null $fuerza_cierre_ton Para inyectoras
 * @property float|null $diametro_husillo_mm Para extrusoras
 * @property \Carbon\Carbon|null $fecha_instalacion
 * @property int $vida_util_años
 * @property string|null $ubicacion
 * @property string $estado_actual ENUM: operativa, mantenimiento, parada, averia
 * @property bool $activo
 */
class Maquina extends Model
{
    use HasFactory;

    protected $table = 'maquinas';

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

    protected $attributes = [
        'activo' => true,
        'estado_actual' => 'operativa',
        'vida_util_años' => 15,
        'unidad_capacidad' => 'unidades/hora',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquina_id');
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'maquina_id');
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'maquina_id');
    }

    public function paros(): HasMany
    {
        return $this->hasMany(ParadaProduccion::class, 'maquina_id');
    }

    public function registros(): HasMany
    {
        return $this->hasMany(RegistroProduccion::class, 'maquina_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOperativas($query)
    {
        return $query->where('estado_actual', 'operativa');
    }

    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado_actual', 'mantenimiento');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado_actual', 'operativa')
                    ->where('activo', true);
    }

    public function estaDisponible(): bool
    {
        return $this->estado_actual === 'operativa' && $this->activo;
    }

    public function necesitaMantenimiento(): bool
    {
        $ultimoMantenimiento = $this->mantenimientos()
                                   ->where('estado', 'completado')
                                   ->orderBy('fecha_fin', 'desc')
                                   ->first();

        if (!$ultimoMantenimiento) {
            return true;
        }

        $diasDesdeUltimo = now()->diffInDays($ultimoMantenimiento->fecha_fin);

        return $diasDesdeUltimo >= 30;
    }

    public function calcularOEE(\Carbon\Carbon $fecha = null): array
    {
        $fecha = $fecha ?? now()->toDateString();

        $kpi = KpiDiario::where('maquina_id', $this->id)
                       ->where('fecha', $fecha)
                       ->first();

        if (!$kpi) {
            return [
                'disponibilidad' => 0,
                'rendimiento' => 0,
                'calidad' => 0,
                'oee' => 0,
            ];
        }

        return [
            'disponibilidad' => $kpi->disponibilidad,
            'rendimiento' => $kpi->rendimiento,
            'calidad' => $kpi->calidad,
            'oee' => $kpi->oee,
        ];
    }

    public function getHorasOperacion(\Carbon\Carbon $desde = null, \Carbon\Carbon $hasta = null): float
    {
        $desde = $desde ?? now()->subDays(30);
        $hasta = $hasta ?? now();

        $minutosOperacion = RegistroProduccion::where('maquina_id', $this->id)
                                             ->whereBetween('fecha_hora', [$desde, $hasta])
                                             ->count() * 60;

        return $minutosOperacion / 60;
    }

    public function bloquear(string $motivo = 'Mantenimiento programado'): void
    {
        $this->estado_actual = 'mantenimiento';
        $this->save();

        event(new \App\Events\MaquinaBloqueada($this, $motivo));
    }

    public function liberar(): void
    {
        $this->estado_actual = 'operativa';
        $this->save();

        event(new \App\Events\MaquinaLiberada($this));
    }

    public function validarParametro(string $parametro, float $valor): bool
    {
        return match($parametro) {
            'temperatura' => $valor >= $this->temp_min_operacion && $valor <= $this->temp_max_operacion,
            'presion' => $valor <= $this->presion_max_bar,
            'velocidad' => $valor <= $this->velocidad_max_rpm,
            default => true,
        };
    }

    public function calcularEficienciaEnergetica(): float
    {
        if (!$this->consumo_energia_kwh || !$this->capacidad_produccion) {
            return 0;
        }

        return ($this->capacidad_produccion / $this->consumo_energia_kwh) * 100;
    }

    public function getUltimoMantenimiento()
    {
        return $this->mantenimientos()
                   ->orderBy('fecha_fin', 'desc')
                   ->first();
    }

    public function getEdadAños(): int
    {
        if (!$this->fecha_instalacion) {
            return 0;
        }

        return now()->diffInYears($this->fecha_instalacion);
    }

    public function getVidaUtilRestante(): int
    {
        return max(0, $this->vida_util_años - $this->getEdadAños());
    }
}
