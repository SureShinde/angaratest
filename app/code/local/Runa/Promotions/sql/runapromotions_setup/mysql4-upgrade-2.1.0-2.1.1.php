<?php

$installer = $this;
$installer->startSetup();

//adding all the required attributes
$installer->addAttribute('invoice', 'runa_discount_total', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'runa_discount_shipping', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'runa_item_discount_total', array('type'=>'decimal'));

$installer->endSetup();