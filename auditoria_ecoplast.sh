#!/bin/bash

echo "================================================"
echo "  AUDITORÍA COMPLETA - ECOPLAST SRL"
echo "  Verificación contra Documentación"
echo "================================================"
echo ""

# Tablas esperadas según documentación SQL
echo "=== TABLAS ESPERADAS (Según doc/ecoplast_database_mysql.sql) ==="
echo "1. roles"
echo "2. usuarios"
echo "3. turnos"
echo "4. asignacion_turnos"
echo "5. categorias_insumos"
echo "6. insumos"
echo "7. movimientos_inventario_insumos"
echo "8. formulaciones"
echo "9. componentes_formulacion"
echo "10. tipos_maquina"
echo "11. maquinas"
echo "12. mantenimientos"
echo "13. paros_maquina"
echo "14. categorias_productos"
echo "15. productos"
echo "16. movimientos_inventario_productos"
echo "17. ordenes_produccion"
echo "18. lotes_produccion"
echo "19. registros_produccion"
echo "20. inspecciones_calidad"
echo "21. defectos_calidad"
echo "22. registro_defectos"
echo "23. kpis_diarios"
echo "24. kpis_mensuales"
echo "25. alertas"
echo "26. certificaciones"
echo "27. auditorias"
echo ""
echo "Total esperado: 27 tablas"
echo ""

echo "=== MODELOS EXISTENTES ==="
ls -1 app/Models/*.php | wc -l | awk '{print "Total actual: "$1" modelos"}'
echo ""
ls -1 app/Models/*.php | sed 's|app/Models/||' | sed 's|.php||' | sort
echo ""

echo "=== VERIFICACIÓN DE MODELOS CRÍTICOS ==="
MODELOS_CRITICOS=(
    "Rol"
    "User"
    "Turno"
    "AsignacionTurno"
    "CategoriaInsumo"
    "Insumo"
    "MovimientoInventarioInsumo"
    "Formulacion"
    "ComponenteFormulacion"
    "TipoMaquinaria"
    "Maquinaria"
    "Mantenimiento"
    "ParadaProduccion"
    "CategoriaProducto"
    "ProductoTerminado"
    "MovimientoInventarioProducto"
    "OrdenProduccion"
    "LoteProduccion"
    "RegistroProduccion"
    "InspeccionCalidad"
    "DefectoCalidad"
    "RegistroDefecto"
    "KpiDiario"
    "KpiMensual"
    "Alerta"
    "Certificacion"
    "Auditoria"
)

for modelo in "${MODELOS_CRITICOS[@]}"; do
    if [ -f "app/Models/$modelo.php" ]; then
        echo "✅ $modelo.php"
    else
        echo "❌ FALTA: $modelo.php"
    fi
done

echo ""
echo "=== VERIFICACIÓN DE OBSERVERS ==="
if [ -f "app/Observers/RegistroProduccionObserver.php" ]; then
    echo "✅ RegistroProduccionObserver.php"
else
    echo "❌ FALTA: RegistroProduccionObserver.php"
fi

if [ -f "app/Observers/AlertaObserver.php" ]; then
    echo "✅ AlertaObserver.php"
else
    echo "❌ FALTA: AlertaObserver.php"
fi

if [ -f "app/Observers/InsumoObserver.php" ]; then
    echo "✅ InsumoObserver.php"
else
    echo "❌ FALTA: InsumoObserver.php"
fi

if [ -f "app/Observers/OrdenProduccionObserver.php" ]; then
    echo "✅ OrdenProduccionObserver.php"
else
    echo "❌ FALTA: OrdenProduccionObserver.php"
fi

echo ""
echo "================================================"
echo "  FIN DE AUDITORÍA"
echo "================================================"
