-- phpMyAdmin SQL Dump
-- version 5.2.3-1.fc43
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 09 mai 2026 à 00:52
-- Version du serveur : 10.11.16-MariaDB
-- Version de PHP : 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `StardusteDB`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
                            `id` int(11) NOT NULL,
                            `commentaire` varchar(500) NOT NULL,
                            `comlike` int(11) NOT NULL,
                            `comdislike` int(11) NOT NULL,
                            `favorited` tinyint(4) DEFAULT NULL,
                            `date_comment` datetime NOT NULL,
                            `CommentUploaderId` int(11) NOT NULL,
                            `CommentVideoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `commentaire`, `comlike`, `comdislike`, `favorited`, `date_comment`, `CommentUploaderId`, `CommentVideoId`) VALUES
                                                                                                                                              (2, 'test', 0, 0, NULL, '2026-05-07 07:48:05', 4, 2),
                                                                                                                                              (3, 'test', 0, 0, NULL, '2026-05-07 09:56:55', 4, 2),
                                                                                                                                              (4, 'tres bonne vidéo ^', 0, 0, NULL, '2026-05-07 09:57:18', 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
    ('DoctrineMigrations\\Version20260308020247', '2026-03-08 02:03:12', 48);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
                                      `id` bigint(20) NOT NULL,
                                      `body` longtext NOT NULL,
                                      `headers` longtext NOT NULL,
                                      `queue_name` varchar(190) NOT NULL,
                                      `created_at` datetime NOT NULL,
                                      `available_at` datetime NOT NULL,
                                      `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateurs`
--

CREATE TABLE `Utilisateurs` (
                                `id` int(11) NOT NULL,
                                `pseudo` varchar(24) NOT NULL,
                                `subscribers` int(11) NOT NULL,
                                `JOIN_DATE` date DEFAULT NULL,
                                `uploaded_video` int(11) NOT NULL,
                                `is_admin` tinyint(4) DEFAULT NULL,
                                `age` int(11) NOT NULL,
                                `password` varchar(255) NOT NULL,
                                `email` varchar(50) NOT NULL,
                                `IP_ADRESSE` varchar(255) DEFAULT NULL,
                                `LAST_LOGIN` datetime DEFAULT NULL,
                                `pfppath` varchar(255) DEFAULT NULL,
                                `uuid` varchar(36) NOT NULL,
                                `Certified` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Utilisateurs`
--

INSERT INTO `Utilisateurs` (`id`, `pseudo`, `subscribers`, `JOIN_DATE`, `uploaded_video`, `is_admin`, `age`, `password`, `email`, `IP_ADRESSE`, `LAST_LOGIN`, `pfppath`, `uuid`, `Certified`) VALUES
                                                                                                                                                                                                  (4, 'Tiramysou', 0, '2026-03-16', 0, 0, 19, '$2y$13$rKruJph0NqCUVKTgLYtVbuCrHuiCvC/2VMOy1ENZVZl2crOzMYi1G', 'lhuiliereole@gmail.com', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, '/uploads/ProfilePictures/tira_pfp.webp', '019cf6e0-fc32-7ba7-b753-51aa0e35deef', NULL),
                                                                                                                                                                                                  (5, 'john doe', 0, '2026-03-16', 0, 0, 20, '$2y$13$/hu8ANrRCpeE8KcAO3k/WeTnVfQAI5RhNdSbW2zktkd5JOg.NQdJi', 't@t.t', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, NULL, '019cf714-79ce-7340-bf81-bc1caa0da913', NULL),
                                                                                                                                                                                                  (6, 'Elios', 0, '2026-03-17', 0, 0, 20, '$2y$13$Qli0YLQEAic/nY6g25OOSOvXhuD0QpPPUBA2xhXgSjqKWdIAuDwVi', 'eliosderagol@gmail.com', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, NULL, '019cfcf0-887a-767d-aee8-5b2954a2152d', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `video`
--

CREATE TABLE `video` (
                         `id` int(11) NOT NULL,
                         `like_vid` int(11) NOT NULL,
                         `title` varchar(28) NOT NULL,
                         `video_duration` int(11) NOT NULL,
                         `video_url` varchar(255) DEFAULT NULL,
                         `thumbnail` varchar(255) DEFAULT NULL,
                         `upload_date` datetime DEFAULT NULL,
                         `dislike_vid` int(11) NOT NULL,
                         `description` varchar(1024) DEFAULT NULL,
                         `status` tinyint(4) NOT NULL,
                         `categorie` varchar(255) DEFAULT NULL,
                         `uuid` varchar(32) NOT NULL,
                         `is_short` tinyint(4) NOT NULL,
                         `views` int(11) NOT NULL,
                         `uploader_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `video`
--

INSERT INTO `video` (`id`, `like_vid`, `title`, `video_duration`, `video_url`, `thumbnail`, `upload_date`, `dislike_vid`, `description`, `status`, `categorie`, `uuid`, `is_short`, `views`, `uploader_id`) VALUES
                                                                                                                                                                                                                (1, 0, 'spinning noob ahaha', 6, 'uploads/shorts/5f9df9e115cb89c96992e62967f7ed4f/5f9df9e115cb89c96992e62967f7ed4f.mp4', 'uploads/fallbacksElement/FallbackThumbnail.webp', '2026-03-23 18:20:26', 0, 'spinning noob lol', 1, 'humour', '5f9df9e115cb89c96992e62967f7ed4f', 1, 0, 4),
                                                                                                                                                                                                                (2, 0, 'spinning robloxian ah ah', 28, 'uploads/videos/37d856aa4a1f564c73f4112269f5ce39/37d856aa4a1f564c73f4112269f5ce39.mp4', 'uploads/videos/37d856aa4a1f564c73f4112269f5ce39/Thumbnail37d856aa4a1f564c73f4112269f5ce39.jpg', '2026-03-24 09:19:58', 0, 'its a spiinning robloxian :P', 1, 'humour', '37d856aa4a1f564c73f4112269f5ce39', 0, 0, 4);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F9E962AA31CFA96` (`CommentUploaderId`),
  ADD KEY `IDX_5F9E962A44739184` (`CommentVideoId`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_514AEAA610C6BEC4` (`email`),
  ADD UNIQUE KEY `UNIQ_514AEAA6D17F50A6` (`uuid`);

--
-- Index pour la table `video`
--
ALTER TABLE `video`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7CC7DA2CD17F50A6` (`uuid`),
  ADD KEY `IDX_7CC7DA2C16678C77` (`uploader_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `video`
--
ALTER TABLE `video`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
    ADD CONSTRAINT `FK_5F9E962A44739184` FOREIGN KEY (`CommentVideoId`) REFERENCES `video` (`id`),
  ADD CONSTRAINT `FK_5F9E962AA31CFA96` FOREIGN KEY (`CommentUploaderId`) REFERENCES `Utilisateurs` (`id`);

--
-- Contraintes pour la table `video`
--
ALTER TABLE `video`
    ADD CONSTRAINT `FK_7CC7DA2C16678C77` FOREIGN KEY (`uploader_id`) REFERENCES `Utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
