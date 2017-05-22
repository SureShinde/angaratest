<?php
$this->startSetup();

$this->run("CREATE TABLE IF NOT EXISTS `angara_pb_metal_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `metal` varchar(255) NOT NULL,
  `finding_multiplier` DOUBLE UNSIGNED NOT NULL,
  `price` double unsigned NOT NULL,
  `constant` double unsigned NOT NULL,
  `labour` double unsigned NOT NULL,
  `alias` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `angara_pb_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `margin` double NOT NULL DEFAULT '0.5',
  `retail_percent` DOUBLE NOT NULL DEFAULT '0.4',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `angara_pb_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(128) NOT NULL,
  `avg_metal_weight` double NOT NULL,
  `finding_14kgold` double NOT NULL DEFAULT '0',
  `pricing_style` varchar(255) NOT NULL DEFAULT '''auto''' COMMENT '''auto'',''manual''',
  `tested` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `tested_by` mediumint(8) unsigned DEFAULT NULL,
  `approved_by` mediumint(8) unsigned DEFAULT NULL,
  `tested_on` datetime DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `theme` VARCHAR(50) NULL DEFAULT NULL,
  `coordinates` TEXT NULL,
  `angara_pb_category_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `angara_pb_setting_type` (
	`id` VARCHAR(255) NOT NULL,
	`setting_cost` DOUBLE UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `angara_pb_stone` (
`id` text
,`shape` varchar(255)
,`title` varchar(255)
,`grade` varchar(32)
,`size` varchar(16)
,`weight` double unsigned
,`constant` double unsigned
,`cost` double
);
CREATE TABLE IF NOT EXISTS `angara_pb_stone_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `angara_pb_stone_price_per_carat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stone_id` int(10) unsigned NOT NULL,
  `stone_grade` varchar(32) NOT NULL,
  `shape_id` int(10) unsigned NOT NULL,
  `stone_size` varchar(16) NOT NULL,
  `price_per_carat` double unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `angara_pb_stone_shape` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shape` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `angara_pb_stone_weight` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stone_id` int(10) unsigned NOT NULL,
  `shape_id` int(10) unsigned NOT NULL,
  `stone_size` varchar(16) NOT NULL,
  `weight` double unsigned NOT NULL,
  `weight_constant` double unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
DROP TABLE IF EXISTS `angara_pb_stone`;

CREATE TABLE IF NOT EXISTS `angara_pb_product_grade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `angara_pb_product_id` int(10) unsigned NOT NULL,
  `grade` varchar(16) NOT NULL,
  `is_default` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `angara_pb_product_id` (`angara_pb_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `angara_pb_product_metal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `angara_pb_product_id` int(10) unsigned NOT NULL,
  `metal` varchar(64) NOT NULL,
  `alias` VARCHAR(50) NOT NULL,
  `is_default` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `angara_pb_product_id` (`angara_pb_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `angara_pb_product_stone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `angara_pb_product_id` int(10) unsigned NOT NULL,
  `center_stone_size` VARCHAR(50) NOT NULL,
  `avg_metal_weight_adjustment` double NOT NULL,
  `stone1_size` VARCHAR(50) NOT NULL,
  `stone3_size` VARCHAR(50) NOT NULL,
  `center_stone_count` int(10) unsigned NOT NULL,
  `stone1_count` int(10) unsigned NOT NULL,
  `stone3_count` int(10) unsigned NOT NULL,
  `center_stone_weight` double unsigned NOT NULL,
  `stone1_weight` double unsigned NOT NULL,
  `stone3_weight` double unsigned NOT NULL,
  `is_default` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `angara_pb_product_id` (`angara_pb_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

");

$this->endSetup();