<?php
class Angara_Newproducts_Model_Observer {
	
	public function catalogProductSaveAfter($observer) { 
		$_product = $observer->getEvent()->getProduct();
		$productId= $_product->getId();
			$isNew = $_product->getIsNewProduct();
			if($isNew==1 && $_product->getIsNewProductDate()==''):
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$product = Mage::getModel('catalog/product')->load($productId);
				$product->setIsNewProductDate(now())->save();
			endif;
	}
}
?> 