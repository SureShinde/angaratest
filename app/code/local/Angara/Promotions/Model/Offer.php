<?php
class Angara_Promotions_Model_Offer extends Mage_Core_Model_Abstract
{
    public function process($appliedRules){
		Mage::getSingleton('core/session')->setPromotionApplied(true);
		if(empty($appliedRules) || (count(Mage::getModel('promotions/cart')->getSalableItems()) == 0) ){
			Mage::getModel('promotions/cart')->removeAllFreeItems();
		}
		else{
			$freeItemsInCart = Mage::getModel('promotions/cart')->getFreeItems();
			$applicableFreeItemIds = $this->_getApplicableFreeItemIds($appliedRules);
			
			foreach($freeItemsInCart as $freeItemInCart){
				$freeItemInCartId = $freeItemInCart->getId();
				if(!in_array($freeItemInCartId, $applicableFreeItemIds)){
					Mage::getModel('promotions/cart')->removeFreeItem($freeItemInCartId);
				}
				else{
					if(($key = array_search($freeItemInCartId, $applicableFreeItemIds)) !== false) {
						unset($applicableFreeItemIds[$key]);
					}
				}
			}
			
			foreach($applicableFreeItemIds as $applicableFreeItemId){
				$applicableFreeProduct = Mage::getModel('catalog/product')->load($applicableFreeItemId);
				if($applicableFreeProduct->getId()){							
					Mage::getModel('promotions/cart')->addFreeItem($applicableFreeProduct);
				}
			}
		}
	}
	
	public function setDefaultOffer($platform){
		$channel = $this->getApplicableChannel();
		$coupon = Mage::getModel('promotions/coupon')->getDefaultCoupon($channel, $platform);
		if($coupon){
			$rule = Mage::getModel('salesrule/coupon')->load($coupon->getRuleId(), 'rule_id');
			if($rule){
				Mage::getSingleton('core/session')->setPromotionCode($rule->getCode());
				
				/* s: shipping method to bind with coupon code. */				
				try {
					$applicableShippingMethod = $this->_getApplicableShippingMethod($rule->getRuleId());
					if($applicableShippingMethod){
						$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();				// Base Currency
						$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		// Current Currency	
						
						if($currentCurrencyCode == $baseCurrencyCode){
							Mage::getSingleton('checkout/session')->setData('shipment', $applicableShippingMethod);
						}
						else{
							Mage::getSingleton('checkout/session')->setData('shipment', 'angnonusflatrate_angnonusflatrate');
						}
					}
				}
				catch (Exception $e) {
					Mage::logException($e);
				}	
				/* e: shipping method to bind with coupon code. */
			}
		}
	}
	
	public function getApplicableChannel(){
		$channel = Mage::getSingleton('core/session')->getVisitorChannel();
		if($channel && $channel->getStatus()){
			return $channel;
		}
		// assuming 1 to be direct channel
		return Mage::getModel('promotions/channel')->load(1);
	}
	
	public function getApplicableCoupons($platform){
		// return all special coupons based on channel
		$channel = $this->getApplicableChannel();
		return Mage::getModel('promotions/coupon')->getCoupons($channel, $platform);
	}
	
	public function getFreeProducts($coupon){
		$freeItems = array();
		$freeProduct1 = $coupon->getFreeProduct1Id();
		if(!empty($freeProduct1)){
			$product = Mage::getModel('catalog/product')->load($freeProduct1);
			if($product->getId())
				$freeItems[] = $product;
		}
		$freeProduct2 = $coupon->getFreeProduct2Id();
		if(!empty($freeProduct2)){
			$product = Mage::getModel('catalog/product')->load($freeProduct2);
			if($product->getId())
				$freeItems[] = $product;
		}
		$freeProduct3 = $coupon->getFreeProduct3Id();
		if(!empty($freeProduct3)){
			$product = Mage::getModel('catalog/product')->load($freeProduct3);
			if($product->getId())
				$freeItems[] = $product;
		}
		$freeProduct4 = $coupon->getFreeProduct4Id();
		if(!empty($freeProduct4)){
			$product = Mage::getModel('catalog/product')->load($freeProduct4);
			if($product->getId())
				$freeItems[] = $product;
		}
		return $freeItems;
	}	
	
	private function _getApplicableFreeItemIds($appliedRules){
		$appliedRules = array_unique(explode(',', $appliedRules));
		$grandtotalwithout_easy = Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
		$freeItems = array();
		foreach($appliedRules as $appliedRule){
			$promotionalCoupon = Mage::getModel('promotions/coupon')->loadByRuleId($appliedRule);
			if($promotionalCoupon){
				$freeProduct1 = $promotionalCoupon->getFreeProduct1Id();
				//	S:VA
				if(!empty($freeProduct1) && ($promotionalCoupon->getMinPriceFp1() <= $grandtotalwithout_easy) && ($grandtotalwithout_easy <=$promotionalCoupon->getMaxPriceFp1())){
					$freeItems[] = $freeProduct1;
				}
				$freeProduct2 = $promotionalCoupon->getFreeProduct2Id();
				if(!empty($freeProduct2) && ($promotionalCoupon->getMinPriceFp2() <= $grandtotalwithout_easy) && ($grandtotalwithout_easy <= $promotionalCoupon->getMaxPriceFp2())){
					$freeItems[] = $freeProduct2;
				}
				$freeProduct3 = $promotionalCoupon->getFreeProduct3Id();
				if(!empty($freeProduct3) && ($promotionalCoupon->getMinPriceFp3() <= $grandtotalwithout_easy) && ($grandtotalwithout_easy <= $promotionalCoupon->getMaxPriceFp3())){
					$freeItems[] = $freeProduct3;
				}
				$freeProduct4 = $promotionalCoupon->getFreeProduct4Id();
				if(!empty($freeProduct4) && ($promotionalCoupon->getMinPriceFp4() <= $grandtotalwithout_easy) && ($grandtotalwithout_easy <= $promotionalCoupon->getMaxPriceFp4())){
					$freeItems[] = $freeProduct4;
				}
				//	E:VA
			}
		}
		return array_unique($freeItems);
	}
	
	/* s: shipping method to bind with coupon code. */
	public function _getApplicableShippingMethod($ruleId){
		if($ruleId){			
			$coupon = Mage::getModel('promotions/coupon')->getCollection()
						->addFieldToSelect('rule_id')
						->addFieldToSelect('valid_shipping')
						->addFieldToFilter('rule_id', $ruleId)
						->getFirstItem();		
			if(count($coupon) > 0){
				$shippingMethod = $coupon->getValidShipping();
			}
		}
		return $shippingMethod;	
	}
	/* e: shipping method to bind with coupon code. */	
}