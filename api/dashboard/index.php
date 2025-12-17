<?php
require "../Database.php";

$pdo = (new Database())->pdo();
$apiUrl = "../index.php";

// Get list of all tables in the database
$tables = $pdo->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_TYPE = 'BASE TABLE'
")->fetchAll(PDO::FETCH_COLUMN);

// Get query parameter table or default to first table
$table = $_GET["table"] ?? $tables[0];
?>
<!DOCTYPE html>
<html>

<!-- Basic HTML Head -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic API Dashboard</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <h1>📊 API Dashboard</h1>

    <?php include "./components/tableDropDown.php"; ?>

    <hr>

    <!-- Table Visualization -->
    <h2>Table: <?= $table ?></h2>

    <?php
    // Fetch columns
    $columns = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?");
    $columns->execute([$table]);
    $columns = $columns->fetchAll(PDO::FETCH_COLUMN);

    // Fetch table rows
    $stmt = $pdo->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
    ?>

    <!-- Create Button -->
    <button class="btn btn-save" id="openCreateModal">+ Create new</button>

    <?php include "./components/createModal.php" ?>

    <?php include "./components/editModal.php" ?>

    <script>
        const fkMap = <?= json_encode($fkMap) ?>;
        const columns = <?= json_encode($columns) ?>;
    </script>

    <?php
    // FK Mapping Regeln dynamisch
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
    ?>

    <?php include "./components/dataTable.php"; ?>

    <!-- Scripts -->
    <script>
        const table = "<?= $table ?>";
        const api = "<?= $apiUrl ?>";
    </script>
    <script src="script.js"></script>

</body>

</html>