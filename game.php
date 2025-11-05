<!DOCTYPE html>
<html lang="de" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Spiel | PlayHub</title>
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
  $game = null;
  if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT Games.*, Users.Name AS Publisher FROM Games JOIN Users ON Games.PublisherId = Users.ID WHERE Games.ID = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
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
              <img src="<?= htmlspecialchars($game['ThumbnailURL'] ?? 'default-thumb.png') ?>" alt="<?= htmlspecialchars($game['Name']) ?>">
            </figure>
          </div>
          <div class="column">
            <h1 class="title"><?= htmlspecialchars($game['Name']) ?></h1>
            <h2 class="subtitle">von <?= htmlspecialchars($game['Publisher']) ?></h2>
            <p><strong>Preis:</strong> <?= number_format($game['Price'], 2) ?> €</p>
            <p><strong>Veröffentlicht am:</strong> <?= htmlspecialchars($game['PublishingDate']) ?></p>
            <hr>
            <p><?= nl2br(htmlspecialchars($game['Description'])) ?></p>
          </div>
        </div>
      <?php else: ?>
        <div class="notification is-danger">Spiel nicht gefunden.</div>
      <?php endif; ?>
    </div>
  </section>
  <?php $conn->close(); ?>
  <script src="navbar.js"></script>
</body>

</html>