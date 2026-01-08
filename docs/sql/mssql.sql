use playhub_hrbac;

CREATE TABLE
    public_users (
        id INT PRIMARY KEY IDENTITY (1, 1),
        username NVARCHAR (255) NOT NULL,
        email NVARCHAR (255) NOT NULL,
        password NVARCHAR (255) NOT NULL,
        balance FLOAT
    );

CREATE TABLE
    public_games (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL,
        description NVARCHAR (MAX),
        thumbnail_path NVARCHAR (255),
        price FLOAT,
        publish_datetime DATETIME
    );

CREATE TABLE
    public_reviews (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        review_datetime DATETIME,
        stars INT,
        content NVARCHAR (MAX),
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (game_id) REFERENCES public_games (id)
    );

CREATE TABLE
    public_users_games (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        buy_datetime DATETIME,
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (game_id) REFERENCES public_games (id)
    );

CREATE TABLE
    public_wishlists (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        added_datetime DATETIME,
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (game_id) REFERENCES public_games (id)
    );

CREATE TABLE
    public_studios (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL,
        description NVARCHAR (MAX),
        user_id INT,
        FOREIGN KEY (user_id) REFERENCES public_users (id)
    );

CREATE TABLE
    public_publishers_games (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        studio_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (game_id) REFERENCES public_games (id),
        FOREIGN KEY (studio_id) REFERENCES public_studios (id)
    );

CREATE TABLE
    public_developers_games (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        studio_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (game_id) REFERENCES public_games (id),
        FOREIGN KEY (studio_id) REFERENCES public_studios (id)
    );

CREATE TABLE
    game_categories (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL
    );

CREATE TABLE
    game_games_categories (
        id INT PRIMARY KEY IDENTITY (1, 1),
        game_id INT NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (game_id) REFERENCES public_games (id),
        FOREIGN KEY (category_id) REFERENCES game_categories (id)
    );

CREATE TABLE
    game_platforms (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL
    );

CREATE TABLE
    game_games_platforms (
        id INT PRIMARY KEY IDENTITY (1, 1),
        game_id INT NOT NULL,
        platform_id INT NOT NULL,
        FOREIGN KEY (game_id) REFERENCES public_games (id),
        FOREIGN KEY (platform_id) REFERENCES game_platforms (id)
    );

CREATE TABLE
    hrbac_roles (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL
    );

CREATE TABLE
    hrbac_permissions (
        id INT PRIMARY KEY IDENTITY (1, 1),
        name NVARCHAR (255) NOT NULL,
        description NVARCHAR (MAX)
    );

CREATE TABLE
    hrbac_users_roles (
        id INT PRIMARY KEY IDENTITY (1, 1),
        user_id INT NOT NULL,
        role_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES public_users (id),
        FOREIGN KEY (role_id) REFERENCES hrbac_roles (id)
    );

CREATE TABLE
    hrbac_roles_inherits (
        id INT PRIMARY KEY IDENTITY (1, 1),
        parent_role_id INT NOT NULL,
        child_role_id INT NOT NULL,
        FOREIGN KEY (parent_role_id) REFERENCES hrbac_roles (id),
        FOREIGN KEY (child_role_id) REFERENCES hrbac_roles (id)
    );

CREATE TABLE
    hrbac_roles_permissions (
        id INT PRIMARY KEY IDENTITY (1, 1),
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        FOREIGN KEY (role_id) REFERENCES hrbac_roles (id),
        FOREIGN KEY (permission_id) REFERENCES hrbac_permissions (id)
    );

-- Adminpanel change logs (Dashboard / CRUD API)
CREATE TABLE
    admin_logs (
        id INT PRIMARY KEY IDENTITY (1, 1),
        log_datetime DATETIME NOT NULL DEFAULT GETDATE(),
        action NVARCHAR(20) NOT NULL,
        table_name NVARCHAR(255) NOT NULL,
        record_id INT NULL,
        actor NVARCHAR(255) NULL,
        ip NVARCHAR(45) NULL,
        user_agent NVARCHAR(255) NULL,
        old_data NVARCHAR(MAX) NULL,
        new_data NVARCHAR(MAX) NULL
    );