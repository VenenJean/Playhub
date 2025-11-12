<?php
class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            $servername = "(localdb)\\MSSQLLocalDB";
            $username = "php_user";
            $password = "StrongPassword123!";
            $database = "playhub";

            try {
                self::$conn = new PDO("sqlsrv:server=$servername;Database=$database", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die(json_encode(["error" => "Database connection failed", "message" => $e->getMessage()]));
            }
        }
        return self::$conn;
    }
}
