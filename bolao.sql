-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: localhost    Database: bolao_copa
-- ------------------------------------------------------
-- Server version	5.5.5-10.9.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_opts`
--

DROP TABLE IF EXISTS `admin_opts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_opts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_opts`
--

LOCK TABLES `admin_opts` WRITE;
/*!40000 ALTER TABLE `admin_opts` DISABLE KEYS */;
INSERT INTO `admin_opts` VALUES (1,'phone','11914699934',NULL,'2022-12-13 19:12:54'),(2,'rules','Ganha quem acerta mais jogos, caso haja empate o valor ser├í dividido entre os ganhadores.|Apenas as cartelas validadas antes dos jogos come├ºarem estar├úo concorrendo ao bol├úo, verifique sempre sua cartela foi validada ap├│s pagamento a fim de garantir sua participa├º├úo e evitar problemas. Caso haja erro da parte da organiza├º├úo na marca├º├úo da cartela, ser├í devolvido para o participante apenas os R$10,00 referente ao valor da cartela.|Ser├í exibido na parte superior da cartela a porcentagem do valor apurado que ser├í destinado a organiza├º├úo do bol├úo.|Jogos adiados ser├úo desconsiderados na apura├º├úo dos resultados, a n├úo ser que a partida ocorra antes do fim da rodada.|Jogos cancelados quando j├í iniciados a partida, valer├í o placar no ato do cancelamento|Jogos do bol├úo ser├í v├ílido nos 90 MINUTOS , prorroga├º├úo ├® considerado uma 2┬¬ partida.|O(s) ganhador(es) receber├í(├úo) o pr├¬mio em at├® 2 dias ap├│s a finaliza├º├úo da rodada.|O atendimento para efetua├º├úo do pagamento da cartela ser├í feito via Wathsapp com a organiza├º├úo do bol├úo. Ap├│s feita a aposta uma nova aba ser├í automaticamente redirecionada para o n├║mero de atendimento|',NULL,'2022-12-13 20:59:29'),(3,'home_bg','Site/Images/HomeBackground/f3wSFV1wp6vDlrfISBbb1c4GTM0a76M8ciWGuvjW.jpg',NULL,'2022-12-13 19:24:44'),(4,'logo','Site/Images/Logo/lJR4jgMV2ECF2Nw61KWdD2ECxxCTl0bhmpjTRUAY.png',NULL,'2022-12-16 14:56:49'),(5,'name','bol├úo de        <span>futebol</span>',NULL,'2022-12-26 18:24:45'),(6,'p_color','#00763c',NULL,'2022-12-13 19:38:44'),(7,'s_color','#f9db03',NULL,'2022-12-13 19:38:44'),(8,'name_color_1','#ba4a4a',NULL,'2022-12-13 19:24:44'),(9,'name_color_2','#0dbf5d',NULL,'2022-12-13 19:24:45'),(10,'bonus_bg_image','Site/Images/BonusBackground/NhKRvsevbkiCtA64bsPohREOA6vaJ1zlarWHxsPr.png',NULL,'2022-12-26 18:13:21'),(11,'bonus_text_color_1','#f2e37b',NULL,'2022-12-26 18:24:45'),(12,'bonus_text_color_2','#dd9e34',NULL,'2022-12-26 18:24:45');
/*!40000 ALTER TABLE `admin_opts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendant_id` bigint(20) unsigned NOT NULL,
  `user_card_id` bigint(20) unsigned DEFAULT NULL,
  `attendant_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_card_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_attendant_id_foreign` (`attendant_id`),
  KEY `attendances_user_card_id_foreign` (`user_card_id`),
  CONSTRAINT `attendances_attendant_id_foreign` FOREIGN KEY (`attendant_id`) REFERENCES `attendants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_user_card_id_foreign` FOREIGN KEY (`user_card_id`) REFERENCES `user_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,1,NULL,'Admin','Rodrigo','11914699934','ad5s21','doubt','2022-12-28 21:36:51','2022-12-28 21:36:51'),(2,1,NULL,'Admin','Rodrigo','11914699934','ad5s21','doubt','2022-12-28 21:37:11','2022-12-28 21:37:11'),(3,1,NULL,'Admin',NULL,NULL,NULL,'doubt','2022-12-28 21:40:48','2022-12-28 21:40:48'),(4,1,NULL,'Admin',NULL,NULL,NULL,'doubt','2022-12-28 21:42:22','2022-12-28 21:42:22'),(5,1,NULL,'Admin',NULL,NULL,NULL,'doubt','2022-12-28 21:44:50','2022-12-28 21:44:50'),(6,1,NULL,'Admin','Rodrigo','11914699934','123456','doubt','2022-12-28 21:45:40','2022-12-28 21:45:40'),(7,1,NULL,'Admin','rodrigo','11914699934','123456','doubt','2022-12-28 21:47:00','2022-12-28 21:47:00'),(8,1,NULL,'Admin',NULL,NULL,NULL,'doubt','2022-12-28 21:58:06','2022-12-28 21:58:06'),(9,6,NULL,'Atendente 3',NULL,NULL,NULL,'doubt','2022-12-29 21:41:37','2022-12-29 21:41:37'),(10,6,NULL,'Atendente 3',NULL,NULL,NULL,'doubt','2022-12-29 21:43:31','2022-12-29 21:43:31'),(11,6,NULL,'Atendente 3',NULL,NULL,NULL,'doubt','2022-12-29 21:43:38','2022-12-29 21:43:38');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendants`
--

DROP TABLE IF EXISTS `attendants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_permission` tinyint(1) NOT NULL,
  `doubt_permission` tinyint(1) NOT NULL,
  `validate_permission` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `attendants_user_id_foreign` (`user_id`),
  CONSTRAINT `attendants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendants`
--

LOCK TABLES `attendants` WRITE;
/*!40000 ALTER TABLE `attendants` DISABLE KEYS */;
INSERT INTO `attendants` VALUES (1,2,'Admin','99999999999','rodrigo',1,1,1,'2022-12-28 13:07:25','2022-12-30 22:33:09'),(4,5,'Atendente','11914699933','sadfsadf',1,0,1,'2022-12-28 15:00:30','2022-12-30 20:35:46'),(5,6,'Atendente 2','11914699932','sadfsadfs',1,1,1,'2022-12-28 15:03:13','2022-12-28 15:03:13'),(6,7,'Atendente 3','11914699931','dsfsadfsadf',1,1,1,'2022-12-28 15:05:46','2022-12-28 15:05:46'),(7,10,'Teste permissao','11914699937','',0,0,0,'2022-12-30 18:15:31','2022-12-30 19:29:30');
/*!40000 ALTER TABLE `attendants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bets`
--

DROP TABLE IF EXISTS `bets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_card_id` bigint(20) unsigned NOT NULL,
  `match_id` int(11) NOT NULL,
  `match_src` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_round` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bets_user_card_id_foreign` (`user_card_id`),
  CONSTRAINT `bets_user_card_id_foreign` FOREIGN KEY (`user_card_id`) REFERENCES `user_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bets`
--

LOCK TABLES `bets` WRITE;
/*!40000 ALTER TABLE `bets` DISABLE KEYS */;
/*!40000 ALTER TABLE `bets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matchs` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `endtime` datetime NOT NULL,
  `price` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'common',
  `host_percentage` int(11) NOT NULL,
  `bonus` int(11) DEFAULT NULL,
  `valuation` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cards`
