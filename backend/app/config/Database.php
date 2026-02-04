
<?php
// Clase de conexión PDO reutilizable
class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error de conexión a la base de datos', 'details' => $e->getMessage()]);
                exit;
            }
        }
        return self::$instance;
    }
}
