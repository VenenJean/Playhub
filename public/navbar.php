<nav class="navbar is-dark ph-navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand" style="display:flex; align-items:center; gap:8px;">
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="mainNavbar">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>
    <div id="mainNavbar" class="navbar-menu">
        <div class="navbar-center">
            <a class="navbar-item" href="/PlayHub/public/index.php">
                <img src="/PlayHub/public/graphics/playhub-banner.svg" alt="PlayHub">
            </a>
            <a class="navbar-item<?php if (isset($page) && $page === 'home') echo ' active'; ?>" href="/PlayHub/public/index.php">Home</a>
            <a class="navbar-item<?php if (isset($page) && $page === 'browse') echo ' active'; ?>" href="/PlayHub/public/browse.php">Browse</a>
            <a class="navbar-item nav-categories<?php if (isset($page) && $page === 'categories') echo ' active'; ?>" href="/PlayHub/public/categories.php">Categories</a>
            <a class="navbar-item<?php if (isset($page) && $page === 'admin') echo ' active'; ?>" href="/PlayHub/admin/admin.php">Admin</a>
        </div>
        <div class="navbar-end">
            <div class="brand-buttons">
                <a class="button sign-in" href="/PlayHub/public/sign/signin.php">Sign In</a>
                <a class="button sign-up" href="/PlayHub/public/sign/signup.php">Sign Up</a>
            </div>
        </div>
    </div>
</nav>