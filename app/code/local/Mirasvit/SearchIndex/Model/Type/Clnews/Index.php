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


class Mirasvit_SearchIndex_Model_Type_Clnews_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'clnews';

    public function getCode()
    {
        return self::INDEX_CODE;
    }

    public function getPrimaryKey()
    {
        return 'news_id';
    }

    public function getAvailableAttributes()
    {
         $result = array(
            'title'            => Mage::helper('searchindex')->__('Title'),
            'short_content'    => Mage::helper('searchindex')->__('Short Description'),
            'full_content'     => Mage::helper('searchindex')->__('Full Description'),
            'author'           => Mage::helper('searchindex')->__('Author'),
            'meta_keywords'    => Mage::helper('searchindex')->__('Meta Keywords'),
            'meta_description' => Mage::helper('searchindex')->__('Meta Description'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('clnews/news')->getCollection();
        $collection->addFieldToFilter('status', 1);
        
        $this->joinMatched($collection, 'main_table.news_id');

        return $collection;
    }
}