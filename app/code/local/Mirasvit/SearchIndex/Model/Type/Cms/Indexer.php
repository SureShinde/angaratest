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
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Model_Type_Cms_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);

        $ignore = explode(',', Mage::getStoreConfig('searchindex/cms/ignore'));

        if (count($ignore) > 0) {
            $collection->addFieldToFilter('identifier', array('nin' => $ignore));
        }

        if ($entityIds) {
            $collection->addFieldToFilter('page_id', array('in' => $entityIds));
        }

        $collection->getSelect()->where('main_table.page_id > ?', $lastEntityId)
            ->limit($limit)
            ->order('main_table.page_id');

        return $collection;
    }
}