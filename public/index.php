<?php
include '../database/db.php';
$conn = Database::getConnection();

// Neueste Spiele
$stmt = $conn->query("
    SELECT 
        g.id AS GameID,
        g.name AS GameName,
        g.thumbnail_path AS Thumbnail,
        g.price AS Price,
        u.username AS Publisher
    FROM public_games g
    LEFT JOIN public_publishers_games pg ON g.id = pg.game_id
    LEFT JOIN public_users u ON pg.user_id = u.id
    ORDER BY g.publish_datetime DESC
    OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY
");
$games = $stmt->fetchAll();

// Kategorien
$stmtC = $conn->query("
    SELECT name 
    FROM game_categories 
    ORDER BY name ASC
");
$categories = $stmtC->fetchAll();

$page = 'home';
?>
<!DOCTYPE html>
<html lang="de" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PlayHub</title>
    <link rel="icon" href="graphics/favicon.svg">
    <link rel="stylesheet" href="styles/playhub.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="section">
        <div class="container">
            <h1 class="title">Willkommen bei PlayHub!</h1>
            <p class="subtitle">Dein Marktplatz für digitale Spiele.</p>
            <hr>

            <h2 class="subtitle">Neueste Spiele</h2>
            <div class="columns is-multiline">
                <?php if ($games && count($games) > 0): ?>
                    <?php foreach ($games as $game): ?>
                        <div class="column is-one-fifth">
                            <a href="game.php?id=<?= urlencode($game['GameID']) ?>" style="text-decoration:none; color:inherit;">
                                <div class="card is-clickable" style="cursor:pointer;">
                                    <div class="card-image">
                                        <figure class="image is-4by3">
                                            <img src="<?= htmlspecialchars($game['Thumbnail'] ?? 'default-thumb.png') ?>"
                                                alt="<?= htmlspecialchars($game['GameName']) ?>">
                                        </figure>
                                    </div>
                                    <div class="card-content">
                                        <p class="title is-6" style="font-size:1em;">
                                            <?= htmlspecialchars($game['GameName']) ?>
                                        </p>
                                        <p class="subtitle is-7" style="font-size:0.92em;">
                                            von <?= htmlspecialchars($game['Publisher'] ?? 'Unbekannt') ?>
                                        </p>
                                        <p><strong><?= number_format($game['Price'], 2) ?> €</strong></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Keine Spiele gefunden.</p>
                <?php endif; ?>
            </div>

            <hr>

            <h2 class="subtitle">Kategorien</h2>
            <div class="tags">
                <?php if ($categories && count($categories) > 0): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="categories.php?cat=<?= urlencode($cat['name']) ?>"
                            class="tag is-warning is-medium"
                            style="margin-bottom:6px; text-decoration:none;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>Keine Kategorien vorhanden.</span>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php Database::close(); ?>
    <script src="js/navbar.js"></script>
</body>

</html>