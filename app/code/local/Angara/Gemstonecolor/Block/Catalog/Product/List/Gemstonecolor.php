<?php

class Angara_Gemstonecolor_Block_Catalog_Product_List_Gemstonecolor extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_columnCount = 4;

    protected $_items;

    protected $_itemCollection;

    protected $_itemLimits = array();

    protected function _prepareData()
    {
        $product = Mage::registry('product');
		if($product){
			/* @var $product Mage_Catalog_Model_Product */
			$this->_itemCollection = $product->getGemstonecolorProductCollection()
				->setPositionOrder()
				->addStoreFilter()
				->setPageSize(5)
				;
			if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
				/*Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
					Mage::getSingleton('checkout/session')->getQuoteId()
				);*/
	
				$this->_addProductAttributesAndPrices($this->_itemCollection);
			}
			
			//Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
	
			/* In case we need to limit the matching bands
			if ($this->getItemLimit('gemstonecolor') > 0) {
				$this->_itemCollection->setPageSize($this->getItemLimit('gemstonecolor'));
			}*/
	
			$this->_itemCollection->load();
	
			/**
			 * Event needed In case
			 * Updating collection with desired items
			 */
			/*Mage::dispatchEvent('catalog_product_gemstonecolor', array(
				'product'       => $product,
				'collection'    => $this->_itemCollection,
				'limit'         => $this->getItemLimit()
			));*/
	
			foreach ($this->_itemCollection as $product) {
				$product->setDoNotUseCategoryId(true);
			}
		}

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }
	
	public function getMainProduct(){
		return Mage::registry('product');
	}

    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

    public function getRowCount()
    {
        return ceil(count($this->getItemCollection()->getItems())/$this->getColumnCount());
    }

    public function setColumnCount($columns)
    {
        if (intval($columns) > 0) {
            $this->_columnCount = intval($columns);
        }
        return $this;
    }

    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }

    /**
     * Set how many items we need to show in gemstonecolor block
     * Notice: this parameter will be also applied
     *
     * @param string $type
     * @param int $limit
     * @return Mage_Catalog_Block_Product_List_Gemstonecolor
     */
    public function setItemLimit($type, $limit)
    {
        if (intval($limit) > 0) {
            $this->_itemLimits[$type] = intval($limit);
        }
        return $this;
    }

    public function getItemLimit($type = '')
    {
        if ($type == '') {
            return $this->_itemLimits;
        }
        if (isset($this->_itemLimits[$type])) {
            return $this->_itemLimits[$type];
        }
        else {
            return 0;
        }
    }
	
}
