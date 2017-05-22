<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;

$installer->startSetup();
$installer->run("
INSERT INTO `{$installer->getTable('searchindex/index')}` (index_code, title, position, status, is_active)
VALUES(
   'mage_catalog_product', 'Products', '0', '3', '1'
)
");

$installer->endSetup();
