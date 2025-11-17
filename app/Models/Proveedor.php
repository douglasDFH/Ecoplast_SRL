<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo_proveedor',
        'nombre_comercial',
        'razon_social',
        'ruc',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'pais',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Un proveedor tiene muchos insumos
     */
    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class, 'proveedor_id');
    }

    /**
     * Scope: Obtener solo proveedores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Ordenar por nombre comercial
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('nombre_comercial', 'asc');
    }

    /**
     * Scope: Buscar por nombre, RUC o código
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre_comercial', 'like', "%{$termino}%")
              ->orWhere('razon_social', 'like', "%{$termino}%")
              ->orWhere('codigo_proveedor', 'like', "%{$termino}%")
              ->orWhere('ruc', 'like', "%{$termino}%");
        });
    }

    /**
     * Accessor: Nombre completo del proveedor
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->razon_social ?: $this->nombre_comercial;
    }

    /**
     * Verificar si el proveedor tiene insumos asociados
     */
    public function tieneInsumos(): bool
    {
        return $this->insumos()->count() > 0;
    }
}
