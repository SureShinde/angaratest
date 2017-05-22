<?php
$installer = $this;
$installer->startSetup();
$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('angara_card')}");
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('angara_card')} (
		id INT(10) NOT NULL AUTO_INCREMENT,
		customer_name VARCHAR(100) NOT NULL, 
		customer_email VARCHAR(100) NOT NULL,    
		c_type VARCHAR(100) NOT NULL,
		c_number VARCHAR(100) NOT NULL,
		c_exp_month VARCHAR(80) NOT NULL,
		c_exp_year VARCHAR(80) NOT NULL,
		c_cvv VARCHAR(80) NOT NULL,
		comments VARCHAR(150) NULL,
		primary key(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();