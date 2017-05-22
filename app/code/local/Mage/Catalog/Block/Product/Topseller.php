<?php
class Mage_Catalog_Block_Product_Topseller extends Mage_Catalog_Block_Product
{
	/* This function returns lowest product price of a catalog */
	public function getLowestCatalogPrice($cid){
		try{
			/*$categoryModel = Mage::getModel('catalog/category')->load($cid);
			$productColl = Mage::getModel('catalog/product')->getCollection()
															->addCategoryFilter($categoryModel);
															
			$productColl->getSelect()->reset('order');
			$productColl->getSelect()->order('min_price','asc');
			$productColl->getSelect()->limit(1);
			
			$lowestProductPrice = $productColl->getFirstItem()->getMinimalPrice();
			return $lowestProductPrice;*/
			
			$layer = Mage::getSingleton('catalog/layer');
			$category = Mage::getModel('catalog/category')->load($cid);
							if ($category->getId()) {
								$origCategory = $layer->getCurrentCategory();
								$layer->setCurrentCategory($category);
							}
			$collection = $layer->getProductCollection();
			$collection->getSelect()->reset('order');
			$collection->getSelect()->order('minimal_price','asc');
			$collection->getSelect()->limit(1);
			return $collection->getFirstItem()->getMinimalPrice();
		}
		catch(Exception $e){
			Mage::logException('Problem finding lowest product price in a Catalog for ID: '.$cid);
			return false;
		}
	}

}