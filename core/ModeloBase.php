<?php
// =====================================================================
// FILE: core/ModeloBase.php
// =====================================================================
// DESCRIPCIÓN: Clase abstracta base para todos los modelos. Establece la conexión a la base de datos mediante PDO (con charset utf8mb4, modo de excepciones y prepares emulados desactivados) y expone un método execute() para ejecutar consultas preparadas de forma segura.
// UBICACIÓN MVC: Model (base)
// ¿POR QUÉ EXISTE? Centraliza la conexión a la BD y los patrones de consulta. Los modelos concretos (ModeloUsuarios, ModeloCitas, etc.) heredan $this->db y $this->execute() sin repetir la lógica de conexión.
// CÓMO SE USA: Cada modelo concreto extiende ModeloBase, llama a parent::__construct() y usa $this->execute($sql, $params) para ejecutar consultas con parámetros nombrados.
// MÉTODOS CLAVE:
//   - __construct(): crea la conexión PDO usando las constantes DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD.
//   - execute($query, $parameters): prepara y ejecuta la consulta; retorna el PDOStatement para que el llamador haga fetch/fetchAll.
// CONFIGURACIÓN PDO:
//   - ERRMODE_EXCEPTION: lanza excepciones en errores SQL (capturadas por try/catch en controladores).
//   - FETCH_ASSOC: devuelve arrays asociativos por defecto.
//   - EMULATE_PREPARES=false: usa prepared statements reales de MySQL (más seguro contra inyección).
// =====================================================================

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

    // Obtiene la conexi�n PDO subyacente para transacciones.
    public function getConnection(): PDO
    {
        return $this->db;
    }
}
