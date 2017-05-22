<?php
$installer = $this;
$installer->startSetup();

$table_prefix = Mage::getConfig()->getTablePrefix();
$dataTable = $table_prefix.$this->getTable('angara_card');
$fieldsSql = 'SHOW COLUMNS FROM ' . $dataTable;
$cols = $this->getConnection()->fetchCol($fieldsSql);
if (!in_array('order_number', $cols)){
	$installer->run("ALTER TABLE `{$dataTable}` ADD `order_id` INT(10) NULL AFTER `c_cvv`");
}

$installer->endSetup();?>