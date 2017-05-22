<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE `{$this->getTable('bannerslider')}` ADD `image_type` int(2) NULL default '4' AFTER filename;
	ALTER TABLE `{$this->getTable('bannerslider')}` ADD `image_position` int(2) NULL default '1' AFTER image_type;
	ALTER TABLE `{$this->getTable('bannerslider')}` ADD `image_alt` varchar(255) NULL AFTER image_position;
");

$installer->endSetup();
?>