<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiDiario extends Model
{
    protected $table = 'kpis_diarios';
    public $timestamps = false;

    protected $fillable = [
        'fecha', 'maquina_id', 'turno_id',
        'unidades_planificadas', 'unidades_producidas',
        'unidades_conformes', 'unidades_defectuosas',
        'disponibilidad', 'rendimiento', 'calidad', 'oee',
    ];

    protected $casts = [
        'fecha' => 'date',
        'disponibilidad' => 'decimal:2',
        'rendimiento' => 'decimal:2',
        'calidad' => 'decimal:2',
        'oee' => 'decimal:2',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }
}
