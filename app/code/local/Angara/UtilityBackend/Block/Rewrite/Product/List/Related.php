<?php
/*
	S:VA	Block Rewrite
*/
class Angara_UtilityBackend_Block_Rewrite_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
   
    protected function _prepareData()
    {
		/* parent::_prepareData(); */

		if(Mage::registry('product')){
			$product = Mage::registry('product');
		}
		else{		
			$productId = $this->getRequest()->getParam('productId');
			if($productId){	
				$product = Mage::getModel('catalog/product')->load($productId);
			}
		}
        //$product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        if($product){
			$this->_itemCollection = $product->getRelatedProductCollection()
				->addAttributeToSelect('required_options')
				->setPositionOrder()
				->addStoreFilter();
	
			if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
				Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
					Mage::getSingleton('checkout/session')->getQuoteId()
				);
				$this->_addProductAttributesAndPrices($this->_itemCollection);
			}
	//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_itemCollection);			//	include in stock products only
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
	
			$this->_itemCollection->load();
	
			foreach ($this->_itemCollection as $product) {
				$product->setDoNotUseCategoryId(true);
			}
		}
        return $this;
    }   
}