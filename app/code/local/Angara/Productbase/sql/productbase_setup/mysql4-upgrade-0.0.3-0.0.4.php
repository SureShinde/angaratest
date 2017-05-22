<?php

$installer = $this;
$installer->startSetup();

try
{
    $installer->run("
	ALTER TABLE `angara_pb_product`  ADD COLUMN `modified` DATETIME NULL AFTER `angara_pb_category_id`;
");

} catch(Exception $e) { 
	Mage::logException($e); 
}

$installer->endSetup();