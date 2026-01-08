<?php
include "./globals.php";

// Read-only log view
$limit = (int)($_GET['limit'] ?? 200);
if ($limit <= 0) $limit = 200;
if ($limit > 1000) $limit = 1000;

try {
    $stmt = $pdo->prepare("SELECT TOP ($limit) * FROM admin_logs ORDER BY log_datetime DESC, id DESC");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $logs = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs</title>
    <link rel="stylesheet" href="main.css">
    <style>
        .log-json {
            max-width: 560px;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 12px;
            background: rgba(0, 0, 0, 0.04);
            padding: 8px;
            border-radius: 8px;
        }
        .topbar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .topbar a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <h1 style="margin: 0;">🧾 Admin Logs</h1>
        <a class="btn" href="index.php">← Back to Dashboard</a>
    </div>

    <p>Showing newest <strong><?= htmlspecialchars((string)$limit) ?></strong> entries.</p>

    <?php if (!empty($error ?? null)) : ?>
        <div class="error" style="padding:12px; border:1px solid rgba(255,0,0,.2); border-radius:12px;">
            <strong>Could not load admin_logs</strong><br>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <table border="1" cellspacing="0" cellpadding="6" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>time</th>
                <th>action</th>
                <th>table</th>
                <th>old_data</th>
                <th>new_data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $row) : ?>
                <tr>
                    <td><?= htmlspecialchars((string)($row['log_datetime'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($row['action'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($row['table_name'] ?? '')) ?></td>
                    <td><div class="log-json"><?= htmlspecialchars((string)($row['old_data'] ?? '')) ?></div></td>
                    <td><div class="log-json"><?= htmlspecialchars((string)($row['new_data'] ?? '')) ?></div></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
