CREATE DATABASE IF NOT EXISTS housebudget;
USE housebudget;

CREATE TABLE IF NOT EXISTS `month` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `year` varchar(10) NOT NULL,
  `description` text,
  `lastupdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `monthid` bigint(20) NOT NULL,
  `catid` bigint(20) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT 0,
  `date` date DEFAULT NULL,
  `catOrNot` char(1) DEFAULT 'N',
  `description` text,
  PRIMARY KEY (`id`)
);
