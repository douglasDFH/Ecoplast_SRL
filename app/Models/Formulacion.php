<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Formulacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formulaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'producto_terminado_id',
        'creado_por',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación: Una formulación pertenece a un producto terminado.
     */
    public function productoTerminado(): BelongsTo
    {
        return $this->belongsTo(ProductoTerminado::class, 'producto_terminado_id');
    }

    /**
     * Relación: Una formulación es creada por un usuario.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación: Una formulación tiene muchos insumos.
     */
    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'formulacion_insumo', 'formulacion_id', 'insumo_id')
                    ->withPivot('cantidad_necesaria')
                    ->withTimestamps();
    }

    /**
     * Scope: Solo formulaciones activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
