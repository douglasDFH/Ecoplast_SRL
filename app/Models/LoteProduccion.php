<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Modelo LoteProduccion
 *
 * Representa un lote de producción con trazabilidad completa.
 * Cada lote tiene un número único y estado de aprobación.
 *
 * @property int $id
 * @property string $numero_lote
 * @property int $orden_id
 * @property int $cantidad
 * @property Carbon $fecha_fabricacion
 * @property Carbon $fecha_vencimiento
 * @property array|null $trazabilidad_insumos JSON con lotes de insumos
 * @property string $estado_lote (cuarentena, aprobado, rechazado, distribuido)
 * @property string|null $ubicacion_almacen
 */
class LoteProduccion extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'lotes_produccion';

    /**
     * Indica si el modelo debe usar timestamps.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'numero_lote',
        'orden_id',
        'cantidad',
        'fecha_fabricacion',
        'fecha_vencimiento',
        'trazabilidad_insumos',
        'estado_lote',
        'ubicacion_almacen',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_fabricacion' => 'datetime',
        'fecha_vencimiento' => 'date',
        'trazabilidad_insumos' => 'array',
        'cantidad' => 'integer',
    ];

    /**
     * Valores por defecto de los atributos.
     */
    protected $attributes = [
        'estado_lote' => 'cuarentena',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Orden de producción que generó este lote.
     */
    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class, 'orden_id');
    }

    /**
     * Inspecciones de calidad realizadas a este lote.
     */
    public function inspecciones(): HasMany
    {
        return $this->hasMany(InspeccionCalidad::class, 'lote_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para lotes en cuarentena.
     */
    public function scopeEnCuarentena($query)
    {
        return $query->where('estado_lote', 'cuarentena');
    }

    /**
     * Scope para lotes aprobados.
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado_lote', 'aprobado');
    }

    /**
     * Scope para lotes rechazados.
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado_lote', 'rechazado');
    }

    /**
     * Scope para lotes próximos a vencer.
     */
    public function scopeProximosAVencer($query, int $dias = 30)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('fecha_vencimiento', '>', now())
                    ->where('estado_lote', 'aprobado');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    /**
     * Aprueba el lote y actualiza el stock de producto.
     *
     * @return void
     */
    public function aprobar(): void
    {
        $this->estado_lote = 'aprobado';
        $this->save();

        // Trigger actualizará el stock del producto
        event(new \App\Events\LoteAprobado($this));
    }

    /**
     * Rechaza el lote.
     *
     * @return void
     */
    public function rechazar(): void
    {
        $this->estado_lote = 'rechazado';
        $this->save();

        event(new \App\Events\LoteRechazado($this));
    }

    /**
     * Obtiene la trazabilidad completa del lote.
     *
     * @return array
     */
    public function getTrazabilidad(): array
    {
        return $this->trazabilidad_insumos ?? [];
    }

    /**
     * Genera número de certificado del lote.
     *
     * @return string
     */
    public function generarCertificado(): string
    {
        return "CERT-ECO-{$this->numero_lote}-" . now()->format('Ymd');
    }

    /**
     * Envía el lote a cuarentena.
     *
     * @return void
     */
    public function enviarACuarentena(): void
    {
        $this->estado_lote = 'cuarentena';
        $this->save();
    }

    /**
     * Calcula días de vida útil restantes.
     *
     * @return int
     */
    public function calcularVidaUtil(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    /**
     * Verifica si el lote está vencido.
     *
     * @return bool
     */
    public function estaVencido(): bool
    {
        return now()->greaterThan($this->fecha_vencimiento);
    }

    /**
     * Verifica condiciones del lote.
     *
     * @return array
     */
    public function verificarCondiciones(): array
    {
        return [
            'vencido' => $this->estaVencido(),
            'dias_restantes' => $this->calcularVidaUtil(),
            'aprobado' => $this->estado_lote === 'aprobado',
            'tiene_inspecciones' => $this->inspecciones()->exists(),
        ];
    }

    /**
     * Genera número de lote automático.
     *
     * @param int $ordenId
     * @return string
     */
    public static function generarNumeroLote(int $ordenId): string
    {
        $fecha = now()->format('Ymd');
        $secuencia = static::where('orden_id', $ordenId)->count() + 1;
        return "LOTE-{$ordenId}-{$fecha}-" . str_pad($secuencia, 3, '0', STR_PAD_LEFT);
    }
}
