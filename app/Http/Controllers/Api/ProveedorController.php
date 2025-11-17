<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    /**
     * Listar proveedores con filtros y paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = Proveedor::withCount('insumos');

        // Filtro por activo
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        // Filtro por ciudad
        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }

        // Filtro por país
        if ($request->filled('pais')) {
            $query->where('pais', $request->pais);
        }

        // Ordenamiento
        $query->ordenados();

        // Si se solicita 'all', retornar todos sin paginación
        if ($request->boolean('all')) {
            $proveedores = $query->get();
            return response()->json([
                'data' => $proveedores,
                'total' => $proveedores->count(),
            ]);
        }

        // Paginación
        $perPage = $request->input('per_page', 15);
        $proveedores = $query->paginate($perPage);

        return response()->json($proveedores);
    }

    /**
     * Crear un nuevo proveedor
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_proveedor' => 'required|string|max:20|unique:proveedores,codigo_proveedor',
            'nombre_comercial' => 'required|string|max:200',
            'razon_social' => 'nullable|string|max:200',
            'ruc' => 'nullable|string|max:20|unique:proveedores,ruc',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'codigo_proveedor.required' => 'El código del proveedor es obligatorio',
            'codigo_proveedor.unique' => 'El código del proveedor ya existe',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio',
            'ruc.unique' => 'El RUC ya está registrado',
            'email.email' => 'El email no tiene un formato válido',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor = Proveedor::create($request->all());

        return response()->json([
            'message' => 'Proveedor creado exitosamente',
            'data' => $proveedor->load('insumos')
        ], 201);
    }

    /**
     * Mostrar un proveedor específico
     */
    public function show(int $id): JsonResponse
    {
        $proveedor = Proveedor::withCount('insumos')
            ->with('insumos')
            ->findOrFail($id);

        return response()->json([
            'data' => $proveedor
        ]);
    }

    /**
     * Actualizar un proveedor
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo_proveedor' => 'required|string|max:20|unique:proveedores,codigo_proveedor,' . $id,
            'nombre_comercial' => 'required|string|max:200',
            'razon_social' => 'nullable|string|max:200',
            'ruc' => 'nullable|string|max:20|unique:proveedores,ruc,' . $id,
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'codigo_proveedor.required' => 'El código del proveedor es obligatorio',
            'codigo_proveedor.unique' => 'El código del proveedor ya existe',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio',
            'ruc.unique' => 'El RUC ya está registrado',
            'email.email' => 'El email no tiene un formato válido',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor->update($request->all());

        return response()->json([
            'message' => 'Proveedor actualizado exitosamente',
            'data' => $proveedor->load('insumos')
        ]);
    }

    /**
     * Eliminar un proveedor
     */
    public function destroy(int $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);

        // Verificar si tiene insumos asociados
        if ($proveedor->tieneInsumos()) {
            return response()->json([
                'message' => 'No se puede eliminar el proveedor porque tiene insumos asociados'
            ], 409);
        }

        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de proveedores
     */
    public function estadisticas(): JsonResponse
    {
        $stats = [
            'total' => Proveedor::count(),
            'activos' => Proveedor::activos()->count(),
            'inactivos' => Proveedor::where('activo', false)->count(),
            'con_insumos' => Proveedor::has('insumos')->count(),
            'por_pais' => Proveedor::selectRaw('pais, COUNT(*) as total')
                ->groupBy('pais')
                ->orderByDesc('total')
                ->get(),
            'por_ciudad' => Proveedor::selectRaw('ciudad, COUNT(*) as total')
                ->whereNotNull('ciudad')
                ->groupBy('ciudad')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Obtener proveedores por ciudad
     */
    public function porCiudad(string $ciudad): JsonResponse
    {
        $proveedores = Proveedor::where('ciudad', $ciudad)
            ->activos()
            ->ordenados()
            ->withCount('insumos')
            ->get();

        return response()->json([
            'data' => $proveedores,
            'total' => $proveedores->count(),
        ]);
    }

    /**
     * Obtener proveedores por país
     */
    public function porPais(string $pais): JsonResponse
    {
        $proveedores = Proveedor::where('pais', $pais)
            ->activos()
            ->ordenados()
            ->withCount('insumos')
            ->get();

        return response()->json([
            'data' => $proveedores,
            'total' => $proveedores->count(),
        ]);
    }

    /**
     * Alternar estado activo/inactivo
     */
    public function toggleActivo(int $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->activo = !$proveedor->activo;
        $proveedor->save();

        return response()->json([
            'message' => 'Estado del proveedor actualizado',
            'data' => $proveedor
        ]);
    }

    /**
     * Obtener insumos de un proveedor
     */
    public function insumos(int $id): JsonResponse
    {
        $proveedor = Proveedor::findOrFail($id);
        $insumos = $proveedor->insumos()
            ->with(['categoria', 'tipoMaterial'])
            ->activos()
            ->orderBy('nombre_insumo')
            ->get();

        return response()->json([
            'proveedor' => $proveedor->only(['id', 'codigo_proveedor', 'nombre_comercial']),
            'insumos' => $insumos,
            'total' => $insumos->count(),
        ]);
    }
}
