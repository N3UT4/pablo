<?php
// =====================================================================
// FILE: app/models/ModeloCitas.php
// =====================================================================
// DESCRIPCIÓN: Modelo de datos para la tabla 'appointments' (citas). Proporciona operaciones CRUD completas, consultas con JOINs a usuarios, artistas y servicios, reportes exportables con filtro por fechas, listados para el panel de tatuador, y métodos de métricas/transacciones para el dashboard administrativo.
// UBICACIÓN MVC: Model
// ¿POR QUÉ EXISTE? Encapsula todas las consultas SQL relacionadas con citas. Los controladores (ControladorTatuador, ControladorDashboard) y el DashboardService delegan las consultas de citas a este modelo.
// CÓMO SE USA: Instanciado por ControladorTatuador, ControladorDashboard y DashboardService. Los métodos retornan arrays asociativos (fetchAll) o PDOStatement (para conteos).
// CAMPOS DE LA TABLA appointments:
//   id, user_id, artist_id, service_id, fecha_cita, hora_cita, detalle_personalizado, observaciones, estado, created_at
// ESTADOS DE CITA: pendiente, confirmada, completada, cancelada
// MÉTODOS CLAVE POR CATEGORÍA:
//   CRUD: create(), find(), all(), update(), delete()
//   Reportes: reporteCitas() — CSV con subquery de total abonado
//   Cliente: listByUser(), latestPendingConsent()
//   Tatuador: findByArtist(), getDetalleCliente()
//   Métricas: getTotalCitas(), getCountByEstado(), getCitasThisMonth(), getTotalAbonos(), getLatestCitas(), getTransacciones()
// CONSULTAS COMPLEJAS:
//   - reporteCitas(): subquery SELECT IFNULL(SUM(monto)) para calcular abonos por cita
//   - listByUser()/findByArtist(): LEFT JOIN payments para datos de pago
//   - getDetalleCliente(): múltiples LEFT JOIN (consents, payments) para el expediente del tatuador
// NOTA: getLatestCitas() y getTransacciones() usan LIMIT con variable PHP interpolada (no parámetro). Aunque el valor es siempre int, es una práctica que debería revisarse.
// RECURSOS: Hereda de ModeloBase (conexión PDO, método execute()).
// =====================================================================

