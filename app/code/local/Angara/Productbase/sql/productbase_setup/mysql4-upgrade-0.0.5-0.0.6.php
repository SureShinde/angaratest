<?php

$installer = $this;
$installer->startSetup();

try
{
    $installer->run("
	ALTER TABLE `angara_pb_product`
		ADD COLUMN `finding_silver` DOUBLE NOT NULL DEFAULT '0' AFTER `avg_metal_weight`,
		ADD COLUMN `finding_platinum` DOUBLE NOT NULL DEFAULT '0' AFTER `finding_14kgold`
");

} catch(Exception $e) { 
	Mage::logException($e); 
}

$installer->endSetup();