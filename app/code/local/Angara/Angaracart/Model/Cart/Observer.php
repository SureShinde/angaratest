<?php
class Angara_Angaracart_Model_Cart_Observer extends Mage_Core_Model_Abstract
{
	function __construct(){}
	
	protected function _saveByoDiamond($diamond)
    {
        // Load up a product that has the custom options and get the attributes, in this case we just have one custom option
       // This custom option is a text field and our end goal is to populate that with sku-product name
	   	$_productId = Mage::getModel('catalog/product')->getIdBySku('ANGCBYO007');
        $_product   = Mage::getModel('catalog/product')->load($_productId);
        if($_product->getId()){
			$_product->setPrice($diamond['diamond']['total_sales_price_in_currency']);
			$_product->setIsSuperMode(true);
			/*$additionalOptions[] = array(
				'label' => 'Information',
				'value' => $diamond['diamond']['shape'].' Diamond '.$diamond['diamond']['size'].'ct '.$diamond['diamond']['color'].' '.$diamond['diamond']['clarity'].' '.$diamond['diamond']['cut'].' cut'
			);*/
			if($diamond['diamond']['shape'] != ''){
				$additionalOptions[] = array(
					'label' => 'Shape',
					'value' => $diamond['diamond']['shape']
				);
			}
			if($diamond['diamond']['size'] != ''){
				$additionalOptions[] = array(
					'label' => 'Size',
					'value' => $diamond['diamond']['size'].'ct'
				);
			}
			if($diamond['diamond']['color'] != ''){
				$additionalOptions[] = array(
					'label' => 'Color',
					'value' => $diamond['diamond']['color']
				);
			}
			if($diamond['diamond']['clarity'] != ''){
				$additionalOptions[] = array(
					'label' => 'Clarity',
					'value' => $diamond['diamond']['clarity']
				);
			}
			if($diamond['diamond']['cut'] != ''){
				$additionalOptions[] = array(
					'label' => 'Cut',
					'value' => $diamond['diamond']['cut']
				);
			}
			$additionalOptions[] = array(
				'label' => 'SecureInformation',
				'value' => serialize($diamond)
			);
			/*$additionalOptions[] = array(
				'label' => 'Information',
				'value' => serialize($diamond)
			);*/    
			$_product->addCustomOption('additional_options', serialize($additionalOptions));
			$stockData = array(
                'manage_stock' => 1,
                'is_in_stock'  => 1,
                'qty'          => 1
            );
            $_product->setStockData($stockData);
			$info = new Varien_Object();
			$info->setQty(1);
			$info->setOptions($diamond);
			//Mage::getSingleton("checkout/cart")->addProduct($_product, $info)->save();
			try{
				// Load the cart, add the product, and save!  You can also update the cart to say it was updated, but that is not required
				$cart = Mage::getSingleton('checkout/cart');
				$cart->addProduct($_product, $info);
				$addedItem = Mage::getSingleton('checkout/session')
				   ->getQuote()
				   ->getItemsCollection()
				   ->getLastItem();
				$addedItem->setCustomPrice($diamond['diamond']['total_sales_price_in_currency']);
				$addedItem->setOriginalCustomPrice($diamond['diamond']['total_sales_price_in_currency']);
				$addedItem->getProduct()->setIsSuperMode(true);
				$addedItem->save();
				$cart->save();
				Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
				Mage::getSingleton('core/session')->unsByoRingSession();
				Mage::getSingleton('core/session')->unsByoPendantSession();
			} catch( Exception $e) {
				Mage::logException($e);
				Mage::throwException('Diamond Not Added');
			}
		}

    }
		
	public function checkoutCartProductAddAfter($observer)
    {
		
	}
	
	public function checkoutQuoteInit($observer)
    {
		Mage::getSingleton('checkout/session')->setCartInit(true);
		# todo improve code as the event is firing continuously until the user opens the cart
		//$quote = $observer->getQuote();		
	}
	
