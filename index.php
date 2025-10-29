<!DOCTYPE html>
<html lang="de" data-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PlayHub</title>
    <link rel="icon" href="favicon.svg">
    <link rel="stylesheet" href="globals.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  </head>
  <body>
    <?php
    $host = 'db5018764785.hosting-data.io';
    $user = 'dbu2235954';
    $pass = 'PHub%2025!';
    $db = 'dbs14835705';
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
      die('Verbindung fehlgeschlagen: ' . $conn->connect_error);
    }
    $games = $conn->query("SELECT Games.ID, Games.Name, Games.ThumbnailURL, Games.Price, Users.Name AS Publisher FROM Games JOIN Users ON Games.PublisherId = Users.ID ORDER BY PublishingDate DESC LIMIT 5");
    $categories = $conn->query("SELECT Name FROM Categories ORDER BY Name ASC");
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
          <?php if ($games && $games->num_rows > 0): ?>
            <?php while($game = $games->fetch_assoc()): ?>
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
            <?php endwhile; ?>
          <?php else: ?>
            <p>Keine Spiele gefunden.</p>
          <?php endif; ?>
        </div>
        <hr>
        <h2 class="subtitle">Kategorien</h2>
        <div class="tags">
          <?php if ($categories && $categories->num_rows > 0): ?>
            <?php while($cat = $categories->fetch_assoc()): ?>
              <a href="categories.php?cat=<?= urlencode($cat['Name']) ?>" class="tag is-warning is-medium" style="margin-bottom:6px; text-decoration:none;"> <?= htmlspecialchars($cat['Name']) ?> </a>
            <?php endwhile; ?>
          <?php else: ?>
            <span>Keine Kategorien vorhanden.</span>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php $conn->close(); ?>
    <script src="navbar.js"></script>
  </body>
</html>