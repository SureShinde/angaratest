<?php
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('abandoncart')};
CREATE TABLE {$this->getTable('abandoncart')} (
	`abandoncart_id` INT(10) UNSIGNED NOT NULL auto_increment ,
	`quote_item_id` INT(20) NOT NULL , 
	`customer_id` INT(14) NOT NULL , 
	`customer_firstname` VARCHAR(50) NOT NULL , 
	`customer_lastname` VARCHAR(50) NOT NULL , 
	`customer_email` VARCHAR(100) NOT NULL , 
	`product_id` INT(20) NOT NULL ,
	`product_sku` VARCHAR(120) NOT NULL ,
	`product_name` VARCHAR(100) NOT NULL ,
	`product_url` VARCHAR(150) NOT NULL , 
	`product_image` VARCHAR(200) NOT NULL ,
    `product_price` DECIMAL(12,4) NOT NULL ,
	`created_at` DATETIME NOT NULL ,
	`flag` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`abandoncart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
?>