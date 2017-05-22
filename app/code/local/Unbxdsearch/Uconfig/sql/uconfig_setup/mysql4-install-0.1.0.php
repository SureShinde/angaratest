<?php


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$installer->getTable('unbxd_uconfig_conf')}`;

CREATE TABLE `{$installer->getTable('unbxd_uconfig_conf')}` (
    `uconfig_id` int(10) unsigned NOT NULL auto_increment,
    `action` varchar(255) NOT NULL default '',
    `value` varchar(255),
    PRIMARY KEY  (`uconfig_id`)
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    	


INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('address','empty');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('recoverindex','0');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('doUpload','true');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('Fullexport','true');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('Lastindex','empty');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('log','empty');
INSERT INTO `{$installer->getTable('unbxd_uconfig_conf')}` (action,value) VALUES ('updatelock','false');
		
");
$installer->endSetup();
 

