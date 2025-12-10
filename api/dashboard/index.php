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

    <!-- Table Selection -->
    <form method="GET">
        <label>Select table: </label>
        <select name="table" onchange="this.form.submit()">
            <?php foreach ($tables as $t): ?>
                <option value="<?= $t ?>" <?= $t == $table ? "selected" : "" ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </form>

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

    <!-- Create Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" data-close="createModal">&times;</span>
            <h3>Create new row</h3>

            <?php
            $fkMap = [
                'game_id'      => ['table' => 'public_games', 'label' => 'name'],
                'category_id'  => ['table' => 'game_categories', 'label' => 'name'],
                'platform_id'  => ['table' => 'game_platforms', 'label' => 'name'],
                'user_id'      => ['table' => 'public_users', 'label' => 'username'],
                'studio_id'    => ['table' => 'public_studios', 'label' => 'name'],
                'role_id'      => ['table' => 'hrbac_roles', 'label' => 'name'],
                'permission_id' => ['table' => 'hrbac_permissions', 'label' => 'name']
            ];
            ?>

            <form id="createForm">
                <?php foreach ($columns as $col): ?>
                    <?php if ($col === "id") continue; ?>
                    <label><b><?= ucfirst(str_replace('_id', '', $col)) ?></b></label><br>

                    <?php if (isset($fkMap[$col])): ?>
                        <select name="<?= $col ?>" style="width:300px;">
                            <?php
                            $ref = $fkMap[$col];
                            $items = $pdo->query("SELECT id, {$ref['label']} AS text FROM {$ref['table']} ORDER BY text")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($items as $item) {
                                echo "<option value='{$item['id']}'>{$item['text']}</option>";
                            }
                            ?>
                        </select><br><br>
                    <?php else: ?>
                        <input type="text" name="<?= $col ?>" style="width:300px"><br><br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </form>


            <button class="btn btn-save" onclick="createRow()">Create</button>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" data-close="editModal">&times;</span>
            <h3 id="editTitle">Edit</h3>

            <form id="editForm"></form>

            <button class="btn btn-save" id="saveEditBtn">Save</button>
        </div>
    </div>

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
    <!-- Table Data -->
    <table>
        <tr>
            <!-- Generates all columns dynamically -->
            <?php foreach ($columns as $col):
                $label = str_replace('_id', '', $col); // game_id -> game
            ?>
                <th><?= ucfirst($label) ?></th>
            <?php endforeach; ?>
            <th>Actions</th>
        </tr>

        <!-- Insert data from columns dynamically as rows -->
        <?php foreach ($rows as $row): ?>
            <tr id="row-<?= $row["id"] ?>">
                <?php foreach ($columns as $col): ?>
                    <td>
                        <?php
                        if (isset($fkLookup[$col])) {
                            $ref = $fkLookup[$col];
                            $q = $pdo->prepare("SELECT {$ref['column']} FROM {$ref['table']} WHERE id=?");
                            $q->execute([$row[$col]]);
                            echo $q->fetchColumn() ?? "Unknown";
                        } else {
                            echo htmlspecialchars($row[$col]);
                        }
                        ?>
                    </td>
                    <!-- Convert special characters into HTML entities e.g. & to &amp; -->
                <?php endforeach; ?>
                <td>
                    <button class="btn btn-edit" onclick="editRow(<?= $row['id'] ?>)">Edit</button>
                    <button class="btn btn-del" onclick="deleteRow(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Scripts -->
    <script>
        const table = "<?= $table ?>";
        const api = "<?= $apiUrl ?>";
    </script>
    <script src="script.js"></script>

</body>

</html>