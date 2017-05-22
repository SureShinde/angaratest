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
 * @version   2.2.8
 * @revision  334
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Mirasvit_SearchIndex
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com)
 */


/**
 * Represent Index model for Mirasvit Promotional Offers
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Model_Type_Maction_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'maction';

    public function getCode()
    {
        return self::INDEX_CODE;
    }

    public function getPrimaryKey()
    {
        return 'action_id';
    }

    public function getAvailableAttributes()
    {
         $result = array(
            'name'              => Mage::helper('searchindex')->__('Name'),
            'short_description' => Mage::helper('searchindex')->__('Short Description'),
            'full_description'  => Mage::helper('searchindex')->__('Full Description'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('action/action')->getCollection();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addStoreFilter(Mage::app()->getStore()->getId());

        $this->joinMatched($collection, 'main_table.action_id');

        return $collection;
    }
}