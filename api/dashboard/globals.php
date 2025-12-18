<?php
require_once __DIR__ . '/lib.php';

$fkMap = fk_map();
$pdo = getPdo();
$apiUrl = "../index.php";

// Get list of all tables in the database
$tables = $pdo->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_TYPE = 'BASE TABLE'
")->fetchAll(PDO::FETCH_COLUMN);
