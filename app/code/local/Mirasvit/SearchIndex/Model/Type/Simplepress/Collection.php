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


class Mirasvit_SearchIndex_Model_Type_Simplepress_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function __construct($resource = null)
    {
        $this->_construct();
        $this->_resource = Mage::getSingleton('core/resource');
        $this->setConnection(Mage::helper('searchindex/index')->getIndexModel('simplepress')->getConnection());
        $this->_initSelect();
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(array('forum' => $this->getForumTable()), 'forum.forum_id = main_table.forum_id');
        // $this->getSelect()->where('main_table.post_type=?', 'post')
        //     ->where('main_table.post_status=?', 'publish');

        return $this;
    }

    public function getMainTable()
    {
        return Mage::getStoreConfig('searchindex/simplepress/table_prefix').'topics';
    }

    public function getForumTable()
    {
        return Mage::getStoreConfig('searchindex/simplepress/table_prefix').'forums';
    }
}