<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaInsumo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador API para Categorías de Insumos
 */
class CategoriaInsumoController extends Controller
{
    /**
     * Listar todas las categorías de insumos
     */
    public function index(Request $request): JsonResponse
    {
        $query = CategoriaInsumo::withCount('insumos');

        // Filtro por biodegradable
        if ($request->has('es_biodegradable')) {
            $query->where('es_biodegradable', $request->boolean('es_biodegradable'));
        }

        // Búsqueda
        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre_categoria', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $categorias = $query->orderBy('nombre_categoria', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    /**
     * Mostrar una categoría específica
     */
    public function show(int $id): JsonResponse
    {
        $categoria = CategoriaInsumo::with('insumos')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre_categoria' => 'required|string|max:100|unique:categorias_insumos,nombre_categoria',
            'descripcion' => 'nullable|string|max:500',
            'es_biodegradable' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria = CategoriaInsumo::create([
            'nombre_categoria' => $request->nombre_categoria,
            'descripcion' => $request->descripcion,
            'es_biodegradable' => $request->get('es_biodegradable', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Actualizar una categoría
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $categoria = CategoriaInsumo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre_categoria' => 'sometimes|string|max:100|unique:categorias_insumos,nombre_categoria,' . $id,
            'descripcion' => 'nullable|string|max:500',
            'es_biodegradable' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria->update($request->only(['nombre_categoria', 'descripcion', 'es_biodegradable']));

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categoria
        ]);
    }

    /**
     * Eliminar una categoría
     */
    public function destroy(int $id): JsonResponse
    {
        $categoria = CategoriaInsumo::findOrFail($id);

        // Verificar que no tenga insumos asociados
        if ($categoria->insumos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una categoría que tiene insumos asociados'
            ], 400);
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}
