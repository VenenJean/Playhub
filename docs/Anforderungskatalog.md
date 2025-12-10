# Anforderungskatalog

Folgende Operationen sollen auf genannten Tabellen möglich sein: <br/>
(Join-Tables sind als `[tableName1][tableName2]` bennant e.g. `GameCategories`)

```sql
=== ACTIONS ===
C = Create
R = Read
U = Update
D = Delete
A = Assign

=== GENERAL ===
(CRUD Operations)

----- | Users
----- | Studios
----- | Games
----- | Reviews
----- | Wishlist
----- | Categories
----- | Platforms

=== GAME SPECIFIC ===
(CRUDA Operations)

----- | GameCategories
----- | GamePlatforms
----- | GamePublishers
----- | GameDevelopers
----- | GameReviews

=== ROLE SPECIFIC ===
(CRUD & A Operations)
----- | Roles
----- | Permissions
----- | RolePermissions
----- | RoleInherits*

=== USER SPECIFIC===
(CRUDA Operations)
----- | UserRoles
----- | UserStudios
----- | UserWishlists
----- | UserPermissions*
```
