<?php
$installer = $this;
$rulesTable = $installer->getTable('sales/order');

$installer->startSetup();

$table_prefix = Mage::getConfig()->getTablePrefix();
$dataTable = $table_prefix.$rulesTable;
$fieldsSql = 'SHOW COLUMNS FROM ' . $dataTable;
$cols = $this->getConnection()->fetchCol($fieldsSql);
if (!in_array('story_email_shared', $cols)){
	$installer->run("ALTER TABLE `{$dataTable}` ADD `story_email_shared` INT(2) NOT NULL DEFAULT '0'");
}

$installer->endSetup();
?>