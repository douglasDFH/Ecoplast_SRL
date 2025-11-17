<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Insumo - Materias Primas Biodegradables
 *
 * Gestiona insumos biodegradables (PLA, PHA, PBS, PBAT, Almidón, etc.)
 * con certificaciones y trazabilidad completa.
 *
 * @property int $id
 * @property string $codigo_insumo
 * @property string $nombre_insumo
 * @property int $categoria_id
 * @property int|null $tipo_material_id FK a tipos_materiales
 * @property string $tipo_material ENUM: PLA, PHA, PBS, PBAT, Almidon, Celulosa, Aditivo, Pigmento, Otro (deprecado, usar tipo_material_id)
 * @property string $unidad_medida ENUM: kg, ton, litro, unidad
 * @property float|null $densidad g/cm³
 * @property float|null $temperatura_fusion °C
 * @property string|null $certificacion_biodegradable Ej: EN 13432, ASTM D6400
 * @property string|null $proveedor
 * @property float $precio_unitario
 * @property float $stock_minimo
 * @property float $stock_actual
 * @property \Carbon\Carbon|null $fecha_caducidad_lote
 * @property bool $activo
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Insumo extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'insumos';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'codigo_insumo',
        'nombre_insumo',
        'descripcion',
        'categoria_id',
        'tipo_material_id',
        'tipo_material', // Mantener temporalmente para compatibilidad
        'unidad_medida',
        'densidad',
        'temperatura_fusion',
        'certificacion_biodegradable',
        'proveedor',
        'precio_unitario',
        'stock_minimo',
        'stock_actual',
        'fecha_caducidad_lote',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'densidad' => 'decimal:3',
        'temperatura_fusion' => 'decimal:1',
        'precio_unitario' => 'decimal:2',
        'fecha_caducidad_lote' => 'date',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Valores por defecto de los atributos.
     */
    protected $attributes = [
        'stock_actual' => 0,
        'activo' => true,
        'unidad_medida' => 'kg',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Categoría a la que pertenece este insumo.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaInsumo::class, 'categoria_id');
    }

    /**
     * Tipo de material de este insumo.
     */
    public function tipoMaterial(): BelongsTo
    {
        return $this->belongsTo(TipoMaterial::class, 'tipo_material_id');
    }

    /**
     * Movimientos de inventario de este insumo.
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventarioInsumo::class, 'insumo_id');
    }

    /**
     * Formulaciones en las que participa este insumo.
     * Relación many-to-many a través de componentes_formulacion.
     */
    public function formulaciones(): HasMany
    {
        return $this->hasMany(ComponenteFormulacion::class, 'insumo_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para insumos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para insumos con stock bajo.
     */
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<', 'stock_minimo')
                    ->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo de material.
     */
    public function scopePorTipoMaterial($query, string $tipo)
    {
        return $query->where('tipo_material', $tipo);
    }

    /**
     * Scope para insumos biodegradables.
     */
    public function scopeBiodegradables($query)
    {
        return $query->whereIn('tipo_material', ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa']);
    }

    /**
     * Scope para insumos certificados.
     */
    public function scopeCertificados($query)
    {
        return $query->whereNotNull('certificacion_biodegradable');
    }

    /**
     * Scope para insumos próximos a caducar.
     */
    public function scopeProximosACaducar($query, int $dias = 30)
    {
        return $query->whereNotNull('fecha_caducidad_lote')
                    ->where('fecha_caducidad_lote', '<=', now()->addDays($dias))
                    ->where('fecha_caducidad_lote', '>', now());
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si el stock está por debajo del mínimo.
     */
    public function verificarStockBajo(): bool
    {
        return $this->stock_actual < $this->stock_minimo;
    }

    /**
     * Reserva una cantidad de insumo (no resta del stock, solo valida).
     */
    public function reservar(float $cantidad): bool
    {
        if ($this->stock_actual >= $cantidad) {
            return true;
        }

        throw new \Exception("Stock insuficiente de {$this->nombre_insumo}. Disponible: {$this->stock_actual}, Solicitado: {$cantidad}");
    }

    /**
     * Consume una cantidad de insumo (resta del stock).
     */
    public function consumir(float $cantidad, int $usuarioId, string $motivo = 'Consumo en producción'): void
    {
        if ($this->stock_actual < $cantidad) {
            throw new \Exception("Stock insuficiente de {$this->nombre_insumo}");
        }

        // El Observer se encargará de actualizar el stock
        MovimientoInventarioInsumo::create([
            'insumo_id' => $this->id,
            'tipo_movimiento' => 'salida',
            'cantidad' => $cantidad,
            'usuario_id' => $usuarioId,
            'motivo' => $motivo,
        ]);
    }

    /**
     * Ajusta el inventario manualmente.
     */
    public function ajustarInventario(float $cantidadNueva, int $usuarioId, string $motivo): void
    {
        MovimientoInventarioInsumo::create([
            'insumo_id' => $this->id,
            'tipo_movimiento' => 'ajuste',
            'cantidad' => $cantidadNueva,
            'usuario_id' => $usuarioId,
            'motivo' => $motivo,
        ]);
    }

    /**
     * Obtiene el precio actual del insumo.
     */
    public function getPrecioActual(): float
    {
        return $this->precio_unitario;
    }

    /**
     * Verifica si es un material biodegradable.
     */
    public function esBiodegradable(): bool
    {
        // Usar la relación si existe
        if ($this->tipo_material_id && $this->relationLoaded('tipoMaterial')) {
            return $this->tipoMaterial->clasificacion === 'Polímero Biodegradable';
        }

        // Fallback al ENUM para compatibilidad
        return in_array($this->tipo_material, ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa']);
    }

    /**
     * Verifica si tiene certificación de biodegradabilidad.
     */
    public function tieneCertificacion(): bool
    {
        return !empty($this->certificacion_biodegradable);
    }

    /**
     * Verifica si el lote está caducado.
     */
    public function estaCaducado(): bool
    {
        if (!$this->fecha_caducidad_lote) {
            return false;
        }

        return now()->greaterThan($this->fecha_caducidad_lote);
    }

    /**
     * Calcula el valor del inventario actual.
     */
    public function getValorInventario(): float
    {
        return $this->stock_actual * $this->precio_unitario;
    }

    /**
     * Obtiene el porcentaje de stock disponible.
     */
    public function getPorcentajeStock(): float
    {
        if ($this->stock_minimo == 0) {
            return 100;
        }

        return ($this->stock_actual / $this->stock_minimo) * 100;
    }

    /**
     * Obtiene el estado del stock (crítico, bajo, normal, alto).
     */
    public function getEstadoStock(): string
    {
        $porcentaje = $this->getPorcentajeStock();

        if ($porcentaje < 50) {
            return 'crítico';
        } elseif ($porcentaje < 100) {
            return 'bajo';
        } elseif ($porcentaje >= 100 && $porcentaje < 150) {
            return 'normal';
        } else {
            return 'alto';
        }
    }

    /**
     * Genera alerta automática si el stock está bajo.
     */
    public function generarAlertaStockBajo(): void
    {
        if ($this->verificarStockBajo()) {
            Alerta::create([
                'tipo_alerta' => 'stock_bajo',
                'severidad' => $this->getPorcentajeStock() < 50 ? 'critico' : 'advertencia',
                'titulo' => "Stock bajo: {$this->nombre_insumo}",
                'mensaje' => "El insumo {$this->nombre_insumo} tiene stock bajo. Actual: {$this->stock_actual} {$this->unidad_medida}. Mínimo: {$this->stock_minimo} {$this->unidad_medida}",
                'entidad_tipo' => 'insumo',
                'entidad_id' => $this->id,
            ]);
        }
    }
}
