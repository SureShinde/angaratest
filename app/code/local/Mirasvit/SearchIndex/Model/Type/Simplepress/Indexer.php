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


/*******************************************
Mirasvit
This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
If you wish to customize this module for your needs
Please refer to http://www.magentocommerce.com for more information.
@category Mirasvit
@copyright Copyright (C) 2012 Mirasvit (http://mirasvit.com.ua), Vladimir Drok <dva@mirasvit.com.ua>, Alexander Drok<alexander@mirasvit.com.ua>
*******************************************/

class Mirasvit_SearchIndex_Model_Type_Simplepress_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $collection = Mage::getModel('searchindex/type_simplepress_collection');

        if ($entityIds) {
            $collection->addFieldToFilter('topic_id', array('in' => $entityIds));
        }

        $collection->getSelect()
            ->where('main_table.topic_id > ?', $lastEntityId)
            ->joinLeft(array('posts' => $this->getPostTable()), 'posts.topic_id = main_table.topic_id', array('GROUP_CONCAT(posts.post_content) AS post_content'))
            ->limit($limit)
            ->group('main_table.topic_id')
            ->order('main_table.topic_id');

        return $collection;
    }

    public function getPostTable()
    {
        return Mage::getStoreConfig('searchindex/simplepress/table_prefix').'posts';
    }
}