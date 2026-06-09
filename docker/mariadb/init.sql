-- phpMyAdmin SQL Dump
-- version 5.2.3-2.fc44
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 01 juin 2026 à 23:55
-- Version du serveur : 11.8.6-MariaDB
-- Version de PHP : 8.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `StardusteDataBase`
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
                            `CommentVideoId` int(11) NOT NULL,
                            `date_comment` datetime NOT NULL,
                            `CommentUploaderId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `commentaire`, `comlike`, `comdislike`, `favorited`, `CommentVideoId`, `date_comment`, `CommentUploaderId`) VALUES
                                                                                                                                              (6, 'a beautiful garden', 0, 0, NULL, 8, '2026-06-01 23:39:57', 4),
                                                                                                                                              (7, 'pretty fast gameplay x)', 0, 0, NULL, 6, '2026-06-01 23:40:08', 4);

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
                                                                                           ('DoctrineMigrations\\Version20260308020247', '2026-03-08 02:03:12', 48),
                                                                                           ('DoctrineMigrations\\Version20260601000000', '2026-06-01 19:37:38', 37),
                                                                                           ('DoctrineMigrations\\Version20260601010000', '2026-06-01 20:07:57', 137);

-- --------------------------------------------------------

--
-- Structure de la table `live_stream`
--

CREATE TABLE `live_stream` (
                               `id` int(11) NOT NULL,
                               `streamer_id` int(11) NOT NULL,
                               `slug` varchar(32) NOT NULL,
                               `title` varchar(128) NOT NULL,
                               `category` varchar(80) DEFAULT NULL,
                               `thumbnail_path` varchar(255) DEFAULT NULL,
                               `playback_url` varchar(255) DEFAULT NULL,
                               `live` tinyint(1) NOT NULL,
                               `viewers` int(11) NOT NULL,
                               `started_at` datetime DEFAULT NULL,
                               `created_at` datetime NOT NULL,
                               `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `live_stream`
--

INSERT INTO `live_stream` (`id`, `streamer_id`, `slug`, `title`, `category`, `thumbnail_path`, `playback_url`, `live`, `viewers`, `started_at`, `created_at`, `updated_at`) VALUES
    (2, 4, '60223db87940047b9572ef4b7213a16d', 'Live de Tiramysou', NULL, NULL, 'http://localhost:8081/hls/sd_live_07a3881429c5985985290fc6cb4496f722c64ac95da3b070.m3u8', 0, 0, '2026-06-01 20:36:46', '2026-06-01 20:25:16', '2026-06-01 20:38:28');

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
-- Structure de la table `opinion`
--

