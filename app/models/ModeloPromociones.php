<?php
// Modelo de datos para promociones/cupones.
// Gestiona los cupones de descuento con fechas de vigencia
// y el registro de redenciones por usuario.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloPromociones extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lista las promociones activas y vigentes.
    public function listActive(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, descripcion, codigo, descuento, fecha_inicio, fecha_fin
             FROM promotions
             WHERE activo = 1
               AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
               AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
             ORDER BY fecha_fin ASC'
        );
        return $statement->fetchAll();
    }

    // Busca un cupón válido por código (activo y dentro de la vigencia).
    public function findValidByCode(string $codigo): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, descripcion, codigo, descuento, fecha_inicio, fecha_fin
             FROM promotions
             WHERE codigo = :codigo
               AND activo = 1
               AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
               AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
             LIMIT 1',
            ['codigo' => strtoupper(trim($codigo))]
        );
        $promo = $statement->fetch();
        return $promo ?: null;
    }

    // Verifica si un usuario ya canjeó una promoción.
    public function alreadyRedeemed(int $promotionId, int $userId): bool
    {
        $statement = $this->execute(
            'SELECT id FROM canjes
             WHERE promocion_id = :promocion_id AND usuario_id = :usuario_id AND cita_id IS NULL
             LIMIT 1',
            ['promocion_id' => $promotionId, 'usuario_id' => $userId]
        );
        return (bool) $statement->fetch();
    }

    // Registra la redención de un cupón para un usuario.
    public function redeem(int $promotionId, int $userId): int
    {
        $this->execute(
            'INSERT INTO canjes (promocion_id, usuario_id)
             VALUES (:promocion_id, :usuario_id)',
            ['promocion_id' => $promotionId, 'usuario_id' => $userId]
        );
        return (int) $this->db->lastInsertId();
    }
}
