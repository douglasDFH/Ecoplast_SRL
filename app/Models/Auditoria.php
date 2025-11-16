<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditorias';
    public $timestamps = false;

    protected $fillable = [
        'tipo_auditoria', 'fecha_auditoria', 'auditor',
        'alcance', 'resultado', 'usuario_responsable_id',
    ];

    protected $casts = [
        'fecha_auditoria' => 'date',
    ];

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_responsable_id');
    }
}
