<?php
/**
 * Sharecart Extension
 *
 * @category   Sutunam
 * @package    Sharecart
 * @author     Sututeam 
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$installer->getTable('sutunam_sharecart_cart')}`;
CREATE TABLE `{$installer->getTable('sutunam_sharecart_cart')}` (
  `sharecart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `items` text NOT NULL,
  `is_shared` smallint(5) unsigned NOT NULL default 0,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`sharecart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

");

$installer->endSetup();
