<?php
require_once __DIR__ . '/..//Database.php';

/**
 * Small helper library for the dashboard to centralize common operations.
 * Keep functions tiny and well-named so the PHP templates stay readable.
 */

function getPdo()
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = (new Database())->pdo();
    }
    return $pdo;
}

function fk_map(): array
{
    // Single source-of-truth for FK columns used by the dashboard
    return [
        'game_id'        => ['table' => 'public_games',    'label' => 'name'],
        'category_id'    => ['table' => 'game_categories', 'label' => 'name'],
        'platform_id'    => ['table' => 'game_platforms', 'label' => 'name'],
        'user_id'        => ['table' => 'public_users',   'label' => 'username'],
        'studio_id'      => ['table' => 'public_studios', 'label' => 'name'],
        'role_id'        => ['table' => 'hrbac_roles',    'label' => 'name'],
        'permission_id'  => ['table' => 'hrbac_permissions', 'label' => 'name'],
        'parent_role_id' => ['table' => 'hrbac_roles',    'label' => 'name'],
        'child_role_id'  => ['table' => 'hrbac_roles',    'label' => 'name'],
    ];
}

function getTables(): array
{
    $pdo = getPdo();
    try {
        return $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'")
            ->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        return [];
    }
}

function getColumns(string $table): array
{
    $pdo = getPdo();
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?");
    $stmt->execute([$table]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getRows(string $table): array
{
    $pdo = getPdo();
    $stmt = $pdo->query("SELECT * FROM {$table}");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFkOptions(string $column): array
{
    $map = fk_map();
    if (!isset($map[$column])) return [];
    $pdo = getPdo();
    $ref = $map[$column];
    try {
        $stmt = $pdo->query("SELECT id, {$ref['label']} AS text FROM {$ref['table']} ORDER BY text");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getFkLabel(string $column, $value)
{
    if ($value === null || $value === '') return null;
    $map = fk_map();
    if (!isset($map[$column])) return null;
    $pdo = getPdo();
    $ref = $map[$column];
    try {
        $stmt = $pdo->prepare("SELECT {$ref['label']} FROM {$ref['table']} WHERE id = ?");
        $stmt->execute([$value]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return null;
    }
}
