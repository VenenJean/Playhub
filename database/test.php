<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Insert | Query</title>
</head>

<body>
    <h1>Database Insert Example</h1>
    <?php
    include 'db.php';

    try {
        $sql = "INSERT INTO BankAccounts (IBAN, Balance) VALUES ('testiban', 1000)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute();
        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</body>

</html>