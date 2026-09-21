<?php
// Modelo de datos para servicios.
// Gestiona los servicios de tatuaje (blackwork, realismo, etc.)
// con sus precios y descripciones.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloServicios extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lista todos los servicios activos.
    public function listActive(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, slug, descripcion, precio_desde FROM servicios WHERE activo = 1 ORDER BY id ASC'
        );
        return $statement->fetchAll();
    }

    // Busca un servicio por su slug (usado en el formulario de abono).
    public function findBySlug(string $slug): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, slug FROM servicios WHERE slug = :slug AND activo = 1 LIMIT 1',
            ['slug' => trim($slug)]
        );
        $service = $statement->fetch();
        return $service ?: null;
    }

    // Crea o actualiza un servicio.
    public function save(int $id, string $nombre, string $slug, float $precioDesde, string $descripcion): bool
    {
        if ($id > 0) {
            $this->execute(
                'UPDATE servicios SET nombre = :nombre, slug = :slug, precio_desde = :precio_desde, descripcion = :descripcion WHERE id = :id',
                ['nombre' => $nombre, 'slug' => $slug, 'precio_desde' => $precioDesde, 'descripcion' => $descripcion, 'id' => $id]
            );
        } else {
            $this->execute(
                'INSERT INTO servicios (nombre, slug, descripcion, precio_desde) VALUES (:nombre, :slug, :descripcion, :precio_desde)',
                ['nombre' => $nombre, 'slug' => $slug, 'descripcion' => $descripcion, 'precio_desde' => $precioDesde]
            );
        }
        return true;
    }

    // Elimina un servicio por ID.
    public function delete(int $id): bool
    {
        $this->execute('DELETE FROM servicios WHERE id = :id', ['id' => $id]);
        return true;
    }

    // Busca un servicio por ID y retorna su precio_desde.
    public function findById(int $id): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, slug, descripcion, precio_desde FROM servicios WHERE id = :id AND activo = 1 LIMIT 1',
            ['id' => $id]
        );
        $service = $statement->fetch();
        return $service ?: null;
    }
}
