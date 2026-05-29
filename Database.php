<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../Logs/error.log');

class Database {
    private static ?PDO $conn = null;
    public static function getConnection(): PDO {
        if(self::$conn === null) {
            $dsn = "mysql:host=localhost;dbname=Portal_OOP;charset=utf8mb4";

            try {
                self::$conn = new PDO($dsn, 'root', '');

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                die("Connection error: " .$e->getMessage()); 
            }
        }
        return self::$conn;
    }
}

?>
