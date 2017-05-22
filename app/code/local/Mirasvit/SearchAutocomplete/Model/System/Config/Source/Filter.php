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


class Mirasvit_SearchAutocomplete_Model_System_Config_Source_Filter
{
    public function toOptionArray()
    {
        $options = array(
            'category' => array(
                'value' => 'category',
                'label' => Mage::helper('searchautocomplete')->__('Category'),
            ),
            'attribute' => array(
                'value' => 'attribute',
                'label' => Mage::helper('searchautocomplete')->__('Attribute'),
            ),
            'none' => array(
                'value' => 'none',
                'label' => Mage::helper('searchautocomplete')->__('No Display'),
            ),
        );

        return $options;
    }
}