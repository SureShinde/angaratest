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


class Mirasvit_SearchAutocomplete_Block_Result extends Mage_Catalog_Block_Product_Abstract
{
    protected $_collections = array();
    protected $_indexes     = array();

    public function _prepareLayout()
    {
        $this->setTemplate('searchautocomplete/autocomplete/result.phtml');

        return parent::_prepareLayout();
    }

    public function init()
    {
        $this->_prepareIndexes();
    }

    protected function _prepareIndexes()
    {
        Mage::dispatchEvent('searchautocomplete_prepare_collection');

        $this->_indexes = Mage::helper('searchautocomplete')->getIndexes(false);

        $maxCount   = Mage::getStoreConfig('searchautocomplete/general/max_results');
        $perIndex   = ceil($maxCount / count($this->_indexes));
        $sizes      = array();
        $additional = 0;
        foreach ($this->_indexes as $index => $label) {
            $st = microtime(true);
            $size = $this->getCollection($index)->getSize();
            // echo __LINE__.'   '.round(microtime(true) - $st, 4).'<br>';
            if ($size >= $perIndex) {
                $sizes[$index] = $perIndex;
            } else {
                $additional = $perIndex - $size;
                $sizes[$index] = $size;
            }

            if ($size == 0) {
                unset($this->_indexes[$index]);
            }
        }
        $additional = $this->_indexes ? ceil($additional / count($this->_indexes)) : 0;
        foreach ($this->_indexes as $index => $label) {
            $sizes[$index] += $additional;
        }

        foreach ($sizes as $index => $size) {
            $this->getCollection($index)->setPageSize($size);
        }
    }

    public function getIndexes()
    {
        return $this->_indexes;
    }

    public function getCollection($index)
    {
        if (!isset($this->_collections[$index])) {
            if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
                $model = Mage::helper('searchindex/index')->getIndex($index);
                $collection = $model->getCollection();
            } else {
                $collection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
            }
            $collection->getSelect()->order('relevance desc');

            if ($index == 'catalog' && $this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                $collection->addCategoryFilter($category);
            }

            $this->_collections[$index] = $collection;
        }

        return $this->_collections[$index];
    }

    public function getItemHtml($index, $item)
    {
        $block = Mage::app()->getLayout()->createBlock('searchautocomplete/result')
            ->setTemplate('searchautocomplete/autocomplete/index/'.str_replace('_', '/', $index).'.phtml')
            ->setItem($item);

        return $block->toHtml();
    }
}