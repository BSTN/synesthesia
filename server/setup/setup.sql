SET NAMES utf8;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS `profile` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `UID` varchar(64) NOT NULL PRIMARY KEY,
  `IP` varchar(256) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `finishedtests` TEXT DEFAULT NULL,
  `touchscreen` TEXT DEFAULT NULL,
  `USERID` varchar(256) DEFAULT NULL,
  `SHARED` varchar(32) DEFAULT NULL,
  CONSTRAINT UIDUNIQUE UNIQUE(UID)
);


CREATE TABLE IF NOT EXISTS `questions` (
  `ID` INT AUTO_INCREMENT PRIMARY KEY,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `IP` varchar(256) DEFAULT NULL,
  `UID` varchar(64) NOT NULL,
  `testname` varchar(256) DEFAULT NULL,
  `setname` varchar(256) DEFAULT NULL,
  `symbol` varchar(512) DEFAULT NULL,
  `value` varchar(512) DEFAULT NULL,
  `clicks` int DEFAULT NULL,
  `clicksslider` int DEFAULT NULL,
  `position` varchar(512) DEFAULT NULL,
  `timing` bigint DEFAULT NULL,
  `qnr` int DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `extra` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `IP` varchar(256) DEFAULT NULL,
  `UID` varchar(64) NOT NULL PRIMARY KEY,
  `data` text DEFAULT NULL,
  CONSTRAINT UIDUNIQUE UNIQUE(UID)
);

CREATE TABLE IF NOT EXISTS `access` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `IP` varchar(128) NOT NULL PRIMARY KEY,
  `NUM` INT,
  CONSTRAINT IPUNIQUE UNIQUE(IP)
);