--

LOCK TABLES `cards` WRITE;
/*!40000 ALTER TABLE `cards` DISABLE KEYS */;
INSERT INTO `cards` VALUES (13,'test','{\"id\":838361,\"src\":\"API_FOOTBALL\",\"round\":null};{\"id\":838362,\"src\":\"API_FOOTBALL\",\"round\":null};{\"id\":838363,\"src\":\"API_FOOTBALL\",\"round\":null};{\"id\":838364,\"src\":\"API_FOOTBALL\",\"round\":null};{\"id\":838365,\"src\":\"API_FOOTBALL\",\"round\":null};','2023-01-01 12:00:00',10,'common',30,2000,4000,'2022-12-26 16:02:57','2022-12-26 16:02:57');
/*!40000 ALTER TABLE `cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `championships`
--

DROP TABLE IF EXISTS `championships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `championships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `championships`
--

LOCK TABLES `championships` WRITE;
/*!40000 ALTER TABLE `championships` DISABLE KEYS */;
INSERT INTO `championships` VALUES (1,'Qualquer','2022-12-12 16:09:56','2022-12-12 16:09:56','custom'),(2,'outro','2022-12-19 17:48:19','2022-12-19 17:48:21','api-football');
/*!40000 ALTER TABLE `championships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendant_id` bigint(20) unsigned DEFAULT NULL,
  `sale` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entries_attendant_id_foreign` (`attendant_id`),
  CONSTRAINT `entries_attendant_id_foreign` FOREIGN KEY (`attendant_id`) REFERENCES `attendants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entries`
--

LOCK TABLES `entries` WRITE;
/*!40000 ALTER TABLE `entries` DISABLE KEYS */;
INSERT INTO `entries` VALUES (1,'127.0.0.1',NULL,0,'2022-12-30 23:14:17','2022-12-30 23:14:17');
/*!40000 ALTER TABLE `entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matchs`
--

DROP TABLE IF EXISTS `matchs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matchs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `championship_id` bigint(20) unsigned NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` datetime NOT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT 0,
  `home_score` int(11) NOT NULL,
  `away_score` int(11) NOT NULL,
  `home_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `away_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `away_flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `src` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Custom',
  PRIMARY KEY (`id`),
  KEY `matchs_championship_id_foreign` (`championship_id`),
  CONSTRAINT `matchs_championship_id_foreign` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matchs`
--

LOCK TABLES `matchs` WRITE;
/*!40000 ALTER TABLE `matchs` DISABLE KEYS */;
INSERT INTO `matchs` VALUES (1,1,'ab','2022-12-14 12:00:00',0,0,0,'qaulquer','mais um','http://localhost:8000/team/flag/FgOzJ3hHnYDm04QJd2X2ViIaM0G6xQq0d04zc5X1.png','http://localhost:8000/team/flag/0YYyTyMcVr5zNBywp9z24jC0YyF32JaaNOwPaKxP.png','2022-12-12 16:11:43','2022-12-12 16:11:43','Custom'),(2,1,'ab','2022-12-18 12:00:00',0,0,0,'mais um','qaulquer','http://localhost:8000/team/flag/0YYyTyMcVr5zNBywp9z24jC0YyF32JaaNOwPaKxP.png','http://localhost:8000/team/flag/FgOzJ3hHnYDm04QJd2X2ViIaM0G6xQq0d04zc5X1.png','2022-12-17 14:01:26','2022-12-17 14:01:26','Custom'),(3,1,'ab','2022-12-18 12:00:00',0,0,0,'mais um','qaulquer','http://localhost:8000/team/flag/0YYyTyMcVr5zNBywp9z24jC0YyF32JaaNOwPaKxP.png','http://localhost:8000/team/flag/FgOzJ3hHnYDm04QJd2X2ViIaM0G6xQq0d04zc5X1.png','2022-12-17 14:01:27','2022-12-17 14:01:27','Custom');
/*!40000 ALTER TABLE `matchs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_12_14_000001_create_personal_access_tokens_table',1),(4,'2022_11_26_115915_create_teams_table',1),(5,'2022_11_26_134003_create_cards_table',1),(6,'2022_11_26_141023_create_user_cards_table',1),(7,'2022_11_26_141112_create_bets_table',1),(8,'2022_11_26_142055_create_admin_opts_table',1),(9,'2022_12_01_153646_create_public_rankings_table',1),(10,'2022_12_07_170734_create_championships_table',1),(11,'2022_12_07_171211_create_matchs_table',1),(12,'2022_12_09_124721_create_foreign_for_championchips_on_teams',1),(13,'2022_12_09_125421_create_foreign_for_championship_id_on_teams',1),(15,'2022_12_18_134647_create_receipts_table',2),(16,'2022_12_19_144102_create_type_column_on_championships_table',3),(17,'2022_12_27_191445_create_attendants_table',4),(18,'2022_12_27_192012_add_foreign_id_for_users_in_attendants_table',5),(20,'2022_12_28_133506_create_attendances_table',6),(22,'2022_12_30_175832_create_entrys_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `public_rankings`
--

DROP TABLE IF EXISTS `public_rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `public_rankings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `public_rankings_card_id_foreign` (`card_id`),
  CONSTRAINT `public_rankings_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `public_rankings`
--

LOCK TABLES `public_rankings` WRITE;
/*!40000 ALTER TABLE `public_rankings` DISABLE KEYS */;
/*!40000 ALTER TABLE `public_rankings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_card_id` bigint(20) unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bets` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receipts_user_card_id_foreign` (`user_card_id`),
  CONSTRAINT `receipts_user_card_id_foreign` FOREIGN KEY (`user_card_id`) REFERENCES `user_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipts`
--

LOCK TABLES `receipts` WRITE;
/*!40000 ALTER TABLE `receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `championship_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_championship_id_foreign` (`championship_id`),
  CONSTRAINT `teams_championship_id_foreign` FOREIGN KEY (`championship_id`) REFERENCES `championships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'qaulquer','http://localhost:8000/team/flag/FgOzJ3hHnYDm04QJd2X2ViIaM0G6xQq0d04zc5X1.png','2022-12-12 16:10:23','2022-12-12 16:10:23',1),(2,'mais um','http://localhost:8000/team/flag/0YYyTyMcVr5zNBywp9z24jC0YyF32JaaNOwPaKxP.png','2022-12-12 16:10:36','2022-12-12 16:10:36',1);
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_cards`
--

DROP TABLE IF EXISTS `user_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_cards_card_id_foreign` (`card_id`),
  CONSTRAINT `user_cards_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_cards`
--

LOCK TABLES `user_cards` WRITE;
/*!40000 ALTER TABLE `user_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Admin','admin@admin.com','99999999999','$2y$10$vxlXvtCI6CMHXE9UBvaRf.qZ55ESSgJ/DHnqNvN83fgNuL1XjhjLW',1,'admin'),(5,'Atendente','atendente@atendente.com','11914699933','$2y$10$mLUZEpIsquICbsgsmiDL5OIS6gjZjcrcxFEFreksdnoBVKMXqnQ9a',1,'attendant'),(6,'Atendente 2','atendente2@atendente.com','11914699932','$2y$10$teYWR85hsSUT9mUxzlVJ0OdrXIvcCnlZ6iOrmXl743r9m1U7K7TnK',1,'attendant'),(7,'Atendente 3','atendente3@atendente.com','11914699931','$2y$10$HKdZHq2tqHwL1P97t.uWCOP9.BVyohg7/kmsLDQGVw6rKVyogHBd.',1,'attendant'),(10,'Teste permissao','atendente8@atendente.com','11914699937','$2y$10$Dr52g5KxXWj30XU/sGcsV.oRX1XFZBsd05/LEkJyRntHbnbbKR.K2',1,'attendant');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-02 11:28:36
