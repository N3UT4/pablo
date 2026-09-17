<?php

require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloPagos extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO payments (appointment_id, monto, metodo, comprobante, estado)
             VALUES (:appointment_id, :monto, :metodo, :comprobante, "pendiente")',
            [
                'appointment_id' => $data['appointment_id'],
                'monto' => $data['monto'],
                'metodo' => $data['metodo'],
                'comprobante' => $data['comprobante'] ?: null,
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