	public function checkoutCartUpdateItemsAfter($observer)
    {
		//$info = $orderItem->getProductOptionByCode('info_buyRequest');
		$this->_resetAppraisal();
		/*Added by Saurabh for insurance module for issue 0000248*/
		$this->_resetInsurance();
		/*End Added by Saurabh for insurance module for issue 0000248*/
    }
	
	public function checkoutCartRemoveItemBefore($observer){
		$itemId = $observer->getItemDeleting();
		$item = Mage::getSingleton('checkout/session')->getQuote()->getItemById($itemId);
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		if($item){
			$info = $item->getBuyRequest();
			$parentItemId = $info->getRelatedItem();
			if(isset($parentItemId)){
				$parentItem = Mage::getSingleton('checkout/session')->getQuote()->getItemById($parentItemId);
				if($parentItem){
					$parentInfo = $parentItem->getBuyRequest();
						$parentInfo->setAppraisal(NULL);	
					$observer->getCart()->updateItem($parentItemId,$parentInfo);
					Mage::getSingleton('checkout/session')->getQuote()->save();
				}
			}
			$parentItemId = $info->getRelatedItem2();
			if(isset($parentItemId)){
				$parentItem = Mage::getSingleton('checkout/session')->getQuote()->getItemById($parentItemId);
				if($parentItem){
					$parentInfo = $parentItem->getBuyRequest();
						$parentInfo->setInsurance(NULL);
					$observer->getCart()->updateItem($parentItemId,$parentInfo);
					Mage::getSingleton('checkout/session')->getQuote()->save();
				}
			}
			$hasAppraisal = $info->getData('appraisal');
			//if(isset($hasAppraisal)){
				if($this->_hasAppraisal($item->getId(), $items)){
					$this->_removeAppraisalByParentItem($item, $items);
				}
			//}
			$hasInsurance = $info->getData('insurance');
			//if(isset($hasInsurance)){
				if($this->_hasInsurance($item->getId(), $items)){
					$this->_removeInsuranceByParentItem($item, $items);
				}
			//}
		
		}
		
		$this->_resetAppraisal();
		$this->_resetInsurance();
	}
	
	public function checkoutCartAddProductComplete($observer){
		$this->_resetAppraisal();
		/*Added by Saurabh for insurance module for issue 0000248*/
		$this->_resetInsurance();
		/*End Added by Saurabh for insurance module for issue 0000248*/		
		
		
		$product = $observer->getProduct();
		if($product->getIsBuildYourOwn()){
			$diamond = $product->getByoModel()->getDiamondSelected();
			if($diamond){
				$this->_saveByoDiamond($diamond);
			}
			else{
				Mage::throwException('Diamond Information Not Found');
			}
		}
		
	}
	
	private function _resetAppraisal(){
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		foreach($items as $item){
			$info = $item->getBuyRequest();
			$hasAppraisal = $info->getData('appraisal');
			if(isset($hasAppraisal)){
				if(!$this->_hasAppraisal($item->getId(), $items)){
					$this->_addAppraisal($item);
				}
				$this->_resetQty($item,$items);
			}
			$hasParent = $info->getData('related_item');
			if(isset($hasParent)){
				if(!$this->_hasItem($hasParent, $items)){
					$this->_removeAppraisal($item->getId());
				}
			}
		}
		
		return;
	}
	
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _resetInsurance(){
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
						
		foreach($items as $item){
			$info = $item->getBuyRequest();
			$hasInsurance = $info->getData('insurance');
				if(isset($hasInsurance)){
					if(!$this->_hasInsurance($item->getId(), $items)){
						$this->_addInsurance($item);
					}
				}
					
				//if($currentUrl==$urlCheck){
					if(Mage::getSingleton('customer/session')->isLoggedIn()){
						$this->_addInsuranceUpdate();
					}
					else{
						$this->_updateInsurance();	
					}	
				//}
				$this->_resetQty2($item,$items);
				$hasParent = $info->getData('related_item2');
				if(isset($hasParent)){
					if(!$this->_hasItem($hasParent, $items)){
						$this->_removeInsurance($item->getId());
					}
				}
			}
			return;
	}
	/*End Added by Saurabh for insurance module for issue 0000248*/
	
