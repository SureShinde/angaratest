<?php
class Angara_Promotions_Model_Cart extends Mage_Checkout_Model_Cart
{
	public function getSalableItems(){
		$salableItems = array();
		$items = $this->getQuote()->getAllVisibleItems();
		foreach($items as $item){
			try{
				$_itemIsSalable = true;
				if($additionalOptions = $item->getOptionByCode('additional_options')) {
					$additionalOptions = unserialize($additionalOptions->getValue());
					foreach($additionalOptions as $additionalOption){
						if($additionalOption['label'] == 'ItemType' && ($additionalOption['value'] == 'Free' || $additionalOption['value'] == 'Add-on')){
							 $_itemIsSalable = false;
						}
					}
				}
				if(preg_match('/AGIF/i',$item->getSku())){
					$_itemIsSalable = false;
				}
				if($_itemIsSalable)
					$salableItems[] = $item;
			}
			catch (Exception $e) {
				Mage::logException($e);
			}
		}
		return $salableItems;
	}
    
	public function getFreeItems(){
		$freeItems = array();
		$items = $this->getQuote()->getAllItems();
		foreach($items as $item){
			try{
				if($additionalOptions = $item->getOptionByCode('additional_options')) {
					$additionalOptions = unserialize($additionalOptions->getValue());
					foreach($additionalOptions as $additionalOption){
						if($additionalOption['label'] == 'ItemType' && $additionalOption['value'] == 'Free'){
							$freeItems[] = $item; 
						}
					}
				}
			}
			catch (Exception $e) {
				Mage::logException($e);
			}
		}	
		return $freeItems;
	}
	
	public function addFreeItem($product){
		$additionalOptions[] = array(
		 'label' => 'ItemType',
		 'value' => 'Free'
		);    
		$product->addCustomOption('additional_options', serialize($additionalOptions));
		$info = new Varien_Object();
		$info->setQty(1);
		$this->addProduct($product, $info);
	}
	
	public function applyPromotion($code){
		$this->getQuote()->setCouponCode($code);
		/* $this->save(); */
		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	}
	
	public function removeFreeItem($id){
		$this->removeItem($id);
	}

	public function removeAllFreeItems(){
		$freeItems = $this->getFreeItems();
		foreach($freeItems as $freeItem){
			$this->removeFreeItem($freeItem->getId());
		}
	}
}	 