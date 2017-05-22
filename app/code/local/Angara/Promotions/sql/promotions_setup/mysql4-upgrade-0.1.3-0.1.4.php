<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('angara_promotional_channel')} ADD `offer_banner_desktop` int(2) NULL default '0' AFTER `catalog_banner_mobile`;
	ALTER TABLE {$this->getTable('angara_promotional_channel')} ADD `offer_banner_mobile` int(2) NULL default '0' AFTER `offer_banner_desktop`;
");
$installer->endSetup();