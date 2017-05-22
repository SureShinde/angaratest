<?php
$installer = $this;

$installer->startSetup();

$installer->run("

 
 
 CREATE TABLE IF NOT EXISTS {$this->getTable('abandoncartmailchimp')} (
	`abandoncartmailchimp_id` int(11) unsigned NOT NULL auto_increment,
	`quote_id` INT(20) NOT NULL ,
	`quote_item_id` INT(20) NOT NULL , 
	`session_id`  char(64) NOT NULL , 
	`quantity` VARCHAR(100) NOT NULL , 
	`product_id` INT(20) NOT NULL ,
	`product_sku` VARCHAR(120) NOT NULL ,
	`product_name` VARCHAR(100) NOT NULL ,
	`product_url` VARCHAR(150) NOT NULL , 
	`product_image` VARCHAR(200) NOT NULL ,
    `product_price` VARCHAR(100) NOT NULL,
	`currency_symbol` VARCHAR(20),
	`coupon_code` VARCHAR(25) ,
	`ring_size` VARCHAR(25) ,
	`band_width` VARCHAR(25) ,
	`stone_size` VARCHAR(25) ,
	`stone_grade` VARCHAR(25) ,
	`metal_type` VARCHAR(25) ,	
	`created_at` DATETIME NOT NULL ,
	
	
PRIMARY KEY (`abandoncartmailchimp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





CREATE TABLE IF NOT EXISTS {$this->getTable('mailchimpsession')} (
	`mailchimpsession_id` int(11) unsigned NOT NULL auto_increment,
	`session_id` char(64) NOT NULL, 
	`visitor_email` VARCHAR(100) NOT NULL ,
	`visitor_firstname` VARCHAR(50) , 
	`visitor_lastname` VARCHAR(50)  , 
    `created_at` DATETIME NOT NULL , 
	`email_count` INT(20) NOT NULL ,
	`check_count` INT(20) NOT NULL ,
	
PRIMARY KEY (`mailchimpsession_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
 
 CREATE TABLE IF NOT EXISTS {$this->getTable('mailchimp_unsubscribers')} (
	`mailchimp_id` int(11) unsigned NOT NULL auto_increment,	
	`unsubscriber_email` VARCHAR(100) NOT NULL ,		
PRIMARY KEY (`mailchimp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS {$this->getTable('mailchimp_sent')} (
	`sent_id` int(11) unsigned NOT NULL auto_increment,		
	`sent_email` VARCHAR(100) NOT NULL ,
	`email_count` INT(20) NOT NULL ,
	`visitor_firstname` VARCHAR(50) , 
	`quote_id` INT(20) NOT NULL ,
	`created_at` DATETIME NOT NULL ,
	
PRIMARY KEY (`sent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
 
 
 


    "
	
	);

	

$installer->endSetup(); 
?>