-- MySQL dump 10.13  Distrib 5.1.53, for slackware-linux-gnu (i486)
--
-- Host: localhost    Database: simplestore
-- ------------------------------------------------------
-- Server version	5.1.53-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `description` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `date_created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_modified` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Food','Ready to eat foods, food ingredients not included',1293453680,31,NULL,NULL),(2,'Beverages','Includes softdrinks, energy drink',1293454334,31,NULL,NULL),(3,'Alcoholic','Beers and gin',1293454393,31,1293456419,31),(4,'Cooking ingredient','Cooking ingredients',1293454452,31,1293591360,31),(5,'Household','Household items such as soaps, etc',1293454491,31,NULL,NULL),(6,'Load','Cellphone load',1293454541,31,NULL,NULL),(7,'Diaper','',1293454552,31,NULL,NULL),(8,'Cigarette','',1293454606,31,NULL,NULL),(9,'Others','',1293454637,31,NULL,NULL);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `code_name` varchar(16) CHARACTER SET latin1 NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `description` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `date_created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_modified` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_name` (`code_name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
INSERT INTO `item` VALUES (1,3,'REDHORSE-1L','Red Horse - 1L','',1293586031,31,NULL,NULL),(2,3,'REDHORSE-500ML','Red Horse 500mL','',1293586589,31,1293587818,31),(3,3,'SANMIG-GRANDE','San Miguel Grande','Pilsen',1293587639,31,1293587839,31),(4,1,'STICK-O','Stick-O','',1293587857,31,NULL,NULL),(5,1,'HAPPYBLUE','Happy Blue','',1293587874,31,NULL,NULL),(6,1,'HAPPYGREEN-ADOBO','Happy Green - Chicken Adobo','',1293587888,31,1293590608,31),(7,1,'CHAMPY','Champy','',1293587902,31,NULL,NULL),(10,2,'RC-800mL','RC 800mL','RC ng bayan',1293589203,31,NULL,NULL),(11,2,'RC-240mL','RC 240mL','RC ng bayan',1293589287,31,1293589325,31),(12,1,'MAXX-GREEN','Maxx Green','',1293590123,31,1293590159,31),(13,1,'MAXXYELLOW','Maxx Yellow','',1293590168,31,NULL,NULL),(14,1,'MAXXWHITE','Maxx White','',1293590174,31,NULL,NULL),(15,1,'MENTOSVIOLET','Mentos Violet','',1293590206,31,NULL,NULL),(16,1,'CHOCOLATESTICKS','Chocolate Sticks','',1293590245,31,NULL,NULL),(17,1,'LOLLIPOP','Lollipop','Choco pop',1293590322,31,NULL,NULL),(18,1,'HALU-HALO','Halu-halo','',1293590355,31,NULL,NULL),(19,1,'DING-DONG','Ding-dong','',1293590361,31,NULL,NULL),(20,1,'CORNBITS-FLAKES','Corn Bits - Flakes','',1293590419,31,NULL,NULL),(21,1,'BOSSBAWANG','Boss Bawang','',1293590451,31,NULL,NULL),(22,1,'MUNCHERGREENPEAS','Muncher Green Peas','',1293590464,31,NULL,NULL),(23,1,'CHEEPEECORNCHIPS','Cheepee Corn Chips','',1293590488,31,NULL,NULL),(24,1,'MUNCHERCOATEDGRE','Muncher Coated Green Peas','Red color',1293590521,31,NULL,NULL),(25,1,'YASUIGREENPEAS','Yasui Green Peas','',1293590545,31,NULL,NULL),(26,1,'HAPPYRED-BBQ','Happy Red - BBQ','',1293590566,31,NULL,NULL),(27,9,'GRIPSHAIRWAX','Grips Hair Wax','',1293590656,31,NULL,NULL),(28,9,'YOUNGSSTYLINGGEL','Young\'s Styling Gel','',1293591009,31,1293591022,31),(29,1,'FUDGEEBARR-CHOCO','Fudgee Barr Choco Blast','',1293591049,31,1293591062,31),(30,1,'CHEESECAKE','Cheese Cake','Lemon\'s Square',1293591093,31,NULL,NULL),(31,1,'CHIKITOHOTSPICY','Chikito Hot & Spicy','',1293591131,31,NULL,NULL),(32,1,'CHIKITOGARLIC','Chikito Garlic','Garlic flavor',1293591164,31,NULL,NULL),(33,1,'KARAOKEGARLICCHI','Karaoke Garlic & Chili','',1293591195,31,NULL,NULL),(34,4,'CHCKNBREAD-GARLC','Chicken Breading - Garlic','',1293591238,31,1293591291,31),(35,4,'CHCKNBREAD-CHSE','Chicken Breading - Cheese','',1293591318,31,1293591333,31),(36,4,'PAMINTADUROG','Paminta Durog','',1293591401,31,NULL,NULL),(37,4,'PAMINTABUO','Paminta Buo','',1293591410,31,NULL,NULL),(38,5,'CHLORINE','Chlorine','',1293591447,31,NULL,NULL),(39,5,'SALIC','Salic','',1293591461,31,NULL,NULL),(40,5,'ANYEL','Anyel','',1293591487,31,NULL,NULL),(41,1,'MRJIGGLESJELLYST','Mr. Jiggles Jelly Stick','',1293591585,31,NULL,NULL),(42,8,'FORTUNE-RED','Fortune - Red','',1293599442,31,NULL,NULL),(43,8,'FORTUNE-WHITE','Fortune - White','',1293599454,31,NULL,NULL),(44,8,'MARLBOROLIGHTS','Marlboro Lights','',1293599475,31,NULL,NULL),(45,8,'MARLBORO','Marlboro','',1293599498,31,NULL,NULL),(46,7,'BABYSOFT-MEDIUM','Baby Soft - Medium','',1293599542,31,NULL,NULL),(47,7,'BABYSOFT-LARGE','Baby Soft - Large','',1293599552,31,NULL,NULL),(48,6,'SMARTECONOMY30','Smart Economy 30','',1293599578,31,NULL,NULL),(49,6,'SMARTALLTXT20','Smart AllTXT 20','',1293599597,31,NULL,NULL);
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price`
--

DROP TABLE IF EXISTS `price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `effective_date` int(10) unsigned NOT NULL,
  `date_created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_modified` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `effective_date` (`effective_date`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price`
--

LOCK TABLES `price` WRITE;
/*!40000 ALTER TABLE `price` DISABLE KEYS */;
INSERT INTO `price` VALUES (1,11,'5.00',1263916800,1293622576,31,1293622595,31),(2,11,'1.00',1275321600,1293622619,31,1293622650,31),(3,11,'7.00',1285862400,1293622666,31,NULL,NULL),(4,10,'15.00',1293552000,1293622687,31,NULL,NULL),(5,12,'1.00',1293552000,1293622699,31,NULL,NULL),(6,14,'1.00',1293552000,1293622703,31,NULL,NULL),(7,13,'1.00',1293552000,1293622707,31,NULL,NULL);
/*!40000 ALTER TABLE `price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_modified` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_log`
--

DROP TABLE IF EXISTS `stock_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trans_type` tinyint(3) unsigned NOT NULL COMMENT '1=restock, 2=unstock, 3=adjust',
  `remarks` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `date_created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_modified` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_log`
--

LOCK TABLES `stock_log` WRITE;
/*!40000 ALTER TABLE `stock_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_log_detail`
--

DROP TABLE IF EXISTS `stock_log_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_log_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_log_detail`
--

LOCK TABLES `stock_log_detail` WRITE;
/*!40000 ALTER TABLE `stock_log_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_log_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `last_login_ip` varchar(32) DEFAULT NULL,
  `banned` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `date_joined` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (31,'dc.eros@gmail.com','admin','5be739ee9798a9856cf52a67d41daa0c247bb7efbf95aa9c1d13086a92fbef1a',7,1293596310,'127.0.0.1',0,1,1293436587);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `user_ip` varchar(32) NOT NULL,
  `token` varchar(32) CHARACTER SET ucs2 NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `activation` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Used for activation, not for autologin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-12-29 19:47:23