// Modelo de datos para citas/appointment.
// Proporciona operaciones CRUD y consultas especializadas para
// citas, reportes, métricas de dashboard y transacciones.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloCitas extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Crea una nueva cita y retorna su ID.
    public function create(array $data): int
    {
        // INSERT con parámetros nombrados — estado por defecto 'pendiente'
        $this->execute(
            'INSERT INTO appointments
                (user_id, artist_id, service_id, fecha_cita, hora_cita, detalle_personalizado, observaciones, estado)
             VALUES
                (:user_id, :artist_id, :service_id, :fecha_cita, :hora_cita, :detalle_personalizado, :observaciones, "pendiente")',
            [
                'user_id' => $data['user_id'],
                'artist_id' => $data['artist_id'],
                'service_id' => $data['service_id'],
                'fecha_cita' => $data['fecha_cita'],
                'hora_cita' => $data['hora_cita'],
                'detalle_personalizado' => $data['detalle_personalizado'] ?: null,
                'observaciones' => $data['observaciones'] ?: null,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    // Busca una cita por ID con datos del tatuador y servicio.
    // SELECT con JOINs a artists y services para obtener nombres legibles
    public function find(int $id): ?array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.user_id, ap.artist_id, ap.service_id, ap.fecha_cita, ap.hora_cita,
                    ap.detalle_personalizado, ap.observaciones, ap.estado,
                    a.nombre AS tatuador, s.nombre AS servicio
             FROM appointments ap
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             WHERE ap.id = :id LIMIT 1',
            ['id' => $id]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // Lista todas las citas ordenadas por fecha descendente.
    // SELECT con JOINs a users, artists y services — ordena por fecha/hora descendente
    public function all(): array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.fecha_cita, ap.hora_cita, ap.estado,
                    u.nombre AS cliente, a.nombre AS tatuador, s.nombre AS servicio
             FROM appointments ap
             JOIN users u ON u.id = ap.user_id
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC'
        );
        return $statement->fetchAll();
    }

    // Actualiza campos de una cita existente.
    public function update(int $id, array $data): bool
    {
        // Construye la cláusula SET dinámicamente solo con campos presentes (lista blanca)
        $campos = ['fecha_cita', 'hora_cita', 'detalle_personalizado', 'observaciones', 'estado'];
        $sets = [];
        $params = ['id' => $id];
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $sets[] = "$campo = :$campo";
                $params[$campo] = $data[$campo];
            }
        }
        if (empty($sets)) {
            return false;
        }
        $this->execute('UPDATE appointments SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
        return true;
    }

    // Elimina una cita por ID.
    public function delete(int $id): bool
    {
        $this->execute('DELETE FROM appointments WHERE id = :id', ['id' => $id]);
        return true;
    }

    // Datos para el reporte exportable de citas (rúbrica: reportes).
    // Permite filtrar por rango de fechas.
    public function reporteCitas(?string $desde = null, ?string $hasta = null): array
    {
        // Subquery: calcula el total abonado por cita (SUM de pagos pendientes/verificados)
        // La cláusula WHERE 1=1 permite concatenar filtros de fecha dinámicamente
        $sql = 'SELECT ap.id, u.nombre AS cliente, u.email AS email_cliente,
                       a.nombre AS tatuador, s.nombre AS servicio,
                       ap.fecha_cita, ap.hora_cita, ap.estado,
                       (SELECT IFNULL(SUM(p.monto), 0) FROM payments p
                         WHERE p.appointment_id = ap.id
                           AND p.estado IN ("pendiente","verificado")) AS total_abonado
                FROM appointments ap
                JOIN users u ON u.id = ap.user_id
                JOIN artists a ON a.id = ap.artist_id
                JOIN services s ON s.id = ap.service_id
                WHERE 1 = 1';
        $params = [];
        if ($desde) {
            $sql .= ' AND ap.fecha_cita >= :desde';
            $params['desde'] = $desde;
        }
        if ($hasta) {
            $sql .= ' AND ap.fecha_cita <= :hasta';
            $params['hasta'] = $hasta;
        }
        $sql .= ' ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC';

        return $this->execute($sql, $params)->fetchAll();
    }

    // Lista las citas de un usuario específico con datos de pago incluidos.
    public function listByUser(int $userId): array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.fecha_cita, ap.hora_cita, ap.estado, ap.detalle_personalizado, ap.observaciones,
                    a.nombre AS tatuador, s.nombre AS servicio,
                    p.monto, p.metodo, p.estado AS estado_pago
             FROM appointments ap
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             LEFT JOIN payments p ON p.appointment_id = ap.id
             WHERE ap.user_id = :user_id
             ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC',
            ['user_id' => $userId]
        );
        return $statement->fetchAll();
    }

    // Última cita del usuario que aún no tiene consentimiento firmado.
    public function latestPendingConsent(int $userId): ?array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.fecha_cita
             FROM appointments ap
             LEFT JOIN consents c ON c.appointment_id = ap.id
             WHERE ap.user_id = :user_id AND c.id IS NULL
             ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC
             LIMIT 1',
            ['user_id' => $userId]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // --- Métodos para el panel de tatuador ---

    // Lista las citas asignadas a un artista específico.
    public function findByArtist(int $artistId): array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.fecha_cita, ap.hora_cita, ap.estado, ap.detalle_personalizado, ap.observaciones,
                    u.nombre AS cliente, u.email AS email_cliente, u.documento, u.telefono,
                    s.nombre AS servicio, p.monto, p.estado AS estado_pago
             FROM appointments ap
             JOIN users u ON u.id = ap.user_id
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             LEFT JOIN payments p ON p.appointment_id = ap.id
             WHERE ap.artist_id = :artist_id
             ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC',
            ['artist_id' => $artistId]
        );
        return $statement->fetchAll();
    }

    // Cambia el estado de una cita (pendiente, confirmada, completada, cancelada).
    public function updateEstado(int $citaId, string $estado): bool
    {
        $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
        if (!in_array($estado, $estadosValidos, true)) {
            return false;
        }
        $this->execute(
            'UPDATE appointments SET estado = :estado WHERE id = :id',
            ['estado' => $estado, 'id' => $citaId]
        );
        return true;
    }

    // Detalle completo del cliente para el expediente del tatuador.
    // Incluye datos de consentimiento y pago.
    // SELECT con múltiples LEFT JOINs: consents (firma/legal) y payments (abono)
    // Permite ver el expediente completo de un cliente para el tatuador
    public function getDetalleCliente(int $citaId, int $artistId): ?array
    {
        $statement = $this->execute(
            'SELECT ap.id, ap.fecha_cita, ap.hora_cita, ap.estado, ap.detalle_personalizado, ap.observaciones,
                    u.id AS user_id, u.nombre AS cliente, u.email AS email_cliente, u.documento, u.telefono, u.fecha_nacimiento,
                    s.nombre AS servicio, s.precio_desde,
                    c.nombre_cliente AS consent_nombre, c.documento AS consent_documento,
                    c.fecha_nacimiento AS consent_fecha_nac, c.firma_cliente, c.acepta_riesgos,
                    p.monto AS abono_monto, p.metodo AS abono_metodo, p.estado AS abono_estado
             FROM appointments ap
             JOIN users u ON u.id = ap.user_id
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             LEFT JOIN consents c ON c.appointment_id = ap.id
             LEFT JOIN payments p ON p.appointment_id = ap.id
             WHERE ap.id = :id AND ap.artist_id = :artist_id
             LIMIT 1',
            ['id' => $citaId, 'artist_id' => $artistId]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // --- Métodos para métricas y transacciones del dashboard ---

    // Total de citas.
    public function getTotalCitas(): \PDOStatement
    {
        return $this->execute('SELECT COUNT(*) AS c FROM appointments');
    }

    // Conteo de citas por estado.
    public function getCountByEstado(string $estado): \PDOStatement
    {
        return $this->execute(
            'SELECT COUNT(*) AS c FROM appointments WHERE estado = :estado',
            ['estado' => $estado]
        );
    }

    // Citas programadas desde hoy.
    public function getCitasThisMonth(): \PDOStatement
    {
        return $this->execute('SELECT COUNT(*) AS c FROM appointments WHERE fecha_cita >= CURDATE()');
    }

    // Total de abonos pendientes/verificados.
    public function getTotalAbonos(): \PDOStatement
    {
        return $this->execute(
            'SELECT COALESCE(SUM(monto), 0) AS total FROM payments WHERE estado IN ("pendiente","verificado")'
        );
    }

    // Últimas N citas con datos completos.
    public function getLatestCitas(int $limit = 5): array
    {
        // LIMIT con variable PHP interpolada directamente en el string SQL.
        // Aunque $limit es siempre int, esto sería vulnerable si se pasara string.
        $statement = $this->execute(
            "SELECT ap.id, u.nombre AS cliente, a.nombre AS tatuador, s.nombre AS servicio,
                    ap.fecha_cita, ap.hora_cita, ap.estado,
                    p.monto, p.estado AS estado_pago
             FROM appointments ap
             JOIN users u ON u.id = ap.user_id
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             LEFT JOIN payments p ON p.appointment_id = ap.id
             ORDER BY ap.created_at DESC
             LIMIT $limit"
        );
        return $statement->fetchAll();
    }

    // Lista de transacciones para administración.
    public function getTransacciones(int $limit = 200): array
    {
        $statement = $this->execute(
            "SELECT ap.id AS cita_id, u.nombre AS cliente, a.nombre AS tatuador,
                    s.nombre AS servicio, ap.fecha_cita, ap.hora_cita, ap.estado,
                    p.monto, p.metodo, p.estado AS estado_pago, ap.detalle_personalizado
             FROM appointments ap
             JOIN users u ON u.id = ap.user_id
             JOIN artists a ON a.id = ap.artist_id
             JOIN services s ON s.id = ap.service_id
             LEFT JOIN payments p ON p.appointment_id = ap.id
             ORDER BY ap.fecha_cita DESC, ap.hora_cita DESC
             LIMIT $limit"
        );
        return $statement->fetchAll();
    }
}
