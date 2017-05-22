<?php
//$installer->startSetup();

$installer = $this;


$installer->run("
            ALTER TABLE  {$this->getTable('abandoncartmailchimp')} ADD `share_url` text;
            ");


$installer->endSetup();