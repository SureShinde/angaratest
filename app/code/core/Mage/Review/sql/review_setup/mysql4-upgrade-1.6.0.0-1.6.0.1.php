<?php
$installer = $this;
$installer->startSetup();

  
$installer->run("
	ALTER TABLE  {$this->getTable('review/review')} 
	ADD `type_status_id` TEXT( 50 );
	");

$installer->endSetup();