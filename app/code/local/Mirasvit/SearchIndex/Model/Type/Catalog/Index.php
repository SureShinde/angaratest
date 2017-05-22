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
 * Represent Index model for Magento Catalog
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Model_Type_Catalog_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'catalog';

    public function getCode()
    {
        return self::INDEX_CODE;
    }

    public function getPrimaryKey()
    {
        return 'product_id';
    }

    /**
     * Catalog always enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return true;
    }

    public function getAvailableAttributes()
    {
        $result = array();
        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $productAttributeCollection->addIsSearchableFilter();
        foreach ($productAttributeCollection as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

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