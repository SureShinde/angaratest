<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute('order', 'runa_discount_total', array('type'=>'decimal'));
$installer->addAttribute('quote', 'runa_discount_total', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'runa_item_discount_total', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'runa_item_discount_total', array('type'=>'decimal'));

$installer->endSetup();
