<?php

require_once APP_ROOT . '/core/ModeloBase.php';

class ModeloServicios extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listActive(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, slug, descripcion, precio_desde FROM services WHERE activo = 1 ORDER BY id ASC'
        );
        return $statement->fetchAll();
    }

    // El formulario de abono envía el "slug" del estilo (blackwork, realismo, etc.).
    public function findBySlug(string $slug): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, slug FROM services WHERE slug = :slug AND activo = 1 LIMIT 1',
            ['slug' => trim($slug)]
        );
        $service = $statement->fetch();
        return $service ?: null;
    }
}
