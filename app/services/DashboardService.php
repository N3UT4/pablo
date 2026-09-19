<?php
// =====================================================================
// FILE: app/servicios/DashboardService.php
// =====================================================================
// DESCRIPCIÓN: Service de negocio del dashboard. Extrae la lógica de consulta y agregación de datos del ControladorDashboard, centralizando el cálculo de métricas, listados de transacciones/usuarios/servicios en un único lugar.
// UBICACIÓN MVC: Service (capa de lógica de negocio)
// ¿POR QUÉ EXISTE? Mantiene los controladores delgados (thin controllers). Los controladores solo verifican permisos, llaman al service y pasan los datos a la vista. Si la lógica de negocio cambia, solo se modifica aquí.
// CÓMO SE USA: Instanciado por ControladorDashboard en el constructor, recibiendo ModeloCitas, ModeloUsuarios y ModeloServicios como dependencias.
// MÉTODOS CLAVE:
//   - getMetrics(): retorna un array con total_citas, citas por estado (pendientes/confirmadas/completadas/canceladas), citas_mes, total_abonos, y últimas 5 citas.
//   - getTransacciones($limit): delega a ModeloCitas::getTransacciones() para listar pagos/asociados a citas.
//   - getUsuarios(): delega a ModeloUsuarios::all() para listar todos los usuarios.
//   - getServicios(): delega a ModeloServicios::listActive() para listar servicios activos.
// ESTRUCTURA DE DATOS de getMetrics():
//   {
//     total_citas: int,        // COUNT(*) de citas
//     pendientes: int,         // COUNT con estado='pendiente'
//     confirmadas: int,        // COUNT con estado='confirmada'
//     completadas: int,        // COUNT con estado='completada'
//     canceladas: int,         // COUNT con estado='cancelada'
//     citas_mes: int,          // COUNT con fecha_cita >= CURDATE()
//     total_abonos: float,     // SUM(monto) de abonos con estado pendiente/verificado
//     ultimas_citas: array     // 5 citas más recientes con datos completos
//   }
// MANEJO DE ERRORES: cada método envuelve las llamadas en try/catch y retorna valores por defecto (0, [], 0.0) si la BD falla, registrando el error con error_log().
// RECURSOS: ModeloCitas, ModeloUsuarios, ModeloServicios (inyectados en constructor).
// =====================================================================

// Service: Operaciones del dashboard (métricas, transacciones, gestión).
// Extrae la lógica de negocio y consultas SQL de ControladorDashboard.
class DashboardService
{
    private ModeloCitas $citaModel;
    private ModeloUsuarios $userModel;
    private ModeloServicios $serviceModel;

    public function __construct(ModeloCitas $citaModel, ModeloUsuarios $userModel, ModeloServicios $serviceModel)
    {
        $this->citaModel = $citaModel;
        $this->userModel = $userModel;
        $this->serviceModel = $serviceModel;
    }

    // Métricas generales del dashboard (solo admin).
    // Inicializa con valores por defecto para evitar errores si la BD falla.
    // Estructura: total_citas, estados (pendientes/confirmadas/completadas/canceladas),
    //             citas_mes, total_abonos, ultimas_citas
    public function getMetrics(): array
    {
        $metrics = [
            'total_citas' => 0,
            'pendientes' => 0,
            'confirmadas' => 0,
            'completadas' => 0,
            'canceladas' => 0,
            'citas_mes' => 0,
            'total_abonos' => 0.0,
            'ultimas_citas' => [],
        ];

        try {
            $totalCitas = (int) ($this->citaModel->getTotalCitas()->fetch()['c'] ?? 0);
            $pendientes = (int) ($this->citaModel->getCountByEstado('pendiente')->fetch()['c'] ?? 0);
            $confirmadas = (int) ($this->citaModel->getCountByEstado('confirmada')->fetch()['c'] ?? 0);
            $completadas = (int) ($this->citaModel->getCountByEstado('completada')->fetch()['c'] ?? 0);
            $canceladas = (int) ($this->citaModel->getCountByEstado('cancelada')->fetch()['c'] ?? 0);
            $citasMes = (int) ($this->citaModel->getCitasThisMonth()->fetch()['c'] ?? 0);
            $totalAbonos = (float) ($this->citaModel->getTotalAbonos()->fetch()['total'] ?? 0);

            $ultimasCitas = $this->citaModel->getLatestCitas(5) ?: [];

            $metrics = [
                'total_citas' => $totalCitas,
                'pendientes' => $pendientes,
                'confirmadas' => $confirmadas,
                'completadas' => $completadas,
                'canceladas' => $canceladas,
                'citas_mes' => $citasMes,
                'total_abonos' => $totalAbonos,
                'ultimas_citas' => $ultimasCitas,
            ];
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando métricas: ' . $e->getMessage());
        }

        return $metrics;
    }

    // Lista las transacciones/pagos para administración.
    public function getTransacciones(int $limit = 200): array
    {
        try {
            return $this->citaModel->getTransacciones($limit);
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando transacciones: ' . $e->getMessage());
            return [];
        }
    }

    // Lista todos los usuarios (solo administrador).
    public function getUsuarios(): array
    {
        try {
            return $this->userModel->all();
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando usuarios: ' . $e->getMessage());
            return [];
        }
    }

    // Lista los servicios activos.
    public function getServicios(): array
    {
        try {
            return $this->serviceModel->listActive();
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando servicios: ' . $e->getMessage());
            return [];
        }
    }
}
