<?php

$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$installer->getTable('customerreview/customerreviewlog')};
CREATE TABLE {$installer->getTable('customerreview/customerreviewlog')} (
			`id` int(11) unsigned NOT NULL auto_increment,
			`email` varchar(255)  NOT NULL,
			`sequence_number` varchar(2) NULL default '',
			`order_id` int(11) unsigned  NULL,
			`date` Datetime,
			`followup_object_id` int(11) unsigned NOT NULL,
			`status` varchar(1) default 'S',
			`rule_id` varchar(2),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('customerreview/customerreviewsubmission')};
CREATE TABLE {$installer->getTable('customerreview/customerreviewsubmission')} (
			`id` int(11) unsigned NOT NULL auto_increment,
			`email` varchar(255)  NOT NULL,
			`item_id` int(11) unsigned NOT NULL ,
			`review_id` int(11) unsigned NOT NULL,
			`sequence_number`  varchar(2) NULL default '',
			`order_id` int(11) unsigned  NULL,
			`date` Datetime,
			`rule_id` varchar(2),			
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
