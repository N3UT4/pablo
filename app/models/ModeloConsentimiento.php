<?php
// Modelo de datos para consentimientos informados.
// Almacena los datos del consentimiento firmado por el cliente
// incluyendo firma digital y datos del acudiente (si aplica).
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloConsentimiento extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Verifica si ya existe un consentimiento para una cita.
    public function existsForAppointment(int $appointmentId): bool
    {
        $statement = $this->execute(
            'SELECT id FROM consents WHERE appointment_id = :id LIMIT 1',
            ['id' => $appointmentId]
        );
        return (bool) $statement->fetch();
    }

    // Registra un consentimiento informado completo.
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO consents
                (appointment_id, nombre_cliente, documento, fecha_nacimiento, procedimiento, acepta_riesgos,
                 firma_cliente, acudiente_nombre, acudiente_documento, parentesco, firma_acudiente)
             VALUES
                (:appointment_id, :nombre_cliente, :documento, :fecha_nacimiento, :procedimiento, :acepta_riesgos,
                 :firma_cliente, :acudiente_nombre, :acudiente_documento, :parentesco, :firma_acudiente)',
            [
                'appointment_id' => $data['appointment_id'],
                'nombre_cliente' => $data['nombre_cliente'],
                'documento' => $data['documento'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'procedimiento' => $data['procedimiento'],
                'acepta_riesgos' => $data['acepta_riesgos'] ? 1 : 0,
                'firma_cliente' => $data['firma_cliente'],
                'acudiente_nombre' => $data['acudiente_nombre'] ?: null,
                'acudiente_documento' => $data['acudiente_documento'] ?: null,
                'parentesco' => $data['parentesco'] ?: null,
                'firma_acudiente' => $data['firma_acudiente'] ?: null,
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
