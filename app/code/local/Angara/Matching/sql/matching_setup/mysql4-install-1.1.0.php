<?php
$installer = $this;

$installer->startSetup();

$installer->run("



 
 
 CREATE TABLE IF NOT EXISTS {$this->getTable('matching_emails')} (
	`id` int(11) unsigned NOT NULL auto_increment,
	`email_id` VARCHAR(100)  ,
	`customer_firstname` VARCHAR(100)  ,
	`email_count` VARCHAR(120)  ,
	`product_master_sku` VARCHAR(100)  ,
	`product_sku` VARCHAR(100),
	`matching1_master_sku` VARCHAR(100)  ,
	`matching1_sku` VARCHAR(100)  ,
	`matching1_stone` VARCHAR(100)  ,
	`matching1_metal` VARCHAR(100)  ,
	`matching2_master_sku` VARCHAR(100)  ,
	`matching2_sku` VARCHAR(100)  ,
	`matching2_stone` VARCHAR(100)  ,
	`matching2_metal` VARCHAR(100)  ,	
	`exactmatching_master_sku` VARCHAR(100)  , 
	`exactmatching_sku` VARCHAR(100)  , 
	
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





CREATE TABLE IF NOT EXISTS {$this->getTable('matching_emailcount')} (
	`id` int(11) unsigned NOT NULL auto_increment,
	`email_id` VARCHAR(100)  ,
	`count` VARCHAR(100)  ,
	
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
 
 
 CREATE TABLE IF NOT EXISTS {$this->getTable('matching')} (
	`matching_id` int(11) unsigned NOT NULL auto_increment,
	`customer_email` VARCHAR(100)  ,
	`customer_firstname` VARCHAR(100)  ,
	`skus` VARCHAR(1000)  ,
	
PRIMARY KEY (`matching_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
 


    "
	
	);

	

$installer->endSetup(); 
?>