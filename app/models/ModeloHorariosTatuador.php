<?php
// Modelo de datos para horarios del tatuador.
// Gestiona la disponibilidad semanal de cada tatuador
// usando la tabla horarios_artistas con upsert (ON DUPLICATE KEY UPDATE).
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloHorariosTatuador extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtiene los horarios de un artista ordenados por día de la semana.
    // El campo dia_semana usa: 1=Lunes, 2=Martes, ..., 6=Sábado, 0=Domingo.
    public function getByArtist(int $artistId): array
    {
        $statement = $this->execute(
            'SELECT id, artista_id, dia_semana, hora_inicio, hora_fin, disponible
             FROM horarios_artistas
             WHERE artista_id = :artista_id
             ORDER BY FIELD(dia_semana, 1,2,3,4,5,6,0)',
            ['artista_id' => $artistId]
        );
        return $statement->fetchAll();
    }

    // Guarda/actualiza el horario de un día específico (upsert).
    public function save(int $artistId, int $dia, string $horaInicio, string $horaFin, bool $disponible): bool
    {
        $this->execute(
            'INSERT INTO horarios_artistas (artista_id, dia_semana, hora_inicio, hora_fin, disponible)
             VALUES (:artista_id, :dia_semana, :hora_inicio, :hora_fin, :disponible)
             ON DUPLICATE KEY UPDATE
               hora_inicio = VALUES(hora_inicio),
               hora_fin = VALUES(hora_fin),
               disponible = VALUES(disponible)',
            [
                'artista_id' => $artistId,
                'dia_semana' => $dia,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'disponible' => $disponible ? 1 : 0,
            ]
        );
        return true;
    }

    // Alterna la disponibilidad de un día sin cambiar horarios.
    public function toggleDay(int $artistId, int $dia, bool $disponible): bool
    {
        $this->execute(
            'INSERT INTO horarios_artistas (artista_id, dia_semana, disponible)
             VALUES (:artista_id, :dia_semana, :disponible)
             ON DUPLICATE KEY UPDATE disponible = VALUES(disponible)',
            [
                'artista_id' => $artistId,
                'dia_semana' => $dia,
                'disponible' => $disponible ? 1 : 0,
            ]
        );
        return true;
    }
}
