<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ComponenteFormulacion
 *
 * Representa los componentes (insumos) que forman parte de una formulación.
 * Tabla pivote con información adicional.
 *
 * @property int $id
 * @property int $formulacion_id
 * @property int $insumo_id
 * @property float $porcentaje Porcentaje en peso (%)
 * @property float $cantidad_base Cantidad para 100kg de mezcla
 * @property int $orden_adicion Orden de adición al proceso
 * @property string|null $notas
 */
class ComponenteFormulacion extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'componentes_formulacion';

    /**
     * Indica si el modelo debe usar timestamps.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'formulacion_id',
        'insumo_id',
        'porcentaje',
        'cantidad_base',
        'orden_adicion',
        'notas',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'porcentaje' => 'decimal:2',
        'cantidad_base' => 'decimal:3',
        'orden_adicion' => 'integer',
    ];

    /**
     * Valores por defecto de los atributos.
     */
    protected $attributes = [
        'orden_adicion' => 1,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Formulación a la que pertenece este componente.
     */
    public function formulacion(): BelongsTo
    {
        return $this->belongsTo(Formulacion::class);
    }

    /**
     * Insumo que forma parte de la formulación.
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula la cantidad necesaria para una cantidad específica de producto.
     *
     * @param float $cantidadTotal Cantidad total de producto a fabricar (kg)
     * @return float Cantidad necesaria de este insumo
     */
    public function calcularCantidadNecesaria(float $cantidadTotal): float
    {
        return ($cantidadTotal * $this->porcentaje) / 100;
    }

    /**
     * Verifica si el porcentaje está dentro del rango válido.
     *
     * @return bool
     */
    public function porcentajeValido(): bool
    {
        return $this->porcentaje > 0 && $this->porcentaje <= 100;
    }

    /**
     * Obtiene el nombre del insumo.
     *
     * @return string
     */
    public function getNombreInsumo(): string
    {
        return $this->insumo->nombre ?? 'Desconocido';
    }

    /**
     * Verifica si es un componente principal (>50%).
     *
     * @return bool
     */
    public function esComponentePrincipal(): bool
    {
        return $this->porcentaje > 50;
    }
}
