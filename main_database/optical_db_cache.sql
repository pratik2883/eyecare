-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: optical_db
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('gem-opticians-cache-filter_options','a:8:{s:6:\"brands\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:58:{i:59;s:5:\"ALCON\";i:49;s:15:\"ARMANI EXCHANGE\";i:63;s:4:\"BAND\";i:57;s:14:\"BAUSH AND LOMB\";i:21;s:8:\"BURBERRY\";i:32;s:12:\"CALVIN KLEIN\";i:41;s:7:\"CARRERA\";i:16;s:7:\"CARTIER\";i:61;s:4:\"CASE\";i:60;s:4:\"CEPL\";i:65;s:6:\"CLIPON\";i:46;s:15:\"DOLCE & GABBANA\";i:20;s:13:\"DOLCE&GABBANA\";i:25;s:4:\"ELLE\";i:40;s:14:\"EMPORIO ARMANI\";i:26;s:6:\"ESPRIT\";i:51;s:7:\"FERRARI\";i:24;s:4:\"FILA\";i:28;s:17:\"FRENCH CONNECTION\";i:29;s:5:\"GUCCI\";i:23;s:5:\"GUESS\";i:45;s:6:\"JAGUAR\";i:58;s:19:\"JOHNSON AND JOHNSON\";i:38;s:7:\"LACOSTE\";i:47;s:11:\"MARC JACOBS\";i:42;s:8:\"MAUI JIM\";i:27;s:12:\"MICHAEL KORS\";i:15;s:9:\"MONTBLANC\";i:31;s:4:\"NIKE\";i:39;s:6:\"OAKLEY\";i:50;s:16:\"OAKLEY META HSTN\";i:18;s:6:\"PERSOL\";i:48;s:8:\"POLAROID\";i:22;s:6:\"POLICE\";i:35;s:5:\"PRADA\";i:30;s:4:\"PUMA\";i:44;s:6:\"RAYBAN\";i:56;s:14:\"RAYBAN FERRARI\";i:55;s:25:\"RAYBAN META GEN-2 BLAYZER\";i:53;s:27:\"RAYBAN META GEN-2 HEADLINER\";i:54;s:25:\"RAYBAN META GEN-2 SCRIBER\";i:52;s:25:\"RAYBAN META GEN-2 WAYFARE\";i:70;s:14:\"SAFETY GLASSES\";i:17;s:9:\"SALVATORE\";i:14;s:9:\"SILHOUTTE\";i:68;s:14:\"SPECTACLE CASE\";i:62;s:5:\"SPRAY\";i:37;s:7:\"STEPPER\";i:66;s:8:\"STERICON\";i:64;s:9:\"SUPPORTER\";i:19;s:9:\"SWAROVSKI\";i:69;s:16:\"SWIMMING GLASSES\";i:13;s:7:\"TOMFORD\";i:33;s:14:\"TOMMY HILFIGER\";i:36;s:25:\"UNITED COLORS OF BENETTON\";i:34;s:7:\"VERSACE\";i:43;s:5:\"VOGUE\";i:67;s:5:\"ZEISS\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:7:\"genders\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;s:6:\"FEMALE\";i:1;s:4:\"MALE\";i:2;s:6:\"UNISEX\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:12:\"frame_shapes\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:8:{i:0;s:7:\"AVIATOR\";i:1;s:7:\"CAT EYE\";i:2;s:4:\"HEXA\";i:3;s:4:\"OVAL\";i:4;s:9:\"RECTANGLE\";i:5;s:5:\"ROUND\";i:6;s:6:\"SQUARE\";i:7;s:4:\"WRAP\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:15:\"frame_materials\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;s:10:\"HILAFILCON\";i:1;s:5:\"METAL\";i:2;s:11:\"NELFILCON A\";i:3;s:7:\"PLASTIC\";i:4;s:7:\"RIMLESS\";i:5;s:6:\"RUBBER\";i:6;s:10:\"SENOFILCON\";i:7;s:16:\"SILICON HYDROGEL\";i:8;s:5:\"SUPRA\";i:9;s:5:\"THRED\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:12:\"frame_colors\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:20:{i:0;s:5:\"BEIGE\";i:1;s:5:\"BLACK\";i:2;s:4:\"BLUE\";i:3;s:5:\"BROWN\";i:4;s:6:\"COPPER\";i:5;s:4:\"GOLD\";i:6;s:5:\"GREEN\";i:7;s:4:\"GREY\";i:8;s:3:\"GUN\";i:9;s:8:\"MULTIPLE\";i:10;s:6:\"ORANGE\";i:11;s:4:\"PICH\";i:12;s:4:\"PINK\";i:13;s:6:\"PURPLE\";i:14;s:3:\"RED\";i:15;s:6:\"SILVER\";i:16;s:8:\"TORTOISE\";i:17;s:11:\"TRANSPARENT\";i:18;s:5:\"WHITE\";i:19;s:6:\"YELLOW\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"frame_sizes\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:44:{i:0;s:3:\"120\";i:1;s:6:\"120 ML\";i:2;s:3:\"130\";i:3;s:3:\"135\";i:4;s:3:\"139\";i:5;s:3:\"140\";i:6;s:6:\"300 ML\";i:7;s:6:\"355 ML\";i:8;s:2:\"36\";i:9;s:2:\"39\";i:10;s:2:\"40\";i:11;s:2:\"43\";i:12;s:2:\"44\";i:13;s:2:\"45\";i:14;s:2:\"46\";i:15;s:2:\"47\";i:16;s:2:\"48\";i:17;s:2:\"49\";i:18;s:2:\"50\";i:19;s:6:\"500 ML\";i:20;s:2:\"51\";i:21;s:2:\"52\";i:22;s:2:\"53\";i:23;s:2:\"54\";i:24;s:2:\"55\";i:25;s:2:\"56\";i:26;s:2:\"57\";i:27;s:2:\"58\";i:28;s:2:\"59\";i:29;s:2:\"60\";i:30;s:5:\"60 ML\";i:31;s:2:\"61\";i:32;s:2:\"62\";i:33;s:2:\"63\";i:34;s:2:\"64\";i:35;s:2:\"65\";i:36;s:2:\"67\";i:37;s:2:\"72\";i:38;s:4:\"8 ML\";i:39;s:2:\"87\";i:40;s:5:\"90 ML\";i:41;s:10:\"BC 8.4,8.8\";i:42;s:10:\"BC 8.5,9.0\";i:43;s:6:\"BC 8.6\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"categories\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:5:{i:0;s:11:\"accessories\";i:1;s:14:\"contact_lenses\";i:2;s:10:\"eyeglasses\";i:3;s:4:\"kids\";i:4;s:10:\"sunglasses\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"price_range\";a:2:{s:3:\"min\";d:50;s:3:\"max\";d:84450;}}',1786874177),('gem-opticians-cache-store_settings','a:6:{s:10:\"store_name\";s:14:\"EyeCare Studio\";s:13:\"store_tagline\";s:9:\"Est. 1969\";s:8:\"app_name\";s:14:\"EyeCare Studio\";s:24:\"section_categories_title\";s:10:\"Categories\";s:20:\"section_offers_title\";s:19:\"Offers & Highlights\";s:24:\"section_collection_title\";s:14:\"Our Collection\";}',1786874142);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-16 14:39:55
