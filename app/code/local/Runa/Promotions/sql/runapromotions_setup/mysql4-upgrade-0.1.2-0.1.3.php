<?php

$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('runa_service_log')} ADD 
  quote_id bigint(20) NOT NULL after log_id;
  
	");
$installer->endSetup();