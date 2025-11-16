<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Producto - Productos Biodegradables
 *
 * Gestiona productos biodegradables compostables con certificaciones
 * (EN 13432, ASTM D6400, OK Compost, Seedling, BPI, etc.)
 *
 * @property int $id
 * @property string $codigo_producto
 * @property string $nombre_producto
 * @property int $categoria_producto_id
 * @property string|null $descripcion
 * @property string $material_principal ENUM: PLA, PHA, PBS, PBAT, Almidon, Mixto
 * @property string|null $certificacion_compostable Ej: OK Compost, Seedling, BPI
 * @property int|null $tiempo_compostaje_dias
 * @property float|null $capacidad_carga_kg
 * @property float $peso_unitario_gramos
 * @property string|null $dimensiones Ej: 30x40cm, Diámetro 8cm
 * @property string $color
 * @property int|null $espesor_micras
 * @property int|null $formulacion_id
 * @property int|null $tiempo_ciclo_segundos
 * @property int $piezas_por_ciclo
 * @property float|null $temperatura_proceso °C
 * @property float $precio_venta
 * @property string $unidad_venta ENUM: unidad, paquete, caja, kg
 * @property int $unidades_por_paquete
 * @property int $stock_minimo
 * @property int $stock_actual
 * @property string|null $imagen_producto
 * @property bool $activo
 */
class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'codigo_producto',
        'nombre_producto',
        'categoria_producto_id',
        'descripcion',
        'material_principal',
        'certificacion_compostable',
        'tiempo_compostaje_dias',
        'capacidad_carga_kg',
        'peso_unitario_gramos',
        'dimensiones',
        'color',
        'espesor_micras',
        'formulacion_id',
        'tiempo_ciclo_segundos',
        'piezas_por_ciclo',
        'temperatura_proceso',
        'precio_venta',
        'unidad_venta',
        'unidades_por_paquete',
        'stock_minimo',
        'stock_actual',
        'imagen_producto',
        'activo',
    ];

    protected $casts = [
        'tiempo_compostaje_dias' => 'integer',
        'capacidad_carga_kg' => 'decimal:2',
        'peso_unitario_gramos' => 'decimal:2',
        'espesor_micras' => 'integer',
        'tiempo_ciclo_segundos' => 'integer',
        'piezas_por_ciclo' => 'integer',
        'temperatura_proceso' => 'decimal:1',
        'precio_venta' => 'decimal:2',
        'stock_minimo' => 'integer',
        'stock_actual' => 'integer',
        'activo' => 'boolean',
    ];

    protected $attributes = [
        'stock_actual' => 0,
        'activo' => true,
        'color' => 'natural',
        'piezas_por_ciclo' => 1,
        'unidad_venta' => 'unidad',
        'unidades_por_paquete' => 1,
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function formulacion(): BelongsTo
    {
        return $this->belongsTo(Formulacion::class, 'formulacion_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventarioProducto::class, 'producto_id');
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'producto_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<', 'stock_minimo')
                    ->where('activo', true);
    }

    public function scopeCertificados($query)
    {
        return $query->whereNotNull('certificacion_compostable');
    }

    public function scopeCompostables($query)
    {
        return $query->whereIn('material_principal', ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon']);
    }

    public function esCertificado(): bool
    {
        return !empty($this->certificacion_compostable);
    }

    public function esCompostableIndustrial(): bool
    {
        return in_array($this->material_principal, ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon']);
    }

    public function getCertificaciones(): array
    {
        if (!$this->certificacion_compostable) {
            return [];
        }

        return array_map('trim', explode(',', $this->certificacion_compostable));
    }

    public function calcularTiempoProduccion(int $cantidad): int
    {
        if (!$this->tiempo_ciclo_segundos) {
            return 0;
        }

        $ciclosNecesarios = ceil($cantidad / $this->piezas_por_ciclo);
        return $ciclosNecesarios * $this->tiempo_ciclo_segundos;
    }

    public function getPorcentajeStock(): float
    {
        if ($this->stock_minimo == 0) {
            return 100;
        }

        return ($this->stock_actual / $this->stock_minimo) * 100;
    }
}
