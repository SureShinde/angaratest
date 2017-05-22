<?php
class Angara_Promotions_Model_Observer
{
	public function applyBestPossiblePromotion(Varien_Event_Observer $observer)
	{
		$cart = $observer->getEvent()->getCart();
		$appliedCoupon = $cart->getQuote()->getCouponCode();
		$promotionCode = Mage::getSingleton('core/session')->getPromotionCode();
		if($promotionCode !== $appliedCoupon){
			Mage::getModel('promotions/cart')->applyPromotion($promotionCode);
		}
		Mage::getSingleton('core/session')->setPromotionApplied(false);
		//Mage::getModel('promotions/offer')->process($cart->getQuote()->getAppliedRuleIds());
	}

	public function initChannel(Varien_Event_Observer $observer)
	{
		$visitor = $observer->getEvent()->getVisitor();
		$channel = Mage::getModel('promotions/channel')->getChannelByUrl($visitor->getRequestUri());
		$platform = Mage::helper('promotions')->getPlatform();
		Mage::getSingleton('core/session')->setVisitorChannel($channel);
		Mage::getModel('promotions/offer')->setDefaultOffer($platform);
		Mage::getSingleton('core/session')->setPromotionApplied(false);
	}
}