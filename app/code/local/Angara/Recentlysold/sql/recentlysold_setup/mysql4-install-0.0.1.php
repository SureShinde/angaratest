<?php
$this->startSetup();

$this->run("CREATE TABLE `angara_recentlysold_item` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`product_name` VARCHAR(64) NOT NULL,
	`image` VARCHAR(256) NOT NULL,
	`price` DOUBLE UNSIGNED NOT NULL,
	`bought_by` VARCHAR(32) NOT NULL,
	`time` DATETIME NOT NULL,
	`location` VARCHAR(64) NOT NULL,
	`link` VARCHAR(256) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;
");

$this->endSetup();