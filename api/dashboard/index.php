<?php
include "./globals.php";

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
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">📊 API Dashboard</h1>
        <a class="btn" href="logs.php">🧾 View Logs</a>
    </div>

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

    <?php include "./components/dataTable.php"; ?>

    <!-- Scripts -->
    <script>
        const table = "<?= $table ?>";
        const api = "<?= $apiUrl ?>";
    </script>
    <script src="script.js"></script>

</body>

</html>