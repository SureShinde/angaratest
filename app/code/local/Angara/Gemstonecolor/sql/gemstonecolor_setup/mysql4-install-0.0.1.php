<?php 

/**
 * Angara
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Angara
 * @package     Angara_JetDotCom
 * @author 		Angara Core Team <coreteam@angara.com>
 * @copyright   Copyright Angara
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/**
 * Install product link types
 */
$data = array(
    array(
        'link_type_id'  => Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab_Gemstonecolor::LINK_TYPE_GEMSTONECOLOR,
        'code'  => 'gemstonecolor'
    ),
);

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('catalog/product_link_type'), $bind);
}

/**
 * install product link attributes
 */
$data = array(
    array(
        'link_type_id'                  => Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab_Gemstonecolor::LINK_TYPE_GEMSTONECOLOR,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab_Gemstonecolor::LINK_TYPE_GEMSTONECOLOR,
        'product_link_attribute_code'   => 'gemcolorimage',
        'data_type'                     => 'varchar'
    ),
);

$installer->getConnection()->insertMultiple($installer->getTable('catalog/product_link_attribute'), $data);
				
$installer->endSetup();