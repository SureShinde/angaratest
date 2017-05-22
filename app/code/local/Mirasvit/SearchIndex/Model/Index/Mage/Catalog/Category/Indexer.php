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


class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Category_Indexer extends Mirasvit_SearchIndex_Model_Indexer_Abstract
{
    protected function _getSearchableEntities($storeId, $entityIds, $lastEntityId, $limit = 100)
    {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addFieldToFilter('is_active', 1)->addNameToResult();

        $collection->getSelect()->where('e.entity_id > ?', $lastEntityId)
            ->limit($limit)
            ->order('e.entity_id');

        foreach ($this->getIndexModel()->getAttributes() as $attr => $weight) {
            $collection->addAttributeToSelect($attr)
                ->addAttributeToSort($attr, 'ASC');
        }

        if ($entityIds) {
            $collection->addFieldToFilter('entity_id', array('in' => $entityIds));
        }

        return $collection;
    }
}