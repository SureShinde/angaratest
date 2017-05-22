<?php

$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'runa_discount_shipping', array('type'=>'decimal'));
$installer->addAttribute('quote', 'runa_discount_shipping', array('type'=>'decimal'));

$installer->endSetup();