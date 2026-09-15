<?php

require_once APP_ROOT . '/app/models/ModeloCitas.php';

// Reportes exportables (rúbrica: generación de reportes).
//   index.php?action=reporte-citas        -> descarga CSV (Excel)
//   index.php?action=reporte-citas-json   -> reporte en JSON
// Parámetros opcionales: desde=YYYY-MM-DD&hasta=YYYY-MM-DD
class ControladorReportes extends ControladorBase
{
    private ModeloCitas $appointmentModel;

    public function __construct()
    {
        // Control de acceso por rol: los reportes son solo para admin/tatuador.
        $rol = $_SESSION['user']['rol'] ?? null;
        if (!in_array($rol, ['admin', 'tatuador'], true)) {
            $this->json(false, 'Acceso restringido: solo administradores o tatuadores.', [], 403);
        }
        $this->appointmentModel = new ModeloCitas();
    }

    private function filtros(): array
    {
        $desde = trim((string) ($_GET['desde'] ?? '')) ?: null;
        $hasta = trim((string) ($_GET['hasta'] ?? '')) ?: null;
        return [$desde, $hasta];
    }

    // Reporte en JSON (consumible por la API / frontend).
    public function citasJson(): void
    {
        try {
            [$desde, $hasta] = $this->filtros();
            $filas = $this->appointmentModel->reporteCitas($desde, $hasta);
            $this->json(true, 'ok', [
                'total' => count($filas),
                'desde' => $desde,
                'hasta' => $hasta,
                'data' => $filas,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible generar el reporte.', [], 500);
        }
    }

    // Reporte exportable en CSV (se abre en Excel).
    public function citasCsv(): void
    {
        try {
            [$desde, $hasta] = $this->filtros();
            $filas = $this->appointmentModel->reporteCitas($desde, $hasta);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_citas_' . date('Ymd_His') . '.csv"');

            $out = fopen('php://output', 'w');
            // BOM para que Excel muestre bien las tildes.
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Cliente', 'Email', 'Tatuador', 'Servicio', 'Fecha', 'Hora', 'Estado', 'Total abonado']);

            foreach ($filas as $f) {
                fputcsv($out, [
                    $f['id'],
                    $f['cliente'],
                    $f['email_cliente'],
                    $f['tatuador'],
                    $f['servicio'],
                    $f['fecha_cita'],
                    $f['hora_cita'],
                    $f['estado'],
                    $f['total_abonado'],
                ]);
            }

            fclose($out);
            exit;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible generar el reporte.', [], 500);
        }
    }
}
