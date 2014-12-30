-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `diverse`;
CREATE TABLE `diverse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `second_id` int(10) unsigned NOT NULL,
  `signed_int_null_default` int(5) DEFAULT '3',
  `100_char` varchar(100) NOT NULL,
  `200_char_null` varchar(200) DEFAULT NULL,
  `comment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time is fleeting',
  `float` float(12,10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `second_id` (`second_id`),
  CONSTRAINT `diverse_ibfk_2` FOREIGN KEY (`second_id`) REFERENCES `second` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='second';


DROP TABLE IF EXISTS `second`;
CREATE TABLE `second` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_two` int(10) unsigned NOT NULL,
  `id_three` int(10) unsigned NOT NULL,
  `id_four` int(10) unsigned NOT NULL,
  `id_five` int(10) unsigned NOT NULL,
  `id_six` int(10) unsigned NOT NULL,
  `id_seven` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_two` (`id_two`),
  UNIQUE KEY `id_three_id_four` (`id_three`,`id_four`),
  KEY `id_five` (`id_five`),
  KEY `id_six_id_seven` (`id_six`,`id_seven`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2014-12-30 07:28:56
