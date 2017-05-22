<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `max_price_fp1` FLOAT NULL default '0' AFTER `min_price_fp1`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `max_price_fp2` FLOAT NULL default '0' AFTER `min_price_fp2`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `max_price_fp3` FLOAT NULL default '0' AFTER `min_price_fp3`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `max_price_fp4` FLOAT NULL default '0' AFTER `min_price_fp4`;
");
$installer->endSetup();