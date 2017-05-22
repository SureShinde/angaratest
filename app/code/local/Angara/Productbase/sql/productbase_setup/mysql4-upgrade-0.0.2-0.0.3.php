<?php

$installer = $this;
$installer->startSetup();

try
{
    $installer->run("
ALTER TABLE `angara_pb_product_stone`
	CHANGE COLUMN `center_stone_count` `center_stone_count` VARCHAR(50) NOT NULL AFTER `stone3_size`,
	CHANGE COLUMN `stone1_count` `stone1_count` VARCHAR(50) NOT NULL AFTER `center_stone_count`,
	CHANGE COLUMN `stone3_count` `stone3_count` VARCHAR(50) NOT NULL AFTER `stone1_count`;
");

} catch(Exception $e) { 
	Mage::logException($e); 
}

$installer->endSetup();