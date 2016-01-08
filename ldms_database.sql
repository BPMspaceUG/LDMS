CREATE DATABASE  IF NOT EXISTS `ldms` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;
USE `ldms`;

--
--  Database: ldms
-- 

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
-- Table structure for table `document_labels`
--

DROP TABLE IF EXISTS `document_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_labels` (
  `document_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`document_id`,`label`),
  KEY `label_idx` (`label`),
  CONSTRAINT `document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `label` FOREIGN KEY (`label`) REFERENCES `labels` (`label`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `mailtype` varchar(2) NOT NULL,
  `direction` enum('IN','OUT') NOT NULL,
  `externalID` varchar(255) NOT NULL,
  `internalID` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `comment` text,
  PRIMARY KEY (`document_id`),
  KEY `mailtype_idx` (`mailtype`),
  KEY `externalID_idx` (`externalID`),
  KEY `internalID_idx` (`internalID`),
  CONSTRAINT `externalID` FOREIGN KEY (`externalID`) REFERENCES `external_parties` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `internalID` FOREIGN KEY (`internalID`) REFERENCES `internal_parties` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mailtype` FOREIGN KEY (`mailtype`) REFERENCES `mailtypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20512 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `external_parties`
--

DROP TABLE IF EXISTS `external_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_parties` (
  `id` char(40) NOT NULL,
  `external_party` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `internal_parties`
--

DROP TABLE IF EXISTS `internal_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_parties` (
  `id` varchar(255) NOT NULL,
  `internal_party` varchar(255) DEFAULT NULL,
  `asana_workspace_id` varchar(45) DEFAULT NULL,
  `asana_project_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels` (
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailtypes`
--

DROP TABLE IF EXISTS `mailtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailtypes` (
  `id` varchar(2) NOT NULL,
  `mailtype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notify_users`
--

DROP TABLE IF EXISTS `notify_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notify_users` (
  `mail` varchar(250) COLLATE utf8_bin NOT NULL,
  `name` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table content for table `mailtypes`
--

LOCK TABLES `mailtypes` WRITE;
/*!40000 ALTER TABLE `mailtypes` DISABLE KEYS */;
INSERT INTO `mailtypes` VALUES ('AN','Angebot'),('AT','Antragsformular'),('BE','Bestellung'),('BW','Z Bewirtung normal'),('EC','EC-Karten Belege'),('GU','Gutschrift'),('IA','In-Course Assesment'),('KA','Kassenbelege und Kassenbuch'),('KK','Kreditkartenbeleg'),('KO','Kontoauszug'),('KR','Rechnung (klein)'),('LS','Lieferschein'),('MG','Mahnung'),('RE','Rechnung'),('RK','Reisekostenabrechnung'),('SB','Z Bewirtung bei Schulung'),('SV','Schriftverkehr und Sonstiges'),('TE','Testergebnisse'),('TF','Trainerfeedback'),('TL','Teilnehmerliste'),('TR','Teilnehmerfeedback'),('VE','Vertrag'),('ZS','Zeitschriften/Kataloge');
/*!40000 ALTER TABLE `mailtypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
