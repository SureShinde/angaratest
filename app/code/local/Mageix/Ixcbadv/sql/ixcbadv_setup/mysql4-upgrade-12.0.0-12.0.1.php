<?php
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLC's  End User License Agreement
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://ixcba.com/index.php/license-guide/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@mageix.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * 
 * Magento Mageix IXCBADV Module
 *
 * @category	Checkout & Payments, Customer Registration & Login
 * @package 	Mageix_Ixcbadv
 * @copyright   Copyright (c) 2014 -  Mageix LLC 
 * @author      Brian Graham
 * @license	    http://ixcba.com/index.php/license-guide/   End User License Agreement
 *
 *
 *
 * Magento Mageix IXCBA Module
 * 
 * @category   Checkout & Payments
 * @package	   Mageix_Ixcba
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @author     Brian Graham
 * @license   http://mageix.com/index.php/license-guide/   End User License Agreement
 */

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('ixcbadv_gift_information')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('ixcbadv_gift_information')}` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) DEFAULT NULL,
  `order_comments` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS {$this->getTable('ixcbadv_order_transaction')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('ixcbadv_order_transaction')}` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(255) NOT NULL COMMENT '',
  `order_id` varchar(255) NOT NULL,
  `ref_id` varchar(255) NOT NULL,
  `cm_id` varchar(255) DEFAULT NULL COMMENT '',
  `transaction_type_id` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `invoice_id` varchar(255) DEFAULT NULL COMMENT '',
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `UNQ_IXCBADV_ORDER` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



DROP TABLE IF EXISTS {$this->getTable('ixcbadv_error_log')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('ixcbadv_error_log')}` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `error_type` varchar(255) NOT NULL,
  `error_code` varchar(255) NOT NULL,
  `request_id` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS {$this->getTable('ixcbadv_amazon_user')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('ixcbadv_amazon_user')}` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(255) NOT NULL,
  `amazon_user_id` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unq_customer` (`customer_id`,`amazon_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


");


$installer->endSetup();