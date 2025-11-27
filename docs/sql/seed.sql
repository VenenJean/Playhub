USE playhub_hrbac;

GO
-------------------------------------
-- USERS
-------------------------------------
INSERT INTO
    public_users (username, email, password, balance)
VALUES
    (
        'alice',
        'alice@example.com',
        'hashed_pw_1',
        100.50
    ),
    ('bob', 'bob@example.com', 'hashed_pw_2', 45.00),
    (
        'charlie',
        'charlie@example.com',
        'hashed_pw_3',
        200.00
    );

GO
-------------------------------------
-- STUDIOS
-------------------------------------
INSERT INTO
    public_studios (name, description, user_id)
VALUES
    (
        'PixelForge Studio',
        'Indie studio focusing on retro games.',
        1
    ),
    (
        'Nightfall Works',
        'Dark fantasy AAA game studio.',
        2
    );

GO
-------------------------------------
-- GAMES
-------------------------------------
INSERT INTO
    public_games (
        name,
        description,
        thumbnail_path,
        price,
        publish_datetime
    )
VALUES
    (
        'Sky Explorer',
        'Open-world sky adventure game.',
        'sky_explorer.png',
        29.99,
        GETDATE ()
    ),
    (
        'Dungeon Shadows',
        'Dark dungeon crawler with permadeath.',
        'dungeon_shadows.png',
        49.99,
        GETDATE ()
    ),
    (
        'Retro Racer',
        'Pixel-art style racing game.',
        'retro_racer.png',
        19.99,
        GETDATE ()
    );

GO
-------------------------------------
-- PUBLISHERS (users/studios/games)
-------------------------------------
INSERT INTO
    public_publishers_games (user_id, game_id, studio_id)
VALUES
    (1, 1, 1),
    (2, 2, 2),
    (1, 3, 1);

GO
-------------------------------------
-- DEVELOPERS
-------------------------------------
INSERT INTO
    public_developers_games (user_id, game_id, studio_id)
VALUES
    (1, 1, 1),
    (2, 2, 2),
    (3, 3, 1);

GO
-------------------------------------
-- GAME CATEGORIES
-------------------------------------
INSERT INTO
    game_categories (name)
VALUES
    ('Adventure'),
    ('RPG'),
    ('Racing'),
    ('Action'),
    ('Indie');

GO
-------------------------------------
-- GAME → CATEGORY
-------------------------------------
INSERT INTO
    game_games_categories (game_id, category_id)
VALUES
    (1, 1), -- Sky Explorer -> Adventure
    (2, 2), -- Dungeon Shadows -> RPG
    (2, 4), -- Dungeon Shadows -> Action
    (3, 3), -- Retro Racer -> Racing
    (3, 5);

-- Retro Racer -> Indie
GO
-------------------------------------
-- PLATFORMS
-------------------------------------
INSERT INTO
    game_platforms (name)
VALUES
    ('PC'),
    ('PlayStation'),
    ('Xbox'),
    ('Switch');

GO
-------------------------------------
-- GAME → PLATFORM
-------------------------------------
INSERT INTO
    game_games_platforms (game_id, platform_id)
VALUES
    (1, 1),
    (2, 1),
    (2, 2),
    (3, 1),
    (3, 4);

GO
-------------------------------------
-- PURCHASES
-------------------------------------
INSERT INTO
    public_users_games (user_id, game_id, buy_datetime)
VALUES
    (1, 1, GETDATE ()),
    (1, 3, GETDATE ()),
    (2, 2, GETDATE ());

GO
-------------------------------------
-- WISHLISTS
-------------------------------------
INSERT INTO
    public_wishlists (user_id, game_id, added_datetime)
VALUES
    (2, 1, GETDATE ()),
    (3, 2, GETDATE ()),
    (3, 3, GETDATE ());

GO
-------------------------------------
-- REVIEWS
-------------------------------------
INSERT INTO
    public_reviews (user_id, game_id, review_datetime, stars, content)
VALUES
    (1, 1, GETDATE (), 5, 'Amazing experience!'),
    (
        2,
        2,
        GETDATE (),
        4,
        'Great, but difficulty is high.'
    ),
    (3, 3, GETDATE (), 5, 'Fun and nostalgic.');

GO
-------------------------------------
-- HRBAC ROLES
-------------------------------------
INSERT INTO
    hrbac_roles (name)
VALUES
    ('admin'),
    ('moderator'),
    ('developer'),
    ('user');

GO
-------------------------------------
-- HRBAC PERMISSIONS
-------------------------------------
INSERT INTO
    hrbac_permissions (name, description)
VALUES
    ('manage_users', 'Add, edit and delete users'),
    ('manage_games', 'Add or update games'),
    ('post_review', 'Create game reviews'),
    ('buy_game', 'Purchase games');

GO
-------------------------------------
-- HRBAC ROLE PERMISSIONS
-------------------------------------
INSERT INTO
    hrbac_roles_permissions (role_id, permission_id)
VALUES
    (1, 1), -- admin -> manage_users
    (1, 2), -- admin -> manage_games
    (1, 3), -- admin -> post_review
    (1, 4), -- admin -> buy_game
    (2, 2), -- moderator -> manage_games
    (2, 3), -- moderator -> post_review
    (3, 2), -- developer -> manage_games
    (4, 3), -- user -> post_review
    (4, 4);

-- user -> buy_game
GO
-------------------------------------
-- HRBAC USER ROLES
-------------------------------------
INSERT INTO
    hrbac_users_roles (user_id, role_id)
VALUES
    (1, 1), -- Alice is admin
    (2, 4), -- Bob is user
    (3, 4);

-- Charlie is user
GO
-------------------------------------
-- HRBAC ROLE INHERITANCE
-------------------------------------
INSERT INTO
    hrbac_roles_inherits (parent_role_id, child_role_id)
VALUES
    (1, 2), -- admin > moderator
    (2, 4);

-- moderator > user
GO