<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo DefectoCalidad
 *
 * Catálogo de defectos de calidad con severidad.
 *
 * @property int $id
 * @property string $codigo_defecto
 * @property string $nombre_defecto
 * @property string|null $descripcion
 * @property string $severidad (critico, mayor, menor)
 * @property bool $activo
 */
class DefectoCalidad extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'defectos_calidad';

    /**
     * Indica si el modelo debe usar timestamps.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'codigo_defecto',
        'nombre_defecto',
        'descripcion',
        'severidad',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Valores por defecto de los atributos.
     */
    protected $attributes = [
        'activo' => true,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Registros de defectos que usan este tipo de defecto.
     */
    public function registros(): HasMany
    {
        return $this->hasMany(RegistroDefecto::class, 'defecto_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para defectos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para defectos por severidad.
     */
    public function scopePorSeveridad($query, string $severidad)
    {
        return $query->where('severidad', $severidad);
    }

    /**
     * Scope para defectos críticos.
     */
    public function scopeCriticos($query)
    {
        return $query->where('severidad', 'critico');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si el defecto es crítico.
     *
     * @return bool
     */
    public function esCritico(): bool
    {
        return $this->severidad === 'critico';
    }

    /**
     * Obtiene el peso numérico de la severidad.
     *
     * @return int
     */
    public function getPesoSeveridad(): int
    {
        return match($this->severidad) {
            'critico' => 3,
            'mayor' => 2,
            'menor' => 1,
            default => 0,
        };
    }

    /**
     * Cuenta cuántas veces se ha registrado este defecto.
     *
     * @return int
     */
    public function contarOcurrencias(): int
    {
        return $this->registros()->sum('cantidad');
    }

    /**
     * Obtiene el color asociado a la severidad.
     *
     * @return string
     */
    public function getColorSeveridad(): string
    {
        return match($this->severidad) {
            'critico' => '#ef4444', // rojo
            'mayor' => '#f59e0b',   // naranja
            'menor' => '#eab308',   // amarillo
            default => '#6b7280',   // gris
        };
    }

    /**
     * Desactiva el defecto.
     *
     * @return void
     */
    public function desactivar(): void
    {
        $this->activo = false;
        $this->save();
    }

    /**
     * Activa el defecto.
     *
     * @return void
     */
    public function activar(): void
    {
        $this->activo = true;
        $this->save();
    }
}
