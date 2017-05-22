<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `rule_valid_for` int(2) NULL default '0' AFTER `id`;
");
$installer->endSetup();