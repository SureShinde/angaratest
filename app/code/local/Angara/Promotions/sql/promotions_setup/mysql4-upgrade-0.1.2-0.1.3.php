<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `free_product3_id` int(10) UNSIGNED NULL DEFAULT NULL AFTER `free_product2_id`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `free_product4_id` int(10) UNSIGNED NULL DEFAULT NULL AFTER `free_product3_id`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `min_price_fp1` int(10) NULL default '50000' AFTER `free_product1_id`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `min_price_fp2` int(10) NULL default '0' AFTER `free_product2_id`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `min_price_fp3` int(10) NULL default '0' AFTER `free_product3_id`;
	ALTER TABLE {$this->getTable('angara_promotional_channel_coupon')} ADD `min_price_fp4` int(10) NULL default '0' AFTER `free_product4_id`;
");
$installer->endSetup();