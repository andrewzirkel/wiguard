-- MySQL dump 10.13  Distrib 5.1.34, for apple-darwin9.5.0 (i386)
--
-- Host: wiguard    Database: wiguard
-- ------------------------------------------------------
-- Server version	5.1.37-1ubuntu5.5-log

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
-- Current Database: `wiguard`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `wiguard` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `wiguard`;

--
-- Table structure for table `DSConfig`
--

DROP TABLE IF EXISTS `DSConfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DSConfig` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DSServerURL` text,
  `DSAdminUser` text,
  `DSAdminPassword` text,
  `DSIntegrate` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DSConfig`
--

LOCK TABLES `DSConfig` WRITE;
/*!40000 ALTER TABLE `DSConfig` DISABLE KEYS */;
INSERT INTO `DSConfig` VALUES (1,'http://lionserver:60080/','dsadmin','#anubis666',1);
/*!40000 ALTER TABLE `DSConfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DSGroups`
--

DROP TABLE IF EXISTS `DSGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DSGroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DSGroup` varchar(45) NOT NULL,
  `DSWorkflow` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=451 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DSGroups`
--

LOCK TABLES `DSGroups` WRITE;
/*!40000 ALTER TABLE `DSGroups` DISABLE KEYS */;
INSERT INTO `DSGroups` VALUES (447,'09','null'),(448,'10','040622200030'),(449,'11','040622200020'),(450,'08','null');
/*!40000 ALTER TABLE `DSGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DSWorkflows`
--

DROP TABLE IF EXISTS `DSWorkflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DSWorkflows` (
  `ID` varchar(45) NOT NULL,
  `description` longtext,
  `title` longtext,
  `group` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DSWorkflows`
--

LOCK TABLES `DSWorkflows` WRITE;
/*!40000 ALTER TABLE `DSWorkflows` DISABLE KEYS */;
INSERT INTO `DSWorkflows` VALUES ('040622200000','This simple workflow enables you to select a volume (HFS, NFTS or EXT format) in order to create a disk image. The disk image will be stored automatically on the DeployStudio repository. ','Create a master from a volume',NULL),('040622200010','This simple workflow enables you to restore a disk image (HFS, NFTS or EXT format) located on the DeployStudio repository to a local disk or volume.','Restore a master on a volume',NULL),('040622200020','This simple workflow enables you to install a package located on the DeployStudio repository to a local volume.','Install a package (pkg)',NULL),('040622200030','This workflow shows the 4 tasks required to restore a triple-boot machine.','Triple-OS restoration',NULL);
/*!40000 ALTER TABLE `DSWorkflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `user` varchar(20) NOT NULL,
  `password` longtext,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Authorized rdius administrators';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES ('addmac','*4DE8816DFC8D7966E8E5CE1511213BD3CC6C739F',2),('balvini','*473BF04EEF1665C2054A1D6CE2B51CC575A50C31',2),('bmalloy','*4DDDE63698F487F19F76FF3E4CBB86C7F7FDC6EE',2),('jway','*F3B3BBE75E8B290C68664D4AE528BCFF4019A66F',1),('merb','*4C931587A9A7779F6B9F09FA1854DDE5044E9882',2),('pdieciedue','*01AD1BC53300E79BDFFE16F0BE0A28AAE2900F50',2),('ralbright','*9852FACD51F32D484E72F056F40B1B72B9250B10',2),('smartin','*9F4E5385EF453DB0188815D4DF7B31636AB09113',1),('testuser','*D37C49F9CBEFBF8B6F4B165AC703AA271E079004',1),('zirkelad','*5858DA0F670399EFE4F8CA99DD5DF75D7543E087',3);
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `computername`
--

DROP TABLE IF EXISTS `computername`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `computername` (
  `MACAddress` varchar(12) NOT NULL,
  `ComputerName` text NOT NULL,
  PRIMARY KEY (`MACAddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store Computer Names';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `computername`
--

LOCK TABLES `computername` WRITE;
/*!40000 ALTER TABLE `computername` DISABLE KEYS */;
/*!40000 ALTER TABLE `computername` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `computers`
--

DROP TABLE IF EXISTS `computers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `computers` (
  `ETHMAC` varchar(12) DEFAULT NULL,
  `WiMAC` varchar(12) DEFAULT NULL,
  `sn` varchar(45) DEFAULT NULL,
  `ComputerName` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `computers`
--

LOCK TABLES `computers` WRITE;
/*!40000 ALTER TABLE `computers` DISABLE KEYS */;
/*!40000 ALTER TABLE `computers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-10-03  0:03:01
