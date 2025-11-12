<?php

class Database
{
    private static $conn = null;

    // Returns a shared PDO connection
    public static function getConnection()
    {
        if (self::$conn === null) {
            // DB credentials - Torben
            // $host = 'db5018764785.hosting-data.io';
            // $db   = 'dbs14835705';
            // $user = 'dbu2235954';
            // $pass = 'PHub%2025!';

            // DB Local credentials
            $host = "(localdb)\\MSSQLLocalDB";
            $user = "php_user";
            $pass = "StrongPassword123!";
            $db = "playhub";

            $dsn = "sqlsrv:server=$host;Database=$db;";

            try {
                self::$conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die(json_encode(["error" => "Database connection failed", "message" => $e->getMessage()]));
            }
        }
        return self::$conn;
    }

    // Close the shared connection
    public static function close()
    {
        if (self::$conn !== null) {
            self::$conn = null;
        }
    }
}
