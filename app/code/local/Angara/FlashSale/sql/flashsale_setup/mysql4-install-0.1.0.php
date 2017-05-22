<?php
$installer = $this;
$installer->startSetup();
$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('flashsale')}");
$sql=<<<SQLTEXT
create table {$this->getTable('flashsale')} (
    flashsale_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
    flashsale_name VARCHAR(128) NOT NULL, 
	description TEXT NULL,
    from_date DATETIME NOT NULL,
	to_date	DATETIME NOT NULL,
	product_id varchar(50) NOT NULL,
	is_active SMALLINT(6) NOT NULL DEFAULT '1' COMMENT 'Rule Is Active',
    primary key(flashsale_id)
);
INSERT INTO `flashsale` (`flashsale_id`, `flashsale_name`, `description`, `from_date`, `to_date`, `product_id`, `is_active`) VALUES
	(1, 'SP0140S', 'SP0140S', '2015-09-14 04:00:00', '2015-09-14 05:00:00', 'SP0140S', 0),
	(2, 'SP0137S', 'SP0137S', '2015-09-14 05:00:00', '2015-09-14 06:00:00', 'SP0137S', 0),
	(3, 'SP0169S', 'SP0169S', '2015-09-14 06:00:00', '2015-09-14 07:00:00', 'SP0169S', 0),
	(4, 'SP0148S_N', 'SP0148S_N', '2015-09-14 07:00:00', '2015-09-14 08:00:00', 'SP0148S_N', 0),
	(5, 'SP0152S', 'SP0152S', '2015-09-14 00:00:00', '2015-09-14 01:00:00', 'SP0152S', 0),
	(6, 'SP0102S', 'SP0102S', '2015-09-14 01:00:00', '2015-09-14 02:00:00', 'SP0102S', 0),
	(7, 'SP0664S', 'SP0664S', '2015-09-14 02:00:00', '2015-09-14 03:00:00', 'SP0664S', 0),
	(8, 'SP0566S', 'SP0566S', '2015-09-14 03:00:00', '2015-09-14 04:00:00', 'SP0566S', 0);	
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 