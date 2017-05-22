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
 * Represent Index model for Aheadworks blog
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Model_Type_Awblog_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'awblog';

    public function getCode()
    {
        return self::INDEX_CODE;
    }

    public function getPrimaryKey()
    {
        return 'post_id';
    }

    public function getAvailableAttributes()
    {
         $result = array(
            'title'         => Mage::helper('searchindex')->__('Title'),
            'short_content' => Mage::helper('searchindex')->__('Short Content'),
            'post_content'  => Mage::helper('searchindex')->__('Post Content'),
            'tags'          => Mage::helper('searchindex')->__('Tags'),
            'category'      => Mage::helper('searchindex')->__('Category Name'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('blog/post')->getCollection();
        $collection->addFieldToFilter('status', 1);
        $collection->addStoreFilter(Mage::app()->getStore()->getId());

        $this->joinMatched($collection, 'main_table.post_id');

        return $collection;
    }
}