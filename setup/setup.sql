SET NAMES utf8;
SET time_zone = '+00:00';

CREATE TABLE `profile` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UID` varchar(64) NOT NULL,
  `IP` varchar(256) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `USERID` varchar(256) DEFAULT NULL,
  CONSTRAINT UIDUNIQUE UNIQUE(UID)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;


CREATE TABLE `questions` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IP` varchar(256) DEFAULT NULL,
  `UID` varchar(64) NOT NULL,
  `testname` int DEFAULT NULL,
  `setname` int DEFAULT NULL,
  `symbol` int DEFAULT NULL,
  `value` varchar(512) DEFAULT NULL,
  `clicks` int DEFAULT NULL,
  `clicksslider` int DEFAULT NULL,
  `timing` int DEFAULT NULL,
  `qnr` int DEFAULT NULL,
  `interrupted` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `extraquestions` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IP` varchar(256) DEFAULT NULL,
  `UID` varchar(64) NOT NULL,
  `values` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;