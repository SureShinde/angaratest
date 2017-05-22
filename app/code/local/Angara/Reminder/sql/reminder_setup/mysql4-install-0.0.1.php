<?php
$this->startSetup();

/**
 * Create table 'reminder/reminder'
 */
$this->startSetup();

$this->run("CREATE TABLE IF NOT EXISTS reminder (
  reminder_id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Reminder ID',
  store_id int(10) unsigned DEFAULT '0' COMMENT 'Store ID',
  customer_id int(10) unsigned DEFAULT '0' COMMENT 'Customer ID',
  firstname varchar(100) DEFAULT NULL,
  lastname varchar(100) DEFAULT NULL,
  relationship varchar(100) DEFAULT NULL,
  occasion varchar(100) DEFAULT NULL,
  occasion_date date DEFAULT '0000-00-00',
  ring_size varchar(100) DEFAULT NULL,
  favorite_gemstone varchar(100) DEFAULT NULL,
  comments text COMMENT 'Short description of reminder list item',
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last updated date',
  PRIMARY KEY (reminder_id),
  KEY reminder_id (reminder_id)
);
");

$this->endSetup();