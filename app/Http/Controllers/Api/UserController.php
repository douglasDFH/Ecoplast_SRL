<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->get()->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'nombre_completo' => $usuario->nombre_completo,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
                'dni' => $usuario->dni,
                'direccion' => $usuario->direccion,
                'activo' => $usuario->activo,
                'ultimo_acceso' => $usuario->ultimo_acceso,
                'foto_perfil' => $usuario->foto_perfil,
                'roles' => $usuario->roles->map(function ($rol) {
                    return [
                        'id' => $rol->id,
                        'name' => $rol->name,
                        'guard_name' => $rol->guard_name,
                    ];
                }),
                'created_at' => $usuario->created_at,
                'updated_at' => $usuario->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $usuarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'telefono' => 'nullable|string|max:20',
            'dni' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:spatie_roles,id',
            'turno_id' => 'nullable|exists:turnos,id',
            'activo' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $usuario = User::create([
                'nombre_completo' => $request->nombre_completo,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'dni' => $request->dni,
                'direccion' => $request->direccion,
                'turno_id' => $request->turno_id,
                'activo' => $request->activo ?? true,
            ]);

            // Asignar roles usando Spatie
            $usuario->roles()->attach($request->roles);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario->load('roles')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
            'dni' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:spatie_roles,id',
            'turno_id' => 'nullable|exists:turnos,id',
            'activo' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $usuario->update([
                'nombre_completo' => $request->nombre_completo,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'dni' => $request->dni,
                'direccion' => $request->direccion,
                'turno_id' => $request->turno_id,
                'activo' => $request->activo ?? $usuario->activo,
            ]);

            // Sincronizar roles usando Spatie
            $usuario->roles()->sync($request->roles);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $usuario->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $usuario)
    {
        try {
            // Remover todos los roles antes de eliminar
            $usuario->roles()->detach();
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request, User $usuario)
    {
        $request->validate([
            'password' => 'required|string|min:6'
        ]);

        try {
            $usuario->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar contraseña: ' . $e->getMessage()
            ], 500);
        }
    }
}
