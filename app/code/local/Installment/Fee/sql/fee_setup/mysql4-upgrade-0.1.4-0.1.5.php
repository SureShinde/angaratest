<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE sales_flat_quote_item ADD COLUMN no_of_installment INT(10) NULL DEFAULT '1' COMMENT 'Number of installment' AFTER runa_item_discount_total");

$installer->endSetup();