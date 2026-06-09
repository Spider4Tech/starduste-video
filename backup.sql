/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.7-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: StardusteDB
-- ------------------------------------------------------
-- Server version	11.8.7-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `Utilisateurs`
--

DROP TABLE IF EXISTS `Utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `live_stream_key` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_514AEAA610C6BEC4` (`email`),
  UNIQUE KEY `UNIQ_514AEAA6D17F50A6` (`uuid`),
  UNIQUE KEY `UNIQ_514AEAA6F3BE9D10` (`live_stream_key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Utilisateurs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `Utilisateurs` WRITE;
/*!40000 ALTER TABLE `Utilisateurs` DISABLE KEYS */;
INSERT INTO `Utilisateurs` VALUES
(4,'Tiramysou',0,'2026-03-16',0,0,19,'$2y$13$rKruJph0NqCUVKTgLYtVbuCrHuiCvC/2VMOy1ENZVZl2crOzMYi1G','lhuiliereole@gmail.com','12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0',NULL,'uploads/ProfilePictures/4/6a1ba2ee91dfd.webp','019cf6e0-fc32-7ba7-b753-51aa0e35deef',NULL,NULL,'sd_live_07a3881429c5985985290fc6cb4496f722c64ac95da3b070'),
(5,'john doe',0,'2026-03-16',0,0,20,'$2y$13$/hu8ANrRCpeE8KcAO3k/WeTnVfQAI5RhNdSbW2zktkd5JOg.NQdJi','t@t.t','12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0',NULL,NULL,'019cf714-79ce-7340-bf81-bc1caa0da913',NULL,NULL,NULL),
(6,'Elios',0,'2026-03-17',0,0,20,'$2y$13$Qli0YLQEAic/nY6g25OOSOvXhuD0QpPPUBA2xhXgSjqKWdIAuDwVi','eliosderagol@gmail.com','12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0',NULL,NULL,'019cfcf0-887a-767d-aee8-5b2954a2152d',NULL,NULL,NULL),
(7,'john',0,'2026-06-01',0,0,18,'$2y$13$6FrIVKxX4Wv8Gsl9tECC8O3fbRJ1BRQgnqgTDk4J7v2PIyq.J5ntK','johndoe@mail.com','4be83b311542895031de5564fbf5b39fc74828842667b140f188f7a3ed924996',NULL,NULL,'019e84b9-b6ad-7377-a4fd-b47060ac6287',NULL,'uploads/Banner/7/6a1de2c8d2a4c.webp',NULL);
/*!40000 ALTER TABLE `Utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire` varchar(500) NOT NULL,
  `comlike` int(11) NOT NULL,
  `comdislike` int(11) NOT NULL,
  `favorited` tinyint(4) DEFAULT NULL,
  `CommentVideoId` int(11) NOT NULL,
  `date_comment` datetime NOT NULL,
  `CommentUploaderId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5F9E962A44739184` (`CommentVideoId`),
  KEY `IDX_5F9E962AA31CFA96` (`CommentUploaderId`),
  CONSTRAINT `FK_5F9E962A44739184` FOREIGN KEY (`CommentVideoId`) REFERENCES `video` (`id`),
  CONSTRAINT `FK_5F9E962AA31CFA96` FOREIGN KEY (`CommentUploaderId`) REFERENCES `Utilisateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
(1,'trés bonne vidéo d\'un joueur roblox qui tourne sur lui même je trouve qu\'elle es tassez reussite ^^',0,0,NULL,2,'2026-04-25 00:18:51',4),
(2,'ça tourne et moi j\'aime bien :D',0,0,NULL,2,'2026-05-09 23:44:00',4),
(3,'he\'s so bad lol x\'D',0,0,NULL,3,'2026-05-25 00:36:15',4),
(4,'lol',1,0,NULL,5,'2026-05-25 21:55:58',4);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES
('DoctrineMigrations\\Version20260308020247','2026-03-08 02:03:12',48),
('DoctrineMigrations\\Version20260601000000','2026-06-01 19:37:38',37),
('DoctrineMigrations\\Version20260601010000','2026-06-01 20:07:57',137);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `live_stream`
--

DROP TABLE IF EXISTS `live_stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_stream` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_53B48F53989D9B62` (`slug`),
  KEY `IDX_53B48F53B4216C33` (`streamer_id`),
  CONSTRAINT `FK_53B48F53B4216C33` FOREIGN KEY (`streamer_id`) REFERENCES `Utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_stream`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `live_stream` WRITE;
/*!40000 ALTER TABLE `live_stream` DISABLE KEYS */;
INSERT INTO `live_stream` VALUES
(2,4,'60223db87940047b9572ef4b7213a16d','Live de Tiramysou',NULL,NULL,'http://localhost:8081/hls/sd_live_07a3881429c5985985290fc6cb4496f722c64ac95da3b070.m3u8',0,0,'2026-06-01 20:36:46','2026-06-01 20:25:16','2026-06-01 20:38:28');
/*!40000 ALTER TABLE `live_stream` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `opinion`
--

DROP TABLE IF EXISTS `opinion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `opinion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(25) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id_id` int(11) DEFAULT NULL,
  `video_id_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `commentid_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AB02B0279D86650F` (`user_id_id`),
  KEY `IDX_AB02B027F02697F5` (`video_id_id`),
  KEY `IDX_AB02B0274574CA0` (`commentid_id`),
  CONSTRAINT `FK_AB02B0274574CA0` FOREIGN KEY (`commentid_id`) REFERENCES `comments` (`id`),
  CONSTRAINT `FK_AB02B0279D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `Utilisateurs` (`id`),
  CONSTRAINT `FK_AB02B027F02697F5` FOREIGN KEY (`video_id_id`) REFERENCES `video` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opinion`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `opinion` WRITE;
/*!40000 ALTER TABLE `opinion` DISABLE KEYS */;
INSERT INTO `opinion` VALUES
(244,'Disliked','2026-05-26 04:00:42',4,5,'VIDEO',NULL),
(248,'Liked','2026-05-27 02:52:18',4,5,'COMMENT',4);
/*!40000 ALTER TABLE `opinion` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `subscribe`
--

DROP TABLE IF EXISTS `subscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscribed_to_id` int(11) NOT NULL,
  `subscribers_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_68B95F3EF9B6176` (`subscribed_to_id`),
  KEY `IDX_68B95F3E4F6E6AC1` (`subscribers_id`),
  CONSTRAINT `FK_68B95F3E4F6E6AC1` FOREIGN KEY (`subscribers_id`) REFERENCES `Utilisateurs` (`id`),
  CONSTRAINT `FK_68B95F3EF9B6176` FOREIGN KEY (`subscribed_to_id`) REFERENCES `Utilisateurs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribe`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `subscribe` WRITE;
/*!40000 ALTER TABLE `subscribe` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribe` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `video`
--

DROP TABLE IF EXISTS `video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `uploader_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7CC7DA2CD17F50A6` (`uuid`),
  KEY `IDX_7CC7DA2C16678C77` (`uploader_id`),
  CONSTRAINT `FK_7CC7DA2C16678C77` FOREIGN KEY (`uploader_id`) REFERENCES `Utilisateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `video` WRITE;
/*!40000 ALTER TABLE `video` DISABLE KEYS */;
INSERT INTO `video` VALUES
(1,0,'spinning noob ahaha',6,'uploads/shorts/5f9df9e115cb89c96992e62967f7ed4f/5f9df9e115cb89c96992e62967f7ed4f.mp4','uploads/fallbacksElement/FallbackThumbnail.webp','2026-03-23 18:20:26',0,'spinning noob lol',1,'humour','5f9df9e115cb89c96992e62967f7ed4f',1,0,4),
(2,0,'spinning robloxian ah ah',28,'uploads/videos/37d856aa4a1f564c73f4112269f5ce39/37d856aa4a1f564c73f4112269f5ce39.mp4','uploads/videos/37d856aa4a1f564c73f4112269f5ce39/Thumbnail37d856aa4a1f564c73f4112269f5ce39.jpg','2026-03-24 09:19:58',1,'its a spiinning robloxian :P',1,'humour','37d856aa4a1f564c73f4112269f5ce39',0,0,4),
(3,1,'fortnite player break his keyboard because he\'s bad',21,'uploads/videos/ec751cb8799d4d5eaef987ea35effc5d/ec751cb8799d4d5eaef987ea35effc5d.mp4','uploads/fallbacksElement/FallbackThumbnail.webp','2026-05-24 17:32:24',0,'lol',1,'humour','ec751cb8799d4d5eaef987ea35effc5d',0,0,4),
(4,0,'test meme',15,'uploads/shorts/0cb09eb4566cda063a060b87463da746/0cb09eb4566cda063a060b87463da746.mp4','uploads/fallbacksElement/FallbackThumbnail.webp','2026-05-25 01:24:16',0,'test',1,'humour','0cb09eb4566cda063a060b87463da746',1,0,4),
(5,0,'test meme',12,'uploads/videos/9affb6eb4f7986e7d00a36fb17f81495/9affb6eb4f7986e7d00a36fb17f81495.mp4','uploads/fallbacksElement/FallbackThumbnail.webp','2026-05-25 01:24:29',1,'test',1,'humour','9affb6eb4f7986e7d00a36fb17f81495',0,0,4);
/*!40000 ALTER TABLE `video` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-01 21:51:15
