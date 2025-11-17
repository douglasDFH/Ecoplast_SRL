<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
        <!-- Header -->
        <div class="mb-8 rounded-3xl p-6 shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2" style="color: #263238;">Gestión de Inventario</h1>
                    <p class="text-lg" style="color: #607D8B;">Control de insumos y materia prima</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button
                        @click="abrirModalMovimiento('entrada')"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); 
                               box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; 
                               color: white;"
                    >
                        ➕ Entrada
                    </button>
                    <button
                        @click="abrirModalMovimiento('salida')"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                        style="background: linear-gradient(145deg, #FF9800, #F57C00); 
                               box-shadow: 12px 12px 24px #e65100, -12px -12px 24px #ffb74d; 
                               color: white;"
                    >
                        ➖ Salida
                    </button>
                    <button
                        @click="cargarInsumos"
                        class="p-4 rounded-2xl transition-all hover-button"
                        style="background: linear-gradient(145deg, #2196F3, #1976D2); 
                               box-shadow: 12px 12px 24px #1565c0, -12px -12px 24px #42a5f5; 
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
                    <span class="text-sm font-medium" style="color: #607D8B;">Total Insumos</span>
                    <span class="text-3xl">📦</span>
                </div>
                <p class="text-4xl font-bold" style="color: #2196F3;">{{ estadisticas.total_insumos }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Tipos registrados</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Stock Bajo</span>
                    <span class="text-3xl">⚠️</span>
                </div>
                <p class="text-4xl font-bold" style="color: #FF9800;">{{ estadisticas.stock_bajo }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Requieren reposición</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Próximos a Vencer</span>
                    <span class="text-3xl">⏰</span>
                </div>
                <p class="text-4xl font-bold" style="color: #F44336;">{{ estadisticas.proximos_vencer }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Próximos 30 días</p>
            </div>

            <div class="rounded-3xl p-6 shadow-neumorphic" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" style="color: #607D8B;">Valor Total</span>
                    <span class="text-3xl">💰</span>
                </div>
                <p class="text-4xl font-bold" style="color: #4CAF50;">${{ formatearNumero(estadisticas.valor_total) }}</p>
                <p class="text-sm mt-2" style="color: #607D8B;">Inventario actual</p>
            </div>
        </div>

        <!-- Alertas Críticas -->
        <div v-if="alertasCriticas.length > 0" 
             class="mb-6 rounded-3xl p-6 shadow-neumorphic border-l-4 border-red-500" 
             style="background: linear-gradient(145deg, #ffebee, #ffcdd2);">
            <div class="flex items-start">
                <span class="text-3xl mr-4">🚨</span>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-red-800 mb-3">Alertas Críticas</h3>
                    <div class="space-y-2">
                        <div v-for="alerta in alertasCriticas" :key="alerta.id"
                             class="flex items-center justify-between p-3 rounded-2xl"
                             style="background: linear-gradient(145deg, #ffcdd2, #ef9a9a);">
                            <div>
                                <p class="font-semibold text-red-900">{{ alerta.insumo?.nombre }}</p>
                                <p class="text-sm text-red-700">{{ alerta.mensaje }}</p>
                            </div>
                            <button
                                @click="resolverAlerta(alerta)"
                                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                style="background: linear-gradient(145deg, #F44336, #D32F2F); 
                                       box-shadow: 8px 8px 16px #b71c1c, -8px -8px 16px #ff5252; 
                                       color: white;"
                            >
                                Resolver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-6 rounded-3xl p-6 shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Categoría</label>
                    <select
                        v-model="filtros.categoria"
                        @change="aplicarFiltros"
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Todas las categorías</option>
                        <option value="materia_prima">Materia Prima</option>
                        <option value="aditivos">Aditivos</option>
                        <option value="pigmentos">Pigmentos</option>
                        <option value="embalaje">Embalaje</option>
                    </select>
                </div>

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
                        <option value="stock_bajo">⚠️ Stock Bajo</option>
                        <option value="stock_critico">🚨 Stock Crítico</option>
                        <option value="proximo_vencer">⏰ Próximo a Vencer</option>
                        <option value="stock_ok">✅ Stock OK</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Buscar</label>
                    <input
                        type="text"
                        v-model="filtros.busqueda"
                        @input="aplicarFiltros"
                        placeholder="Nombre o código..."
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Proveedor</label>
                    <select
                        v-model="filtros.proveedor_id"
                        @change="aplicarFiltros"
                        class="w-full px-4 py-2 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Todos los proveedores</option>
                        <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                            {{ proveedor.nombre_comercial }}
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        @click="limpiarFiltros"
                        class="w-full px-4 py-2 rounded-2xl font-medium transition-all hover-button"
                        style="background: linear-gradient(145deg, #607D8B, #455A64); 
                               box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; 
                               color: white;"
                    >
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Insumos -->
        <div class="rounded-3xl overflow-hidden shadow-neumorphic" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1);">
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Código</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Insumo</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Categoría</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Stock Actual</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Stock Mínimo</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Estado</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Vencimiento</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2" style="border-color: #2196F3;"></div>
                                    <span class="ml-3 text-lg" style="color: #607D8B;">Cargando inventario...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="insumosFiltrados.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center" style="color: #607D8B;">
                                <span class="text-5xl mb-4 block">📦</span>
                                <p class="text-lg">No se encontraron insumos</p>
                            </td>
                        </tr>
                        <tr v-else v-for="insumo in insumosFiltrados" :key="insumo.id"
                            :class="getRowClass(insumo)"
                            class="border-b border-blue-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 transition-all">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold" style="color: #263238;">{{ insumo.codigo }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold" style="color: #263238;">{{ insumo.nombre }}</p>
                                    <p class="text-sm" style="color: #607D8B;">{{ insumo.descripcion }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      :class="getCategoriaClase(insumo.categoria)">
                                    {{ getCategoriaTexto(insumo.categoria) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-2xl font-bold" :style="`color: ${getColorStock(insumo)};`">
                                        {{ formatearNumero(insumo.cantidad_actual) }}
                                    </p>
                                    <p class="text-sm" style="color: #607D8B;">{{ insumo.unidad_medida }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold" style="color: #607D8B;">{{ formatearNumero(insumo.stock_minimo) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span :class="getEstadoClase(insumo)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ getEstadoTexto(insumo) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p v-if="insumo.fecha_vencimiento" 
                                   :class="esProximoVencer(insumo.fecha_vencimiento) ? 'text-red-600 font-bold' : ''"
                                   style="color: #607D8B;">
                                    {{ formatearFecha(insumo.fecha_vencimiento) }}
                                </p>
                                <p v-else style="color: #B0BEC5;">N/A</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="verMovimientos(insumo)"
                                        class="px-3 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                        style="background: linear-gradient(145deg, #2196F3, #1976D2); 
                                               box-shadow: 8px 8px 16px #1565c0, -8px -8px 16px #42a5f5; 
                                               color: white;"
                                        title="Historial"
                                    >
                                        📋
                                    </button>
                                    <button
                                        @click="abrirModalMovimiento('entrada', insumo)"
                                        class="px-3 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); 
                                               box-shadow: 8px 8px 16px #2e7031, -8px -8px 16px #5ec962; 
                                               color: white;"
                                        title="Entrada"
                                    >
                                        ➕
                                    </button>
                                    <button
                                        @click="abrirModalMovimiento('salida', insumo)"
                                        class="px-3 py-2 rounded-xl text-sm font-semibold transition-all hover-button"
                                        style="background: linear-gradient(145deg, #FF9800, #F57C00); 
                                               box-shadow: 8px 8px 16px #e65100, -8px -8px 16px #ffb74d; 
                                               color: white;"
                                        title="Salida"
                                    >
                                        ➖
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Movimiento (Entrada/Salida) -->
        <div v-if="modalMovimientoAbierto" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="rounded-3xl shadow-neumorphic max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between p-6 border-b border-blue-200">
                    <h2 class="text-2xl font-bold" style="color: #263238;">
                        {{ tipoMovimiento === 'entrada' ? '➕ Entrada de Inventario' : '➖ Salida de Inventario' }}
                    </h2>
                    <button
                        @click="cerrarModalMovimiento"
                        class="hover-button rounded-xl px-3 py-2 transition-all"
                        style="color: #607D8B;"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="guardarMovimiento" class="p-6 space-y-6">
                    <!-- Insumo -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Insumo <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="formularioMovimiento.insumo_id"
                            required
                            :disabled="insumoSeleccionado !== null"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        >
                            <option value="">Seleccionar insumo...</option>
                            <option v-for="insumo in insumos" :key="insumo.id" :value="insumo.id">
                                {{ insumo.nombre }} ({{ insumo.codigo }}) - Stock: {{ insumo.cantidad_actual }} {{ insumo.unidad_medida }}
                            </option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            v-model.number="formularioMovimiento.cantidad"
                            required
                            min="0.01"
                            step="0.01"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none text-2xl font-bold"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Tipo de Movimiento (solo para salida) -->
                    <div v-if="tipoMovimiento === 'salida'">
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Motivo <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="formularioMovimiento.motivo"
                            required
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        >
                            <option value="">Seleccionar motivo...</option>
                            <option value="produccion">Uso en Producción</option>
                            <option value="ajuste">Ajuste de Inventario</option>
                            <option value="merma">Merma/Desperdicio</option>
                            <option value="vencimiento">Producto Vencido</option>
                            <option value="devolucion">Devolución a Proveedor</option>
                        </select>
                    </div>

                    <!-- Orden de Producción (si es para producción) -->
                    <div v-if="tipoMovimiento === 'salida' && formularioMovimiento.motivo === 'produccion'">
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Orden de Producción</label>
                        <select
                            v-model="formularioMovimiento.orden_produccion_id"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        >
                            <option value="">Seleccionar orden...</option>
                            <option v-for="orden in ordenesActivas" :key="orden.id" :value="orden.id">
                                {{ orden.codigo_orden }} - {{ orden.producto?.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Proveedor (solo para entrada) -->
                    <div v-if="tipoMovimiento === 'entrada'">
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Proveedor</label>
                        <select
                            v-model="formularioMovimiento.proveedor_id"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        >
                            <option value="">Seleccionar proveedor...</option>
                            <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                                {{ proveedor.nombre_comercial }}
                            </option>
                        </select>
                    </div>

                    <!-- Número de Documento -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Número de Documento
                        </label>
                        <input
                            type="text"
                            v-model="formularioMovimiento.numero_documento"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                            placeholder="Factura, remito, etc."
                        />
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Observaciones</label>
                        <textarea
                            v-model="formularioMovimiento.observaciones"
                            rows="3"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                            placeholder="Detalles adicionales del movimiento..."
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-blue-200">
                        <button
                            type="button"
                            @click="cerrarModalMovimiento"
                            class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                            style="background: linear-gradient(145deg, #607D8B, #455A64); 
                                   box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; 
                                   color: white;"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-6 py-3 rounded-2xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="tipoMovimiento === 'entrada' 
                                ? 'background: linear-gradient(145deg, #4CAF50, #388E3C); box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; color: white;'
                                : 'background: linear-gradient(145deg, #FF9800, #F57C00); box-shadow: 12px 12px 24px #e65100, -12px -12px 24px #ffb74d; color: white;'"
                        >
                            <span v-if="submitting">Guardando...</span>
                            <span v-else>💾 Registrar {{ tipoMovimiento === 'entrada' ? 'Entrada' : 'Salida' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Historial -->
        <div v-if="modalHistorialAbierto" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="rounded-3xl shadow-neumorphic max-w-4xl w-full max-h-[90vh] overflow-y-auto" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between p-6 border-b border-blue-200">
                    <h2 class="text-2xl font-bold" style="color: #263238;">
                        📋 Historial de Movimientos - {{ insumoSeleccionado?.nombre }}
                    </h2>
                    <button
                        @click="modalHistorialAbierto = false"
                        class="hover-button rounded-xl px-3 py-2 transition-all"
                        style="color: #607D8B;"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="space-y-3" v-if="movimientos.length > 0">
                        <div v-for="mov in movimientos" :key="mov.id"
                             class="rounded-2xl p-4"
                             style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1); box-shadow: inset 4px 4px 8px #c5dbe9, inset -4px -4px 8px #ffffff;">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="text-2xl">{{ mov.tipo === 'entrada' ? '➕' : '➖' }}</span>
                                        <span class="font-bold text-lg" style="color: #263238;">
                                            {{ mov.tipo === 'entrada' ? 'ENTRADA' : 'SALIDA' }}
                                        </span>
                                        <span :class="mov.tipo === 'entrada' ? 'text-green-600' : 'text-orange-600'" 
                                              class="text-xl font-bold">
                                            {{ formatearNumero(mov.cantidad) }} {{ mov.insumo?.unidad_medida }}
                                        </span>
                                    </div>
                                    <p class="text-sm mb-1" style="color: #607D8B;">
                                        <strong>Documento:</strong> {{ mov.numero_documento || 'N/A' }}
                                    </p>
                                    <p v-if="mov.observaciones" class="text-sm" style="color: #607D8B;">
                                        {{ mov.observaciones }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold" style="color: #263238;">
                                        {{ formatearFecha(mov.fecha_movimiento) }}
                                    </p>
                                    <p class="text-xs" style="color: #607D8B;">
                                        {{ mov.usuario?.name || 'Sistema' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12" style="color: #607D8B;">
                        <span class="text-5xl mb-4 block">📋</span>
                        <p class="text-lg">No hay movimientos registrados</p>
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
const insumos = ref([]);
const proveedores = ref([]);
const ordenesActivas = ref([]);
const movimientos = ref([]);
const insumoSeleccionado = ref(null);
const modalMovimientoAbierto = ref(false);
const modalHistorialAbierto = ref(false);
const tipoMovimiento = ref('entrada'); // 'entrada' o 'salida'

// Estadísticas
const estadisticas = ref({
    total_insumos: 0,
    stock_bajo: 0,
    proximos_vencer: 0,
    valor_total: 0
});

// Alertas
const alertasCriticas = ref([]);

// Filtros
const filtros = ref({
    categoria: '',
    estado: '',
    busqueda: '',
    proveedor_id: ''
});

// Formulario de Movimiento
const formularioMovimiento = ref({
    insumo_id: '',
    cantidad: 0,
    motivo: '',
    orden_produccion_id: '',
    proveedor_id: '',
    numero_documento: '',
    observaciones: ''
});

// Computed
const insumosFiltrados = computed(() => {
    let resultado = [...insumos.value];

    if (filtros.value.categoria) {
        resultado = resultado.filter(i => i.categoria === filtros.value.categoria);
    }

    if (filtros.value.estado) {
        resultado = resultado.filter(i => {
            const estado = getEstadoInsumo(i);
            return estado === filtros.value.estado;
        });
    }

    if (filtros.value.busqueda) {
        const busqueda = filtros.value.busqueda.toLowerCase();
        resultado = resultado.filter(i => 
            i.nombre.toLowerCase().includes(busqueda) ||
            i.codigo.toLowerCase().includes(busqueda)
        );
    }

    if (filtros.value.proveedor_id) {
        resultado = resultado.filter(i => i.proveedor_id == filtros.value.proveedor_id);
    }

    return resultado;
});

// Métodos
const cargarInsumos = async () => {
    loading.value = true;
    try {
        const response = await api.get('/movimientos-inventario/resumen/inventario');
        insumos.value = response.data.data || response.data;
        calcularEstadisticas();
        cargarAlertasCriticas();
    } catch (error) {
        console.error('Error al cargar insumos:', error);
    } finally {
        loading.value = false;
    }
};

const cargarProveedores = async () => {
    try {
        const response = await api.get('/proveedores', {
            params: { activo: true }
        });
        proveedores.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
    }
};

const cargarOrdenesActivas = async () => {
    try {
        const response = await api.get('/ordenes-produccion', {
            params: { 
                estado: 'en_proceso',
                incluir: 'producto'
            }
        });
        ordenesActivas.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar órdenes:', error);
    }
};

const calcularEstadisticas = () => {
    const total = insumos.value.length;
    const stockBajo = insumos.value.filter(i => i.cantidad_actual <= i.stock_minimo && i.cantidad_actual > 0).length;
    const proximosVencer = insumos.value.filter(i => esProximoVencer(i.fecha_vencimiento)).length;
    const valorTotal = insumos.value.reduce((sum, i) => sum + (i.cantidad_actual * (i.costo_unitario || 0)), 0);

    estadisticas.value = {
        total_insumos: total,
        stock_bajo: stockBajo,
        proximos_vencer: proximosVencer,
        valor_total: valorTotal
    };
};

const cargarAlertasCriticas = () => {
    alertasCriticas.value = insumos.value
        .filter(i => i.cantidad_actual === 0 || esProximoVencer(i.fecha_vencimiento))
        .map(i => ({
            id: i.id,
            insumo: i,
            mensaje: i.cantidad_actual === 0 
                ? '🚨 Stock agotado - Requiere reposición urgente'
                : '⏰ Vence en menos de 7 días'
        }));
};

const abrirModalMovimiento = (tipo, insumo = null) => {
    tipoMovimiento.value = tipo;
    insumoSeleccionado.value = insumo;
    formularioMovimiento.value = {
        insumo_id: insumo?.id || '',
        cantidad: 0,
        motivo: '',
        orden_produccion_id: '',
        proveedor_id: '',
        numero_documento: '',
        observaciones: ''
    };
    modalMovimientoAbierto.value = true;
    if (tipo === 'salida') {
        cargarOrdenesActivas();
    }
};

const cerrarModalMovimiento = () => {
    modalMovimientoAbierto.value = false;
    insumoSeleccionado.value = null;
};

const guardarMovimiento = async () => {
    if (!formularioMovimiento.value.insumo_id || formularioMovimiento.value.cantidad <= 0) {
        alert('Debe seleccionar un insumo e ingresar una cantidad válida');
        return;
    }

    if (tipoMovimiento.value === 'salida' && !formularioMovimiento.value.motivo) {
        alert('Debe seleccionar un motivo para la salida');
        return;
    }

    submitting.value = true;
    try {
        const payload = {
            ...formularioMovimiento.value,
            tipo: tipoMovimiento.value,
            fecha_movimiento: new Date().toISOString()
        };

        await api.post('/movimientos-inventario', payload);
        alert(`${tipoMovimiento.value === 'entrada' ? 'Entrada' : 'Salida'} registrada exitosamente`);
        cerrarModalMovimiento();
        await cargarInsumos();
    } catch (error) {
        console.error('Error al guardar movimiento:', error);
        alert('Error al registrar el movimiento');
    } finally {
        submitting.value = false;
    }
};

const verMovimientos = async (insumo) => {
    insumoSeleccionado.value = insumo;
    try {
        const response = await api.get('/movimientos-inventario', {
            params: { 
                insumo_id: insumo.id,
                incluir: 'insumo,usuario',
                orden: 'fecha_movimiento,desc',
                limite: 50
            }
        });
        movimientos.value = response.data.data || response.data;
        modalHistorialAbierto.value = true;
    } catch (error) {
        console.error('Error al cargar movimientos:', error);
    }
};

const resolverAlerta = (alerta) => {
    abrirModalMovimiento('entrada', alerta.insumo);
};

const aplicarFiltros = () => {
    // Los filtros se aplican automáticamente con computed
};

const limpiarFiltros = () => {
    filtros.value = {
        categoria: '',
        estado: '',
        busqueda: '',
        proveedor_id: ''
    };
};

const getEstadoInsumo = (insumo) => {
    if (insumo.cantidad_actual === 0) return 'stock_critico';
    if (insumo.cantidad_actual <= insumo.stock_minimo) return 'stock_bajo';
    if (esProximoVencer(insumo.fecha_vencimiento)) return 'proximo_vencer';
    return 'stock_ok';
};

const getRowClass = (insumo) => {
    const estado = getEstadoInsumo(insumo);
    if (estado === 'stock_critico') return 'bg-red-50';
    if (estado === 'stock_bajo') return 'bg-yellow-50';
    if (estado === 'proximo_vencer') return 'bg-orange-50';
    return 'bg-gradient-to-r from-blue-50 to-blue-50';
};

const getColorStock = (insumo) => {
    if (insumo.cantidad_actual === 0) return '#F44336';
    if (insumo.cantidad_actual <= insumo.stock_minimo) return '#FF9800';
    return '#4CAF50';
};

const getEstadoClase = (insumo) => {
    const estado = getEstadoInsumo(insumo);
    const clases = {
        'stock_critico': 'bg-red-100 text-red-800',
        'stock_bajo': 'bg-yellow-100 text-yellow-800',
        'proximo_vencer': 'bg-orange-100 text-orange-800',
        'stock_ok': 'bg-green-100 text-green-800'
    };
    return clases[estado];
};

const getEstadoTexto = (insumo) => {
    const estado = getEstadoInsumo(insumo);
    const textos = {
        'stock_critico': '🚨 Crítico',
        'stock_bajo': '⚠️ Bajo',
        'proximo_vencer': '⏰ Por Vencer',
        'stock_ok': '✅ Normal'
    };
    return textos[estado];
};

const getCategoriaClase = (categoria) => {
    const clases = {
        'materia_prima': 'bg-blue-100 text-blue-800',
        'aditivos': 'bg-purple-100 text-purple-800',
        'pigmentos': 'bg-pink-100 text-pink-800',
        'embalaje': 'bg-gray-100 text-gray-800'
    };
    return clases[categoria] || 'bg-gray-100 text-gray-800';
};

const getCategoriaTexto = (categoria) => {
    const textos = {
        'materia_prima': 'Materia Prima',
        'aditivos': 'Aditivos',
        'pigmentos': 'Pigmentos',
        'embalaje': 'Embalaje'
    };
    return textos[categoria] || categoria;
};

const esProximoVencer = (fecha) => {
    if (!fecha) return false;
    const fechaVencimiento = new Date(fecha);
    const hoy = new Date();
    const diasDiferencia = Math.floor((fechaVencimiento - hoy) / (1000 * 60 * 60 * 24));
    return diasDiferencia >= 0 && diasDiferencia <= 30;
};

const formatearNumero = (numero) => {
    return new Intl.NumberFormat('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(numero);
};

const formatearFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Lifecycle
onMounted(async () => {
    await Promise.all([
        cargarInsumos(),
        cargarProveedores()
    ]);
});
</script>

<style scoped>
.shadow-neumorphic {
    box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;
}

.hover-button:hover {
    box-shadow: 7px 7px 14px #4a5a63, -7px -7px 14px #6e8495;
}
</style>
