<!DOCTYPE html>
<html lang="de" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spiel | PlayHub</title>
    <link rel="icon" href="graphics/favicon.svg">
    <link rel="stylesheet" href="styles/playhub.css">
</head>

<body>
    <?php
    include '../database/db.php';
    $conn = Database::getConnection();
    $game = null;

    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("
            SELECT 
                g.id AS GameID,
                g.name AS Name,
                g.thumbnail_path AS ThumbnailURL,
                g.price AS Price,
                g.publish_datetime AS PublishDate,
                g.description AS Description,
                u.username AS Publisher
            FROM public_games g
            LEFT JOIN public_publishers_games pg ON g.id = pg.game_id
            LEFT JOIN public_users u ON pg.user_id = u.id
            WHERE g.id = :id
        ");
        $stmt->execute([':id' => $_GET['id']]);
        $game = $stmt->fetch();
    }

    $page = '';
    ?>

    <?php include 'navbar.php'; ?>

    <section class="section">
        <div class="container">
            <?php if ($game): ?>
                <div class="columns">
                    <div class="column is-one-third">
                        <figure class="image is-3by4">
                            <img src="<?= htmlspecialchars($game['ThumbnailURL'] ?? 'default-thumb.png') ?>"
                                alt="<?= htmlspecialchars($game['Name']) ?>">
                        </figure>
                    </div>
                    <div class="column">
                        <h1 class="title"><?= htmlspecialchars($game['Name']) ?></h1>
                        <h2 class="subtitle">
                            von <?= htmlspecialchars($game['Publisher'] ?? 'Unbekannt') ?>
                        </h2>
                        <p><strong>Preis:</strong> <?= number_format($game['Price'], 2) ?> €</p>
                        <p><strong>Veröffentlicht am:</strong> <?= htmlspecialchars($game['PublishDate']) ?></p>
                        <hr>
                        <p><?= nl2br(htmlspecialchars($game['Description'] ?? 'Keine Beschreibung verfügbar')) ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="notification is-danger">Spiel nicht gefunden.</div>
            <?php endif; ?>
        </div>
    </section>

    <?php Database::close(); ?>
    <script src="js/navbar.js"></script>
</body>

</html>