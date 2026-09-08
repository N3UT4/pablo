<?php

abstract class Model
{
    protected ?PDO $db = null;

    public function __construct()
    {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $this->db = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $this->db = null;
            error_log('DB connection unavailable: ' . $e->getMessage());
        }
    }

    protected function execute(string $query, array $parameters = []): PDOStatement
    {
        if ($this->db === null) {
            throw new RuntimeException('La base de datos no está disponible en este momento.');
        }

        $statement = $this->db->prepare($query);
        $statement->execute($parameters);
        return $statement;
    }
}
