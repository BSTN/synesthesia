SET NAMES utf8;
SET time_zone = '+00:00';

CREATE TABLE `profile` (
  `created` timestamp NOT NULL DEFAULT 0,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `UID` varchar(64) NOT NULL PRIMARY KEY,
  `IP` varchar(256) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `USERID` varchar(256) DEFAULT NULL,
  `SHARED` varchar(32) DEFAULT NULL,
  CONSTRAINT UIDUNIQUE UNIQUE(UID)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;


CREATE TABLE `questions` (
  `ID` INT AUTO_INCREMENT PRIMARY KEY,
  `created` timestamp NOT NULL DEFAULT 0,
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
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `extra` (
  `created` timestamp NOT NULL DEFAULT 0,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `IP` varchar(256) DEFAULT NULL,
  `UID` varchar(64) NOT NULL PRIMARY KEY,
  `data` text DEFAULT NULL,
  CONSTRAINT UIDUNIQUE UNIQUE(UID)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `access` (
  `created` timestamp NOT NULL DEFAULT 0,
  `modified` timestamp DEFAULT NOW() ON UPDATE NOW(),
  `IP` varchar(256) NOT NULL PRIMARY KEY,
  `NUM` INT,
  CONSTRAINT IPUNIQUE UNIQUE(IP)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;