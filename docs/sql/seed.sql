USE [playhub];
GO

-- ========== BANK ACCOUNTS ==========
INSERT INTO BankAccounts (IBAN, Balance) VALUES
('DE00123456789012345678', 500.50),
('DE00987654321098765432', 1200.00),
('DE00223344556677889900', 950.75),
('DE00112233445566778899', 150.00),
('DE00334455667788990011', 4300.25);
GO

-- ========== USERS ==========
INSERT INTO Users (Name, Password, Email, BankAccountId) VALUES
('Alice Games', 'pass123', 'alice@playhub.com', 'DE00123456789012345678'),
('Bob Dev', 'pass123', 'bob@playhub.com', 'DE00987654321098765432'),
('Charlie Studio', 'pass123', 'charlie@playhub.com', 'DE00223344556677889900'),
('Dana Designer', 'pass123', 'dana@playhub.com', 'DE00112233445566778899'),
('Eve Publisher', 'pass123', 'eve@playhub.com', 'DE00334455667788990011');
GO

-- ========== CATEGORIES ==========
INSERT INTO Categories (Name) VALUES
('Action'),
('Adventure'),
('RPG'),
('Simulation'),
('Strategy'),
('Horror'),
('Sports'),
('Puzzle'),
('Racing'),
('Multiplayer');
GO

-- ========== GAMES (15 total) ==========
INSERT INTO Games (PublisherId, PublishingDate, ThumbnailURL, Name, Price) VALUES
(1, '2023-05-20', 'https://example.com/thumbs/1.jpg', 'CyberQuest', 49.99),
(1, '2024-01-12', 'https://example.com/thumbs/2.jpg', 'Neon Strike', 59.99),
(2, '2022-11-03', 'https://example.com/thumbs/3.jpg', 'Kingdom Forge', 39.99),
(2, '2024-03-15', 'https://example.com/thumbs/4.jpg', 'Rally Rush', 29.99),
(3, '2021-07-08', 'https://example.com/thumbs/5.jpg', 'Haunted Depths', 19.99),
(3, '2024-06-25', 'https://example.com/thumbs/6.jpg', 'Mystic Realms', 44.99),
(4, '2023-09-10', 'https://example.com/thumbs/7.jpg', 'Galactic Traders', 34.99),
(4, '2022-02-14', 'https://example.com/thumbs/8.jpg', 'Farm Life 2', 24.99),
(5, '2023-12-01', 'https://example.com/thumbs/9.jpg', 'Shadow Hunters', 59.99),
(5, '2021-05-30', 'https://example.com/thumbs/10.jpg', 'Brain Maze', 14.99),
(1, '2023-10-10', 'https://example.com/thumbs/11.jpg', 'Street Rivals', 39.99),
(2, '2022-08-20', 'https://example.com/thumbs/12.jpg', 'Hidden Temple', 29.99),
(3, '2023-03-09', 'https://example.com/thumbs/13.jpg', 'Warpath Commander', 49.99),
(4, '2024-08-17', 'https://example.com/thumbs/14.jpg', 'Pixel Puzzles', 9.99),
(5, '2022-09-29', 'https://example.com/thumbs/15.jpg', 'Soccer League X', 44.99);
GO

-- ========== GAME CATEGORIES ==========
INSERT INTO GameCategory (GameId, CategoryId) VALUES
(1, 1), (1, 10),   -- CyberQuest: Action + Multiplayer
(2, 1), (2, 6),    -- Neon Strike: Action + Horror
(3, 3), (3, 5),    -- Kingdom Forge: RPG + Strategy
(4, 9), (4, 10),   -- Rally Rush: Racing + Multiplayer
(5, 6),             -- Haunted Depths: Horror
(6, 3), (6, 2),    -- Mystic Realms: RPG + Adventure
(7, 5), (7, 4),    -- Galactic Traders: Strategy + Simulation
(8, 4),             -- Farm Life 2: Simulation
(9, 1), (9, 6),    -- Shadow Hunters: Action + Horror
(10, 8),            -- Brain Maze: Puzzle
(11, 9), (11, 10), -- Street Rivals: Racing + Multiplayer
(12, 2), (12, 8),  -- Hidden Temple: Adventure + Puzzle
(13, 1), (13, 5),  -- Warpath Commander: Action + Strategy
(14, 8),            -- Pixel Puzzles: Puzzle
(15, 7), (15, 10); -- Soccer League X: Sports + Multiplayer
GO

-- ========== SAMPLE USER LIBRARY ==========
INSERT INTO UserBibliothek (UserId, GameId) VALUES
(1, 3), (1, 4), (1, 6),
(2, 1), (2, 5), (2, 9),
(3, 2), (3, 7),
(4, 10), (4, 14),
(5, 8), (5, 15);
GO

-- ========== SAMPLE COMMENTS ==========
INSERT INTO Comments (Content, UserId, GameId) VALUES
('Amazing gameplay and graphics!', 1, 1),
('Could use more content updates.', 2, 3),
('Loved the story and soundtrack.', 3, 6),
('A bit buggy, but fun overall.', 4, 4),
('Perfect for horror fans!', 5, 5);
GO

-- ========== SAMPLE COMMENT REVIEWS ==========
INSERT INTO UserCommentReview (Status, CommentId, UserId) VALUES
('like', 1, 2),
('like', 1, 3),
('dislike', 2, 1),
('like', 3, 4),
('like', 4, 5),
('dislike', 5, 2);
GO

-- ========== ROLES ==========
INSERT INTO Roles (Name) VALUES ('Admin'), ('Publisher'), ('User');
GO

-- ========== PERMISSIONS (simple placeholders) ==========
INSERT INTO Permissions DEFAULT VALUES;
INSERT INTO Permissions DEFAULT VALUES;
INSERT INTO Permissions DEFAULT VALUES;
GO

-- ========== ROLE-PERMISSION LINK ==========
INSERT INTO RolePermission (RoleId, PermissionId) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (3, 1);
GO

-- ========== USER ROLES ==========
INSERT INTO UserRole (UserId, RoleId) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 3),
(5, 1);
GO

-- ========== TRANSACTION HISTORY ==========
INSERT INTO TransactionHistory (UserId, GameId, PurchaseDate) VALUES
(1, 3, '2024-02-10'),
(1, 6, '2024-06-01'),
(2, 5, '2023-11-09'),
(3, 7, '2024-04-21'),
(4, 14, '2023-09-10'),
(5, 15, '2024-07-25');
GO
