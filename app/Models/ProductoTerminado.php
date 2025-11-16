<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductoTerminado extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacidad_carga_kg' => 'decimal:2',
        'peso_unitario_gramos' => 'decimal:2',
        'temperatura_proceso' => 'decimal:1',
        'precio_venta' => 'decimal:2',
        'stock_minimo' => 'integer',
        'stock_actual' => 'integer',
        'tiempo_compostaje_dias' => 'integer',
        'espesor_micras' => 'integer',
        'tiempo_ciclo_segundos' => 'integer',
        'piezas_por_ciclo' => 'integer',
        'unidades_por_paquete' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un producto terminado pertenece a una categoría.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    /**
     * Relación: Un producto terminado puede tener una formulación asociada.
     */
    public function formulacion(): BelongsTo
    {
        return $this->belongsTo(Formulacion::class, 'formulacion_id');
    }

    /**
     * Relación: Un producto terminado tiene muchos movimientos de inventario.
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventarioProducto::class, 'producto_id');
    }

    /**
     * Relación: Un producto terminado puede tener muchas órdenes de producción.
     */
    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'producto_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Solo productos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Solo productos certificados compostables.
     */
    public function scopeCertificados($query)
    {
        return $query->whereNotNull('certificacion_compostable');
    }

    /**
     * Scope: Filtrar por material principal.
     */
    public function scopePorMaterial($query, $material)
    {
        return $query->where('material_principal', $material);
    }

    // ==================== MÉTODOS DE AYUDA ====================

    /**
     * Verificar si el producto tiene certificación compostable.
     */
    public function esCertificado(): bool
    {
        return !is_null($this->certificacion_compostable);
    }

    /**
     * Obtener días de compostaje.
     */
    public function getDiasCompostaje(): ?int
    {
        return $this->tiempo_compostaje_dias;
    }

    /**
     * Verificar si el producto está por debajo del stock mínimo.
     */
    public function necesitaReposicion(): bool
    {
        return $this->stock_actual < $this->stock_minimo;
    }

    /**
     * Calcular costo por unidad basado en la formulación.
     */
    public function calcularCostoUnitario(): float
    {
        // Lógica simplificada - en producción debería calcularse desde la formulación
        return $this->precio_venta * 0.6; // Estimación básica
    }
}
