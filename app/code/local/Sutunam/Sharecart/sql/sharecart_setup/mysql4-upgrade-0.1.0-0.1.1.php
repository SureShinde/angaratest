<?php
/**
 * Sharecart Extension
 *
 * @category   Sutunam
 * @package    Sharecart
 * @author     Sututeam 
 */

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('sutunam_sharecart_cart')}` DROP COLUMN `is_shared`;
");

$installer->endSetup();
