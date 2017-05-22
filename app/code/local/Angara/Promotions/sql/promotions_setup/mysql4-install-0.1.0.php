<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `angara_promotional_channel` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`url_identifier` VARCHAR(64) NOT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`home_hero_banner_desktop` INT UNSIGNED NULL DEFAULT NULL,
	`home_hero_banner_tablet` INT UNSIGNED NULL DEFAULT NULL,
	`home_hero_banner_mobile` INT UNSIGNED NULL DEFAULT NULL,
	`catalog_banner_desktop` INT UNSIGNED NULL DEFAULT NULL,
	`catalog_banner_tablet` INT UNSIGNED NULL DEFAULT NULL,
	`catalog_banner_mobile` INT UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `url_identifier` (`url_identifier`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE `angara_promotional_channel_coupon` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`rule_id` INT(10) UNSIGNED NOT NULL,
	`channel_id` INT(10) UNSIGNED NOT NULL,
	`criteria_message` TEXT NOT NULL,
	`criteria_error_message` TEXT NOT NULL,
	`terms` TEXT NOT NULL,
	`priority` INT(10) UNSIGNED NOT NULL DEFAULT '1',
	`display_on_frontend` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`short_description` TEXT NOT NULL,
	`long_description` TEXT NOT NULL,
	`free_product1_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`free_product2_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `rule_id` (`rule_id`),
	INDEX `FK__angara_promotional_coupon_channel` (`channel_id`),
	CONSTRAINT `FK__angara_promotional_channel_coupon_salesrule` FOREIGN KEY (`rule_id`) REFERENCES `salesrule` (`rule_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK__angara_promotional_coupon_channel` FOREIGN KEY (`channel_id`) REFERENCES `angara_promotional_channel` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 