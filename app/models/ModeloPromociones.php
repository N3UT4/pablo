<?php

require_once APP_ROOT . '/core/ModeloBase.php';

class ModeloPromociones extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

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

    public function alreadyRedeemed(int $promotionId, int $userId): bool
    {
        $statement = $this->execute(
            'SELECT id FROM promotion_redemptions
             WHERE promotion_id = :promotion_id AND user_id = :user_id AND appointment_id IS NULL
             LIMIT 1',
            ['promotion_id' => $promotionId, 'user_id' => $userId]
        );
        return (bool) $statement->fetch();
    }

    public function redeem(int $promotionId, int $userId): int
    {
        $this->execute(
            'INSERT INTO promotion_redemptions (promotion_id, user_id)
             VALUES (:promotion_id, :user_id)',
            ['promotion_id' => $promotionId, 'user_id' => $userId]
        );
        return (int) $this->db->lastInsertId();
    }
}
