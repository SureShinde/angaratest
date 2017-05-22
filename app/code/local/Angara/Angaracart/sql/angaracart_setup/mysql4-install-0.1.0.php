<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('insurance')};
CREATE TABLE {$this->getTable('insurance')} (
	`insurance_id` INT(10) UNSIGNED NOT NULL auto_increment ,
	`increment_id` VARCHAR(100) NOT NULL , 
	`product_id` VARCHAR(64) NOT NULL ,
	`product_sku` VARCHAR(100) NOT NULL ,
	`order_id` VARCHAR(100) NOT NULL ,
    `insurance_amount` DECIMAL(12,4) NOT NULL ,
	`created_at` DATETIME NOT NULL ,
PRIMARY KEY (`insurance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
?>