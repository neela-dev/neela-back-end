-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5289
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for db_neela
CREATE DATABASE IF NOT EXISTS `db_neela` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `db_neela`;

-- Dumping structure for table db_neela.tbl_campaign
CREATE TABLE IF NOT EXISTS `tbl_campaign` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) DEFAULT NULL,
  `campaign_name` varchar(100) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`_id`),
  KEY `is_delete` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;


-- Dumping structure for table db_neela.tbl_campaign_invite
CREATE TABLE IF NOT EXISTS `tbl_campaign_invite` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `campaign_uid` varchar(80) NOT NULL,
  `msisdn` varchar(20) NOT NULL,
  `invitee` varchar(20) NOT NULL COMMENT 'invitee msisdn',
  `message_status` int(1) NOT NULL DEFAULT '0',
  `time_stamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_archived` int(1) DEFAULT '0',
  PRIMARY KEY (`_id`),
  KEY `campaign_uid` (`campaign_uid`),
  KEY `msisdn` (`msisdn`),
  KEY `is_archived` (`is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_neela.tbl_campaign_invite: ~0 rows (approximately)

-- Dumping structure for table db_neela.tbl_campaign_participant
CREATE TABLE IF NOT EXISTS `tbl_campaign_participant` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `campaign_uid` varchar(80) DEFAULT NULL,
  `username` varchar(25) DEFAULT 'Unspecified',
  `msisdn` varchar(15) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` int(1) DEFAULT '0',
  PRIMARY KEY (`_id`),
  KEY `msisdn` (`msisdn`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_neela.tbl_campaign_participant: ~0 rows (approximately)

-- Dumping structure for table db_neela.tbl_contribution
CREATE TABLE IF NOT EXISTS `tbl_contribution` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `trx_no` varchar(25) NOT NULL,
  `msisdn` varchar(20) NOT NULL,
  `amount` double(15,2) DEFAULT '0.00',
  `running_bal` double(15,2) DEFAULT '0.00',
  `ref_no` varchar(80) NOT NULL COMMENT 'owner uuid',
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_archived` int(1) DEFAULT '0',
  PRIMARY KEY (`_id`),
  UNIQUE KEY `trx_no` (`trx_no`),
  KEY `msisdn` (`msisdn`),
  KEY `ref_no` (`ref_no`),
  KEY `is_archived` (`is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_neela.tbl_contribution: ~0 rows (approximately)

-- Dumping structure for table db_neela.tbl_user
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `paswd` varchar(100) NOT NULL,
  `child_ref_no` varchar(100) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT 'B' COMMENT 'F - individual B - business',
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` int(1) DEFAULT '0',
  `is_suspended` int(1) DEFAULT '0',
  PRIMARY KEY (`_id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `is_active` (`is_active`),
  KEY `is_suspended` (`is_suspended`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table db_neela.tbl_user: ~2 rows (approximately)

-- Dumping structure for table db_neela.tbl_user_extra
CREATE TABLE IF NOT EXISTS `tbl_user_extra` (
  `_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_uid` varchar(100) NOT NULL,
  `entity_name` varchar(80) DEFAULT NULL,
  `telephone_no` varchar(25) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `api_key` varchar(80) NOT NULL,
  PRIMARY KEY (`_id`),
  UNIQUE KEY `user_uid` (`user_uid`),
  KEY `api_key` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table db_neela.tbl_user_extra: ~1 rows (approximately)

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
