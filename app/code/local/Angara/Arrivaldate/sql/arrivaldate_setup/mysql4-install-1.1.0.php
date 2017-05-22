<?php
$installer = $this;

$installer->startSetup();

$installer->run(" 

CREATE TABLE IF NOT EXISTS {$this->getTable('angara_leadtimerules')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `lead_time` int(10) NOT NULL UNIQUE,
  `shipping_method` varchar(255) NOT NULL,
  `arrival_text` varchar(255) NOT NULL, 
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('angara_daterules')}(
  `id` int(11) unsigned NOT NULL auto_increment,
  `location` varchar(255) NOT NULL UNIQUE, 
  `date` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	

$installer->endSetup(); 
?>