<?php
//$installer->startSetup();

$installer = $this;


$installer->run("
            ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `gender` varchar( 20 );

             ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `birthday` DATE;
              ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `relationship_status` varchar( 30 );
             ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `wedding_date` DATE;
             ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `zip` varchar( 10 );
             ALTER TABLE  {$this->getTable('newsletter_subscriber')} ADD `free_shipping` int( 20 ) DEFAULT 0;
            ");


$installer->endSetup();