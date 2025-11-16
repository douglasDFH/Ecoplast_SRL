<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificacion extends Model
{
    protected $table = 'certificaciones';
    public $timestamps = false;

    protected $fillable = [
        'nombre_certificacion', 'tipo_certificacion',
        'numero_certificado', 'fecha_emision',
        'fecha_vencimiento', 'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function estaVigente(): bool
    {
        return $this->estado === 'vigente' && now()->lessThan($this->fecha_vencimiento);
    }
}
