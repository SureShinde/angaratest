<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('videoupload')};
CREATE TABLE {$this->getTable('videoupload')} (
  `videoupload_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
`entity_id` int(11) NOT NULL,
`video_id` int(11) NOT NULL,
  `video_type` tinyint(4) NULL,
  `videoname` varchar(255)  NOT NULL default '',
`title` varchar(255)  NOT NULL default '',
  `thumname` varchar(128)   NOT NULL default '',
  `product` varchar(255)   NOT NULL default '',
   `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`videoupload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 