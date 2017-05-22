<?php

class Angara_Offers_Block_ChooseYourGift extends Mage_Core_Block_Template
{
	
	public function isOfferMinimized()
	{
		return Mage::getSingleton("checkout/session")->getData("offer_minimized");
	}
	public function hasOffer()
	{
		return Mage::getSingleton("checkout/session")->getData("has_offer_to_process");
	}
	public function getProducts()
	{
		if(Mage::getSingleton("checkout/session")->getData("has_offer_to_process")){
			$autoapply_coupon = Mage::getSingleton("checkout/session")->getData("offer_code");
			if(empty($autoapply_coupon)){
				$autoapply_coupon = Mage::helper('checkout/cart')->getCart()->getQuote()->getCouponCode();
			}
			if(!empty($autoapply_coupon)){
				$memberOfferCoupon = Mage::getModel('salesrule/coupon')->load($autoapply_coupon, 'code');
				$promo_data = Mage::getModel('salesrule/rule')->load($memberOfferCoupon->getRuleId());
				$promo_desc = $promo_data->getDescription();
				if($promo_desc!=''){
					$promo_xml = simplexml_load_string($promo_desc);
					$products = array();
					foreach($promo_xml->prodoption as $prod) {

						$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $prod['id']);
						if($product){
							$products[] = $product;
						}
					}
					return $products;
				}
			}
		}
		return false;
	}
}