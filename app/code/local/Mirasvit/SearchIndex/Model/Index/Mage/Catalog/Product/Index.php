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


class Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Product_Index extends Mirasvit_SearchIndex_Model_Index
{
    public function getBaseGroup()
    {
        return 'Magento';
    }
    
    public function getBaseTitle()
    {
        return 'Catalog Products';
    }

    public function getPrimaryKey()
    {
        return 'product_id';
    }

    public function getIsActive()
    {
        return true;
    }

    public function isLocked()
    {
        return true;
    }

    public function getFieldsets()
    {
        return array(
            'mage_catalog_product_additional'
        );
    }

    public function getAvailableAttributes()
    {
        $result = array();
        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $productAttributeCollection->addIsSearchableFilter();
        foreach ($productAttributeCollection as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        $result['category'] = 'Category';

        return $result;
    }

    /**
     * After process search, we save count search resutls to query
     *
     * @return Mirasvit_SearchIndex_Model_Index_Catalog
     */
    protected function _processSearch()
    {
        parent::_processSearch();

        $query  = Mage::helper('catalogsearch')->getQuery();
        $query->setNumResults(count($this->_matchedIds))
            ->setIsProcessed(1)
            ->save();

        return $this;
    }

    public function getCollection()
    {
        $matchedIds = $this->getMatchedIds();
        $collection = Mage::getModel('catalogsearch/layer')->getProductCollection();
        // $this->joinMatched($collection);

        return $collection;
    }
}