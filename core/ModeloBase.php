<?php
// Modelo base abstracto que proporciona la conexión a la base de datos
// mediante PDO y un método auxiliar para ejecutar consultas preparadas.
// Todos los modelos del proyecto heredan de esta clase.
abstract class ModeloBase
{
    protected PDO $db;

    public function __construct()
    {
        // Crea la conexión PDO con UTF-8 y excepciones para errores
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        $this->db = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    // Ejecuta una consulta SQL preparada con los parámetros dados.
    // Retorna el statement listo para fetch/fetchAll.
    protected function execute(string $query, array $parameters = []): PDOStatement
    {
        $statement = $this->db->prepare($query);
        $statement->execute($parameters);
        return $statement;
    }
}
