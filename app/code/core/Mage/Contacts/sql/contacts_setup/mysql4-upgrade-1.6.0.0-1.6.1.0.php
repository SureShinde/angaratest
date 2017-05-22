<?php
$installer = $this;
$installer->startSetup();
$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('custom_form')}");
$installer->run("
				create table {$this->getTable('custom_form')} (
					form_id int(11) not null auto_increment, 
					form_type varchar(20),
					first_name varchar(20), 
					last_name varchar(20), 
					email varchar(30),
					primary key(form_id)
				);
");
$installer->endSetup();