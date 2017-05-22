<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('additionalinformation')};
CREATE TABLE {$this->getTable('additionalinformation')} (
  `additionalinformation_id` int(11) unsigned NOT NULL auto_increment,
  `order_increment_id` int(20) NOT NULL,
  `customer_firstname` varchar(150) NOT NULL default '',
  `customer_lastname` varchar(150) NOT NULL default '',
  `customer_order_email` varchar(150) NOT NULL default '',
  `customer_govt_email` varchar(150) NOT NULL default '',
  `customer_images` varchar(1000) NOT NULL default '',
  `customer_bank_name` varchar(150) NOT NULL default '',
  `customer_bank_auth_code` varchar(150) NOT NULL default '',
  `otp_password` varchar(100) NOT NULL default '',
  `admin_user_id` int(11) NOT NULL,
  `admin_user_firstname` varchar(150) NOT NULL default '',
  `admin_user_lastname` varchar(150) NOT NULL default '',
  `admin_user_name` varchar(80) NOT NULL default '',
  `is_mail_sent` tinyint(1) unsigned NOT NULL,
  `flag` tinyint(1) unsigned NOT NULL,
  `otp_flag` tinyint(1) unsigned NOT NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`additionalinformation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 