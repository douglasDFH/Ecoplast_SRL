<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroProduccion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registros_produccion';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orden_id',
        'maquina_id',
        'operador_id',
        'fecha_hora',
        'piezas_producidas',
        'piezas_conformes',
        'piezas_defectuosas',
        'tipo_defecto',
        'temperatura_zona1',
        'temperatura_zona2',
        'temperatura_zona3',
        'temperatura_zona4',
        'presion_inyeccion',
        'velocidad_husillo',
        'tiempo_ciclo_real',
        'consumo_energia_kwh',
        'consumo_material_kg',
        'scrap_kg',
        'observaciones',
        'alerta_calidad',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_hora' => 'datetime',
        'piezas_producidas' => 'integer',
        'piezas_conformes' => 'integer',
        'piezas_defectuosas' => 'integer',
        'alerta_calidad' => 'boolean',
        'temperatura_zona1' => 'decimal:1',
        'temperatura_zona2' => 'decimal:1',
        'temperatura_zona3' => 'decimal:1',
        'temperatura_zona4' => 'decimal:1',
        'presion_inyeccion' => 'decimal:2',
        'velocidad_husillo' => 'decimal:2',
        'tiempo_ciclo_real' => 'decimal:2',
        'consumo_energia_kwh' => 'decimal:3',
        'consumo_material_kg' => 'decimal:3',
        'scrap_kg' => 'decimal:3',
    ];

    /**
     * Relación: Un registro de producción pertenece a una orden de producción.
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_id');
    }

    /**
     * Relación: Un registro tiene una máquina.
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    /**
     * Relación: Un registro de producción es realizado por un operador (usuario).
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }
}
