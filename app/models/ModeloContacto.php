<?php
// Modelo de datos para contacto/mensajes.
// Almacena los mensajes enviados a través del formulario de contacto.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloContacto extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Registra un nuevo mensaje de contacto con estado "nuevo".
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO mensajes (nombre, email, asunto, mensaje, estado)
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
