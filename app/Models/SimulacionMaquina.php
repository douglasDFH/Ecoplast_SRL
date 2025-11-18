<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulacionMaquina extends Model
{
    use HasFactory;

    protected $table = 'simulaciones_maquinas';

    protected $fillable = [
        'maquina_id',
        'orden_produccion_id',
        'estado_simulacion',
        'unidades_producidas',
        'unidades_conformes',
        'unidades_defectuosas',
        'porcentaje_progreso',
        'temperatura_zona1',
        'temperatura_zona2',
        'temperatura_zona3',
        'temperatura_zona4',
        'presion_actual',
        'velocidad_husillo_actual',
        'tiempo_ciclo_actual',
        'consumo_energia_acumulado',
        'tasa_defectos',
        'inicio_simulacion',
        'fin_simulacion',
        'tiempo_transcurrido_segundos',
        'ultimo_ciclo',
        'eficiencia_actual',
        'ciclos_completados',
    ];

    protected $casts = [
        'unidades_producidas' => 'integer',
        'unidades_conformes' => 'integer',
        'unidades_defectuosas' => 'integer',
        'porcentaje_progreso' => 'decimal:2',
        'temperatura_zona1' => 'decimal:1',
        'temperatura_zona2' => 'decimal:1',
        'temperatura_zona3' => 'decimal:1',
        'temperatura_zona4' => 'decimal:1',
        'presion_actual' => 'decimal:2',
        'velocidad_husillo_actual' => 'decimal:2',
        'tiempo_ciclo_actual' => 'decimal:2',
        'consumo_energia_acumulado' => 'decimal:3',
        'tasa_defectos' => 'decimal:2',
        'eficiencia_actual' => 'decimal:2',
        'ciclos_completados' => 'integer',
        'tiempo_transcurrido_segundos' => 'integer',
        'inicio_simulacion' => 'datetime',
        'fin_simulacion' => 'datetime',
        'ultimo_ciclo' => 'datetime',
    ];

    // Relaciones
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_produccion_id');
    }

    // Scopes
    public function scopeProduciendo($query)
    {
        return $query->where('estado_simulacion', 'produciendo');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado_simulacion', ['produciendo', 'pausada']);
    }
}
