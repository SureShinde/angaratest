<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$installer->getTable('weblog/blogpost')}`
    CHANGE post post text not null;
");
$installer->endSetup();
//die("You'll see why this is here in a second");
?>