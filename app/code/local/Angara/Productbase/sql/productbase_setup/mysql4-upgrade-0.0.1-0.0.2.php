<?php

$installer = $this;
$installer->startSetup();

try
{
    $installer->run("
ALTER TABLE `angara_pb_setting_type`
	ADD COLUMN `setting_name` VARCHAR(50) NOT NULL AFTER `setting_cost`,
	ADD COLUMN `stone_size` VARCHAR(50) NOT NULL AFTER `setting_name`;
");

} catch(Exception $e) { 
	Mage::logException($e); 
}

$installer->endSetup();