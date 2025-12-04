<?php

class Database
{
    private $pdo;

    public function __construct()
    {
        $config = require __DIR__ . "/config.php";

        // Connection String for MSSQL
        $connectionString = "sqlsrv:Server={$config['db_host']};Database={$config['db_name']}";

        $this->pdo = new PDO($connectionString, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function pdo()
    {
        return $this->pdo;
    }
}
