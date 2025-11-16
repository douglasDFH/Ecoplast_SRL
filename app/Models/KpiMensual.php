<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiMensual extends Model
{
    protected $table = 'kpis_mensuales';
    public $timestamps = false;

    protected $fillable = [
        'año', 'mes', 'maquina_id',
        'total_unidades_producidas', 'total_unidades_conformes',
        'oee_promedio', 'mtbf', 'mttr',
    ];

    protected $casts = [
        'oee_promedio' => 'decimal:2',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }
}
