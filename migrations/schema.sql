DROP DATABASE `commpeak`;
CREATE DATABASE IF NOT EXISTS `commpeak`;

use `commpeak`;

DROP TABLE IF EXISTS `phone`;
CREATE TABLE `phone` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `country` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `continent` VARCHAR(2) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `uploaded_file`;
CREATE TABLE `uploaded_file` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `original_name` VARCHAR(2552) NOT NULL,
    `slugged_name` VARCHAR(255) NOT NULL,
    `upload_path` VARCHAR(255) NOT NULL,
    `upload_datetime` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `call_log`;
CREATE TABLE `call_log` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `customer_id` int(10) NOT NULL,
    `call_date` datetime NOT NULL,
    `duration` int(10) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `ip` VARCHAR(255) NOT NULL,
    `modified_datetime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;