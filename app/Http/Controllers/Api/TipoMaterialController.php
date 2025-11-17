<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TipoMaterialController extends Controller
{
    /**
     * Lista todos los tipos de materiales.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TipoMaterial::withCount('insumos');

        // Filtro por clasificación
        if ($request->has('clasificacion')) {
            $query->where('clasificacion', $request->clasificacion);
        }

        // Filtro por activos
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Búsqueda por nombre o código
        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        // Ordenar por orden de visualización
        $query->ordenados();

        // Paginación o lista completa
        if ($request->boolean('all')) {
            $tiposMateriales = $query->get();
        } else {
            $tiposMateriales = $query->paginate($request->get('per_page', 15));
        }

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales
        ]);
    }

    /**
     * Obtiene estadísticas de los tipos de materiales.
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_tipos' => TipoMaterial::count(),
            'tipos_activos' => TipoMaterial::activos()->count(),
            'por_clasificacion' => TipoMaterial::selectRaw('clasificacion, COUNT(*) as total')
                ->groupBy('clasificacion')
                ->get()
                ->map(fn($item) => [
                    'clasificacion' => $item->clasificacion,
                    'total' => $item->total
                ]),
            'con_insumos' => TipoMaterial::has('insumos')->count(),
            'sin_insumos' => TipoMaterial::doesntHave('insumos')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtiene un tipo de material específico.
     */
    public function show(int $id): JsonResponse
    {
        $tipoMaterial = TipoMaterial::with(['insumos' => function($query) {
            $query->activos()->orderBy('nombre_insumo');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tipoMaterial
        ]);
    }

    /**
     * Crea un nuevo tipo de material.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:20|unique:tipos_materiales,codigo',
            'nombre' => 'required|string|max:100',
            'clasificacion' => 'required|in:Polímero Biodegradable,Aditivo,Pigmento,Otro',
            'descripcion' => 'nullable|string',
            'densidad_min' => 'nullable|numeric|min:0|max:999.999',
            'densidad_max' => 'nullable|numeric|min:0|max:999.999|gte:densidad_min',
            'temperatura_procesamiento_min' => 'nullable|numeric|min:-273|max:9999.9',
            'temperatura_procesamiento_max' => 'nullable|numeric|min:-273|max:9999.9|gte:temperatura_procesamiento_min',
            'tiempo_degradacion_min' => 'nullable|integer|min:0',
            'tiempo_degradacion_max' => 'nullable|integer|min:0|gte:tiempo_degradacion_min',
            'certificaciones_aplicables' => 'nullable|string',
            'color_referencia' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icono' => 'nullable|string|max:50',
            'orden_visualizacion' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipoMaterial = TipoMaterial::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tipo de material creado exitosamente',
            'data' => $tipoMaterial
        ], 201);
    }

    /**
     * Actualiza un tipo de material existente.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tipoMaterial = TipoMaterial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo' => 'sometimes|required|string|max:20|unique:tipos_materiales,codigo,' . $id,
            'nombre' => 'sometimes|required|string|max:100',
            'clasificacion' => 'sometimes|required|in:Polímero Biodegradable,Aditivo,Pigmento,Otro',
            'descripcion' => 'nullable|string',
            'densidad_min' => 'nullable|numeric|min:0|max:999.999',
            'densidad_max' => 'nullable|numeric|min:0|max:999.999|gte:densidad_min',
            'temperatura_procesamiento_min' => 'nullable|numeric|min:-273|max:9999.9',
            'temperatura_procesamiento_max' => 'nullable|numeric|min:-273|max:9999.9|gte:temperatura_procesamiento_min',
            'tiempo_degradacion_min' => 'nullable|integer|min:0',
            'tiempo_degradacion_max' => 'nullable|integer|min:0|gte:tiempo_degradacion_min',
            'certificaciones_aplicables' => 'nullable|string',
            'color_referencia' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icono' => 'nullable|string|max:50',
            'orden_visualizacion' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipoMaterial->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tipo de material actualizado exitosamente',
            'data' => $tipoMaterial->fresh()
        ]);
    }

    /**
     * Elimina un tipo de material.
     */
    public function destroy(int $id): JsonResponse
    {
        $tipoMaterial = TipoMaterial::findOrFail($id);

        // Verificar si tiene insumos asociados
        if ($tipoMaterial->insumos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un tipo de material que tiene insumos asociados. Primero debe reasignar o eliminar los insumos.'
            ], 400);
        }

        $tipoMaterial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de material eliminado exitosamente'
        ]);
    }

    /**
     * Obtiene tipos de materiales por clasificación.
     */
    public function porClasificacion(string $clasificacion): JsonResponse
    {
        $tiposMateriales = TipoMaterial::porClasificacion($clasificacion)
            ->activos()
            ->ordenados()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales
        ]);
    }

    /**
     * Obtiene solo polímeros biodegradables.
     */
    public function biodegradables(): JsonResponse
    {
        $tiposMateriales = TipoMaterial::porClasificacion('Polímero Biodegradable')
            ->activos()
            ->ordenados()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tiposMateriales
        ]);
    }

    /**
     * Activa/desactiva un tipo de material.
     */
    public function toggleActivo(int $id): JsonResponse
    {
        $tipoMaterial = TipoMaterial::findOrFail($id);
        $tipoMaterial->activo = !$tipoMaterial->activo;
        $tipoMaterial->save();

        return response()->json([
            'success' => true,
            'message' => $tipoMaterial->activo ? 'Tipo de material activado' : 'Tipo de material desactivado',
            'data' => $tipoMaterial
        ]);
    }
}
