-- MySQL dump 10.13  Distrib 5.6.24, for osx10.8 (x86_64)
--
-- Host: 66.147.242.153    Database: ctsluthe_ttsurvey
-- ------------------------------------------------------
-- Server version	5.6.32-78.1-log

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
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants` (
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_ids` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participants`
--

LOCK TABLES `participants` WRITE;
/*!40000 ALTER TABLE `participants` DISABLE KEYS */;
INSERT INTO `participants` VALUES ('123-456-789-cat','Test Case','nobody@vmwishes.com');
/*!40000 ALTER TABLE `participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participation_history`
--

DROP TABLE IF EXISTS `participation_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participation_history` (
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `year` smallint(6) NOT NULL,
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`,`year`),
  CONSTRAINT `participation_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `participants` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participation_history`
--

LOCK TABLES `participation_history` WRITE;
/*!40000 ALTER TABLE `participation_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `participation_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `response_free_text`
--

DROP TABLE IF EXISTS `response_free_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_free_text` (
  `item_id` int(11) NOT NULL,
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `year` smallint(6) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`,`user_id`,`year`),
  KEY `item_id` (`item_id`,`year`),
  KEY `response_free_text_ibfk_1` (`user_id`,`year`),
  CONSTRAINT `response_free_text_ibfk_1` FOREIGN KEY (`user_id`, `year`) REFERENCES `participation_history` (`user_id`, `year`) ON DELETE CASCADE,
  CONSTRAINT `response_free_text_ibfk_2` FOREIGN KEY (`item_id`, `year`) REFERENCES `survey_items` (`item_id`, `year`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `response_free_text`
--

LOCK TABLES `response_free_text` WRITE;
/*!40000 ALTER TABLE `response_free_text` DISABLE KEYS */;
/*!40000 ALTER TABLE `response_free_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `response_participation`
--

DROP TABLE IF EXISTS `response_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_participation` (
  `item_id` int(11) NOT NULL,
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `year` smallint(6) NOT NULL,
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `qualifier` text COLLATE utf8_unicode_ci,
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `item_id` (`item_id`,`user_id`,`year`),
  KEY `user_id` (`user_id`,`year`),
  KEY `item_id_2` (`item_id`,`year`),
  CONSTRAINT `response_participation_ibfk_1` FOREIGN KEY (`user_id`, `year`) REFERENCES `participation_history` (`user_id`, `year`) ON DELETE CASCADE,
  CONSTRAINT `response_participation_ibfk_2` FOREIGN KEY (`item_id`, `year`) REFERENCES `survey_items` (`item_id`, `year`) ON DELETE CASCADE,
  CONSTRAINT `response_participation_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `survey_participation_options` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `response_participation`
--

LOCK TABLES `response_participation` WRITE;
/*!40000 ALTER TABLE `response_participation` DISABLE KEYS */;
/*!40000 ALTER TABLE `response_participation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `response_participation_options`
--

DROP TABLE IF EXISTS `response_participation_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_participation_options` (
  `item_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `year` smallint(6) NOT NULL,
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`,`option_id`,`user_id`,`year`),
  KEY `item_id` (`item_id`,`user_id`,`year`),
  CONSTRAINT `response_participation_options_ibfk_1` FOREIGN KEY (`item_id`, `user_id`, `year`) REFERENCES `response_participation` (`item_id`, `user_id`, `year`) ON DELETE CASCADE,
  CONSTRAINT `response_participation_options_ibfk_2` FOREIGN KEY (`item_id`, `option_id`) REFERENCES `survey_participation_options` (`item_id`, `option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `response_participation_options`
--

LOCK TABLES `response_participation_options` WRITE;
/*!40000 ALTER TABLE `response_participation_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `response_participation_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `response_value`
--

DROP TABLE IF EXISTS `response_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_value` (
  `item_id` int(11) NOT NULL,
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `year` smallint(6) NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `qualifier` text COLLATE utf8_unicode_ci,
  `submitted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `item_id` (`item_id`,`user_id`,`year`),
  KEY `item_id_2` (`item_id`,`year`),
  KEY `response_value_ibfk_2` (`user_id`,`year`),
  CONSTRAINT `response_value_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `survey_value_items` (`item_id`) ON DELETE CASCADE,
  CONSTRAINT `response_value_ibfk_2` FOREIGN KEY (`user_id`, `year`) REFERENCES `participation_history` (`user_id`, `year`) ON DELETE CASCADE,
  CONSTRAINT `response_value_ibfk_3` FOREIGN KEY (`item_id`, `year`) REFERENCES `survey_items` (`item_id`, `year`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `response_value`
--

LOCK TABLES `response_value` WRITE;
/*!40000 ALTER TABLE `response_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `response_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_groups`
--

DROP TABLE IF EXISTS `survey_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_groups` (
  `year` smallint(6) NOT NULL DEFAULT '2017',
  `group_index` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` int(1) NOT NULL DEFAULT '1',
  `comment_qualifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `collapsible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`year`,`group_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_groups`
--

LOCK TABLES `survey_groups` WRITE;
/*!40000 ALTER TABLE `survey_groups` DISABLE KEYS */;
INSERT INTO `survey_groups` VALUES (2017,1,NULL,0,NULL,0),(2017,2,'Participation in the Life of CTS',1,NULL,1),(2017,3,'Participation in the Leadership of CTS\r',1,NULL,1),(2017,4,'Worship',1,NULL,1),(2017,5,'Parish Care',1,NULL,1),(2017,6,'Learning',1,NULL,1),(2017,7,'Service',1,'if you have any ideas for new areas of service, please share them here',1),(2017,8,'Outreach',1,NULL,1),(2017,9,'Parish Life',1,'if you have any ideas for new fellowship activities, please share them here',1),(2017,10,'Youth Ministry',1,NULL,1),(2017,11,'Property',1,NULL,1),(2017,12,'Business Administration',1,NULL,1),(2017,13,'Where should CTS focus our energy?',0,NULL,0),(2017,14,'Learning',0,NULL,1),(2017,15,'Service',0,NULL,1),(2017,16,'Outreach',0,NULL,1),(2017,17,'Parish Life',0,NULL,1),(2017,18,'Property',0,NULL,1),(2017,19,'General',0,NULL,1);
/*!40000 ALTER TABLE `survey_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_items`
--

DROP TABLE IF EXISTS `survey_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `year` smallint(6) NOT NULL DEFAULT '2017',
  `group_index` int(11) NOT NULL,
  `order_index` int(11) NOT NULL,
  `item_type` enum('label','participation','free_text','value') COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(1023) COLLATE utf8_unicode_ci DEFAULT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`,`year`),
  UNIQUE KEY `year` (`year`,`group_index`,`order_index`),
  CONSTRAINT `survey_items_ibfk_1` FOREIGN KEY (`year`, `group_index`) REFERENCES `survey_groups` (`year`, `group_index`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_items`
--

LOCK TABLES `survey_items` WRITE;
/*!40000 ALTER TABLE `survey_items` DISABLE KEYS */;
INSERT INTO `survey_items` VALUES (1,2017,1,1,'label',NULL,NULL,0),(2,2017,1,2,'label',NULL,NULL,0),(3,2017,1,3,'label',NULL,NULL,0),(4,2017,1,4,'label',NULL,NULL,0),(5,2017,1,5,'label',NULL,NULL,0),(6,2017,1,6,'label',NULL,NULL,0),(7,2017,1,7,'label',NULL,NULL,0),(8,2017,2,1,'label','I will…',NULL,0),(9,2017,2,2,'participation','participate in worship services',NULL,0),(10,2017,2,3,'participation','participate in education opportunities',NULL,0),(17,2017,2,4,'participation','participate in “hands-on” service projects',NULL,0),(18,2017,2,5,'participation','participate in collection drives',NULL,0),(19,2017,2,6,'participation','participate in fellowship opportunities',NULL,0),(20,2017,2,7,'participation','keep CTS in my personal prayers',NULL,0),(21,2017,3,1,'label','I am willing to serve…',NULL,0),(22,2017,3,2,'participation','as a member of council',NULL,0),(23,2017,3,3,'participation','on the mutual ministry committee',NULL,0),(24,2017,3,4,'participation','as a voting member at synod assembly',NULL,0),(25,2017,4,1,'label',NULL,NULL,0),(26,2017,4,2,'label','I am willing to assist with weekly worship…',NULL,0),(27,2017,4,3,'participation','as an acolyte',NULL,0),(28,2017,4,4,'participation','with altar preparation',NULL,0),(29,2017,4,5,'participation','by providing communion bread',NULL,0),(30,2017,4,6,'participation','as prayer leader',NULL,0),(31,2017,4,7,'participation','as a reader',NULL,0),(32,2017,4,8,'participation','as an usher',NULL,0),(33,2017,4,9,'participation','as a welcomer',NULL,0),(34,2017,4,10,'participation','as a musician',NULL,0),(36,2017,4,12,'participation','by recording sermons',NULL,0),(37,2017,4,13,'label','I am willing to assist with daily worship by…',NULL,0),(38,2017,4,14,'participation','leading daily prayer',NULL,0),(39,2017,4,15,'label','I am willing to support worship by…',NULL,0),(40,2017,4,16,'participation','laundering linens',NULL,0),(41,2017,4,17,'participation','assisting with paraments',NULL,0),(42,2017,4,18,'participation','handling flower donations',NULL,0),(43,2017,4,19,'participation','compiling servants lists',NULL,0),(44,2017,4,20,'participation','preparing bulletins',NULL,0),(45,2017,4,21,'participation','serving on worship committee',NULL,0),(46,2017,5,1,'label',NULL,NULL,0),(47,2017,5,2,'label','I am willing to serve fellow CTS members in need by providing…',NULL,0),(48,2017,5,3,'participation','meals during illness/recovery',NULL,0),(49,2017,5,4,'participation','transportation',NULL,0),(50,2017,5,5,'participation','home “handyman” services',NULL,0),(51,2017,5,6,'label','I am willing to share in the life of fellow CTS members through…',NULL,0),(52,2017,5,7,'participation','card ministry',NULL,0),(53,2017,5,8,'participation','Koinonia',NULL,0),(54,2017,5,9,'participation','young adult activities',NULL,0),(55,2017,6,1,'label',NULL,NULL,0),(56,2017,6,2,'label','I am willing to support traditional Sunday School at CTS through…',NULL,0),(57,2017,6,3,'participation','Adult Forum',NULL,0),(58,2017,6,4,'participation','GGIFT (Intergenerational Learning)',NULL,0),(59,2017,6,5,'participation','lower elementary  Sunday School',NULL,0),(60,2017,6,6,'participation','upper elementary  Sunday School',NULL,0),(61,2017,6,7,'participation','facilitating Kairos',NULL,0),(62,2017,6,8,'label','I am willing to support expanded learning opportunities through… ',NULL,0),(63,2017,6,9,'participation','Bible Study',NULL,0),(64,2017,6,10,'participation','Film Buffs',NULL,0),(65,2017,6,11,'participation','Book Club',NULL,0),(66,2017,7,1,'label',NULL,NULL,0),(67,2017,7,2,'label','I wish to serve those in our community that are in need through…',NULL,0),(68,2017,7,3,'participation','Community Based Shelter',NULL,0),(69,2017,7,4,'participation','Gaithersburg HELP',NULL,0),(70,2017,7,5,'participation','McKenna’s Wagon',NULL,0),(71,2017,7,6,'participation','Thanksgiving baskets',NULL,0),(72,2017,7,7,'participation','farmer’s market',NULL,0),(73,2017,7,8,'participation','delivering flowers to Sunrise',NULL,0),(74,2017,7,9,'participation','delivering clothing to Interfaith',NULL,0),(75,2017,7,10,'label','I wish to serve the greater community and our world through…',NULL,0),(76,2017,7,11,'participation','Creation Care committee',NULL,0),(77,2017,7,12,'participation','Gifts of Hope',NULL,0),(78,2017,7,13,'participation','advocacy/social justice',NULL,0),(79,2017,7,14,'participation','fundraising walks',NULL,0),(80,2017,8,1,'label',NULL,NULL,0),(81,2017,8,2,'label','I wish to get the Word out to our neighbors through…',NULL,0),(82,2017,8,3,'participation','digital communications',NULL,0),(83,2017,8,4,'participation','media relations',NULL,0),(84,2017,8,5,'participation','exterior signage',NULL,0),(85,2017,8,6,'label','I wish to interact with our neighbors through…',NULL,0),(86,2017,8,7,'participation','follow-up contact with visitors',NULL,0),(87,2017,8,8,'participation','annual yard sale',NULL,0),(88,2017,8,9,'participation','farmer’s market',NULL,0),(89,2017,9,1,'label',NULL,NULL,0),(90,2017,9,2,'label','I will participate in the life of the congregation through	…\r',NULL,0),(91,2017,9,3,'participation','after worship hospitality',NULL,0),(92,2017,9,4,'participation','congregational dinners',NULL,0),(93,2017,9,5,'participation','Chili, Cornbread, & Chocolate cook-off',NULL,0),(94,2017,9,6,'participation','CTS fun night',NULL,0),(95,2017,9,7,'participation','Advent/Lent soup suppers',NULL,0),(96,2017,9,8,'participation','other fellowship activities',NULL,0),(97,2017,10,1,'label',NULL,NULL,0),(98,2017,10,2,'label','I am willing to journey with our youth as they participate in Synod events:',NULL,0),(99,2017,10,3,'participation','Chrysalis (high school) ',NULL,0),(100,2017,10,4,'participation','Shekinah (middle school)',NULL,0),(101,2017,10,5,'participation','Shema (elementary school)',NULL,0),(102,2017,10,6,'label',NULL,NULL,0),(103,2017,10,7,'label','I am willing to work behind the scenes through… ',NULL,0),(104,2017,10,8,'participation','Youth Ministry team',NULL,0),(105,2017,10,9,'participation','other means',NULL,0),(106,2017,10,10,'label','I am willing to walk along alongside our youth … ',NULL,0),(107,2017,10,11,'participation','in the vegetable garden',NULL,0),(108,2017,10,12,'participation','along other paths',NULL,0),(114,2017,11,1,'label',NULL,NULL,0),(115,2017,11,2,'label','I am willing to keep our physical building inviting by…',NULL,0),(116,2017,11,3,'participation','cleaning a portion of it',NULL,0),(117,2017,11,4,'participation','handling recycling activities',NULL,0),(118,2017,11,5,'participation','stocking maintenance supplies',NULL,0),(119,2017,11,6,'participation','landscaping and gardening',NULL,0),(120,2017,11,7,'label','I am willing to keep our physical building functional by…',NULL,0),(121,2017,11,8,'participation','participating in work days',NULL,0),(122,2017,11,9,'participation','mowing the lawn',NULL,0),(123,2017,11,10,'participation','shoveling snow',NULL,0),(124,2017,11,11,'participation','providing “handyman” services',NULL,0),(125,2017,12,1,'label',NULL,NULL,0),(126,2017,12,2,'label','I am willing to help with day-to-day operations as…',NULL,0),(127,2017,12,3,'participation','office support',NULL,0),(128,2017,12,4,'participation','facilities usage coordinator',NULL,0),(129,2017,12,5,'participation','librarian',NULL,0),(130,2017,12,6,'participation','photographer',NULL,0),(131,2017,12,7,'label','I am willing to help with communications through…',NULL,0),(132,2017,12,8,'participation','communiqués',NULL,0),(133,2017,12,9,'participation','Footnotes',NULL,0),(134,2017,12,10,'participation','the CTS calendar',NULL,0),(135,2017,12,11,'participation','ctslutheranelca.org',NULL,0),(136,2017,12,12,'label','I am willing to help keep records of who we are through the…',NULL,0),(137,2017,12,13,'participation','CTS directory',NULL,0),(138,2017,12,14,'participation','CTS history',NULL,0),(139,2017,12,15,'participation','parish register',NULL,0),(140,2017,12,16,'participation','archives committee',NULL,0),(141,2017,12,17,'participation','OneDrive',NULL,0),(142,2017,12,18,'label','I am willing help with financial business as…',NULL,0),(143,2017,12,19,'participation','weekly offering counter',NULL,0),(144,2017,12,20,'participation','annual auditor',NULL,0),(147,2017,13,1,'label',NULL,NULL,0),(148,2017,13,2,'label',NULL,NULL,0),(149,2017,13,3,'label',NULL,NULL,0),(165,2017,14,1,'label',NULL,NULL,0),(166,2017,14,2,'label','Are there some you feel have outlived their appeal?',NULL,0),(167,2017,14,3,'label','Are there some that you feel are crucial we keep? ',NULL,0),(168,2017,14,4,'label','Do you have suggestions for new educational programs that would appeal to a significant number of CTS members?',NULL,0),(169,2017,14,5,'free_text','Feedback:',NULL,1),(170,2017,15,1,'label',NULL,NULL,0),(171,2017,15,2,'label','Are there service projects that we should consider dropping in order to focus on other projects? ',NULL,0),(172,2017,15,3,'label','Are there service projects that are core to our identity that must be preserved?',NULL,0),(173,2017,15,4,'label','Do you have suggestions for new service projects that would be adequately supported?',NULL,0),(174,2017,15,5,'free_text','Feedback:',NULL,1),(175,2017,16,1,'label',NULL,NULL,0),(176,2017,16,2,'label','How important is it that we make our presence known in the community (Mont. Village & Gaithersburg)?',NULL,0),(177,2017,16,3,'label','How important is it that the community knows about our programs (across all ministry areas)?',NULL,0),(178,2017,16,4,'label','Do you have suggestions for ways to better interact with our community?',NULL,0),(179,2017,16,5,'free_text','Feedback:',NULL,1),(180,2017,17,1,'label',NULL,NULL,0),(181,2017,17,2,'label','How important are congregational dinners?   Soup suppers?',NULL,0),(182,2017,17,3,'label','Would you rather stick with the tried and true traditions, explore new traditions, or avoid traditions altogether and try to find new activities on a regular basis?',NULL,0),(183,2017,17,4,'label','How often should we have congregational fellowship activities?',NULL,0),(184,2017,17,5,'label','Would you prefer large, highly planned events or small, impromptu events?',NULL,0),(185,2017,17,6,'label','What types of events would you be most likely to attend?',NULL,0),(186,2017,17,7,'free_text','Feedback:',NULL,1),(187,2017,18,1,'label',NULL,NULL,0),(188,2017,18,2,'label','Are you more likely to participate on scheduled work days or are you more likely to take on self-starter projects on your own timeline?',NULL,0),(189,2017,18,3,'label','How important is having well-manicured landscaping?  (Keep in mind that this is typically the first impression a visitor has of us.)',NULL,0),(190,2017,18,4,'label','How important is having every nook and cranny of the building immaculately clean?',NULL,0),(191,2017,18,5,'label','How important is responding quickly to major cosmetic repairs?  (Keep in mind that we must balance the cost of hiring someone to do repairs with the availability of volunteers from within our members.)',NULL,0),(192,2017,18,6,'free_text','Feedback:',NULL,1),(193,2017,19,1,'label',NULL,NULL,0),(194,2017,19,2,'free_text','Feedback:',NULL,1);
/*!40000 ALTER TABLE `survey_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_labels`
--

DROP TABLE IF EXISTS `survey_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_labels` (
  `item_id` int(11) NOT NULL,
  `type` enum('text','image','list') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `level` int(11) NOT NULL DEFAULT '1',
  `italic` int(1) NOT NULL DEFAULT '0',
  `bold` int(1) NOT NULL DEFAULT '0',
  `size` int(11) DEFAULT NULL,
  `value` text CHARACTER SET latin1,
  PRIMARY KEY (`item_id`),
  CONSTRAINT `survey_labels_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `survey_items` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_labels`
--

LOCK TABLES `survey_labels` WRITE;
/*!40000 ALTER TABLE `survey_labels` DISABLE KEYS */;
INSERT INTO `survey_labels` VALUES (1,'text',1,0,0,NULL,'Welcome to the 2017 Time & Talent phase of our annual Stewardship campaigns.  As a small congregation, it is imperative that we use wisely our finite resources of time, skills, talents, and passions.  By participating in this survey, you enable our council to make smarter decisions about where focus our energies and who we can ask to help lead us in our shared ministry.'),(2,'text',2,1,0,NULL,'As you fill out this form, keep in mind that you are being asked your hopes and intentions at this point in time.  You are not signing a contract.  It is perfectly acceptable to change your priorities as time rolls on.  Please give us your best answer that you can based on your current expectations.'),(3,'text',2,1,0,NULL,'Each block below ends with a comments section.  Please feel free to use this space however you best see fit—to clarify a response, to suggest improvements, to share a vision.  We want your feedback.  If you need more space, feel free to add a page or two'),(4,'text',2,1,0,NULL,'Most items provide an info button which brings up additional information about the item.  If you have any additional questions, do not hesitate to ask Pastor Kari, Mike Mayer, or any member of council.'),(5,'text',2,1,0,NULL,'Thank you for your participation,\n'),(6,'image',2,0,0,75,'mm_sig.png'),(7,'text',2,1,0,NULL,'Mike Mayer, CTS Stewardship'),(8,'text',1,0,1,NULL,NULL),(21,'text',1,0,1,NULL,NULL),(25,'text',2,1,0,NULL,'Worship is at the heart of “being church.”  It is what differentiates us from other organizations that serve the community.  It is through worship that we define who we are.'),(26,'text',1,0,1,NULL,NULL),(37,'text',1,0,1,NULL,NULL),(39,'text',1,0,1,NULL,NULL),(46,'text',2,1,0,NULL,'Through Parish Care, we minister to one another’s needs—both physical, spiritual, and emotional.  This is the first of the three cornerstones of “Care, Teach, Serve.”'),(47,'text',1,0,1,NULL,NULL),(51,'text',1,0,1,NULL,NULL),(55,'text',2,1,0,NULL,'Through Learning, we provide opportunities to grow together in faith, knowledge, and spirituality.  This is the second of the three cornerstones of “Care, Teach, Serve.”'),(56,'text',1,0,1,NULL,NULL),(62,'text',1,0,1,NULL,NULL),(66,'text',2,1,0,NULL,'Through Service, we become the hands of God in the world—feeding the hungry, clothing the naked, and lifting up the lowly. This is the third of the three cornerstones of “Care, Teach, Serve.”'),(67,'text',1,0,1,NULL,NULL),(75,'text',1,0,1,NULL,NULL),(80,'text',2,1,0,NULL,'In Outreach we reach out to be the living body of Christ in the greater Gaithersburg/Montgomery Village community—bringing together the principles of Teach and Serve.'),(81,'text',1,0,1,NULL,NULL),(85,'text',1,0,1,NULL,NULL),(89,'text',2,1,0,NULL,'Through Parish Life, we focus in on our need as a community to forge bonds of friendship, family, and unity.  As individuals, we can do good things.  As a strong, loving community, we can do great things.'),(90,'text',1,0,1,NULL,NULL),(97,'text',2,1,0,NULL,'We journey alongside our youth as the Holy Spirit guides them to discover their place in the body of Christ, become disciples, and discern their call to work in partnership with God.'),(98,'text',1,0,1,NULL,NULL),(102,'text',3,1,0,-1,'Note that participation is more than being a chaperone.  You will be working with youth from both CTS and other congregations in the Metro D.C. Synod.  Coordination means organizing our youth to attend, not organizing the events themselves.'),(103,'text',1,0,1,NULL,NULL),(106,'text',1,0,1,NULL,NULL),(114,'text',2,1,0,NULL,'We have been blessed with a facility in a great location that serves as an asset to both our congregation and to the greater community.  Our stewardship of this gift reflects our appreciation for the blessings it brings us.'),(115,'text',1,0,1,NULL,NULL),(120,'text',1,0,1,NULL,NULL),(125,'text',2,1,0,NULL,'We are all blessed with different abilities.  Perhaps the least glamorous and the most undervalued skills are those centered around business administration.  However, without folks stepping up to do these often overlooked tasks, all else comes to a grinding halt.'),(126,'text',1,0,1,NULL,NULL),(131,'text',1,0,1,NULL,NULL),(136,'text',1,0,1,NULL,NULL),(142,'text',1,0,1,NULL,NULL),(147,'text',2,0,0,NULL,'The remainder of this survey contains some rather open ended questions examining which of our activities as a congregation are more valued and more important.  The answers to these questions will help guide council as we decide where it is most important to focus our resources and energies.'),(148,'text',2,0,0,NULL,'As you go through the questions, you will probably notice that the list does not span all aspects of what we do as a congregation. I have intentionally stayed away from questions about worship as the worship committee is already currently exploring these questions.  I have stayed away from any questions about pastoral priorities as that is strictly the purview of Pastor Kari.  Nonetheless, if you have any concerns about how we focus our energy that are not covered explicitly, feel free to add them under general comments.'),(149,'text',2,0,0,NULL,'If you would like to keep your responses anonymous, you may do so.  Simply click on the “anonymous” checkbox above each “Feedback” block.   When you submit your survey, you will be sent a link, by email, to revisit/modify your responses. Hold onto that email as that will be the only mechanism for associating you with your anonymous responses.'),(165,'text',1,0,0,NULL,'We have a number of Christian education programs.  Please comment on the importance to you that we provide a variety of learning opportunities.  In addition, please comment on the specific programs we offer.'),(166,'list',2,0,0,NULL,NULL),(167,'list',2,0,0,NULL,NULL),(168,'list',2,0,0,NULL,NULL),(170,'text',1,0,0,NULL,'CTS offers many ways to serve our community.  For the most part, we have tended to focus on participating in many small projects rather than a few large projects that we all rally around. Please comment on the types of service opportunities on which we should focus.'),(171,'list',2,0,0,NULL,NULL),(172,'list',2,0,0,NULL,NULL),(173,'list',2,0,0,NULL,NULL),(175,'text',1,0,0,NULL,'This is an area that we have struggled with for years.  Some years, we make a concerted effort to get our message out to the community.  Other years, we don’t do quite as much.'),(176,'list',2,0,0,NULL,NULL),(177,'list',2,0,0,NULL,NULL),(178,'list',2,0,0,NULL,NULL),(180,'text',1,0,0,NULL,'CTS has a number of fellowship traditions that go back many years/decades.  Please comment on how important these traditions are, how important fellowship activities are in general, and what type of activities we should be providing.'),(181,'list',2,0,0,NULL,NULL),(182,'list',2,0,0,NULL,NULL),(183,'list',2,0,0,NULL,NULL),(184,'list',2,0,0,NULL,NULL),(185,'list',2,0,0,NULL,NULL),(187,'text',1,0,0,NULL,'Taking care of our property is a never-ending task, but our mission is bigger than taking care of our building.  Please comment on where and how we should focus our energy in taking care of our grounds and building.'),(188,'list',2,0,0,NULL,NULL),(189,'list',2,0,0,NULL,NULL),(190,'list',2,0,0,NULL,NULL),(191,'list',2,0,0,NULL,NULL),(193,'text',1,0,0,NULL,'Please use the following block to provide any additional thoughts for council to consider when it comes to committing time, talent, and energy of our members.');
/*!40000 ALTER TABLE `survey_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_participation_options`
--

DROP TABLE IF EXISTS `survey_participation_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_participation_options` (
  `item_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `require_option_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`,`option_id`),
  CONSTRAINT `survey_participation_options_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `survey_items` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_participation_options`
--

LOCK TABLES `survey_participation_options` WRITE;
/*!40000 ALTER TABLE `survey_participation_options` DISABLE KEYS */;
INSERT INTO `survey_participation_options` VALUES (27,1,'serve',1,NULL),(27,2,'coordinate',1,NULL),(28,1,'serve',1,NULL),(28,2,'coordinate',1,NULL),(29,1,'serve',1,NULL),(29,2,'coordinate',1,NULL),(30,1,'serve',1,NULL),(30,2,'coordinate',1,NULL),(31,1,'serve',1,NULL),(31,2,'coordinate',1,NULL),(32,1,'serve',1,NULL),(32,2,'coordinate',1,NULL),(33,1,'serve',1,NULL),(33,2,'coordinate',1,NULL),(34,1,'vocalist',1,NULL),(34,2,'instrumentalist',1,NULL),(36,1,'serve',1,NULL),(38,1,'serve',1,NULL),(38,2,'coordinate',1,NULL),(40,1,'serve',1,NULL),(40,2,'coordinate',1,NULL),(41,1,'serve',1,NULL),(42,2,'coordinate',1,NULL),(43,1,'serve',1,NULL),(44,1,'serve',1,NULL),(44,2,'back up as needed',1,NULL),(45,1,'serve',1,NULL),(48,1,'serve',1,NULL),(48,2,'coordinate',1,NULL),(49,1,'serve',1,NULL),(49,2,'coordinate',1,NULL),(49,3,'worship',0,1),(49,4,'personal appointments',0,1),(49,5,'kononia',0,1),(49,6,'other activities at CTS',0,1),(50,1,'serve',1,NULL),(52,1,'participate',1,NULL),(52,2,'coordinate',1,NULL),(53,1,'attend',1,NULL),(53,2,'lead',1,NULL),(53,3,'coordinate leaders',1,NULL),(54,1,'participate',1,NULL),(54,2,'organize',1,NULL),(57,1,'attend',1,NULL),(57,2,'lead',1,NULL),(57,3,'coordinate',1,NULL),(58,1,'attend',1,NULL),(58,2,'lead',1,NULL),(58,3,'help plan',1,NULL),(59,1,'teach',1,NULL),(59,2,'assist',1,NULL),(59,3,'substitute',1,NULL),(60,1,'teach',1,NULL),(60,2,'assist',1,NULL),(60,3,'substitute',1,NULL),(61,1,'regularly',1,NULL),(61,2,'as a guest',1,NULL),(61,3,'with special topis',1,NULL),(63,1,'attend',1,NULL),(64,1,'attend',1,NULL),(64,2,'coordinate',1,NULL),(65,1,'attend',1,NULL),(65,2,'coordinate',1,NULL),(68,1,'participate',1,NULL),(68,2,'coordinate',1,NULL),(68,3,'make lunch',0,1),(68,4,'make dinner',0,1),(68,5,'serve dinner',0,1),(69,1,'assist',1,NULL),(69,2,'organize youth',1,NULL),(69,3,'serve as delegate',1,NULL),(70,1,'participate',1,NULL),(70,2,'coordinate',1,NULL),(71,1,'participate',1,NULL),(71,2,'coordinate',1,NULL),(71,3,'contribute',0,1),(71,4,'assemble',0,1),(71,5,'deliver',0,1),(72,1,'gleaning',1,NULL),(73,1,'participate',1,NULL),(74,1,'participate',1,NULL),(76,1,'serve',1,NULL),(76,2,'lead',1,NULL),(77,2,'coordinate',1,NULL),(78,1,'participate',1,NULL),(78,2,'coordinate',1,NULL),(79,1,'participate',1,NULL),(79,2,'coordinate',1,NULL),(82,1,'participate',1,NULL),(82,2,'coordinate',1,NULL),(83,2,'coordinate',1,NULL),(84,1,'participate',1,NULL),(84,2,'coordinate',1,NULL),(86,1,'participate',1,NULL),(86,2,'coordinate',1,NULL),(87,1,'participate',1,NULL),(87,2,'coordinate',1,NULL),(87,3,'sorting/setup',0,1),(87,4,'pricing',0,1),(87,5,'receive donations',0,1),(87,6,'sales',0,1),(87,7,'cleanup',0,1),(87,8,'interacting with guests',0,1),(88,1,'help visitors',1,NULL),(88,2,'coordinate CTS volunteers',1,NULL),(91,1,'provide',1,NULL),(91,2,'coordinate',1,NULL),(92,1,'attend',1,NULL),(92,2,'organize',1,NULL),(93,1,'attend',1,NULL),(93,2,'organize',1,NULL),(94,1,'attend',1,NULL),(94,2,'organize',1,NULL),(95,1,'attend',1,NULL),(95,2,'bring soup',0,1),(95,3,'bring drinks',0,1),(95,4,'bring bread',0,1),(95,5,'bring dessert',0,1),(96,1,'vision',1,NULL),(96,2,'support',1,NULL),(96,3,'organize',1,NULL),(99,1,'participate',1,NULL),(99,2,'coordinate',1,NULL),(100,1,'participate',1,NULL),(100,2,'coordinate',1,NULL),(101,1,'participate',1,NULL),(101,2,'coordinate',1,NULL),(104,1,'member',1,NULL),(105,1,'(please elaborate)',1,NULL),(107,1,'planting',1,NULL),(107,2,'watering',1,NULL),(107,3,'tending',1,NULL),(107,4,'constructing',1,NULL),(108,1,'(please elaborate)',1,NULL),(116,1,'participate',1,NULL),(116,2,'coordinate',1,NULL),(117,1,'participate',1,NULL),(117,2,'coordinate',1,NULL),(118,1,'participate',1,NULL),(118,2,'coordinate',1,NULL),(119,1,'participate',1,NULL),(119,2,'coordinate',1,NULL),(121,1,'participate',1,NULL),(122,1,'participate',1,NULL),(122,2,'coordinate',1,NULL),(123,1,'participate',1,NULL),(123,2,'coordinate',1,NULL),(124,1,'participate',1,NULL),(124,2,'plumbing',0,1),(124,3,'painting',0,1),(124,4,'dry wall',0,1),(124,5,'electrical',0,1),(124,6,'HVAC',0,1),(124,7,'construction',0,1),(124,8,'other',0,1),(127,1,'assist',1,NULL),(127,2,'coordinate',1,NULL),(128,1,'serve',1,NULL),(129,1,'serve',1,NULL),(130,1,'serve',1,NULL),(132,1,'dispatch',1,NULL),(132,2,'administer',1,NULL),(133,1,'edit',1,NULL),(133,2,'distribute',1,NULL),(134,1,'maintain',1,NULL),(134,2,'administer',1,NULL),(135,1,'maintain',1,NULL),(135,2,'administer',1,NULL),(137,1,'maintain',1,NULL),(137,2,'coordinate',1,NULL),(138,1,'maintain',1,NULL),(138,2,'coordinate',1,NULL),(139,1,'maintain',1,NULL),(139,2,'coordinate',1,NULL),(140,1,'serve',1,NULL),(140,2,'coordinate',1,NULL),(141,1,'maintain',1,NULL),(141,2,'administer',1,NULL),(143,1,'serve',1,NULL),(144,2,'serve',1,NULL);
/*!40000 ALTER TABLE `survey_participation_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_participation_qualifiers`
--

DROP TABLE IF EXISTS `survey_participation_qualifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_participation_qualifiers` (
  `item_id` int(11) NOT NULL DEFAULT '0',
  `qualification_option` int(11) NOT NULL,
  `qualification_hint` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`),
  CONSTRAINT `survey_participation_qualifiers_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `survey_participation_options` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_participation_qualifiers`
--

LOCK TABLES `survey_participation_qualifiers` WRITE;
/*!40000 ALTER TABLE `survey_participation_qualifiers` DISABLE KEYS */;
INSERT INTO `survey_participation_qualifiers` VALUES (34,2,'instrument(s)'),(105,1,'I would like to support our youth by...'),(108,1,'I would like to walk alongside our youth by...'),(116,1,'I am willint to clean the following space(s)'),(124,8,'I have the following proprty skills...');
/*!40000 ALTER TABLE `survey_participation_qualifiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_value_items`
--

DROP TABLE IF EXISTS `survey_value_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_value_items` (
  `item_id` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `min_label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `max` int(11) NOT NULL,
  `max_label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`),
  CONSTRAINT `survey_value_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `survey_items` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_value_items`
--

LOCK TABLES `survey_value_items` WRITE;
/*!40000 ALTER TABLE `survey_value_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_value_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_ids`
--

DROP TABLE IF EXISTS `user_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ids` (
  `user_id` char(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_ids`
--

LOCK TABLES `user_ids` WRITE;
/*!40000 ALTER TABLE `user_ids` DISABLE KEYS */;
INSERT INTO `user_ids` VALUES ('123-456-789-cat');
/*!40000 ALTER TABLE `user_ids` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-30  0:21:50
