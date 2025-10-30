-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 08. Okt 2025 um 08:04
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `phub`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bankaccounts`
--

CREATE TABLE `bankaccounts` (
  `IBAN` varchar(34) NOT NULL,
  `Balance` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `Content` varchar(1024) NOT NULL,
  `PostDate` datetime DEFAULT current_timestamp(),
  `UserId` int(11) NOT NULL,
  `GameId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gamecategory`
--

CREATE TABLE `gamecategory` (
  `ID` int(11) NOT NULL,
  `GameId` int(11) NOT NULL,
  `CategoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `games`
--

CREATE TABLE `games` (
  `ID` int(11) NOT NULL,
  `PublisherId` int(11) NOT NULL,
  `PublishingDate` datetime DEFAULT NULL,
  `ThumbnailURL` varchar(512) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permissions`
--

CREATE TABLE `permissions` (
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rolepermission`
--

CREATE TABLE `rolepermission` (
  `ID` int(11) NOT NULL,
  `RoleId` int(11) NOT NULL,
  `PermissionId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `transactionhistory`
--

CREATE TABLE `transactionhistory` (
  `ID` int(11) NOT NULL,
  `PurchaseDate` datetime DEFAULT current_timestamp(),
  `UserId` int(11) NOT NULL,
  `GameId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userbibliothek`
--

CREATE TABLE `userbibliothek` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `GameId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `usercommentreview`
--

CREATE TABLE `usercommentreview` (
  `ID` int(11) NOT NULL,
  `Status` enum('like','dislike') NOT NULL,
  `CommentId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userrole`
--

CREATE TABLE `userrole` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `RoleId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `BankAccountId` varchar(34) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bankaccounts`
--
ALTER TABLE `bankaccounts`
  ADD PRIMARY KEY (`IBAN`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `GameId` (`GameId`);

--
-- Indizes für die Tabelle `gamecategory`
--
ALTER TABLE `gamecategory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `GameId` (`GameId`),
  ADD KEY `CategoryId` (`CategoryId`);

--
-- Indizes für die Tabelle `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PublisherId` (`PublisherId`);

--
-- Indizes für die Tabelle `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `rolepermission`
--
ALTER TABLE `rolepermission`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `RoleId` (`RoleId`),
  ADD KEY `PermissionId` (`PermissionId`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indizes für die Tabelle `transactionhistory`
--
ALTER TABLE `transactionhistory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `GameId` (`GameId`);

--
-- Indizes für die Tabelle `userbibliothek`
--
ALTER TABLE `userbibliothek`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `GameId` (`GameId`);

--
-- Indizes für die Tabelle `usercommentreview`
--
ALTER TABLE `usercommentreview`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CommentId` (`CommentId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indizes für die Tabelle `userrole`
--
ALTER TABLE `userrole`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `RoleId` (`RoleId`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `BankAccountId` (`BankAccountId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gamecategory`
--
ALTER TABLE `gamecategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `games`
--
ALTER TABLE `games`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `permissions`
--
ALTER TABLE `permissions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `rolepermission`
--
ALTER TABLE `rolepermission`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `transactionhistory`
--
ALTER TABLE `transactionhistory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `userbibliothek`
--
ALTER TABLE `userbibliothek`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `usercommentreview`
--
ALTER TABLE `usercommentreview`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `userrole`
--
ALTER TABLE `userrole`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`GameId`) REFERENCES `games` (`ID`);

--
-- Constraints der Tabelle `gamecategory`
--
ALTER TABLE `gamecategory`
  ADD CONSTRAINT `gamecategory_ibfk_1` FOREIGN KEY (`GameId`) REFERENCES `games` (`ID`),
  ADD CONSTRAINT `gamecategory_ibfk_2` FOREIGN KEY (`CategoryId`) REFERENCES `categories` (`ID`);

--
-- Constraints der Tabelle `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`PublisherId`) REFERENCES `users` (`ID`);

--
-- Constraints der Tabelle `rolepermission`
--
ALTER TABLE `rolepermission`
  ADD CONSTRAINT `rolepermission_ibfk_1` FOREIGN KEY (`RoleId`) REFERENCES `roles` (`ID`),
  ADD CONSTRAINT `rolepermission_ibfk_2` FOREIGN KEY (`PermissionId`) REFERENCES `permissions` (`ID`);

--
-- Constraints der Tabelle `transactionhistory`
--
ALTER TABLE `transactionhistory`
  ADD CONSTRAINT `transactionhistory_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `transactionhistory_ibfk_2` FOREIGN KEY (`GameId`) REFERENCES `games` (`ID`);

--
-- Constraints der Tabelle `userbibliothek`
--
ALTER TABLE `userbibliothek`
  ADD CONSTRAINT `userbibliothek_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `userbibliothek_ibfk_2` FOREIGN KEY (`GameId`) REFERENCES `games` (`ID`);

--
-- Constraints der Tabelle `usercommentreview`
--
ALTER TABLE `usercommentreview`
  ADD CONSTRAINT `usercommentreview_ibfk_1` FOREIGN KEY (`CommentId`) REFERENCES `comments` (`ID`),
  ADD CONSTRAINT `usercommentreview_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`ID`);

--
-- Constraints der Tabelle `userrole`
--
ALTER TABLE `userrole`
  ADD CONSTRAINT `userrole_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `userrole_ibfk_2` FOREIGN KEY (`RoleId`) REFERENCES `roles` (`ID`);

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`BankAccountId`) REFERENCES `bankaccounts` (`IBAN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
