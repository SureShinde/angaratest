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


class Mirasvit_SearchIndex_Model_Type_Simpleforum_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $collection = Mage::getModel('forum/post')->getCollection();
        $collection->addStoreFilterToCollection($storeId);

        if ($entityIds) {
            $collection->addFieldToFilter('main_table.post_id', array('in' => $entityIds));
        }

        $tblTopics = Mage::getSingleton('core/resource')->getTableName('forum/topic');
        $collection->getSelect()->joinLeft(array('tbl_topics' => $tblTopics), 'main_table.parent_id = tbl_topics.topic_id', array('topic_title' => 'title', 'topic_description' => 'description'));
        $collection->getSelect()->joinLeft(array('tbl_topics_forum' => $tblTopics), 'tbl_topics.parent_id = tbl_topics_forum.topic_id', array('forum_title' => 'title', 'forum_description' => 'description'));

        $collection->getSelect()->where('main_table.post_id > ?', $lastEntityId)
            ->limit($limit)
            ->order('main_table.post_id');

        return $collection;
    }
}