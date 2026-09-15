<?php

require_once APP_ROOT . '/core/ModeloBase.php';

class ModeloContacto extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO contact_messages (nombre, email, asunto, mensaje, estado)
             VALUES (:nombre, :email, :asunto, :mensaje, "nuevo")',
            [
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'asunto' => $data['asunto'],
                'mensaje' => $data['mensaje'],
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
