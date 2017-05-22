<?php
class Angara_Angaracart_Model_Cart_Observer extends Mage_Core_Model_Abstract
{
	function __construct(){}
		
	public function checkoutCartProductAddAfter($observer)
    {
		//var_dump($observer->getQuoteItem())
		if(Mage::getSingleton('checkout/session')->getCartInit()){
			Mage::getSingleton('checkout/session')->setCartInit(false);
			//$quote = Mage::getSingleton('checkout/session')->getQuote();
			$quote = $observer->getQuoteItem()->getQuote();
			$coupon = $this->_getAutoApplyCoupon($quote);
			if($coupon == ''){
				//	Check if user is a mobile theme or mobile user
				$isMobile = Mage::helper('function')->isMobile();
				if($isMobile){	//	Mobile browser
					$coupon = Mage::helper('function')->getMobileCoupon();	
				}else{
					$coupon = Mage::helper('function')->getDefaultCoupon();	
				}
			}
			$quote->setCouponCode($coupon)->save();
		}
	}
	
	public function checkoutQuoteInit($observer)
    {
		Mage::getSingleton('checkout/session')->setCartInit(true);
		# todo improve code as the event is firing continuously until the user opens the cart
		//$quote = $observer->getQuote();
		
	}
	
	private function _getAutoApplyCoupon($quote){
		// start apply default coupon on the cart if no any coupon apply by the customer - added by anil jain
		$coup_code = $quote->getCouponCode();
		if(empty($coup_code) && !isset($_SESSION['coupon_remove_by_user'])){
		//if(empty($coup_code)){
			if(empty($coup_code) || $coup_code == ''){
				$cartHelper = Mage::helper('checkout/cart');
				//	Code modified by Vaseem to get default coupon code values from backend
				if(Mage::getSingleton("checkout/session")->getData("offer_code")){
					$autoapply_coupon = Mage::getSingleton("checkout/session")->getData("offer_code");
				}
				else if(Mage::getModel('core/cookie')->get('bannertitlecookie')){
					$autoapply_coupon = Mage::helper('function')->getGoogleCustomerCoupon();		
				}else if(Mage::getSingleton('core/session')->getBannertitleSession()){
					switch (Mage::getSingleton('core/session')->getBannertitleSession()) {
						case 'pricegrabber':
							$autoapply_coupon = Mage::helper('function')->getPricegrabberCustomerCoupon();
							break;
						case 'shopping':
							$autoapply_coupon = Mage::helper('function')->getShoppingCustomerCoupon();
							break;
						case 'myshopping':
							$autoapply_coupon = Mage::helper('function')->getMyshoppingCustomerCoupon();
							break;
						case 'pronto':
							$autoapply_coupon = Mage::helper('function')->getPrantoCustomerCoupon();
							break;
						case 'amazon':
							$autoapply_coupon = Mage::helper('function')->getAmazonCustomerCoupon();
							break;
						case 'becomeshopper':
							$autoapply_coupon = Mage::helper('function')->getBecomeCustomerCoupon();
							break;
						case 'shopshopper':
							$autoapply_coupon = Mage::helper('function')->getShopbotCustomerCoupon();
							break;		
						// Code Added by Vaseem Starts
						case 'blue_diamond_rings':
							$autoapply_coupon = Mage::helper('function')->getBlueDiamondRingsCoupon();
							break;								
						case 'blue_diamond_studs':
							$autoapply_coupon = Mage::helper('function')->getBlueDiamondStudsCoupon();
							break;								
						case 'black_diamond_rings':
							$autoapply_coupon = Mage::helper('function')->getBlackDiamondRingsCoupon();
							break;								
						case 'black_diamond_studs':
							$autoapply_coupon = Mage::helper('function')->getBlackDiamondStudsCoupon();
							break;	
						case 'moissanite':
							$autoapply_coupon = Mage::helper('function')->getMoissaniteCoupon();
							break;
						case 'onyx':
							$autoapply_coupon = Mage::helper('function')->getOnyxCoupon();
							break;
						case 'google_paid':
							$autoapply_coupon = Mage::helper('function')->getGooglePaidCoupon();
							break;									
						// Code Added by Vaseem Ends	
					}
				}					
				//	Code modified by Vaseem to get default coupon code values from backend
			}
		}
		return $autoapply_coupon;
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
			//echo $hasAppraisal.'====='.$hasInsurance;exit;
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
				//var_dump($parentItem->getData());exit;
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
				//var_dump($parentItem->getData());exit;
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
		//$appraisal = Mage::getModel('catalog/product')->loadByAttribute('sku',$app_sku);
		
		$appraisal = Mage::getModel('catalog/product');
		$appraisal->load($appraisal->getIdBySku($app_sku));
		
		if ($appraisal->getId()) {
			$info = new Varien_Object();
			$info->setQty(1);
			
			$option = $appraisal->getProductOptionsCollection()->addFieldToFilter('title','For')->getFirstItem();
			//Code added by Vaseem for 241
			if(!strstr($orderItem->getProduct()->getSku(),'engraving')){
				$appraisalOptions = array(
					$option->getId() => $orderItem->getProduct()->getSku()
				);
			}
			//Code added by Vaseem for 241
			$info->setOptions($appraisalOptions);
			
			$info->setRelatedItem($orderItem->getId());
			//$orderItem->setRelatedItem($itemId);
			
			
			Mage::getSingleton("checkout/cart")->addProduct($appraisal, $info)->save();
		}
		return;
	}
	/*Added by Saurabh for insurance module for issue 0000248*/
	private function _addInsurance($orderItem){
		$app_sku = 'INS001';//Mage::getStoreConfig('angaracart/general/appraisalsku');
		$insurance = Mage::getModel('catalog/product');
		$ins_product=$insurance->load($insurance->getIdBySku($app_sku));
		
		$info = new Varien_Object();
		$info->setQty(1);
		$option = $insurance->getProductOptionsCollection()->addFieldToFilter('title','For')->getFirstItem();
		
		//Code added by Vaseem for 241
		if(!strstr($orderItem->getProduct()->getSku(),'engraving')){
			$insuranceOptions = array(
				$option->getId() => $orderItem->getProduct()->getSku(),
			);
		}
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
						$amount=40;
					}
					else{
						$amount=$currencyPrice*0.1;
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
