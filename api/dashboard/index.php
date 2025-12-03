<?php
require "../Database.php";

$pdo = (new Database())->pdo();
$apiUrl = "../index.php";

// Get list of tables
$tables = $pdo->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_TYPE = 'BASE TABLE'
")->fetchAll(PDO::FETCH_COLUMN);

$table = $_GET["table"] ?? $tables[0];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic API Dashboard</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <h1>📊 API Dashboard</h1>

    <form method="GET">
        <label>Select table: </label>
        <select name="table" onchange="this.form.submit()">
            <?php foreach ($tables as $t): ?>
                <option value="<?= $t ?>" <?= $t == $table ? "selected" : "" ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <hr>

    <h2>Table: <?= $table ?></h2>

    <?php
    // Fetch columns
    $columns = $pdo->prepare("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?
    ");
    $columns->execute([$table]);
    $columns = $columns->fetchAll(PDO::FETCH_COLUMN);

    // Fetch table rows
    $stmt = $pdo->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- CREATE BUTTON -->
    <button class="btn btn-save" id="openCreateModal">+ Create new</button>

    <!-- CREATE MODAL -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" data-close="createModal">&times;</span>
            <h3>Create new row</h3>

            <form id="createForm">
                <?php foreach ($columns as $col): ?>
                    <?php if ($col === "id") continue; ?>
                    <label><?= $col ?></label><br>
                    <input type="text" name="<?= $col ?>" style="width: 300px"><br><br>
                <?php endforeach; ?>
            </form>

            <button class="btn btn-save" onclick="createRow()">Create</button>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" data-close="editModal">&times;</span>
            <h3 id="editTitle">Edit</h3>

            <form id="editForm"></form>

            <button class="btn btn-save" id="saveEditBtn">Save</button>
        </div>
    </div>

    <!-- TABLE DATA -->
    <table>
        <tr>
            <?php foreach ($columns as $col): ?>
                <th><?= $col ?></th>
            <?php endforeach; ?>
            <th>Actions</th>
        </tr>

        <?php foreach ($rows as $row): ?>
            <tr id="row-<?= $row["id"] ?>">
                <?php foreach ($columns as $col): ?>
                    <td><?= htmlspecialchars($row[$col]) ?></td>
                <?php endforeach; ?>
                <td>
                    <button class="btn btn-edit" onclick="editRow(<?= $row['id'] ?>)">Edit</button>
                    <button class="btn btn-del" onclick="deleteRow(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        const table = "<?= $table ?>";
        const api = "<?= $apiUrl ?>";
    </script>
    <script src="script.js"></script>

</body>

</html>