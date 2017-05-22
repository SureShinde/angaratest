<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `angara_promotional_banner` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`unique_identifier` VARCHAR(64) NOT NULL,
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`description` TEXT NULL DEFAULT NULL,
	`image_path` VARCHAR(255) NOT NULL,
	`image_title` VARCHAR(255) NOT NULL,
	`image_alt_text` VARCHAR(255) NOT NULL,
	`html_content` TEXT NULL DEFAULT NULL COMMENT 'can be used to put extra html like image maps',
	`has_ticker` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`ticker_time` DATETIME NULL DEFAULT NULL,
	`ticker_left_position` INT NULL DEFAULT NULL,
	`ticker_top_position` INT NULL DEFAULT NULL,
	`created_at` DATETIME NULL DEFAULT NULL,
	`updated_at` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`),
	UNIQUE INDEX `unique_identifier` (`unique_identifier`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
AUTO_INCREMENT=1
;
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 