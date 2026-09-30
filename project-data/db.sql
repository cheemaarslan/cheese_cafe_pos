-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: 3_guys_restaurant_pos
-- ------------------------------------------------------
-- Server version	8.0.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addons`
--

DROP TABLE IF EXISTS `addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `author` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files` json DEFAULT NULL,
  `item_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addons`
--

LOCK TABLES `addons` WRITE;
/*!40000 ALTER TABLE `addons` DISABLE KEYS */;
/*!40000 ALTER TABLE `addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apartment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytic_sections`
--

DROP TABLE IF EXISTS `analytic_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytic_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `analytic_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` tinyint NOT NULL DEFAULT '5',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `analytic_sections_analytic_id_foreign` (`analytic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytic_sections`
--

LOCK TABLES `analytic_sections` WRITE;
/*!40000 ALTER TABLE `analytic_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `analytic_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytics`
--

DROP TABLE IF EXISTS `analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics`
--

LOCK TABLES `analytics` WRITE;
/*!40000 ALTER TABLE `analytics` DISABLE KEYS */;
/*!40000 ALTER TABLE `analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'DHA-1 (main)','DHA@restaurant.net','+536464646464','23.8042375','90.3525979','DHA-1','Lahore','1216','House : 25, Rd: 122, Block A, DHA, Lahore',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `capture_payment_notifications`
--

DROP TABLE IF EXISTS `capture_payment_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capture_payment_notifications` (
  `order_id` bigint unsigned NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capture_payment_notifications`
--

LOCK TABLES `capture_payment_notifications` WRITE;
/*!40000 ALTER TABLE `capture_payment_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `capture_payment_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_cryptocurrency` tinyint unsigned NOT NULL,
  `exchange_rate` decimal(19,2) DEFAULT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint unsigned DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'Dollars','$','USD',10,1.00,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `default_access`
--

DROP TABLE IF EXISTS `default_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `default_access` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `default_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_access`
--

LOCK TABLES `default_access` WRITE;
/*!40000 ALTER TABLE `default_access` DISABLE KEYS */;
INSERT INTO `default_access` VALUES (1,'branch_id',1,1,'2025-11-20 08:44:14','2025-11-20 08:44:14');
/*!40000 ALTER TABLE `default_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dining_tables`
--

DROP TABLE IF EXISTS `dining_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dining_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int DEFAULT NULL,
  `qr_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dining_tables_slug_unique` (`slug`),
  KEY `dining_tables_branch_id_foreign` (`branch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dining_tables`
--

LOCK TABLES `dining_tables` WRITE;
/*!40000 ALTER TABLE `dining_tables` DISABLE KEYS */;
/*!40000 ALTER TABLE `dining_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateway_options`
--

DROP TABLE IF EXISTS `gateway_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateway_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_id` bigint NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` tinyint NOT NULL,
  `activities` longtext COLLATE utf8mb4_unicode_ci,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateway_options`
--

LOCK TABLES `gateway_options` WRITE;
/*!40000 ALTER TABLE `gateway_options` DISABLE KEYS */;
INSERT INTO `gateway_options` VALUES (1,3,'App\\Models\\PaymentGateway','paypal_app_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(2,3,'App\\Models\\PaymentGateway','paypal_client_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(3,3,'App\\Models\\PaymentGateway','paypal_client_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(4,3,'App\\Models\\PaymentGateway','paypal_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(5,3,'App\\Models\\PaymentGateway','paypal_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(6,4,'App\\Models\\PaymentGateway','stripe_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(7,4,'App\\Models\\PaymentGateway','stripe_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(8,4,'App\\Models\\PaymentGateway','stripe_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(9,4,'App\\Models\\PaymentGateway','stripe_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(10,5,'App\\Models\\PaymentGateway','flutterwave_public_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(11,5,'App\\Models\\PaymentGateway','flutterwave_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(12,5,'App\\Models\\PaymentGateway','flutterwave_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(13,5,'App\\Models\\PaymentGateway','flutterwave_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(14,6,'App\\Models\\PaymentGateway','paystack_public_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(15,6,'App\\Models\\PaymentGateway','paystack_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(16,6,'App\\Models\\PaymentGateway','paystack_payment_url','https://api.paystack.co',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(17,6,'App\\Models\\PaymentGateway','paystack_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(18,6,'App\\Models\\PaymentGateway','paystack_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(19,7,'App\\Models\\PaymentGateway','sslcommerz_store_name','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(20,7,'App\\Models\\PaymentGateway','sslcommerz_store_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(21,7,'App\\Models\\PaymentGateway','sslcommerz_store_password','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(22,7,'App\\Models\\PaymentGateway','sslcommerz_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(23,7,'App\\Models\\PaymentGateway','sslcommerz_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(24,8,'App\\Models\\PaymentGateway','mollie_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(25,8,'App\\Models\\PaymentGateway','mollie_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(26,8,'App\\Models\\PaymentGateway','mollie_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(27,9,'App\\Models\\PaymentGateway','senangpay_merchant_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(28,9,'App\\Models\\PaymentGateway','senangpay_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(29,9,'App\\Models\\PaymentGateway','senangpay_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(30,9,'App\\Models\\PaymentGateway','senangpay_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(31,10,'App\\Models\\PaymentGateway','bkash_app_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(32,10,'App\\Models\\PaymentGateway','bkash_app_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(33,10,'App\\Models\\PaymentGateway','bkash_username','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(34,10,'App\\Models\\PaymentGateway','bkash_password','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(35,10,'App\\Models\\PaymentGateway','bkash_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(36,10,'App\\Models\\PaymentGateway','bkash_status','',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(37,11,'App\\Models\\PaymentGateway','paytm_merchant_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(38,11,'App\\Models\\PaymentGateway','paytm_merchant_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(39,11,'App\\Models\\PaymentGateway','paytm_merchant_website','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(40,11,'App\\Models\\PaymentGateway','paytm_channel','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(41,11,'App\\Models\\PaymentGateway','paytm_industry_type','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(42,11,'App\\Models\\PaymentGateway','paytm_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(43,11,'App\\Models\\PaymentGateway','paytm_status','',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(44,12,'App\\Models\\PaymentGateway','razorpay_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(45,12,'App\\Models\\PaymentGateway','razorpay_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(46,12,'App\\Models\\PaymentGateway','razorpay_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(47,12,'App\\Models\\PaymentGateway','razorpay_status','',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(48,13,'App\\Models\\PaymentGateway','mercadopago_client_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(49,13,'App\\Models\\PaymentGateway','mercadopago_client_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(50,13,'App\\Models\\PaymentGateway','mercadopago_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(51,13,'App\\Models\\PaymentGateway','mercadopago_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(52,14,'App\\Models\\PaymentGateway','cashfree_app_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(53,14,'App\\Models\\PaymentGateway','cashfree_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(54,14,'App\\Models\\PaymentGateway','cashfree_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(55,14,'App\\Models\\PaymentGateway','cashfree_status','',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(56,15,'App\\Models\\PaymentGateway','payfast_merchant_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(57,15,'App\\Models\\PaymentGateway','payfast_merchant_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(58,15,'App\\Models\\PaymentGateway','payfast_passphrase','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(59,15,'App\\Models\\PaymentGateway','payfast_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(60,15,'App\\Models\\PaymentGateway','payfast_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(61,16,'App\\Models\\PaymentGateway','skrill_merchant_email','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(62,16,'App\\Models\\PaymentGateway','skrill_merchant_api_password','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(63,16,'App\\Models\\PaymentGateway','skrill_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(64,16,'App\\Models\\PaymentGateway','skrill_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(65,17,'App\\Models\\PaymentGateway','phonepe_client_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(66,17,'App\\Models\\PaymentGateway','phonepe_merchant_user_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(67,17,'App\\Models\\PaymentGateway','phonepe_key_index','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(68,17,'App\\Models\\PaymentGateway','phonepe_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(69,17,'App\\Models\\PaymentGateway','phonepe_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(70,17,'App\\Models\\PaymentGateway','phonepe_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(71,18,'App\\Models\\PaymentGateway','telr_store_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(72,18,'App\\Models\\PaymentGateway','telr_store_auth_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(73,18,'App\\Models\\PaymentGateway','telr_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(74,18,'App\\Models\\PaymentGateway','telr_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(75,19,'App\\Models\\PaymentGateway','iyzico_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(76,19,'App\\Models\\PaymentGateway','iyzico_secret_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(77,19,'App\\Models\\PaymentGateway','iyzico_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(78,19,'App\\Models\\PaymentGateway','iyzico_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(79,20,'App\\Models\\PaymentGateway','pesapal_consumer_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(80,20,'App\\Models\\PaymentGateway','pesapal_consumer_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(81,20,'App\\Models\\PaymentGateway','pesapal_ipn_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(82,20,'App\\Models\\PaymentGateway','pesapal_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(83,20,'App\\Models\\PaymentGateway','pesapal_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(84,21,'App\\Models\\PaymentGateway','midtrans_server_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(85,21,'App\\Models\\PaymentGateway','midtrans_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(86,21,'App\\Models\\PaymentGateway','midtrans_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(87,22,'App\\Models\\PaymentGateway','myfatoorah_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(88,22,'App\\Models\\PaymentGateway','myfatoorah_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(89,22,'App\\Models\\PaymentGateway','myfatoorah_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(90,23,'App\\Models\\PaymentGateway','easypaisa_store_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(91,23,'App\\Models\\PaymentGateway','easypaisa_hash_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(92,23,'App\\Models\\PaymentGateway','easypaisa_username','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(93,23,'App\\Models\\PaymentGateway','easypaisa_password','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(94,23,'App\\Models\\PaymentGateway','easypaisa_mode','',10,'{\"5\":\"sandbox\",\"10\":\"live\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(95,23,'App\\Models\\PaymentGateway','easypaisa_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(96,1,'App\\Models\\SmsGateway','twilio_account_sid','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(97,1,'App\\Models\\SmsGateway','twilio_auth_token','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(98,1,'App\\Models\\SmsGateway','twilio_from','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(99,1,'App\\Models\\SmsGateway','twilio_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(100,2,'App\\Models\\SmsGateway','clickatell_apikey','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(101,2,'App\\Models\\SmsGateway','clickatell_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(102,3,'App\\Models\\SmsGateway','nexmo_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(103,3,'App\\Models\\SmsGateway','nexmo_secret','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(104,3,'App\\Models\\SmsGateway','nexmo_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(105,4,'App\\Models\\SmsGateway','msg91_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(106,4,'App\\Models\\SmsGateway','msg91_sender_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(107,4,'App\\Models\\SmsGateway','msg91_template_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(108,4,'App\\Models\\SmsGateway','msg91_template_variable','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(109,4,'App\\Models\\SmsGateway','msg91_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(110,5,'App\\Models\\SmsGateway','twofactor_module','PROMO_SMS',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(111,5,'App\\Models\\SmsGateway','twofactor_from','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(112,5,'App\\Models\\SmsGateway','twofactor_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(113,5,'App\\Models\\SmsGateway','twofactor_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(114,6,'App\\Models\\SmsGateway','bulksms_username','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(115,6,'App\\Models\\SmsGateway','bulksms_password','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(116,6,'App\\Models\\SmsGateway','bulksms_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(117,7,'App\\Models\\SmsGateway','bulksmsbd_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(118,7,'App\\Models\\SmsGateway','bulksmsbd_sender_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(119,7,'App\\Models\\SmsGateway','bulksmsbd_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(120,8,'App\\Models\\SmsGateway','telesign_api_key','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(121,8,'App\\Models\\SmsGateway','telesign_customer_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(122,8,'App\\Models\\SmsGateway','telesign_sender_id','',5,'\"\"',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(123,8,'App\\Models\\SmsGateway','telesign_status','10',10,'{\"5\":\"enable\",\"10\":\"disable\"}',NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12');
/*!40000 ALTER TABLE `gateway_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_addons`
--

DROP TABLE IF EXISTS `item_addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `addon_item_id` bigint unsigned NOT NULL,
  `addon_item_variation` json DEFAULT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_addons_item_id_foreign` (`item_id`),
  KEY `item_addons_addon_item_id_foreign` (`addon_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_addons`
--

LOCK TABLES `item_addons` WRITE;
/*!40000 ALTER TABLE `item_addons` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_attributes`
--

DROP TABLE IF EXISTS `item_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_attributes`
--

LOCK TABLES `item_attributes` WRITE;
/*!40000 ALTER TABLE `item_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_categories`
--

DROP TABLE IF EXISTS `item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `sort` bigint unsigned NOT NULL DEFAULT '1',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_categories`
--

LOCK TABLES `item_categories` WRITE;
/*!40000 ALTER TABLE `item_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_extras`
--

DROP TABLE IF EXISTS `item_extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_extras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(19,6) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_extras_item_id_foreign` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_extras`
--

LOCK TABLES `item_extras` WRITE;
/*!40000 ALTER TABLE `item_extras` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_extras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_variations`
--

DROP TABLE IF EXISTS `item_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_variations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `item_attribute_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(19,6) NOT NULL DEFAULT '0.000000',
  `caution` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_variations_item_id_foreign` (`item_id`),
  KEY `item_variations_item_attribute_id_foreign` (`item_attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_variations`
--

LOCK TABLES `item_variations` WRITE;
/*!40000 ALTER TABLE `item_variations` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint unsigned NOT NULL,
  `tax_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caution` longtext COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `price` decimal(19,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `item_type` tinyint NOT NULL DEFAULT '5',
  `order` bigint NOT NULL DEFAULT '1',
  `is_featured` tinyint NOT NULL DEFAULT '5',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_item_category_id_foreign` (`item_category_id`),
  KEY `items_tax_id_foreign` (`tax_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_mode` tinyint unsigned NOT NULL DEFAULT '5',
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','en',5,5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(2,'Bangla','bn',5,5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(3,'German','de',5,5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(4,'Arabic','ar',10,5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,'App\\Models\\PaymentGateway',1,'dc4aba60-118f-4efe-8ad9-0a9904ee53f1','payment-gateway','cash-on-delivery','cash-on-delivery.png','image/png','public','public',3437,'[]','[]','[]','[]',1,'2025-11-20 08:41:10','2025-11-20 08:41:10'),(2,'App\\Models\\PaymentGateway',2,'65246b00-55c1-4e78-abe1-51c39b592f5a','payment-gateway','credit','credit.png','image/png','public','public',3885,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(3,'App\\Models\\PaymentGateway',3,'8b068d96-4f5d-43f6-ac4c-d7634cefe5a1','payment-gateway','paypal','paypal.png','image/png','public','public',3809,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(4,'App\\Models\\PaymentGateway',4,'ca7b1880-a933-4763-bf5f-4985237723d0','payment-gateway','stripe','stripe.png','image/png','public','public',3635,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(5,'App\\Models\\PaymentGateway',5,'6e27f88c-6a16-4e9e-936d-e7638fa3fb72','payment-gateway','flutterwave','flutterwave.png','image/png','public','public',5191,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(6,'App\\Models\\PaymentGateway',6,'d0297242-ff40-44eb-b687-e42aac184303','payment-gateway','paystack','paystack.png','image/png','public','public',4195,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(7,'App\\Models\\PaymentGateway',7,'2e856623-46b7-4834-b985-58090e95dc84','payment-gateway','sslcommerz','sslcommerz.png','image/png','public','public',4546,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(8,'App\\Models\\PaymentGateway',8,'53a74560-f885-40f4-ad7a-4b4200c9794f','payment-gateway','mollie','mollie.png','image/png','public','public',8116,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(9,'App\\Models\\PaymentGateway',9,'8bc58439-fb3f-489b-9244-87726ea81e3a','payment-gateway','senangpay','senangpay.png','image/png','public','public',6541,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(10,'App\\Models\\PaymentGateway',10,'ad375276-b9fc-4f75-a0de-5d4487b0d4dd','payment-gateway','bkash','bkash.png','image/png','public','public',5282,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(11,'App\\Models\\PaymentGateway',11,'b82c29d3-f759-4663-9af2-43770e33575d','payment-gateway','paytm','paytm.png','image/png','public','public',3285,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(12,'App\\Models\\PaymentGateway',12,'71d04d41-753f-4086-855b-7d181a5d005e','payment-gateway','razorpay','razorpay.png','image/png','public','public',4847,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(13,'App\\Models\\PaymentGateway',13,'ab48d379-71b4-4002-af2e-032855b2f843','payment-gateway','mercadopago','mercadopago.png','image/png','public','public',11423,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(14,'App\\Models\\PaymentGateway',14,'8cafaf46-5622-40d2-acae-f6c2e4e3f8fa','payment-gateway','cashfree','cashfree.png','image/png','public','public',4940,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(15,'App\\Models\\PaymentGateway',15,'b5863f42-8cb3-4248-8f61-734be76beac0','payment-gateway','payfast','payfast.png','image/png','public','public',2173,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(16,'App\\Models\\PaymentGateway',16,'c54709a6-f563-4ddd-8c89-2271610e9757','payment-gateway','skrill','skrill.png','image/png','public','public',7074,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(17,'App\\Models\\PaymentGateway',17,'fe18e97b-3473-41e6-bb72-149f3a8ebdeb','payment-gateway','phonepe','phonepe.png','image/png','public','public',4417,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(18,'App\\Models\\PaymentGateway',18,'59e3ba34-55c3-4549-bb45-adf7f45e1fc9','payment-gateway','telr','telr.png','image/png','public','public',7594,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(19,'App\\Models\\PaymentGateway',19,'4bb701c6-4c84-4aa4-9ea4-09c832c10955','payment-gateway','iyzico','iyzico.png','image/png','public','public',7652,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(20,'App\\Models\\PaymentGateway',20,'f6736138-5e4b-49a2-b987-4079c7d12fd4','payment-gateway','pesapal','pesapal.png','image/png','public','public',9373,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(21,'App\\Models\\PaymentGateway',21,'d0457088-c33d-4e3c-8ca5-fc8d2cac206d','payment-gateway','midtrans','midtrans.png','image/png','public','public',5877,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(22,'App\\Models\\PaymentGateway',22,'a83d4aa1-90e2-4878-a1d8-734fe2bbc48d','payment-gateway','myfatoorah','myfatoorah.png','image/png','public','public',5411,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(23,'App\\Models\\PaymentGateway',23,'74d1de55-d59a-41a3-bba1-6cc2c9cc746f','payment-gateway','easypaisa','easypaisa.png','image/png','public','public',11255,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(24,'App\\Models\\Language',1,'e4ff6576-7cfe-4289-badf-bc892802a2c2','language','english','english.png','image/png','public','public',1149,'[]','[]','[]','[]',1,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(25,'App\\Models\\Language',2,'d2979961-a823-4c71-8d2f-465f51d8df48','language','bangla','bangla.png','image/png','public','public',1309,'[]','[]','[]','[]',1,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(26,'App\\Models\\Language',3,'960fadf7-16db-4918-b89e-8442fb78dc0f','language','german','german.png','image/png','public','public',1835,'[]','[]','[]','[]',1,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(27,'App\\Models\\Language',4,'6fe6767d-d9e8-43be-8d98-7fa24f3c913b','language','arabic','arabic.png','image/png','public','public',4388,'[]','[]','[]','[]',1,'2025-11-20 08:41:13','2025-11-20 08:41:13');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_sections`
--

DROP TABLE IF EXISTS `menu_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_sections`
--

LOCK TABLES `menu_sections` WRITE;
/*!40000 ALTER TABLE `menu_sections` DISABLE KEYS */;
INSERT INTO `menu_sections` VALUES (1,'Header Section',NULL,NULL,NULL,NULL,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(2,'Footer Section',NULL,NULL,NULL,NULL,'2025-11-20 08:41:07','2025-11-20 08:41:07');
/*!40000 ALTER TABLE `menu_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_templates`
--

DROP TABLE IF EXISTS `menu_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_templates`
--

LOCK TABLES `menu_templates` WRITE;
/*!40000 ALTER TABLE `menu_templates` DISABLE KEYS */;
INSERT INTO `menu_templates` VALUES (1,'Contact Us',NULL,NULL,NULL,NULL,'2025-11-20 08:41:07','2025-11-20 08:41:07');
/*!40000 ALTER TABLE `menu_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL,
  `parent` int unsigned NOT NULL DEFAULT '0',
  `type` int unsigned NOT NULL DEFAULT '1',
  `priority` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'Dashboard','dashboard','dashboard','lab lab-dashboard',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(2,'Items','items','items','lab lab-items',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(3,'Dining Tables','dining_tables','dining-tables','lab lab-dining-table',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(4,'Pos & Orders','pos_and_orders','#','lab lab-pos',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(5,'POS','pos','pos','lab lab-pos',1,4,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(6,'POS Orders','pos_orders','pos-orders','lab lab-pos-orders',1,4,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(7,'Table Orders','table_orders','table-orders','lab lab-reserve-line',1,4,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(8,'K.D.S','k_d_s','kitchen-display-system','lab lab-kds',1,4,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(9,'O.S.S','o_s_s','order-status-screen','lab lab-cds',1,4,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(10,'Promo','promo','#','lab ',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(11,'Offers','offers','offers','lab lab-offers',1,10,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(12,'Users','users','#','lab ',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(13,'Administrators','administrators','administrators','lab lab-administrators',1,12,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(14,'Customers','customers','customers','lab lab-customers',1,12,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(15,'Employees','employees','employees','lab lab-employee',1,12,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(16,'Waiters','waiters','waiters','lab lab-waiter',1,12,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(17,'Chef','chefs','chefs','lab lab-chef',1,12,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(18,'Accounts','accounts','#','lab ',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(19,'Transactions','transactions','transactions','lab lab-transactions',1,18,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(20,'Reports','reports','#','lab ',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(21,'Sales Report','sales_report','sales-report','lab lab-sales-report',1,20,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(22,'Items Report','items_report','items-report','lab lab-items-report',1,20,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(23,'Credit Balance Report','credit_balance_report','credit-balance-report','lab lab-credit-balance-report',1,20,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(24,'Setup','setup','#','lab ',1,0,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(25,'Settings','settings','settings','lab lab-settings',1,24,1,100,'2025-11-20 08:41:07','2025-11-20 08:41:07');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_histories`
--

DROP TABLE IF EXISTS `message_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_histories_message_id_foreign` (`message_id`),
  KEY `message_histories_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_histories`
--

LOCK TABLES `message_histories` WRITE;
/*!40000 ALTER TABLE `message_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2020_09_10_080029_create_menus_table',1),(6,'2022_05_01_142407_create_permission_tables',1),(7,'2022_05_24_204620_create_settings_table',1),(8,'2022_05_25_124629_create_currencies_table',1),(9,'2022_06_26_055545_create_default_access_table',1),(10,'2022_08_10_143500_create_media_table',1),(11,'2022_10_31_015126_create_pesapals_table',1),(12,'2022_11_17_110125_create_branches_table',1),(13,'2022_11_17_110157_create_languages_table',1),(14,'2022_11_17_110300_create_addresses_table',1),(15,'2022_11_17_110428_create_item_categories_table',1),(16,'2022_11_17_110455_create_offers_table',1),(17,'2022_11_17_110459_create_taxes_table',1),(18,'2022_11_17_110514_create_items_table',1),(19,'2022_11_17_110541_create_item_attributes_table',1),(20,'2022_11_17_110621_create_item_variations_table',1),(21,'2022_11_17_110650_create_item_extras_table',1),(22,'2022_11_17_110810_create_orders_table',1),(23,'2022_11_17_110832_create_order_items_table',1),(24,'2022_11_17_111737_create_offer_items_table',1),(25,'2022_11_17_113842_create_menu_sections_table',1),(26,'2022_11_17_114040_create_menu_templates_table',1),(27,'2022_11_17_114144_create_analytics_table',1),(28,'2022_11_17_114516_create_analytics_sections_table',1),(29,'2022_11_17_114835_create_payment_gateways_table',1),(30,'2022_11_17_115136_create_sms_gateways_table',1),(31,'2022_11_17_115341_create_gateway_options_table',1),(32,'2022_11_17_115716_create_addons_table',1),(33,'2022_11_17_120130_create_notifications_table',1),(34,'2022_11_17_120408_create_messages_table',1),(35,'2022_11_17_120624_create_message_histories_table',1),(36,'2022_11_17_120626_create_pages_table',1),(37,'2022_11_17_120627_create_item_addons_table',1),(38,'2022_11_23_125038_create_push_notifications_table',1),(39,'2023_01_09_111734_create_time_slots_table',1),(40,'2023_02_20_180253_create_order_addresses_table',1),(41,'2023_03_06_154954_create_otps_table',1),(42,'2023_03_23_143747_create_transactions_table',1),(43,'2023_03_23_170303_create_capture_payment_notifications_table',1),(44,'2023_03_27_140107_create_notification_alerts_table',1),(45,'2023_07_19_135307_add_soft_delete_column_to_users_table',1),(46,'2023_07_20_095727_add_total_tax_to_orders_table',1),(47,'2023_07_20_095843_add_tax_to_order_items_table',1),(48,'2023_09_05_133748_create_dining_tables_table',1),(49,'2023_11_18_154743_add_dining_table_id_to_order_table',1),(50,'2024_01_22_172712_add_display_mode_to_languages_table',1),(51,'2024_03_07_095727_add_sort_to_item_categories_table',1),(52,'2024_10_28_000000_add_pos_payment_method_and_note_to_orders_table',1),(53,'2025_02_09_000000_add_pos_received_amount_to_orders_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_alerts`
--

DROP TABLE IF EXISTS `notification_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_notification_message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail` tinyint DEFAULT NULL,
  `sms` tinyint DEFAULT NULL,
  `push_notification` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_alerts`
--

LOCK TABLES `notification_alerts` WRITE;
/*!40000 ALTER TABLE `notification_alerts` DISABLE KEYS */;
INSERT INTO `notification_alerts` VALUES (1,'Admin And Branch Manager New Order Message','admin_and_branch_manager_new_order_message','You have a new order.','You have a new order.','You have a new order.',10,10,10,'2025-11-20 08:41:08','2025-11-20 08:41:08');
/*!40000 ALTER TABLE `notification_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offer_items`
--

DROP TABLE IF EXISTS `offer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `offer_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offer_items_offer_id_foreign` (`offer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offer_items`
--

LOCK TABLES `offer_items` WRITE;
/*!40000 ALTER TABLE `offer_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `offer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(19,6) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offers`
--

LOCK TABLES `offers` WRITE;
/*!40000 ALTER TABLE `offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_addresses`
--

DROP TABLE IF EXISTS `order_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apartment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_addresses_order_id_foreign` (`order_id`),
  KEY `order_addresses_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_addresses`
--

LOCK TABLES `order_addresses` WRITE;
/*!40000 ALTER TABLE `order_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `discount` decimal(19,6) NOT NULL,
  `tax_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_rate` decimal(19,6) DEFAULT NULL,
  `tax_type` tinyint DEFAULT NULL,
  `tax_amount` decimal(19,6) DEFAULT NULL,
  `price` decimal(19,6) NOT NULL,
  `item_variations` longtext COLLATE utf8mb4_unicode_ci,
  `item_extras` longtext COLLATE utf8mb4_unicode_ci,
  `item_variation_total` decimal(19,6) DEFAULT '0.000000',
  `item_extra_total` decimal(19,6) DEFAULT '0.000000',
  `total_price` decimal(19,6) DEFAULT '0.000000',
  `instruction` text COLLATE utf8mb4_unicode_ci,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_branch_id_foreign` (`branch_id`),
  KEY `order_items_item_id_foreign` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_serial_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `subtotal` decimal(19,6) NOT NULL,
  `discount` decimal(19,6) DEFAULT '0.000000',
  `delivery_charge` decimal(19,6) DEFAULT '0.000000',
  `total_tax` decimal(19,6) DEFAULT NULL,
  `total` decimal(19,6) NOT NULL,
  `order_type` tinyint NOT NULL DEFAULT '5',
  `order_datetime` timestamp NOT NULL DEFAULT '2025-11-19 20:11:52',
  `delivery_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preparation_time` int NOT NULL DEFAULT '0',
  `is_advance_order` tinyint NOT NULL DEFAULT '5',
  `payment_method` bigint NOT NULL DEFAULT '1',
  `pos_payment_method` tinyint DEFAULT NULL,
  `pos_received_amount` decimal(19,6) DEFAULT '0.000000',
  `pos_payment_note` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` tinyint NOT NULL DEFAULT '10',
  `status` tinyint NOT NULL,
  `dining_table_id` bigint unsigned DEFAULT NULL,
  `delivery_boy_id` bigint DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_branch_id_foreign` (`branch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otps` (
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `otps_phone_index` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otps`
--

LOCK TABLES `otps` WRITE;
/*!40000 ALTER TABLE `otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_section_id` bigint unsigned NOT NULL,
  `template_id` bigint DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pages_menu_section_id_foreign` (`menu_section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `misc` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways`
--

LOCK TABLES `payment_gateways` WRITE;
/*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
INSERT INTO `payment_gateways` VALUES (1,'Cash On Delivery','cash-on-delivery','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(2,'Credit','credit','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(3,'Paypal','paypal','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(4,'Stripe','stripe','{\"input\":[\"stripe.stripeInput.blade.php\"],\"js\":[\"stripe.stripeJs.blade.php\"],\"submit\":true}',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(5,'Flutterwave','flutterwave','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(6,'Paystack','paystack','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(7,'SslCommerz','sslcommerz','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(8,'Mollie','mollie','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(9,'Senangpay','senangpay','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(10,'Bkash','bkash','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(11,'Paytm','paytm','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(12,'Razorpay','razorpay','{\"input\":[],\"js\":[\"razorpay.razorpayJs.blade.php\"],\"submit\":false}',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(13,'Mercadopago','mercadopago','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(14,'Cashfree','cashfree','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(15,'Payfast','payfast','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(16,'Skrill','skrill','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(17,'PhonePe','phonepe','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(18,'Telr','telr','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(19,'Iyzico','iyzico','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(20,'Pesapal','pesapal','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(21,'Midtrans','midtrans','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(22,'MyFatoorah','myfatoorah','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(23,'EasyPaisa','easypaisa','null',10,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12');
/*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `url` varchar(191) DEFAULT NULL,
  `parent` bigint unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Dashboard','dashboard','sanctum','dashboard',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(2,'Items','items','sanctum','items',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(3,'Items Create','items_create','sanctum','items/create',2,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(4,'Items Edit','items_edit','sanctum','items/edit',2,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(5,'Items Delete','items_delete','sanctum','items/delete',2,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(6,'Items Show','items_show','sanctum','items/show',2,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(7,'Dining Tables','dining-tables','sanctum','dining-tables',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(8,'Dining Tables Create','dining_tables_create','sanctum','dining-table/create',7,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(9,'Dining Tables Edit','dining_tables_edit','sanctum','dining-table/edit',7,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(10,'Dining Tables Delete','dining_tables_delete','sanctum','dining-tables/delete',7,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(11,'Dining Tables Show','dining_tables_show','sanctum','dining-tables/show',7,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(12,'POS','pos','sanctum','pos',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(13,'POS Orders','pos-orders','sanctum','pos-orders',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(14,'Table Orders','table-orders','sanctum','table-orders',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(15,'K.D.S','kitchen-display-system','sanctum','kitchen-display-system',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(16,'O.S.S','order-status-screen','sanctum','order-status-screen',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(17,'Offers','offers','sanctum','offers',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(18,'Offers Create','offers_create','sanctum','offers/create',17,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(19,'Offers Edit','offers_edit','sanctum','offers/edit',17,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(20,'Offers Delete','offers_delete','sanctum','offers/delete',17,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(21,'Offers Show','offers_show','sanctum','offers/show',17,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(22,'Administrators','administrators','sanctum','administrators',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(23,'Administrators Create','administrators_create','sanctum','administrators/create',22,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(24,'Administrators Edit','administrators_edit','sanctum','administrators/edit',22,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(25,'Administrators Delete','administrators_delete','sanctum','administrators/delete',22,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(26,'Administrators Show','administrators_show','sanctum','administrators/show',22,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(27,'Delivery Boys','delivery-boys','sanctum','delivery-boys',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(28,'Delivery Boys Create','delivery-boys_create','sanctum','delivery-boys/create',27,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(29,'Delivery Boys Edit','delivery-boys_edit','sanctum','delivery-boys/edit',27,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(30,'Delivery Boys Delete','delivery-boys_delete','sanctum','delivery-boys/delete',27,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(31,'Delivery Boys Show','delivery-boys_show','sanctum','delivery-boys/show',27,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(32,'Customers','customers','sanctum','customers',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(33,'Customers Create','customers_create','sanctum','customers/create',32,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(34,'Customers Edit','customers_edit','sanctum','customers/edit',32,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(35,'Customers Delete','customers_delete','sanctum','customers/delete',32,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(36,'Customers Show','customers_show','sanctum','customers/show',32,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(37,'Employees','employees','sanctum','employees',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(38,'Employees Create','employees_create','sanctum','employees/create',37,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(39,'Employees Edit','employees_edit','sanctum','employees/edit',37,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(40,'Employees Delete','employees_delete','sanctum','employees/delete',37,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(41,'Employees Show','employees_show','sanctum','employees/show',37,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(42,'Waiters','waiters','sanctum','waiters',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(43,'Waiters Create','waiters_create','sanctum','waiters/create',42,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(44,'Waiters Edit','waiters_edit','sanctum','waiters/edit',42,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(45,'Waiters Delete','waiters_delete','sanctum','waiters/delete',42,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(46,'Waiters Show','waiters_show','sanctum','waiters/show',42,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(47,'Chefs','chefs','sanctum','chefs',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(48,'Chefs Create','chefs_create','sanctum','chefs/create',47,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(49,'Chefs Edit','chefs_edit','sanctum','chefs/edit',47,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(50,'Chefs Delete','chefs_delete','sanctum','chefs/delete',47,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(51,'Chefs Show','chefs_show','sanctum','chefs/show',47,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(52,'Transactions','transactions','sanctum','transactions',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(53,'Sales Report','sales-report','sanctum','sales-report',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(54,'Items Report','items-report','sanctum','items-report',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(55,'Credit Balance Report','credit-balance-report','sanctum','credit-balance-report',0,'2025-11-20 08:41:07','2025-11-20 08:41:07'),(56,'Settings','settings','sanctum','settings',0,'2025-11-20 08:41:07','2025-11-20 08:41:07');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'auth_token','30c5a78f69ac456d4bcbe1adeaa58f36ba6547822ea6a09be5b13cb84c969616','[\"*\"]','2025-11-20 08:53:27',NULL,'2025-11-20 08:44:14','2025-11-20 08:53:27');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesapals`
--

DROP TABLE IF EXISTS `pesapals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesapals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` bigint unsigned DEFAULT NULL,
  `billing_address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text COLLATE utf8mb4_unicode_ci,
  `tracking_id` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesapals`
--

LOCK TABLES `pesapals` WRITE;
/*!40000 ALTER TABLE `pesapals` DISABLE KEYS */;
/*!40000 ALTER TABLE `pesapals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_notifications`
--

DROP TABLE IF EXISTS `push_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_notifications`
--

LOCK TABLES `push_notifications` WRITE;
/*!40000 ALTER TABLE `push_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,4),(1,5),(1,6),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(7,5),(8,1),(9,1),(10,1),(11,1),(12,1),(12,5),(12,6),(13,1),(13,5),(13,6),(14,1),(14,5),(15,1),(15,4),(15,5),(16,1),(16,4),(16,5),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(27,5),(28,1),(28,5),(29,1),(29,5),(30,1),(30,5),(31,1),(31,5),(32,1),(32,5),(33,1),(33,5),(34,1),(34,5),(35,1),(35,5),(36,1),(36,5),(37,1),(37,5),(38,1),(38,5),(39,1),(39,5),(40,1),(40,5),(41,1),(41,5),(42,1),(42,5),(43,1),(43,5),(44,1),(44,5),(45,1),(45,5),(46,1),(46,5),(47,1),(47,5),(48,1),(48,5),(49,1),(49,5),(50,1),(50,5),(51,1),(51,5),(52,1),(52,5),(53,1),(53,5),(54,1),(55,1),(56,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(2,'Customer','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(3,'Waiter','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(4,'Chef','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(5,'Branch Manager','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(6,'POS Operator','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07'),(7,'Stuff','sanctum','2025-11-20 08:41:07','2025-11-20 08:41:07');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `settingable_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settingable_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_settingable_type_settingable_id_index` (`settingable_type`,`settingable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'company','company_name','{\"$cast\": null, \"$value\": \"3 Guys\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(2,'company','company_email','{\"$cast\": null, \"$value\": \"info@restaurant.net\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(3,'company','company_phone','{\"$cast\": null, \"$value\": \"+9231234567\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(4,'company','company_website','{\"$cast\": null, \"$value\": \"https://Restaurant.xyz\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(5,'company','company_city','{\"$cast\": null, \"$value\": \"Lahore\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(6,'company','company_state','{\"$cast\": null, \"$value\": \"Lahore\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(7,'company','company_country_code','{\"$cast\": null, \"$value\": \"BGD\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(8,'company','company_zip_code','{\"$cast\": null, \"$value\": \"1216\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(9,'company','company_address','{\"$cast\": null, \"$value\": \"House : 25, Rd: 122, Block A, DHA, Lahore\"}',NULL,NULL,'2025-11-20 08:45:18','2025-11-20 08:45:18'),(10,'site','site_date_format','{\"$cast\": null, \"$value\": \"d-m-Y\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(11,'site','site_time_format','{\"$cast\": null, \"$value\": \"h:i A\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(12,'site','site_default_timezone','{\"$cast\": null, \"$value\": \"Asia/Dhaka\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(13,'site','site_default_branch','{\"$cast\": null, \"$value\": 1}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(14,'site','site_default_currency','{\"$cast\": null, \"$value\": 1}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(15,'site','site_default_currency_symbol','{\"$cast\": null, \"$value\": \"$\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(16,'site','site_currency_position','{\"$cast\": null, \"$value\": 5}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(17,'site','site_digit_after_decimal_point','{\"$cast\": null, \"$value\": \"2\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(18,'site','site_email_verification','{\"$cast\": null, \"$value\": 5}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(19,'site','site_phone_verification','{\"$cast\": null, \"$value\": 10}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(20,'site','site_default_language','{\"$cast\": null, \"$value\": 1}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(21,'site','site_google_map_key','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(22,'site','site_copyright','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(23,'site','site_language_switch','{\"$cast\": null, \"$value\": 5}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(24,'site','site_app_debug','{\"$cast\": null, \"$value\": 10}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(25,'site','site_auto_update','{\"$cast\": null, \"$value\": 10}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(26,'site','site_online_payment_gateway','{\"$cast\": null, \"$value\": 10}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(27,'site','site_default_sms_gateway','{\"$cast\": null, \"$value\": 0}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(28,'site','site_food_preparation_time','{\"$cast\": null, \"$value\": \"30\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(29,'notification','notification_fcm_public_vapid_key','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(30,'notification','notification_fcm_api_key','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(31,'notification','notification_fcm_auth_domain','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(32,'notification','notification_fcm_project_id','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(33,'notification','notification_fcm_storage_bucket','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(34,'notification','notification_fcm_messaging_sender_id','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(35,'notification','notification_fcm_app_id','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(36,'notification','notification_fcm_measurement_id','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(37,'notification','notification_fcm_json_file','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:08','2025-11-20 08:41:08'),(38,'mail','mail_mailer','{\"$cast\": null, \"$value\": \"smtp\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(39,'mail','mail_host','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(40,'mail','mail_port','{\"$cast\": null, \"$value\": \"0\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(41,'mail','mail_username','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(42,'mail','mail_password','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(43,'mail','mail_encryption','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(44,'mail','mail_from_name','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(45,'mail','mail_from_email','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13'),(46,'order_setup','order_setup_food_preparation_time','{\"$cast\": null, \"$value\": \"30\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(47,'order_setup','order_setup_schedule_order_slot_duration','{\"$cast\": null, \"$value\": \"30\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(48,'order_setup','order_setup_takeaway','{\"$cast\": null, \"$value\": 5}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(49,'order_setup','order_setup_delivery','{\"$cast\": null, \"$value\": 5}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(50,'order_setup','order_setup_free_delivery_kilometer','{\"$cast\": null, \"$value\": \"2\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(51,'order_setup','order_setup_basic_delivery_charge','{\"$cast\": null, \"$value\": \"1\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(52,'order_setup','order_setup_charge_per_kilo','{\"$cast\": null, \"$value\": \"1\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(53,'otp','otp_type','{\"$cast\": null, \"$value\": \"5\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(54,'otp','otp_digit_limit','{\"$cast\": null, \"$value\": \"4\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(55,'otp','otp_expire_time','{\"$cast\": null, \"$value\": \"10\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(56,'theme','theme_logo','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(57,'theme','theme_favicon_logo','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(58,'theme','theme_footer_logo','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(59,'license','license_key','{\"$cast\": null, \"$value\": \"1111111111111111111\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(60,'social_media','social_media_facebook','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(61,'social_media','social_media_youtube','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(62,'social_media','social_media_instagram','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14'),(63,'social_media','social_media_twitter','{\"$cast\": null, \"$value\": \"\"}',NULL,NULL,'2025-11-20 08:41:14','2025-11-20 08:41:14');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_gateways`
--

DROP TABLE IF EXISTS `sms_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `misc` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_gateways`
--

LOCK TABLES `sms_gateways` WRITE;
/*!40000 ALTER TABLE `sms_gateways` DISABLE KEYS */;
INSERT INTO `sms_gateways` VALUES (1,'Twilio','twilio','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(2,'Clickatell','clickatell','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(3,'Nexmo','nexmo','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(4,'Msg91','msg91','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(5,'2Factor','twofactor','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(6,'Bulksms','bulksms','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(7,'Bulksmsbd','bulksmsbd','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12'),(8,'Telesign','telesign','null',5,NULL,NULL,NULL,NULL,'2025-11-20 08:41:12','2025-11-20 08:41:12');
/*!40000 ALTER TABLE `sms_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_rate` decimal(19,6) NOT NULL,
  `type` tinyint NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `opening_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `closing_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day` tinyint NOT NULL,
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_slots`
--

LOCK TABLES `time_slots` WRITE;
/*!40000 ALTER TABLE `time_slots` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sign` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+',
  `order_id` bigint unsigned NOT NULL,
  `transaction_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(19,6) NOT NULL DEFAULT '0.000000',
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT '0',
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_guest` tinyint unsigned NOT NULL DEFAULT '10',
  `status` tinyint unsigned NOT NULL DEFAULT '5' COMMENT '5=Active, 10=Inactive',
  `balance` decimal(19,6) NOT NULL DEFAULT '0.000000',
  `creator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint DEFAULT NULL,
  `editor_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editor_id` bigint DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@gmail.com','1254875855','admin','2025-11-20 08:41:13','$2y$10$6uRRv3Q4EmhH0EMIRxtq5uizohClrxCAPz7yq9slqY.cGLjSNsMjW',NULL,NULL,0,'+880',10,5,0.000000,NULL,NULL,NULL,NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:46:39',NULL),(2,'Walking Customer','walkingcustomer@example.com','1254444555','default-customer','2025-11-20 08:41:13','$2y$10$n7aLIpww5X5KJFnUGfK5Xe9Q888lEjaNmHst9bnFyq/q3PFRj/cWW',NULL,NULL,0,'+880',10,5,0.000000,NULL,NULL,NULL,NULL,NULL,'2025-11-20 08:41:13','2025-11-20 08:41:13',NULL);
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

-- Dump completed on 2025-11-20 12:59:01
