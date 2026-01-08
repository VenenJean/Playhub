<?php
// Load configuration and helper classes
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';

$error = '';
$message = '';
$databases = [];
$selectedDb = $_POST['database'] ?? '';

// -------------------------------------------------
// Datenbanken laden
// -------------------------------------------------
try {
    // connect to the master database using credentials from config
    $pdoMaster = new PDO(
        "sqlsrv:Server={$config['db_host']};Database=master",
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
        ]
    );
    $stmt = $pdoMaster->query("
        SELECT name 
        FROM sys.databases
        WHERE name NOT IN ('master','tempdb','model','msdb')
        ORDER BY name
    ");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error = "Fehler: " . $e->getMessage();
}

// Hilfsfunktion: Tabelle prüfen
function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM sys.tables 
        WHERE name = :t
    ");
    $stmt->execute([':t' => $table]);
    return $stmt->fetchColumn() > 0;
}

// Hilfsfunktion: Tabelle leer?
function tableIsEmpty(PDO $pdo, string $table): bool
{
    try {
        $stmt = $pdo->query("SELECT TOP 1 1 FROM $table");
        return $stmt->fetch() === false;
    } catch (PDOException $e) {
        return true;
    }
}

// Tabellendefinitionen
$tables = [
    // ✅ NEU: Admin Logs
    "admin_logs" => <<<SQL
CREATE TABLE admin_logs (
    id INT PRIMARY KEY IDENTITY (1, 1),
    log_datetime DATETIME NOT NULL DEFAULT GETDATE(),
    action NVARCHAR(20) NOT NULL,
    table_name NVARCHAR(255) NOT NULL,
    user_agent NVARCHAR(255) NULL,
    old_data NVARCHAR(MAX) NULL,
    new_data NVARCHAR(MAX) NULL
);
SQL,

    "public_users" => <<<SQL
CREATE TABLE public_users (
    id INT PRIMARY KEY IDENTITY (1,1),
    username NVARCHAR(255) NOT NULL,
    email NVARCHAR(255) NOT NULL,
    password NVARCHAR(255) NOT NULL,
    balance FLOAT
);
SQL,
    "public_games" => <<<SQL
CREATE TABLE public_games (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    thumbnail_path NVARCHAR(255),
    price FLOAT,
    publish_datetime DATETIME
);
SQL,
    "public_reviews" => <<<SQL
CREATE TABLE public_reviews (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    review_datetime DATETIME,
    stars INT,
    content NVARCHAR(MAX),
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "public_users_games" => <<<SQL
CREATE TABLE public_users_games (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    buy_datetime DATETIME,
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "public_wishlists" => <<<SQL
CREATE TABLE public_wishlists (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    added_datetime DATETIME,
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "public_studios" => <<<SQL
CREATE TABLE public_studios (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX),
    user_id INT,
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
);
SQL,
    "public_publishers_games" => <<<SQL
CREATE TABLE public_publishers_games (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    studio_id INT NOT NULL,
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (studio_id) REFERENCES public_studios(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "public_developers_games" => <<<SQL
CREATE TABLE public_developers_games (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    studio_id INT NOT NULL,
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (studio_id) REFERENCES public_studios(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "game_categories" => <<<SQL
CREATE TABLE game_categories (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL
);
SQL,
    "game_games_categories" => <<<SQL
CREATE TABLE game_games_categories (
    id INT PRIMARY KEY IDENTITY (1,1),
    game_id INT NOT NULL,
    category_id INT NOT NULL,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (category_id) REFERENCES game_categories(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "game_platforms" => <<<SQL
CREATE TABLE game_platforms (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL
);
SQL,
    "game_games_platforms" => <<<SQL
CREATE TABLE game_games_platforms (
    id INT PRIMARY KEY IDENTITY (1,1),
    game_id INT NOT NULL,
    platform_id INT NOT NULL,
FOREIGN KEY (game_id) REFERENCES public_games(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (platform_id) REFERENCES game_platforms(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "hrbac_roles" => <<<SQL
CREATE TABLE hrbac_roles (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL
);
SQL,
    "hrbac_permissions" => <<<SQL
CREATE TABLE hrbac_permissions (
    id INT PRIMARY KEY IDENTITY (1,1),
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX)
);
SQL,
    "hrbac_users_roles" => <<<SQL
CREATE TABLE hrbac_users_roles (
    id INT PRIMARY KEY IDENTITY (1,1),
    user_id INT NOT NULL,
    role_id INT NOT NULL,
-- hrbac_users_roles
FOREIGN KEY (user_id) REFERENCES public_users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (role_id) REFERENCES hrbac_roles(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "hrbac_roles_inherits" => <<<SQL
CREATE TABLE hrbac_roles_inherits (
    id INT PRIMARY KEY IDENTITY (1,1),
    parent_role_id INT NOT NULL,
    child_role_id INT NOT NULL,
-- hrbac_roles_inherits
FOREIGN KEY (parent_role_id) REFERENCES hrbac_roles(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (child_role_id) REFERENCES hrbac_roles(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
    "hrbac_roles_permissions" => <<<SQL
CREATE TABLE hrbac_roles_permissions (
    id INT PRIMARY KEY IDENTITY (1,1),
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
-- hrbac_roles_permissions
FOREIGN KEY (role_id) REFERENCES hrbac_roles(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (permission_id) REFERENCES hrbac_permissions(id)
    ON DELETE CASCADE ON UPDATE CASCADE

);
SQL,
];

// Seeds – vorher Tabelleninhalt prüfen!
$seeds = [
    "public_users" => <<<SQL
INSERT INTO public_users (username,email,password,balance) VALUES
('alice','alice@example.com','hashed_pw_1',100.50),
('bob','bob@example.com','hashed_pw_2',45.00),
('charlie','charlie@example.com','hashed_pw_3',200.00);
SQL,
    "public_studios" => <<<SQL
INSERT INTO public_studios (name,description,user_id) VALUES
('PixelForge Studio','Indie studio focusing on retro games.',1),
('Nightfall Works','Dark fantasy AAA game studio.',2);
SQL,
    "public_games" => <<<SQL
INSERT INTO public_games (name,description,thumbnail_path,price,publish_datetime) VALUES
('Sky Explorer','Open-world sky adventure game.','sky_explorer.png',29.99,GETDATE()),
('Dungeon Shadows','Dark dungeon crawler with permadeath.','dungeon_shadows.png',49.99,GETDATE()),
('Retro Racer','Pixel-art style racing game.','retro_racer.png',19.99,GETDATE());
SQL,
    "public_publishers_games" => <<<SQL
INSERT INTO public_publishers_games (user_id,game_id,studio_id) VALUES
(1,1,1),(2,2,2),(1,3,1);
SQL,
    "public_developers_games" => <<<SQL
INSERT INTO public_developers_games (user_id,game_id,studio_id) VALUES
(1,1,1),(2,2,2),(3,3,1);
SQL,
    "game_categories" => <<<SQL
INSERT INTO game_categories (name) VALUES
('Adventure'),('RPG'),('Racing'),('Action'),('Indie');
SQL,
    "game_games_categories" => <<<SQL
INSERT INTO game_games_categories (game_id,category_id) VALUES
(1,1),(2,2),(2,4),(3,3),(3,5);
SQL,
    "game_platforms" => <<<SQL
INSERT INTO game_platforms (name) VALUES
('PC'),('PlayStation'),('Xbox'),('Switch');
SQL,
    "game_games_platforms" => <<<SQL
INSERT INTO game_games_platforms (game_id,platform_id) VALUES
(1,1),(2,1),(2,2),(3,1),(3,4);
SQL,
    "public_users_games" => <<<SQL
INSERT INTO public_users_games (user_id,game_id,buy_datetime) VALUES
(1,1,GETDATE()),(1,3,GETDATE()),(2,2,GETDATE());
SQL,
    "public_wishlists" => <<<SQL
INSERT INTO public_wishlists (user_id,game_id,added_datetime) VALUES
(2,1,GETDATE()),(3,2,GETDATE()),(3,3,GETDATE());
SQL,
    "public_reviews" => <<<SQL
INSERT INTO public_reviews (user_id,game_id,review_datetime,stars,content) VALUES
(1,1,GETDATE(),5,'Amazing experience!'),
(2,2,GETDATE(),4,'Great, but difficulty is high.'),
(3,3,GETDATE(),5,'Fun and nostalgic.');
SQL,
    "hrbac_roles" => <<<SQL
INSERT INTO hrbac_roles (name) VALUES
('admin'),('moderator'),('developer'),('user');
SQL,
    "hrbac_permissions" => <<<SQL
INSERT INTO hrbac_permissions (name,description) VALUES
('manage_users','Add, edit and delete users'),
('manage_games','Add or update games'),
('post_review','Create game reviews'),
('buy_game','Purchase games');
SQL,
    "hrbac_roles_permissions" => <<<SQL
INSERT INTO hrbac_roles_permissions (role_id,permission_id) VALUES
(1,1),(1,2),(1,3),(1,4),
(2,2),(2,3),
(3,2),
(4,3),(4,4);
SQL,
    "hrbac_users_roles" => <<<SQL
INSERT INTO hrbac_users_roles (user_id,role_id) VALUES
(1,1),(2,4),(3,4);
SQL,
    "hrbac_roles_inherits" => <<<SQL
INSERT INTO hrbac_roles_inherits (parent_role_id,child_role_id) VALUES
(1,2),(2,4);
SQL,
];

// Button geklickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedDb && empty($error)) {
    try {
        // Use Database class to create a PDO bound to the selected database
        $database = new Database($selectedDb);
        $pdo = $database->pdo();

        $pdo->beginTransaction();

        // 1. Tabellen erstellen (nur wenn nicht existiert)
        foreach ($tables as $name => $sql) {
            if (!tableExists($pdo, $name)) {
                $pdo->exec($sql);
            }
        }

        // -------------------------------------------------
        // FOREIGN KEY CASCADE FIX (nachträglich erzwingen)
        // -------------------------------------------------
        $cascadeSql = <<<SQL

-- public_wishlists.user_id
IF EXISTS (
    SELECT 1 FROM sys.foreign_keys 
    WHERE name = 'FK__public_wi__user___00200768'
)
ALTER TABLE public_wishlists
DROP CONSTRAINT FK__public_wi__user___00200768;

IF NOT EXISTS (
    SELECT 1 FROM sys.foreign_keys 
    WHERE name = 'FK_public_wishlists_user'
)
ALTER TABLE public_wishlists
ADD CONSTRAINT FK_public_wishlists_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- public_reviews.user_id
DECLARE @fk_reviews NVARCHAR(255);
SELECT @fk_reviews = name
FROM sys.foreign_keys
WHERE parent_object_id = OBJECT_ID('public_reviews')
AND referenced_object_id = OBJECT_ID('public_users');

IF @fk_reviews IS NOT NULL
    EXEC('ALTER TABLE public_reviews DROP CONSTRAINT ' + @fk_reviews);

ALTER TABLE public_reviews
ADD CONSTRAINT FK_public_reviews_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- public_users_games.user_id
ALTER TABLE public_users_games
DROP CONSTRAINT IF EXISTS FK_public_users_games_user;

ALTER TABLE public_users_games
ADD CONSTRAINT FK_public_users_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- hrbac_users_roles.user_id
ALTER TABLE hrbac_users_roles
DROP CONSTRAINT IF EXISTS FK_hrbac_users_roles_user;

ALTER TABLE hrbac_users_roles
ADD CONSTRAINT FK_hrbac_users_roles_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- public_publishers_games.user_id
ALTER TABLE public_publishers_games
DROP CONSTRAINT IF EXISTS FK_public_publishers_games_user;

ALTER TABLE public_publishers_games
ADD CONSTRAINT FK_public_publishers_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- public_developers_games.user_id
ALTER TABLE public_developers_games
DROP CONSTRAINT IF EXISTS FK_public_developers_games_user;

ALTER TABLE public_developers_games
ADD CONSTRAINT FK_public_developers_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


-- public_studios.user_id (SET NULL)
ALTER TABLE public_studios
DROP CONSTRAINT IF EXISTS FK_public_studios_user;

ALTER TABLE public_studios
ADD CONSTRAINT FK_public_studios_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

SQL;

        $pdo->exec($cascadeSql);


        // 2. Seed-Daten einfügen (nur wenn Tabelle leer)
        foreach ($seeds as $table => $sql) {
            if (tableIsEmpty($pdo, $table)) {
                $pdo->exec($sql);
            }
        }



        $pdo->commit();
        $message = "Setup erfolgreich ausgeführt auf Datenbank '$selectedDb'.";
    } catch (PDOException $e) {
        // only attempt rollback if $pdo exists and we're in a transaction
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Fehler: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Playhub HRBAC Setup</title>
    <style>
        body {
            background: #111;
            color: #eee;
            font-family: sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #222;
            padding: 2rem;
            border-radius: 12px;
            width: 350px;
        }

        select,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
        }

        button {
            background: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        button:hover {
            background: #43a047;
        }

        .msg {
            padding: 10px;
            border-radius: 6px;
            margin-top: 1rem;
        }

        .ok {
            background: #2a9d8f;
        }

        .err {
            background: #9b2226;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Playhub HRBAC Setup</h2>

        <?php if ($error): ?>
            <div class="msg err"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="msg ok"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Datenbank wählen:</label>
            <select name="database" required>
                <option value="">-- wählen --</option>
                <?php foreach ($databases as $db): ?>
                    <option value="<?= $db ?>" <?= ($db === $selectedDb ? 'selected' : '') ?>>
                        <?= $db ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Setup ausführen</button>
        </form>
    </div>
</body>

</html>