<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Insumo;
use App\Models\ProductoTerminado;
use App\Models\Maquinaria;
use App\Models\CategoriaInsumo;
use App\Models\CategoriaProducto;
use App\Models\TipoMaquinaria;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\Alerta;
use App\Models\User;

class VerificarModelos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verificar:modelos {--detallado : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que todos los modelos funcionan correctamente con la base de datos';

    /**
     * Modelos principales a verificar
     */
    protected $modelosPrincipales = [
        'Insumo' => Insumo::class,
        'ProductoTerminado' => ProductoTerminado::class,
        'Maquinaria' => Maquinaria::class,
        'CategoriaInsumo' => CategoriaInsumo::class,
        'CategoriaProducto' => CategoriaProducto::class,
        'TipoMaquinaria' => TipoMaquinaria::class,
        'OrdenProduccion' => OrdenProduccion::class,
        'RegistroProduccion' => RegistroProduccion::class,
        'Alerta' => Alerta::class,
        'User' => User::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 VERIFICACIÓN DE MODELOS - Ecoplast SRL');
        $this->line('=====================================');

        $detallado = $this->option('detallado');
        $totalModelos = count($this->modelosPrincipales);
        $modelosOk = 0;

        foreach ($this->modelosPrincipales as $nombre => $clase) {
            try {
                $this->verificarModelo($nombre, $clase, $detallado);
                $modelosOk++;
            } catch (\Exception $e) {
                $this->error("❌ Error en {$nombre}: " . $e->getMessage());
            }
        }

        $this->line('');
        $this->info("📊 RESULTADO: {$modelosOk}/{$totalModelos} modelos verificados correctamente");

        if ($modelosOk === $totalModelos) {
            $this->info('🎉 ¡Todos los modelos están funcionando correctamente!');
        }

        // Verificación especial de modelos biodegradables
        $this->verificarModelosBiodegradables();
    }

