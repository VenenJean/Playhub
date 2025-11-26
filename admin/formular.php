<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>Datenbank | PlayHub</title>
</head>

<body>
    <?php if (!empty($message))
        echo "<p>$message</p>"; ?>
    <h2>Kategorie hinzufügen</h2>
    <form method="post" action="db1.php">
        <input name="category_name" type="text" placeholder="Kategorie" required>
        <button type="submit">Hinzufügen</button>
    </form>
    <h2>Spiel hinzufügen</h2>
    <form method="post" action="../database/db1.php">
        <input name="game_name" type="text" placeholder="Spielname" required>
        <input name="game_price" type="number" step="0.01" placeholder="Preis" required>
        <button type="submit">Hinzufügen</button>
    </form>
</body>

</html>