<!DOCTYPE html>
<html lang="de" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | PlayHub</title>
    <link rel="icon" href="favicon.svg">

    <link rel="stylesheet" href="../styles/playhub.css">
</head>

<body>
    <?php $page = '';
    include '../navbar.php'; ?>
    <section class="section">
        <div class="container" style="max-width:400px;">
            <h1 class="title">Sign In</h1>
            <form method="post" action="signin.php">
                <div class="field">
                    <label class="label">Benutzername</label>
                    <div class="control">
                        <input class="input" type="text" name="username" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Passwort</label>
                    <div class="control">
                        <input class="input" type="password" name="password" required>
                    </div>
                </div>
                <div class="field">
                    <button class="button is-warning" type="submit">Login</button>
                </div>
            </form>
        </div>
    </section>
    <script src="navbar.js"></script>
</body>

</html>