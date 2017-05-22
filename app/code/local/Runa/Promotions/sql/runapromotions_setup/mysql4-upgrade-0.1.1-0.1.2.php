<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('runa_service_log')};
CREATE TABLE  {$this->getTable('runa_service_log')} (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `request_type` text,
  `request_xml` text,
  `response_xml` text,
  `error_message` text,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM;
	");
$installer->endSetup();