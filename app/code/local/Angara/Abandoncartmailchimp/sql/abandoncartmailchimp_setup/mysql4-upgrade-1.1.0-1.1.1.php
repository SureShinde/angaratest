<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('abandoncartmailchimp')} ADD `product_original_price` VARCHAR(100) NOT NULL AFTER `product_image`;
");
$installer->endSetup();
?>