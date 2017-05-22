<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table ship(
ship_id int not null auto_increment, 
name varchar(100), 
sort_order smallint(6) NOT NULL,
enabled tinyint(1) NOT NULL DEFAULT '1',
primary key(ship_id)
);

INSERT INTO `ship` (`ship_id`, `name`, `sort_order`, `enabled`) VALUES
	(1, 'USPS', 0, 1),
	(2, 'UPS', 0, 1),
	(3, 'International Priority', 0, 1),
	(4, 'Saturday Delivery', 0, 1),
	(5, 'Express Saver', 0, 1),
	(6, 'International Economy', 0, 1),
	(7, 'Priority Overnight', 0, 1),
	(8, '2 Day', 0, 1),
	(9, '3 Day', 0, 1),
	(10, 'Ground', 0, 1),
	(11, 'Home', 0, 1),
	(12, 'Standard Overnight', 0, 1);
	
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 