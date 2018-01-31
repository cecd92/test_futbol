-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `test_futbol` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci */;
USE `test_futbol`;

DROP TABLE IF EXISTS `partidos`;
CREATE TABLE `partidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partidos` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `estrella` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `partidos` (`id`, `partidos`, `estrella`) VALUES
(1,	'{ \"history\": [\"GPPEAG\", \"EGEAAG\", \"PAGEPG\", \"PGGGAE\", \"AEEEEG\", \"GPAPPA\"] }',	1);

-- 2018-01-30 13:45:09