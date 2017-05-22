<?php
class Angara_Omniture_Model_Sitecatalyst_Observer extends Mage_Core_Model_Abstract
{
	public function customerLogin($observer)
    {
		Mage::getSingleton('core/session')->setJustLoggedIn(1);
		//var_dump($observer); exit;
		 
	}
	
	public function checkoutCartRemoveItemBefore($observer){
		$itemId = $observer->getItemDeleting();
		if($itemId){
			$item = Mage::getSingleton('checkout/session')->getQuote()->getItemById($itemId);
			if($item)
				Mage::getSingleton('core/session')->setItemRemoved($item->getProduct()->getData('sku'));
		}
	}
	
	public function checkoutCartRemoveItemAfter($observer){
		//$itemId = $observer->getItemDeleted();
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		if(count($items) == 0){
			Mage::getSingleton('core/session')->unsCartOpened();
		}
	}
	
	public function checkoutCartProductAddAfter($observer){
		//echo Mage::getSingleton('core/session')->getCartOpened(); 
		if(!Mage::getSingleton('core/session')->getCartOpened()){
			Mage::getSingleton('core/session')->setCartOpened(true);
			Mage::getSingleton('core/session')->setSCOpened(true);
		}
		else{
			Mage::getSingleton('core/session')->unsSCOpened();
		}
		if(Mage::getSingleton('core/session')->getItemAdded()){
			Mage::getSingleton('core/session')->setItemAdded(Mage::getSingleton('core/session')->getItemAdded().',;'.$observer->getproduct()->getData('sku'));
		}
		else{
			//Mage::getSingleton('core/session')->setItemAdded(Mage::getSingleton('core/session')->getItemAdded().',;'.$observer->getproduct()->getData('sku'));
			Mage::getSingleton('core/session')->setItemAdded($observer->getProduct()->getData('sku'));
		}
		//exit;
	}
	
	public function wishlistAddProduct($observer){
		Mage::getSingleton('core/session')->setWishlistItemAdded(1);
	}
	
	public function checkoutCartUpdateItemsAfter($observer){
		/*$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
		if(count($items) == 0){
			Mage::getSingleton('core/session')->unsCartOpened();
		}
		*/
		// # todo need to implement this
	}
	
}
