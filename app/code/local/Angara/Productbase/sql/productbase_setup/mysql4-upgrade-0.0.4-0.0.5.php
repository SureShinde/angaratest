<?php

$installer = $this;
$installer->startSetup();

try
{
    $installer->run("
	ALTER TABLE `angara_pb_product_stone`
		CHANGE COLUMN `center_stone_weight` `center_stone_weight` VARCHAR(50) NOT NULL AFTER `stone3_count`,
		CHANGE COLUMN `stone1_weight` `stone1_weight` VARCHAR(50) NOT NULL AFTER `center_stone_weight`,
		CHANGE COLUMN `stone3_weight` `stone3_weight` VARCHAR(50) NOT NULL AFTER `stone1_weight`;
");

} catch(Exception $e) { 
	Mage::logException($e); 
}

$installer->endSetup();