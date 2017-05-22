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


class Mirasvit_SearchIndex_Model_Type_Simpleforum_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'simpleforum';

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
            'forum_title'       => Mage::helper('searchindex')->__('Forum Title'),
            'forum_description' => Mage::helper('searchindex')->__('Forum Description'),
            'topic_title'       => Mage::helper('searchindex')->__('Topic Title'),
            'topic_description' => Mage::helper('searchindex')->__('Topic Description'),
            'post'              => Mage::helper('searchindex')->__('Post Content'),
        );

        return $result;
    }

    public function getCollection()
    {
        $collection = Mage::getModel('forum/post')->getCollection();
        $collection->addFieldToFilter('status', 1);
        
        $this->joinMatched($collection, 'main_table.post_id');

        return $collection;
    }
}