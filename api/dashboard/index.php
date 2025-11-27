<?php
// DASHBOARD.PHP — FULLY DYNAMIC API DASHBOARD

require "../Database.php";

$pdo = (new Database())->pdo();

// Get list of tables
$tables = $pdo->query("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_TYPE = 'BASE TABLE'
")->fetchAll(PDO::FETCH_COLUMN);

$table = $_GET["table"] ?? $tables[0];
$apiUrl = "../index.php"; // adjust if API is in a different folder
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dynamic API Dashboard</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #eee;
        }

        .btn {
            padding: 4px 10px;
            cursor: pointer;
        }

        .btn-edit {
            background: #ffc107;
        }

        .btn-del {
            background: #dc3545;
            color: white;
        }

        .btn-save {
            background: #28a745;
            color: white;
        }

        .box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
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

    <!-- CREATE NEW RECORD -->
    <div class="box">
        <h3>Create new row</h3>
        <form id="createForm">
            <?php foreach ($columns as $col): ?>
                <?php if ($col === "id") continue; ?>
                <label><?= $col ?></label><br>
                <input type="text" name="<?= $col ?>" style="width: 300px"><br><br>
            <?php endforeach; ?>
            <button type="button" class="btn btn-save" onclick="createRow()">Create</button>
        </form>
    </div>

    <!-- TABLE DATA DISPLAY -->
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

        /* CREATE */
        function createRow() {
            let formData = {};
            new FormData(document.querySelector("#createForm")).forEach((v, k) => formData[k] = v);

            fetch(`${api}?table=${table}`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(() => location.reload());
        }

        /* EDIT */
        function editRow(id) {
            fetch(`${api}?table=${table}&id=${id}`)
                .then(r => r.json())
                .then(data => {

                    let html = `<div id='editBox' class='box'>
                <h3>Edit row #${id}</h3>
                <form id='editForm'>`;

                    for (let key in data) {
                        if (key === "id") continue;
                        html += `${key}<br><input name='${key}' value='${data[key]}'><br><br>`;
                    }

                    html += `
                </form>
                <button class='btn btn-save' onclick='saveEdit(${id})'>Save</button>
                <button class='btn' onclick='document.getElementById("editBox").remove()'>Cancel</button>
            </div>`;

                    document.body.insertAdjacentHTML("beforeend", html);
                });
        }

        function saveEdit(id) {
            let formData = {};
            new FormData(document.querySelector("#editForm")).forEach((v, k) => formData[k] = v);

            fetch(`${api}?table=${table}&id=${id}`, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(r => r.json())
                .then(() => location.reload());
        }

        /* DELETE */
        function deleteRow(id) {
            if (!confirm("Really delete?")) return;

            fetch(`${api}?table=${table}&id=${id}`, {
                    method: "DELETE"
                })
                .then(r => r.json())
                .then(() => document.querySelector("#row-" + id).remove());
        }
    </script>

</body>

</html>