CREATE TABLE `opinion` (
                           `id` int(11) NOT NULL,
                           `value` varchar(25) NOT NULL,
                           `created_at` datetime NOT NULL,
                           `user_id_id` int(11) DEFAULT NULL,
                           `video_id_id` int(11) DEFAULT NULL,
                           `type` varchar(255) DEFAULT NULL,
                           `commentid_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subscribe`
--

CREATE TABLE `subscribe` (
                             `id` int(11) NOT NULL,
                             `subscribed_to_id` int(11) NOT NULL,
                             `subscribers_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
                                `Certified` tinyint(4) DEFAULT NULL,
                                `bannerpath` varchar(255) DEFAULT NULL,
                                `live_stream_key` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Utilisateurs`
--

INSERT INTO `Utilisateurs` (`id`, `pseudo`, `subscribers`, `JOIN_DATE`, `uploaded_video`, `is_admin`, `age`, `password`, `email`, `IP_ADRESSE`, `LAST_LOGIN`, `pfppath`, `uuid`, `Certified`, `bannerpath`, `live_stream_key`) VALUES
                                                                                                                                                                                                                                   (4, 'Tiramysou', 0, '2026-03-16', 0, 0, 19, '$2y$13$rKruJph0NqCUVKTgLYtVbuCrHuiCvC/2VMOy1ENZVZl2crOzMYi1G', 'lhuiliereole@gmail.com', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, 'uploads/ProfilePictures/4/6a1ba2ee91dfd.webp', '019cf6e0-fc32-7ba7-b753-51aa0e35deef', NULL, NULL, 'sd_live_07a3881429c5985985290fc6cb4496f722c64ac95da3b070'),
                                                                                                                                                                                                                                   (5, 'john doe', 0, '2026-03-16', 0, 0, 20, '$2y$13$/hu8ANrRCpeE8KcAO3k/WeTnVfQAI5RhNdSbW2zktkd5JOg.NQdJi', 't@t.t', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, NULL, '019cf714-79ce-7340-bf81-bc1caa0da913', NULL, NULL, NULL),
                                                                                                                                                                                                                                   (6, 'Elios', 0, '2026-03-17', 0, 0, 20, '$2y$13$Qli0YLQEAic/nY6g25OOSOvXhuD0QpPPUBA2xhXgSjqKWdIAuDwVi', 'eliosderagol@gmail.com', '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0', NULL, NULL, '019cfcf0-887a-767d-aee8-5b2954a2152d', NULL, NULL, NULL),
                                                                                                                                                                                                                                   (7, 'john', 0, '2026-06-01', 0, 0, 18, '$2y$13$6FrIVKxX4Wv8Gsl9tECC8O3fbRJ1BRQgnqgTDk4J7v2PIyq.J5ntK', 'johndoe@mail.com', '4be83b311542895031de5564fbf5b39fc74828842667b140f188f7a3ed924996', NULL, NULL, '019e84b9-b6ad-7377-a4fd-b47060ac6287', NULL, 'uploads/Banner/7/6a1de2c8d2a4c.webp', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `video`
--

CREATE TABLE `video` (
                         `id` int(11) NOT NULL,
                         `like_vid` int(11) NOT NULL,
                         `title` varchar(128) NOT NULL,
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
                                                                                                                                                                                                                (6, 0, 'sonic green hill zone act 1', 129, 'uploads/videos/222eca369d1f74f18da7fe618e69484d/222eca369d1f74f18da7fe618e69484d.mp4', 'uploads/videos/222eca369d1f74f18da7fe618e69484d/Thumbnail222eca369d1f74f18da7fe618e69484d.webp', '2026-06-01 23:23:33', 0, 'green hill zone act sonic generation', 1, 'gaming', '222eca369d1f74f18da7fe618e69484d', 0, 0, 4),
                                                                                                                                                                                                                (7, 0, 'lg the black', 126, 'uploads/videos/661b02c8d5e595f07697d57ddd49424e/661b02c8d5e595f07697d57ddd49424e.mp4', 'uploads/videos/661b02c8d5e595f07697d57ddd49424e/Thumbnail661b02c8d5e595f07697d57ddd49424e.jpg', '2026-06-01 23:28:30', 0, 'showcase vide of lg', 1, 'art', '661b02c8d5e595f07697d57ddd49424e', 0, 0, 4),
                                                                                                                                                                                                                (8, 0, 'garden showcase', 8, 'uploads/shorts/738752944f259fc3576287f901bd74a2/738752944f259fc3576287f901bd74a2.mp4', 'uploads/fallbacksElement/FallbackThumbnail.webp', '2026-06-01 23:37:45', 0, 'a showcase of a garden', 1, 'travel', '738752944f259fc3576287f901bd74a2', 1, 0, 4),
                                                                                                                                                                                                                (9, 0, 'garden showcase', 8, 'uploads/shorts/9166371065ed7654db8f5e279381e7aa/9166371065ed7654db8f5e279381e7aa.mp4', 'uploads/fallbacksElement/FallbackThumbnail.webp', '2026-06-01 23:39:27', 0, 'a showcase of a garden', 1, 'travel', '9166371065ed7654db8f5e279381e7aa', 1, 0, 4);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F9E962A44739184` (`CommentVideoId`),
  ADD KEY `IDX_5F9E962AA31CFA96` (`CommentUploaderId`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `live_stream`
--
ALTER TABLE `live_stream`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_53B48F53989D9B62` (`slug`),
  ADD KEY `IDX_53B48F53B4216C33` (`streamer_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `opinion`
--
ALTER TABLE `opinion`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AB02B0279D86650F` (`user_id_id`),
  ADD KEY `IDX_AB02B027F02697F5` (`video_id_id`),
  ADD KEY `IDX_AB02B0274574CA0` (`commentid_id`);

--
-- Index pour la table `subscribe`
--
ALTER TABLE `subscribe`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_68B95F3EF9B6176` (`subscribed_to_id`),
  ADD KEY `IDX_68B95F3E4F6E6AC1` (`subscribers_id`);

--
-- Index pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_514AEAA610C6BEC4` (`email`),
  ADD UNIQUE KEY `UNIQ_514AEAA6D17F50A6` (`uuid`),
  ADD UNIQUE KEY `UNIQ_514AEAA6F3BE9D10` (`live_stream_key`);

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
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `live_stream`
--
ALTER TABLE `live_stream`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `opinion`
--
ALTER TABLE `opinion`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT pour la table `subscribe`
--
ALTER TABLE `subscribe`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `video`
--
ALTER TABLE `video`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Contraintes pour la table `live_stream`
--
ALTER TABLE `live_stream`
    ADD CONSTRAINT `FK_53B48F53B4216C33` FOREIGN KEY (`streamer_id`) REFERENCES `Utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `opinion`
--
ALTER TABLE `opinion`
    ADD CONSTRAINT `FK_AB02B0274574CA0` FOREIGN KEY (`commentid_id`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `FK_AB02B0279D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `Utilisateurs` (`id`),
  ADD CONSTRAINT `FK_AB02B027F02697F5` FOREIGN KEY (`video_id_id`) REFERENCES `video` (`id`);

--
-- Contraintes pour la table `subscribe`
--
ALTER TABLE `subscribe`
    ADD CONSTRAINT `FK_68B95F3E4F6E6AC1` FOREIGN KEY (`subscribers_id`) REFERENCES `Utilisateurs` (`id`),
  ADD CONSTRAINT `FK_68B95F3EF9B6176` FOREIGN KEY (`subscribed_to_id`) REFERENCES `Utilisateurs` (`id`);

--
-- Contraintes pour la table `video`
--
ALTER TABLE `video`
    ADD CONSTRAINT `FK_7CC7DA2C16678C77` FOREIGN KEY (`uploader_id`) REFERENCES `Utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
