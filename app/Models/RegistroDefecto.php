<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroDefecto extends Model
{
    protected $table = 'registro_defectos';
    public $timestamps = false;

    protected $fillable = [
        'inspeccion_id',
        'defecto_id',
        'cantidad',
        'ubicacion_pieza',
        'imagen_evidencia',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function inspeccion(): BelongsTo
    {
        return $this->belongsTo(InspeccionCalidad::class, 'inspeccion_id');
    }

    public function defecto(): BelongsTo
    {
        return $this->belongsTo(DefectoCalidad::class, 'defecto_id');
    }
}
