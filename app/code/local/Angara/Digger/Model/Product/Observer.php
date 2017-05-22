<?php
class Angara_Digger_Model_Product_Observer extends Mage_Core_Model_Abstract
{
	public function redirectProductView($observer){
		$id = Mage::app()->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($id);
		if($product->getTypeId() == 'simple' && ($product->getVisibility() == 3 || $product->getVisibility() == 2)){
		  $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId()); 
		  if(!empty($parentIds)){
			$parentProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
			$attributes = $parentProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($parentProduct);
			$extraParams = array();
			foreach ($attributes as $attribute){
				foreach ($attribute['values'] as $value){
					$childValue = $product->getData($attribute['attribute_code']);
					if ($value['value_index'] == $childValue){
						$extraParams[] = $attribute['attribute_code'] .'='. $value['value_index'];
					}
				}
			}
			$extraParams = '?'.join('&',$extraParams);			
			Mage::app()
			  ->getResponse()
			  ->setRedirect($parentProduct->getProductUrl().$extraParams, 301)
			  ->sendResponse();
		  }
		}
	}
	
	public function redirectAjaxProductView($observer){
		$id = Mage::app()->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($id);
		if( $product->getTypeId() == 'simple' && $product->getVisibility() == 3){
		  $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId()); 
		  if(!empty($parentIds)){
				
			Mage::app()
			  ->getResponse()
			  ->setRedirect("$parentIds[0]", 301)
			  ->sendResponse();
			   exit();	 
		  
		  }
		}
	}
	
	public function requireReIndex($observer) {
		try{
			$productIds = $observer->getProductIds();
			Mage::getResourceSingleton('catalogsearch/fulltext')->updateIndex(null,$productIds);
		} catch(Exception $e) {
			Mage::logException($e);
		}
	}
	
	public function requireReIndexOfParent($observer) {
		try{
			$product = $product = $observer->getProduct();
			if($product && $product->getId()) {
				$productIds = array($product->getId());
				Mage::getResourceSingleton('catalogsearch/fulltext')->updateIndex(null,$productIds);
			}
		} catch(Exception $e) {
			Mage::logException($e);
		}
	}
	
}
?>