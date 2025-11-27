<?php
$serverName = "(localdb)\MSSQLLocalDB";
$database = "playhub_hrac";
$username = "admin";
$password = "admin";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Kategorie hinzufügen
        if (!empty($_POST['category_name'])) {
            $category_name = $_POST['category_name'];
            $sql = "INSERT INTO dbo.Categories (Name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$category_name]);
            $message .= "Kategorie erfolgreich hinzugefügt!<br>";
        }

        // Spiel hinzufügen
        if (!empty($_POST['game_name']) && isset($_POST['game_price'])) {
            $game_name = $_POST['game_name'];
            $game_price = $_POST['game_price'];
            $sql = "INSERT INTO dbo.Games (Name, Preis) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$game_name, $game_price]);
            $message .= "Spiel erfolgreich hinzugefügt!";
        }
    } catch (PDOException $e) {
        $message = "Verbindung fehlgeschlagen: " . $e->getMessage();
    }
}
