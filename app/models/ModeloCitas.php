<?php

require_once APP_ROOT . '/core/ModeloBase.php';

class ModeloCitas extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data): int
    {
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

    public function update(int $id, array $data): bool
    {
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

    public function delete(int $id): bool
    {
        $this->execute('DELETE FROM appointments WHERE id = :id', ['id' => $id]);
        return true;
    }

    // Datos para el reporte exportable de citas (rúbrica: reportes).
    public function reporteCitas(?string $desde = null, ?string $hasta = null): array
    {
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

    // Última cita del usuario que aún no tiene consentimiento firmado (usada por ControladorConsentimiento).
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
}