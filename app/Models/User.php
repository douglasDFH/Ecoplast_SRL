<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use Carbon\Carbon;
use App\Models\AsignacionTurno;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\InspeccionCalidad;
use App\Models\Mantenimiento;
use App\Models\Alerta;
use App\Models\MovimientoInventarioInsumo;
use App\Models\MovimientoInventarioProducto;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_completo',
        'email',
        'password',
        'telefono',
        'foto_perfil',
        'activo',
        'ultimo_acceso',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==================== RELACIONES ====================

    /**
     * Relación: Usuario tiene muchas asignaciones de turno
     */
    public function asignacionesTurno()
    {
        return $this->hasMany(AsignacionTurno::class, 'usuario_id');
    }

    /**
     * Relación: Órdenes creadas por el usuario
     */
    public function ordenesCreadas()
    {
        return $this->hasMany(OrdenProduccion::class, 'creado_por');
    }

    /**
     * Relación: Órdenes donde es operador
     */
    public function ordenesComoOperador()
    {
        return $this->hasMany(OrdenProduccion::class, 'operador_id');
    }

    /**
     * Relación: Órdenes donde es supervisor
     */
    public function ordenesComoSupervisor()
    {
        return $this->hasMany(OrdenProduccion::class, 'supervisor_id');
    }

    /**
     * Relación: Registros de producción
     */
    public function registrosProduccion()
    {
        return $this->hasMany(RegistroProduccion::class, 'operador_id');
    }

    /**
     * Relación: Inspecciones realizadas
     */
    public function inspeccionesCalidad()
    {
        return $this->hasMany(InspeccionCalidad::class, 'inspector_id');
    }

    /**
     * Relación: Mantenimientos realizados
     */
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'tecnico_id');
    }

    /**
     * Relación: Alertas recibidas
     */
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'usuario_destino_id');
    }

    /**
     * Relación: Alertas no leídas
     */
    public function alertasNoLeidas()
    {
        return $this->hasMany(Alerta::class, 'usuario_destino_id')
            ->where('leida', false)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relación: Movimientos de inventario de insumos
     */
    public function movimientosInsumos()
    {
        return $this->hasMany(MovimientoInventarioInsumo::class, 'usuario_id');
    }

    /**
     * Relación: Movimientos de inventario de productos
     */
    public function movimientosProductos()
    {
        return $this->hasMany(MovimientoInventarioProducto::class, 'usuario_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Solo usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Filtrar por rol
     */
    public function scopeConRol($query, $rolNombre)
    {
        return $query->whereHas('roles', function ($q) use ($rolNombre) {
            $q->where('name', $rolNombre);
        });
    }

    /**
     * Scope: Operadores activos
     */
    public function scopeOperadores($query)
    {
        return $query->activos()->conRol('Operador');
    }

    /**
     * Scope: Usuarios con turno asignado hoy
     */
    public function scopeConTurnoHoy($query)
    {
        return $query->whereHas('asignacionesTurno', function ($q) {
            $q->whereDate('fecha_asignacion', today());
        });
    }

    // ==================== MÉTODOS DE AYUDA ====================

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol($rolNombre): bool
    {
        return $this->hasRole($rolNombre);
    }

    /**
     * Verificar si es admin
     */
    public function esAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }

    /**
     * Verificar si es gerente
     */
    public function esGerente(): bool
    {
        return $this->hasRole('Gerente');
    }

    /**
     * Verificar si es operador
     */
    public function esOperador(): bool
    {
        return $this->hasRole('Operador');
    }

    /**
     * Obtener turno actual del usuario
     */
    public function getTurnoActual()
    {
        return $this->asignacionesTurno()
            ->whereDate('fecha_asignacion', today())
            ->with('turno')
            ->first();
    }

    /**
     * Actualizar último acceso
     */
    public function actualizarAcceso()
    {
        $this->update(['ultimo_acceso' => now()]);
    }

    /**
     * Contar alertas no leídas
     */
    public function contarAlertasNoLeidas(): int
    {
        return $this->alertasNoLeidas()->count();
    }

    // ==================== ATRIBUTOS DINÁMICOS ====================

    /**
     * Obtener nombre corto (primer nombre)
     */
    public function getNombreCortoAttribute(): string
    {
        return explode(' ', $this->nombre_completo)[0];
    }

    /**
     * Obtener iniciales
     */
    public function getInicialesAttribute(): string
    {
        $nombres = explode(' ', $this->nombre_completo);
        $iniciales = '';
        foreach (array_slice($nombres, 0, 2) as $nombre) {
            $iniciales .= strtoupper(substr($nombre, 0, 1));
        }
        return $iniciales;
    }

    /**
     * URL de foto de perfil o avatar por defecto
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }
        
        // Generar avatar con iniciales usando UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nombre_completo) . '&background=22c55e&color=fff';
    }
}
