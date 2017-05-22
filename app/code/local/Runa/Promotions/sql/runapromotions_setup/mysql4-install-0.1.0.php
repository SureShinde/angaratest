<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('runapromotions/debug_log')};
CREATE TABLE {$this->getTable('runapromotions/debug_log')} (
  `log_id` int(11) unsigned NOT NULL auto_increment,
  `priority` varchar(15) NOT NULL default '',
  `priorityName` varchar(15) NOT NULL default '',
  `message` text NOT NULL default '',
  `timestamp` TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
");

$installer->endSetup();
