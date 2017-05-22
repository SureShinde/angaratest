<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara ecommerce. (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('angara_gifts')};
CREATE TABLE IF NOT EXISTS {$this->getTable('angara_gifts')} (
  `gift_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `qty` int(11) NOT NULL default '0',
  `amount` decimal(12,2) NOT NULL default '0.00',
  `store` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  UNIQUE KEY `gift_id` (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('angara_gifts_product')};
CREATE TABLE `{$this->getTable('angara_gifts_product')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gift_id` int(11) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gift_id` (`gift_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();

