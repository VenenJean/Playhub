<!DOCTYPE html>
<html lang="de" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PlayHub</title>
    <link rel="icon" href="../public/graphics/favicon.svg">

    <link rel="stylesheet" href="styles/playhub.css">
</head>

<body>
    <?php
    include '../database/db.php';
    $conn = Database::getConnection();
    $stmtG = $conn->query("SELECT TOP 5 Games.ID, Games.Name, Games.ThumbnailURL, Games.Price, Users.Name AS Publisher FROM Games JOIN Users ON Games.PublisherId = Users.ID ORDER BY PublishingDate DESC");
    $games = $stmtG->fetchAll();
    $stmtC = $conn->query("SELECT Name FROM Categories ORDER BY Name ASC");
    $categories = $stmtC->fetchAll();
    $page = 'home';
    ?>
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
                            <a href="game.php?id=<?= urlencode($game['ID']) ?>" style="text-decoration:none; color:inherit;">
                                <div class="card is-clickable" style="cursor:pointer;">
                                    <div class="card-image">
                                        <figure class="image is-4by3">
                                            <img src="<?= htmlspecialchars($game['ThumbnailURL'] ?? 'default-thumb.png') ?>" alt="<?= htmlspecialchars($game['Name']) ?>">
                                        </figure>
                                    </div>
                                    <div class="card-content">
                                        <p class="title is-6" style="font-size:1em;"><?= htmlspecialchars($game['Name']) ?></p>
                                        <p class="subtitle is-7" style="font-size:0.92em;">von <?= htmlspecialchars($game['Publisher']) ?></p>
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
                        <a href="categories.php?cat=<?= urlencode($cat['Name']) ?>" class="tag is-warning is-medium" style="margin-bottom:6px; text-decoration:none;"> <?= htmlspecialchars($cat['Name']) ?> </a>
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