<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mantenimientos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'maquina_id',
        'tecnico_id',
        'tipo_mantenimiento', // 'Preventivo', 'Correctivo'
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'costo',
        'estado', // 'Programado', 'En Progreso', 'Completado', 'Cancelado'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'costo' => 'decimal:2',
    ];

    /**
     * Relación: Un mantenimiento pertenece a una máquina.
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    /**
     * Relación: Un mantenimiento es realizado por un técnico (usuario).
     */
    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    /**
     * Scope: Mantenimientos pendientes (programados).
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'Programado');
    }

    /**
     * Scope: Mantenimientos en progreso.
     */
    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'En Progreso');
    }

    /**
     * Scope: Mantenimientos completados.
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', 'Completado');
    }
}
