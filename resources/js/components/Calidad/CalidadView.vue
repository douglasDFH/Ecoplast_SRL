<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
        <!-- Header -->
        <div class="mb-8 rounded-3xl p-6 shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2" style="color: #263238;">Control de Calidad</h1>
                    <p class="text-lg" style="color: #607D8B;">Inspección y aprobación de lotes en cuarentena</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button
                        @click="cargarLotes"
                        class="p-4 rounded-2xl transition-all hover-button"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); 
                               box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; 
                               color: white;"
                        title="Actualizar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">En Cuarentena</span>
                    <span class="text-3xl">⏳</span>
                </div>
                <p class="text-4xl font-bold" style="color: #FFA726;">{{ estadisticas.cuarentena }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Pendientes inspección</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Aprobados Hoy</span>
                    <span class="text-3xl">✅</span>
                </div>
                <p class="text-4xl font-bold" style="color: #4CAF50;">{{ estadisticas.aprobados_hoy }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Últimas 24h</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Rechazados Hoy</span>
                    <span class="text-3xl">❌</span>
                </div>
                <p class="text-4xl font-bold" style="color: #F44336;">{{ estadisticas.rechazados_hoy }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Últimas 24h</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Tasa de Aprobación</span>
                    <span class="text-3xl">📊</span>
                </div>
                <p class="text-4xl font-bold" style="color: #2196F3;">{{ estadisticas.tasa_aprobacion }}%</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Últimos 7 días</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-6 rounded-3xl p-6 shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Estado</label>
                    <select
                        v-model="filtros.estado"
                        @change="aplicarFiltros"
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Todos los estados</option>
                        <option value="cuarentena">⏳ En Cuarentena</option>
                        <option value="aprobado">✅ Aprobado</option>
                        <option value="rechazado">❌ Rechazado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Producto</label>
                    <input
                        type="text"
                        v-model="filtros.producto"
                        @input="aplicarFiltros"
                        placeholder="Buscar producto..."
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Desde</label>
                    <input
                        type="date"
                        v-model="filtros.fecha_desde"
                        @change="aplicarFiltros"
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    />
                </div>

                <div class="flex items-end">
                    <button
                        @click="limpiarFiltros"
                        class="w-full px-4 py-2 rounded-2xl font-medium transition-all hover-button"
                        style="background: linear-gradient(145deg, #607D8B, #455A64); 
                               box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; 
                               color: white;"
                    >
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Lotes -->
        <div class="rounded-3xl overflow-hidden shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1);">
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Código Lote</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Producto</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Cantidad</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Orden</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Fecha Producción</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Estado</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2" style="border-color: #2196F3;"></div>
                                    <span class="ml-3 text-lg" style="color: #607D8B;">Cargando lotes...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="lotesFiltrados.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center" style="color: #607D8B;">
                                <span class="text-5xl mb-4 block">📦</span>
                                <p class="text-lg">No hay lotes en cuarentena</p>
                            </td>
                        </tr>
                        <tr v-else v-for="lote in lotesFiltrados" :key="lote.id"
                            class="border-b border-blue-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 transition-all"
                            style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd);">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold" style="color: #263238;">{{ lote.codigo_lote }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span style="color: #263238;">{{ lote.producto?.nombre || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold" style="color: #263238;">{{ formatearNumero(lote.cantidad_producida) }}</span>
                                <span class="text-sm ml-1" style="color: #607D8B;">unidades</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm" style="color: #607D8B;">{{ lote.orden?.codigo_orden || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span style="color: #607D8B;">{{ formatearFecha(lote.fecha_produccion) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getEstadoClase(lote.estado_calidad)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ getEstadoTexto(lote.estado_calidad) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button
                                        v-if="lote.estado_calidad === 'cuarentena'"
                                        @click="seleccionarLoteParaInspeccion(lote)"
                                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                        style="background: linear-gradient(145deg, #2196F3, #1976D2); 
                                               box-shadow: 8px 8px 16px #1565c0, -8px -8px 16px #42a5f5; 
                                               color: white;"
                                    >
                                        🔍 Inspeccionar
                                    </button>
                                    <button
                                        @click="verDetalles(lote)"
                                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                        style="background: linear-gradient(145deg, #607D8B, #455A64); 
                                               box-shadow: 8px 8px 16px #4a5a63, -8px -8px 16px #6e8495; 
                                               color: white;"
                                    >
                                        👁 Ver
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Defectos Comunes -->
        <div class="mt-8 rounded-3xl p-6 shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <h3 class="text-2xl font-bold mb-6" style="color: #263238;">📊 Defectos Más Comunes (Últimos 30 días)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="defecto in topDefectos" :key="defecto.tipo"
                     class="rounded-2xl p-4"
                     style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1); box-shadow: inset 4px 4px 8px #c5dbe9, inset -4px -4px 8px #ffffff;">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold" style="color: #263238;">{{ defecto.tipo }}</span>
                        <span class="text-2xl font-bold" style="color: #F44336;">{{ defecto.cantidad }}</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 2px 2px 4px #d0dfe8;">
                        <div class="h-2 rounded-full transition-all" 
                             :style="`width: ${defecto.porcentaje}%; background: linear-gradient(90deg, #F44336, #D32F2F);`">
                        </div>
                    </div>
                    <p class="text-sm mt-2" style="color: #607D8B;">{{ defecto.porcentaje }}% del total</p>
                </div>
            </div>
        </div>

        <!-- Modal Inspección -->
        <div v-if="modalInspeccionAbierto" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="rounded-3xl shadow-neumorphic max-w-3xl w-full max-h-[90vh] overflow-y-auto" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <!-- Header Modal -->
                <div class="flex items-center justify-between p-6 border-b border-blue-200">
                    <h2 class="text-2xl font-bold" style="color: #263238;">
                        🔍 Inspección de Calidad - {{ loteSeleccionado?.codigo_lote }}
                    </h2>
                    <button
                        @click="cerrarModal"
                        class="hover-button rounded-xl px-3 py-2 transition-all"
                        style="color: #607D8B;"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <form @submit.prevent="guardarInspeccion" class="p-6 space-y-6">
                    <!-- Información del Lote -->
                    <div class="rounded-2xl p-4" 
                         style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff;">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium mb-1" style="color: #455A64;">Producto</p>
                                <p class="font-bold" style="color: #263238;">{{ loteSeleccionado?.producto?.nombre }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium mb-1" style="color: #455A64;">Cantidad</p>
                                <p class="font-bold" style="color: #263238;">{{ formatearNumero(loteSeleccionado?.cantidad_producida) }} unidades</p>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado de Inspección -->
                    <div>
                        <label class="block text-lg font-bold mb-3" style="color: #263238;">
                            Resultado de Inspección <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                type="button"
                                @click="formularioInspeccion.resultado = 'aprobado'"
                                :class="formularioInspeccion.resultado === 'aprobado' ? 'ring-4 ring-green-400' : ''"
                                class="p-6 rounded-2xl font-bold text-lg transition-all hover-button"
                                style="background: linear-gradient(145deg, #4CAF50, #388E3C); 
                                       box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; 
                                       color: white;"
                            >
                                ✅ APROBAR
                            </button>
                            <button
                                type="button"
                                @click="formularioInspeccion.resultado = 'rechazado'"
                                :class="formularioInspeccion.resultado === 'rechazado' ? 'ring-4 ring-red-400' : ''"
                                class="p-6 rounded-2xl font-bold text-lg transition-all hover-button"
                                style="background: linear-gradient(145deg, #F44336, #D32F2F); 
                                       box-shadow: 12px 12px 24px #b71c1c, -12px -12px 24px #ff5252; 
                                       color: white;"
                            >
                                ❌ RECHAZAR
                            </button>
                        </div>
                    </div>

                    <!-- Criterios de Inspección -->
                    <div>
                        <label class="block text-lg font-bold mb-3" style="color: #263238;">Criterios Evaluados</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="criterio in criteriosInspeccion" :key="criterio.id">
                                <label class="flex items-center space-x-3 p-4 rounded-2xl cursor-pointer transition-all"
                                       :class="formularioInspeccion.criterios_evaluados.includes(criterio.id) ? 'ring-2 ring-blue-400' : ''"
                                       style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 8px 8px 16px #d0dfe8, -8px -8px 16px #ffffff;">
                                    <input
                                        type="checkbox"
                                        :value="criterio.id"
                                        v-model="formularioInspeccion.criterios_evaluados"
                                        class="w-5 h-5 rounded"
                                    />
                                    <span class="font-medium" style="color: #263238;">{{ criterio.nombre }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Defecto (si rechazado) -->
                    <div v-if="formularioInspeccion.resultado === 'rechazado'">
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Tipo de Defecto <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="formularioInspeccion.tipo_defecto"
                            required
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        >
                            <option value="">Seleccionar tipo de defecto...</option>
                            <option value="dimensional">Defecto Dimensional</option>
                            <option value="visual">Defecto Visual</option>
                            <option value="material">Defecto de Material</option>
                            <option value="proceso">Defecto de Proceso</option>
                            <option value="contaminacion">Contaminación</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Cantidad Defectuosa (si rechazado) -->
                    <div v-if="formularioInspeccion.resultado === 'rechazado'">
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Cantidad Defectuosa <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            v-model.number="formularioInspeccion.cantidad_defectuosa"
                            required
                            min="0"
                            :max="loteSeleccionado?.cantidad_producida"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                            placeholder="Unidades defectuosas"
                        />
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Observaciones <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="formularioInspeccion.observaciones"
                            required
                            rows="4"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                            placeholder="Detalle los hallazgos de la inspección..."
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-blue-200">
                        <button
                            type="button"
                            @click="cerrarModal"
                            class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                            style="background: linear-gradient(145deg, #607D8B, #455A64); 
                                   box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; 
                                   color: white;"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="!formularioInspeccion.resultado || submitting"
                            class="px-6 py-3 rounded-2xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: linear-gradient(145deg, #2196F3, #1976D2); 
                                   box-shadow: 12px 12px 24px #1565c0, -12px -12px 24px #42a5f5; 
                                   color: white;"
                        >
                            <span v-if="submitting">Guardando...</span>
                            <span v-else>💾 Guardar Inspección</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detalles -->
        <div v-if="modalDetallesAbierto" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="rounded-3xl shadow-neumorphic max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between p-6 border-b border-blue-200">
                    <h2 class="text-2xl font-bold" style="color: #263238;">
                        📋 Detalles del Lote
                    </h2>
                    <button
                        @click="modalDetallesAbierto = false"
                        class="hover-button rounded-xl px-3 py-2 transition-all"
                        style="color: #607D8B;"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4" v-if="loteSeleccionado">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Código Lote</p>
                            <p class="font-mono font-bold" style="color: #263238;">{{ loteSeleccionado.codigo_lote }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Estado</p>
                            <span :class="getEstadoClase(loteSeleccionado.estado_calidad)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                {{ getEstadoTexto(loteSeleccionado.estado_calidad) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Producto</p>
                            <p class="font-bold" style="color: #263238;">{{ loteSeleccionado.producto?.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Cantidad</p>
                            <p class="font-bold" style="color: #263238;">{{ formatearNumero(loteSeleccionado.cantidad_producida) }} unidades</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Fecha Producción</p>
                            <p style="color: #263238;">{{ formatearFecha(loteSeleccionado.fecha_produccion) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: #455A64;">Orden</p>
                            <p class="font-mono" style="color: #263238;">{{ loteSeleccionado.orden?.codigo_orden || 'N/A' }}</p>
                        </div>
                    </div>
                    <div v-if="loteSeleccionado.inspecciones_calidad && loteSeleccionado.inspecciones_calidad.length > 0">
                        <p class="text-sm font-medium mb-2" style="color: #455A64;">Historial de Inspecciones</p>
                        <div class="space-y-2">
                            <div v-for="inspeccion in loteSeleccionado.inspecciones_calidad" :key="inspeccion.id"
                                 class="rounded-2xl p-3"
                                 style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1); box-shadow: inset 4px 4px 8px #c5dbe9, inset -4px -4px 8px #ffffff;">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold" style="color: #263238;">{{ inspeccion.resultado }}</p>
                                        <p class="text-sm" style="color: #607D8B;">{{ inspeccion.observaciones }}</p>
                                    </div>
                                    <span class="text-xs" style="color: #607D8B;">{{ formatearFecha(inspeccion.fecha_inspeccion) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/services/api.js';

// Estado
const loading = ref(false);
const submitting = ref(false);
const lotes = ref([]);
const loteSeleccionado = ref(null);
const modalInspeccionAbierto = ref(false);
const modalDetallesAbierto = ref(false);

// Estadísticas
const estadisticas = ref({
    cuarentena: 0,
    aprobados_hoy: 0,
    rechazados_hoy: 0,
    tasa_aprobacion: 0
});

// Top Defectos
const topDefectos = ref([
    { tipo: 'Dimensional', cantidad: 45, porcentaje: 35 },
    { tipo: 'Visual', cantidad: 32, porcentaje: 25 },
    { tipo: 'Material', cantidad: 28, porcentaje: 22 }
]);

// Filtros
const filtros = ref({
    estado: '',
    producto: '',
    fecha_desde: ''
});

// Criterios de Inspección
const criteriosInspeccion = ref([
    { id: 1, nombre: 'Dimensiones' },
    { id: 2, nombre: 'Apariencia Visual' },
    { id: 3, nombre: 'Grosor' },
    { id: 4, nombre: 'Color' },
    { id: 5, nombre: 'Resistencia' },
    { id: 6, nombre: 'Sellado' }
]);

// Formulario de Inspección
const formularioInspeccion = ref({
    resultado: '', // 'aprobado' o 'rechazado'
    criterios_evaluados: [],
    tipo_defecto: '',
    cantidad_defectuosa: 0,
    observaciones: ''
});

// Computed
const lotesFiltrados = computed(() => {
    let resultado = [...lotes.value];

    if (filtros.value.estado) {
        resultado = resultado.filter(l => l.estado_calidad === filtros.value.estado);
    }

    if (filtros.value.producto) {
        resultado = resultado.filter(l => 
            l.producto?.nombre.toLowerCase().includes(filtros.value.producto.toLowerCase())
        );
    }

    if (filtros.value.fecha_desde) {
        resultado = resultado.filter(l => l.fecha_produccion >= filtros.value.fecha_desde);
    }

    return resultado;
});

// Métodos
const cargarLotes = async () => {
    loading.value = true;
    try {
        const response = await api.get('/lotes-produccion', {
            params: { 
                incluir: 'producto,orden,inspeccionesCalidad',
                estado_calidad: 'cuarentena'
            }
        });
        lotes.value = response.data.data || response.data;
        await cargarEstadisticas();
    } catch (error) {
        console.error('Error al cargar lotes:', error);
    } finally {
        loading.value = false;
    }
};

const cargarEstadisticas = async () => {
    try {
        const hoy = new Date().toISOString().split('T')[0];
        const hace7dias = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

        // Contar estados
        const cuarentena = lotes.value.filter(l => l.estado_calidad === 'cuarentena').length;
        
        // Cargar aprobados y rechazados de hoy
        const responseHoy = await api.get('/inspecciones-calidad', {
            params: { 
                fecha_desde: hoy,
                fecha_hasta: hoy
            }
        });
        const inspeccionesHoy = responseHoy.data.data || responseHoy.data;
        const aprobadosHoy = inspeccionesHoy.filter(i => i.resultado === 'aprobado').length;
        const rechazadosHoy = inspeccionesHoy.filter(i => i.resultado === 'rechazado').length;

        // Calcular tasa de aprobación últimos 7 días
        const response7dias = await api.get('/inspecciones-calidad', {
            params: { 
                fecha_desde: hace7dias,
                fecha_hasta: hoy
            }
        });
        const inspecciones7dias = response7dias.data.data || response7dias.data;
        const totalInspecciones = inspecciones7dias.length;
        const aprobados7dias = inspecciones7dias.filter(i => i.resultado === 'aprobado').length;
        const tasaAprobacion = totalInspecciones > 0 ? Math.round((aprobados7dias / totalInspecciones) * 100) : 0;

        estadisticas.value = {
            cuarentena,
            aprobados_hoy: aprobadosHoy,
            rechazados_hoy: rechazadosHoy,
            tasa_aprobacion: tasaAprobacion
        };
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
};

const seleccionarLoteParaInspeccion = (lote) => {
    loteSeleccionado.value = lote;
    formularioInspeccion.value = {
        resultado: '',
        criterios_evaluados: [],
        tipo_defecto: '',
        cantidad_defectuosa: 0,
        observaciones: ''
    };
    modalInspeccionAbierto.value = true;
};

const guardarInspeccion = async () => {
    if (!formularioInspeccion.value.resultado) {
        alert('Debe seleccionar un resultado (Aprobar o Rechazar)');
        return;
    }

    if (!formularioInspeccion.value.observaciones.trim()) {
        alert('Debe ingresar observaciones');
        return;
    }

    if (formularioInspeccion.value.resultado === 'rechazado' && !formularioInspeccion.value.tipo_defecto) {
        alert('Debe seleccionar el tipo de defecto');
        return;
    }

    submitting.value = true;
    try {
        const payload = {
            lote_id: loteSeleccionado.value.id,
            resultado: formularioInspeccion.value.resultado,
            observaciones: formularioInspeccion.value.observaciones,
            criterios_evaluados: formularioInspeccion.value.criterios_evaluados
        };

        if (formularioInspeccion.value.resultado === 'rechazado') {
            payload.tipo_defecto = formularioInspeccion.value.tipo_defecto;
            payload.cantidad_defectuosa = formularioInspeccion.value.cantidad_defectuosa;
        }

        await api.post('/inspecciones-calidad', payload);

        // Actualizar estado del lote
        await api.put(`/lotes-produccion/${loteSeleccionado.value.id}`, {
            estado_calidad: formularioInspeccion.value.resultado
        });

        alert(`Lote ${formularioInspeccion.value.resultado === 'aprobado' ? 'APROBADO' : 'RECHAZADO'} exitosamente`);
        cerrarModal();
        await cargarLotes();
    } catch (error) {
        console.error('Error al guardar inspección:', error);
        alert('Error al guardar la inspección');
    } finally {
        submitting.value = false;
    }
};

const verDetalles = async (lote) => {
    try {
        const response = await api.get(`/lotes-produccion/${lote.id}`, {
            params: { incluir: 'producto,orden,inspeccionesCalidad' }
        });
        loteSeleccionado.value = response.data.data || response.data;
        modalDetallesAbierto.value = true;
    } catch (error) {
        console.error('Error al cargar detalles:', error);
    }
};

const cerrarModal = () => {
    modalInspeccionAbierto.value = false;
    loteSeleccionado.value = null;
    formularioInspeccion.value = {
        resultado: '',
        criterios_evaluados: [],
        tipo_defecto: '',
        cantidad_defectuosa: 0,
        observaciones: ''
    };
};

const aplicarFiltros = () => {
    // Los filtros se aplican automáticamente con computed
};

const limpiarFiltros = () => {
    filtros.value = {
        estado: '',
        producto: '',
        fecha_desde: ''
    };
};

const formatearNumero = (numero) => {
    return new Intl.NumberFormat('es-ES').format(numero);
};

const formatearFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getEstadoClase = (estado) => {
    const clases = {
        'cuarentena': 'bg-yellow-100 text-yellow-800',
        'aprobado': 'bg-green-100 text-green-800',
        'rechazado': 'bg-red-100 text-red-800'
    };
    return clases[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'cuarentena': '⏳ Cuarentena',
        'aprobado': '✅ Aprobado',
        'rechazado': '❌ Rechazado'
    };
    return textos[estado] || estado;
};

// Lifecycle
onMounted(() => {
    cargarLotes();
});
</script>

<style scoped>
.shadow-neumorphic {
    box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;
}

.hover-button:hover {
    box-shadow: 7px 7px 14px #4a5a63, -7px -7px 14px #6e8495;
}

/* Animación para botón de emergencia */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
</style>