    /**
     * Verificar un modelo específico
     */
    protected function verificarModelo(string $nombre, string $clase, bool $detallado = false)
    {
        $this->line("🔍 Verificando {$nombre}...");

        try {
            // Verificar que la clase existe
            if (!class_exists($clase)) {
                throw new \Exception("Clase {$clase} no existe");
            }

            // Verificar que puede hacer consultas básicas
            $count = $clase::count();
            $this->line("   ✅ Conexión OK - {$count} registros encontrados");

            if ($detallado) {
                $this->mostrarDetallesModelo($nombre, $clase);
            }

            // Verificar relaciones si es detallado
            if ($detallado) {
                $this->verificarRelacionesModelo($nombre, $clase);
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Mostrar detalles de un modelo
     */
    protected function mostrarDetallesModelo(string $nombre, string $clase)
    {
        $modelo = new $clase();

        // Mostrar fillable
        if (property_exists($modelo, 'fillable') && !empty($modelo->fillable)) {
            $this->line("   📝 Fillable: " . implode(', ', $modelo->fillable));
        }

        // Mostrar tabla
        $this->line("   🗄️  Tabla: {$modelo->getTable()}");

        // Mostrar algunos registros de ejemplo
        $registros = $clase::take(2)->get();
        if ($registros->count() > 0) {
            $this->line("   📋 Registros de ejemplo:");
            foreach ($registros as $registro) {
                $this->line("      - ID {$registro->id}: {$registro->getKeyName()}");
            }
        }
    }

    /**
     * Verificar relaciones de un modelo
     */
    protected function verificarRelacionesModelo(string $nombre, string $clase)
    {
        $this->line("   🔗 Verificando relaciones...");

        try {
            switch ($nombre) {
                case 'Insumo':
                    $insumo = Insumo::with('categoria')->first();
                    if ($insumo && $insumo->categoria) {
                        $this->line("      ✅ Relación categoria OK: {$insumo->categoria->nombre_categoria}");
                    }
                    break;

                case 'ProductoTerminado':
                    $producto = ProductoTerminado::with('categoria')->first();
                    if ($producto && $producto->categoria) {
                        $this->line("      ✅ Relación categoria OK: {$producto->categoria->nombre_categoria}");
                    }
                    break;

                case 'Maquinaria':
                    $maquina = Maquinaria::with('tipoMaquina')->first();
                    if ($maquina && $maquina->tipoMaquina) {
                        $this->line("      ✅ Relación tipoMaquina OK: {$maquina->tipoMaquina->nombre_tipo}");
                    }
                    break;

                case 'OrdenProduccion':
                    $orden = OrdenProduccion::with(['producto', 'registros'])->first();
                    if ($orden) {
                        if ($orden->producto) {
                            $this->line("      ✅ Relación producto OK: {$orden->producto->nombre_producto}");
                        }
                        $this->line("      ✅ Relación registros OK: {$orden->registros->count()} registros");
                    }
                    break;
            }
        } catch (\Exception $e) {
            $this->line("      ⚠️  Error en relaciones: " . $e->getMessage());
        }
    }

    /**
     * Verificación especial de modelos biodegradables
     */
    protected function verificarModelosBiodegradables()
    {
        $this->line('');
        $this->info('🌱 VERIFICACIÓN DE MODELOS BIODEGRADABLES');
        $this->line('============================================');

        // Verificar insumos biodegradables
        $this->verificarInsumosBiodegradables();

        // Verificar productos terminados
        $this->verificarProductosTerminados();

        // Verificar maquinaria con parámetros OEE
        $this->verificarMaquinariaOEE();
    }

    /**
     * Verificar insumos biodegradables
     */
    protected function verificarInsumosBiodegradables()
    {
        $this->line('📦 Insumos Biodegradables:');

        $insumos = Insumo::where('activo', true)->get();

        foreach ($insumos as $insumo) {
            $this->line("   ✅ {$insumo->codigo_insumo} - {$insumo->nombre_insumo}");
            $this->line("      Tipo: {$insumo->tipo_material} | Cert: {$insumo->certificacion_biodegradable}");
            $this->line("      Stock: {$insumo->stock_actual} {$insumo->unidad_medida} | Precio: €{$insumo->precio_unitario}/{$insumo->unidad_medida}");
        }

        $this->line("   📊 Total insumos activos: {$insumos->count()}");
    }

    /**
     * Verificar productos terminados biodegradables
     */
    protected function verificarProductosTerminados()
    {
        $this->line('');
        $this->line('🏭 Productos Terminados Biodegradables:');

        $productos = ProductoTerminado::where('activo', true)->get();

        if ($productos->count() > 0) {
            foreach ($productos as $producto) {
                $this->line("   ✅ {$producto->codigo_producto} - {$producto->nombre_producto}");
                if ($producto->certificacion_compostable) {
                    $this->line("      Certificación: {$producto->certificacion_compostable}");
                }
                if ($producto->tiempo_compostaje_dias) {
                    $this->line("      Tiempo compostaje: {$producto->tiempo_compostaje_dias} días");
                }
            }
        } else {
            $this->line('   ℹ️  No hay productos terminados registrados aún');
        }

        $this->line("   📊 Total productos activos: {$productos->count()}");
    }

    /**
     * Verificar maquinaria con parámetros OEE
     */
    protected function verificarMaquinariaOEE()
    {
        $this->line('');
        $this->line('⚙️  Maquinaria con Parámetros OEE:');

        $maquinas = Maquinaria::where('activo', true)->get();

        if ($maquinas->count() > 0) {
            foreach ($maquinas as $maquina) {
                $this->line("   ✅ {$maquina->codigo_maquina} - {$maquina->nombre_maquina}");
                $this->line("      Estado: {$maquina->estado_actual} | Capacidad: {$maquina->capacidad_produccion} kg/h");

                // Calcular OEE si es posible
                try {
                    $oee = $maquina->calcularOEE();
                    $this->line("      OEE calculado: " . number_format($oee * 100, 1) . "%");
                } catch (\Exception $e) {
                    $this->line("      ⚠️  Error calculando OEE: " . $e->getMessage());
                }
            }
        } else {
            $this->line('   ℹ️  No hay maquinaria registrada aún');
        }

        $this->line("   📊 Total máquinas activas: {$maquinas->count()}");
    }
}
