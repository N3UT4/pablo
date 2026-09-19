<?php
// Modelo de datos para galería.
// Gestiona las fotos de trabajos de tatuaje con título, descripción y artista.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloGaleria extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Fotos activas, más recientes primero. $limit = 0 trae todas.
    public function listActive(int $limit = 0): array
    {
        $sql = 'SELECT g.id, g.titulo, g.descripcion, g.imagen, g.created_at, a.nombre AS artista
                FROM galeria g
                LEFT JOIN artistas a ON a.id = g.artista_id
                WHERE g.activo = 1
                ORDER BY g.created_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        return $this->execute($sql)->fetchAll();
    }

    // Registra una nueva foto en la galería.
    public function create(array $data): int
    {
        $titulo = trim((string) ($data['titulo'] ?? ''));
        $imagen = trim((string) ($data['imagen'] ?? ''));

        if ($titulo === '' || $imagen === '') {
            throw new InvalidArgumentException('Faltan el título o la imagen del trabajo.');
        }

        $this->execute(
            'INSERT INTO galeria (artista_id, titulo, descripcion, imagen, activo)
             VALUES (:artista_id, :titulo, :descripcion, :imagen, 1)',
            [
                'artista_id' => $data['artista_id'] ?: null,
                'titulo' => $titulo,
                'descripcion' => trim((string) ($data['descripcion'] ?? '')) ?: null,
                'imagen' => $imagen,
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
