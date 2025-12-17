<?php
require "../Database.php";

// Dynamic FK Mapping Rules
$fkLookup = [
    'game_id'      => ['table' => 'public_games', 'column' => 'name'],
    'category_id'  => ['table' => 'game_categories', 'column' => 'name'],
    'platform_id'  => ['table' => 'game_platforms', 'column' => 'name'],
    'user_id'      => ['table' => 'public_users', 'column' => 'username'],
    'studio_id'    => ['table' => 'public_studios', 'column' => 'name'],
    'role_id'      => ['table' => 'hrbac_roles', 'column' => 'name'],
    'permission_id' => ['table' => 'hrbac_permissions', 'column' => 'name'],
    'parent_role_id' => ['table' => 'hrbac_roles', 'column' => 'name'],
    'child_role_id' => ['table' => 'hrbac_roles', 'column' => 'name'],
    'game_id'      => ['table' => 'public_games', 'column' => 'name'],
];

$fkMap = [
    'game_id'      => ['table' => 'public_games', 'label' => 'name'],
    'category_id'  => ['table' => 'game_categories', 'label' => 'name'],
    'platform_id'  => ['table' => 'game_platforms', 'label' => 'name'],
    'user_id'      => ['table' => 'public_users', 'label' => 'username'],
    'studio_id'    => ['table' => 'public_studios', 'label' => 'name'],
    'role_id'      => ['table' => 'hrbac_roles', 'label' => 'name'],
    'permission_id' => ['table' => 'hrbac_permissions', 'label' => 'name'],
];

$pdo = (new Database())->pdo();
$apiUrl = "../index.php";

// Get list of all tables in the database
$tables = $pdo->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_TYPE = 'BASE TABLE'
")->fetchAll(PDO::FETCH_COLUMN);
