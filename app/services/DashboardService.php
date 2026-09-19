<?php
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
