/* ================================
   PUBLIC_WISHLISTS
================================ */
IF EXISTS (
    SELECT 1 FROM sys.foreign_keys 
    WHERE name = 'FK__public_wi__user___00200768'
)
ALTER TABLE public_wishlists
DROP CONSTRAINT FK__public_wi__user___00200768;

ALTER TABLE public_wishlists
ADD CONSTRAINT FK_public_wishlists_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   PUBLIC_REVIEWS
================================ */
IF EXISTS (
    SELECT 1 FROM sys.foreign_keys 
    WHERE name LIKE 'FK__public_re%'
)
BEGIN
    DECLARE @fk1 NVARCHAR(255)
    SELECT @fk1 = name FROM sys.foreign_keys WHERE parent_object_id = OBJECT_ID('public_reviews')
    EXEC('ALTER TABLE public_reviews DROP CONSTRAINT ' + @fk1)
END

ALTER TABLE public_reviews
ADD CONSTRAINT FK_public_reviews_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   PUBLIC_USERS_GAMES
================================ */
ALTER TABLE public_users_games
DROP CONSTRAINT IF EXISTS FK_public_users_games_user;

ALTER TABLE public_users_games
ADD CONSTRAINT FK_public_users_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   HRBAC_USERS_ROLES
================================ */
ALTER TABLE hrbac_users_roles
DROP CONSTRAINT IF EXISTS FK_hrbac_users_roles_user;

ALTER TABLE hrbac_users_roles
ADD CONSTRAINT FK_hrbac_users_roles_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   PUBLIC_PUBLISHERS_GAMES
================================ */
ALTER TABLE public_publishers_games
DROP CONSTRAINT IF EXISTS FK_public_publishers_games_user;

ALTER TABLE public_publishers_games
ADD CONSTRAINT FK_public_publishers_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   PUBLIC_DEVELOPERS_GAMES
================================ */
ALTER TABLE public_developers_games
DROP CONSTRAINT IF EXISTS FK_public_developers_games_user;

ALTER TABLE public_developers_games
ADD CONSTRAINT FK_public_developers_games_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;


/* ================================
   PUBLIC_STUDIOS (SET NULL)
================================ */
ALTER TABLE public_studios
DROP CONSTRAINT IF EXISTS FK_public_studios_user;

ALTER TABLE public_studios
ADD CONSTRAINT FK_public_studios_user
FOREIGN KEY (user_id)
REFERENCES public_users(id)
ON DELETE SET NULL
ON UPDATE CASCADE;