	private function _resetQty($parentItem,$items){
		$max_qty = $parentItem->getQty();
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item') == $parentItem->getId()){
				if($item->getQty() > $max_qty){
					$item->setQty($max_qty);
				}
			}
		}
		return;
	}
	
	private function _resetQty2($parentItem,$items){
		$max_qty = $parentItem->getQty();
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item2') == $parentItem->getId()){
				if($item->getQty() > $max_qty){
					$item->setQty($max_qty);
				}
			}
		}
		return;
	}
	
	private function _hasAppraisal($itemId, $items){
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item') == $itemId){
				return true;
			}
		}
		return false;
	}
	
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _hasInsurance($itemId, $items){
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item2') == $itemId){
				return true;
			}
		}
		return false;
	}
	/*End Added by Saurabh for insurance module for issue 0000248*/
	
	private function _hasItem($itemId, $items){
		foreach($items as $item){
			if($item->getId() == $itemId){
				return true;
			}
		}
		return false;
	}
	
	private function _removeAppraisal($itemId){
		Mage::getSingleton('checkout/session')->getQuote()->removeItem($itemId)->save();
		return;
	}
	
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _removeInsurance($itemId){
		Mage::getSingleton('checkout/session')->getQuote()->removeItem($itemId)->save();
		return;
	}
	/*End Added by Saurabh for insurance module for issue 0000248*/
	
	private function _removeAppraisalByParentItem($parentItem, $items){
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item') == $parentItem->getId()){
				$this->_removeAppraisal($parentItem->getId());
			}
		}
		return;
	}
	
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _removeInsuranceByParentItem($parentItem, $items){
		foreach($items as $item){
			$info = $item->getBuyRequest();
			if($info->getData('related_item2') == $parentItem->getId()){
				$this->_removeInsurance($parentItem->getId());
			}
		}
		return;
	}
	/*End Added by Saurabh for insurance module for issue 0000248*/
	
	private function _addAppraisal($orderItem){
		$app_sku = Mage::getStoreConfig('angaracart/general/appraisalsku');		
		$appraisal = Mage::getModel('catalog/product');
		$appraisal->load($appraisal->getIdBySku($app_sku));
		
		$additionalOptions[] = array(
		 'label' => 'ItemType',
		 'value' => 'Add-on'
		);    
		$appraisal->addCustomOption('additional_options', serialize($additionalOptions));
		
		if ($appraisal->getId()) {
			$info = new Varien_Object();
			$info->setQty(1);
			
			$option = $appraisal->getProductOptionsCollection()->addFieldToFilter('title','For')->getFirstItem();
			$_options = $orderItem->getProduct()->getTypeInstance(true)->getOrderOptions($orderItem->getProduct());
			$options_sku = '';
			foreach ($_options as $_option) {
				if(is_array($_option)){
					foreach ($_option as $_optionData) {
						if($_optionData['label'] == 'Ring Size'){
							$options_sku .= '_'.$_optionData['print_value'];
						}
						else if($_optionData['label'] == 'Engraving'){
							$options_sku .= '_'.preg_replace('/[^a-zA-Z0-9]/', '', substr($_optionData['print_value'],0, strpos($_optionData['print_value'], ' (')));
						}
					}
				}
			}
			//Code added by Vaseem for 241
			//if(!strstr($orderItem->getProduct()->getSku(),'engraving')){
				$appraisalOptions = array(
					$option->getId() => $orderItem->getProduct()->getSku().$options_sku
				);
			//}
			//Code added by Vaseem for 241
			$info->setOptions($appraisalOptions);
			
			$info->setRelatedItem($orderItem->getId());			
			Mage::getSingleton("checkout/cart")->addProduct($appraisal, $info)->save();
		}
		return;
	}
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _addInsurance($orderItem){
		$app_sku = 'INS001';//Mage::getStoreConfig('angaracart/general/appraisalsku');
		$insurance = Mage::getModel('catalog/product');
		$ins_product=$insurance->load($insurance->getIdBySku($app_sku));
		
		$additionalOptions[] = array(
		 'label' => 'ItemType',
		 'value' => 'Add-on'
		);    
		$ins_product->addCustomOption('additional_options', serialize($additionalOptions));
		
		$info = new Varien_Object();
		$info->setQty(1);
		$option = $insurance->getProductOptionsCollection()->addFieldToFilter('title','For')->getFirstItem();
		$_options = $orderItem->getProduct()->getTypeInstance(true)->getOrderOptions($orderItem->getProduct());
			$options_sku = '';
			foreach ($_options as $_option) {
				if(is_array($_option)){
					foreach ($_option as $_optionData) {
						if($_optionData['label'] == 'Ring Size'){
							$options_sku .= '_'.$_optionData['print_value'];
						}
						else if($_optionData['label'] == 'Engraving'){
							$options_sku .= '_'.preg_replace('/[^a-zA-Z0-9]/', '', substr($_optionData['print_value'],0, strpos($_optionData['print_value'], ' (')));
						}
					}
				}
			}
		//Code added by Vaseem for 241
		//if(!strstr($orderItem->getProduct()->getSku(),'engraving')){
			$insuranceOptions = array(
				$option->getId() => $orderItem->getProduct()->getSku().$options_sku,
			);
		//}
		//Code added by Vaseem for 241
		
		$info->setOptions($insuranceOptions);
		$info->setRelatedItem2($orderItem->getId());
		$cart=Mage::getSingleton("checkout/cart")->addProduct($ins_product, $info);
		$cart->save();

		$items=Mage::getSingleton('checkout/cart')->getItems();
		if(!empty($items)){
			foreach($items as $item){
				if($item->getSku() == $app_sku){
					$info = $item->getBuyRequest();
					$parentItemId = $info->getRelatedItem2();
					$parentItem = Mage::getModel('sales/quote_item')->load($parentItemId);
					$currencyPrice=Mage::helper('core')->currency($parentItem->getPrice(),false,false);
					if($currencyPrice< 200){
						$amount = 50;
					}
					else{
						$amount=$currencyPrice*0.20;	//	S:VA	Set Warranty Percentage
						$amount=round($amount);
					}	
					$item->setCustomPrice($amount);
					$item->setOriginalCustomPrice($amount);
					$item->getProduct()->setIsSuperMode(true);
					$qty = $parentItem->getQty();
					$item->setQty($qty);
					$item->save();
				}
			}
		}
		return;
	}
	
	private function _updateInsurance(){
		$items = Mage::getSingleton('checkout/cart')->getItems();
		$skuId = 'INS001';
		foreach($items as $_it){
			if($_it->getSku()==$skuId){
				$info = $_it->getBuyRequest();
				$parentItemId = $info->getRelatedItem2();
				$parentItem = Mage::getSingleton('checkout/session')->getQuote()->getItemById($parentItemId);
				if($parentItem){
					$qty = $parentItem->getQty();
					$_it->setQty($qty);
					$_it->save();
				}
			}	
		}
		return;
	}
	
	private function _addInsuranceUpdate(){
		$items = Mage::getSingleton('checkout/cart')->getItems();
		$skuId = 'INS001';
		foreach($items as $_it){
			if($_it->getSku()==$skuId){
				$info = $_it->getBuyRequest();
				$parentItemId = $info->getRelatedItem2();
				$parentItem = Mage::getSingleton('checkout/session')->getQuote()->getItemById($parentItemId);
				if($parentItem){
					$qty = $parentItem->getQty();
					$_it->setQty($qty);
					$_it->save();
				}
			}
		}
		return;
	}
	/*End Added by Saurabh for insurance module for issue 0000248*/	
}