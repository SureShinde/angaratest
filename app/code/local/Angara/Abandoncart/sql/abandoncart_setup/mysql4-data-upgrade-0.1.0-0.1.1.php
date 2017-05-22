<?php
// Added by Pankaj for Bug Id : 428
$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE `{$this->getTable('wishlist_item')}` ADD `flag` TINYINT(1) NOT NULL DEFAULT 0 AFTER qty;");

$installer->endSetup(); 
// Ended by Pankaj for Bug Id : 428
?>