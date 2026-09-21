<?php
// Modelo de datos para pagos/abonos.
// Proporciona operaciones CRUD para la tabla abonos.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloPagos extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Registra un nuevo pago/abono para una cita.
    // El estado inicial es siempre "pendiente".
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO abonos (cita_id, monto, metodo, comprobante, estado)
             VALUES (:cita_id, :monto, :metodo, :comprobante, "pendiente")',
            [
                'cita_id' => $data['cita_id'],
                'monto' => $data['monto'],
                'metodo' => $data['metodo'],
                'comprobante' => $data['comprobante'] ?: null,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    // Actualiza el estado de un pago/abono.
    public function updateEstado(int $pagoId, string $estado): bool
    {
        $estadosValidos = ['pendiente', 'verificado', 'rechazado'];
        if (!in_array($estado, $estadosValidos, true)) {
            return false;
        }
        $this->execute(
            'UPDATE abonos SET estado = :estado WHERE id = :id',
            ['estado' => $estado, 'id' => $pagoId]
        );
        return true;
    }
}
