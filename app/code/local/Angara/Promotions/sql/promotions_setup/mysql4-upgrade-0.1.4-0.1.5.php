<?php
$installer = $this;
$rulesTable = $installer->getTable('angara_promotional_channel_coupon');

$installer->startSetup();

$table_prefix = Mage::getConfig()->getTablePrefix();
$dataTable = $table_prefix.$rulesTable;
$fieldsSql = 'SHOW COLUMNS FROM ' . $dataTable;
$cols = $this->getConnection()->fetchCol($fieldsSql);
if (!in_array('valid_shipping', $cols)){
	$installer->run("ALTER TABLE `{$dataTable}` ADD `valid_shipping` varchar(255) NOT NULL DEFAULT 'freeshipping_freeshipping' AFTER `rule_id`");
}

$installer->endSetup(); ?>