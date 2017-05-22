<?php
$installer = $this;
$installer->startSetup();

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('customer_story')}");
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('customer_story')} (
		id INT(10) NOT NULL AUTO_INCREMENT,
		description VARCHAR(250) NOT NULL,
		image_details TEXT NOT NULL,	
		order_id VARCHAR(50) NOT NULL,    
		is_approved ENUM('0','1') NOT NULL DEFAULT '0',
		created_at DATETIME NULL,
		updated_at DATETIME NULL,
		CONSTRAINT `FK_SHARE_STORY_ORDER_ID` FOREIGN KEY (order_id) REFERENCES `{$this->getTable('sales_flat_order')}` (increment_id),
		PRIMARY KEY(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();	?>