<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    // Listar roles
    public function indexRoles()
    {
        return response()->json(Role::with('permissions')->get());
    }

    // Crear rol
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'sometimes|string',
        ]);
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);
        return response()->json($role, 201);
    }

    // Editar rol
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);
        $role->name = $request->name;
        $role->save();
        return response()->json($role);
    }

    // Eliminar rol
    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Rol eliminado']);
    }

    // Listar permisos
    public function indexPermissions()
    {
        return response()->json(Permission::all());
    }

    // Crear permiso
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'guard_name' => 'sometimes|string',
        ]);
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);
        return response()->json($permission, 201);
    }

    // Asignar permisos a un rol
    public function assignPermissionsToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        $role->syncPermissions($request->permissions);
        return response()->json(['message' => 'Permisos asignados']);
    }
}
