<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Turno extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'turnos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_turno',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activo' => 'boolean',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
    ];

    /**
     * Un turno puede tener muchas asignaciones.
     */
    public function asignacionesTurno()
    {
        return $this->hasMany(AsignacionTurno::class);
    }

    /**
     * Un turno tiene muchas órdenes de producción.
     */
    public function ordenesProduccion()
    {
        return $this->hasMany(OrdenProduccion::class);
    }

    /**
     * Calcula la duración del turno en horas como un atributo.
     *
     * @return float
     */
    public function getDuracionHorasAttribute()
    {
        $inicio = Carbon::parse($this->hora_inicio);
        $fin = Carbon::parse($this->hora_fin);

        // Maneja turnos que cruzan la medianoche
        if ($fin->lessThan($inicio)) {
            $fin->addDay();
        }

        return round($fin->diffInMinutes($inicio) / 60.0, 2);
    }
}
