<?php
// Modelo de datos para artistas/tatuadores.
// Lista artistas activos y verifica su existencia.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloArtistas extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lista todos los artistas activos.
    public function listActive(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, bio, foto FROM artists WHERE activo = 1 ORDER BY id ASC'
        );
        return $statement->fetchAll();
    }

    // Verifica que un artista exista y esté activo.
    public function exists(int $artistId): bool
    {
        $statement = $this->execute(
            'SELECT id FROM artists WHERE id = :id AND activo = 1 LIMIT 1',
            ['id' => $artistId]
        );
        return (bool) $statement->fetch();
    }
}
