<?php

require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloHorariosTatuador extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByArtist(int $artistId): array
    {
        $statement = $this->execute(
            'SELECT id, artist_id, dia_semana, hora_inicio, hora_fin, disponible
             FROM artist_schedules
             WHERE artist_id = :artist_id
             ORDER BY FIELD(dia_semana, 1,2,3,4,5,6,0)',
            ['artist_id' => $artistId]
        );
        return $statement->fetchAll();
    }

    public function save(int $artistId, int $dia, string $horaInicio, string $horaFin, bool $disponible): bool
    {
        $this->execute(
            'INSERT INTO artist_schedules (artist_id, dia_semana, hora_inicio, hora_fin, disponible)
             VALUES (:artist_id, :dia_semana, :hora_inicio, :hora_fin, :disponible)
             ON DUPLICATE KEY UPDATE
               hora_inicio = VALUES(hora_inicio),
               hora_fin = VALUES(hora_fin),
               disponible = VALUES(disponible)',
            [
                'artist_id' => $artistId,
                'dia_semana' => $dia,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'disponible' => $disponible ? 1 : 0,
            ]
        );
        return true;
    }

    public function toggleDay(int $artistId, int $dia, bool $disponible): bool
    {
        $this->execute(
            'INSERT INTO artist_schedules (artist_id, dia_semana, disponible)
             VALUES (:artist_id, :dia_semana, :disponible)
             ON DUPLICATE KEY UPDATE disponible = VALUES(disponible)',
            [
                'artist_id' => $artistId,
                'dia_semana' => $dia,
                'disponible' => $disponible ? 1 : 0,
            ]
        );
        return true;
    }
}
