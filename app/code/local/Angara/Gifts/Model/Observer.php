<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */
 
class Angara_Gifts_Model_Observer extends Mage_Core_Model_Abstract
{

	public function checkGiftQty(&$event){		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
        if(Mage::helper('gifts')->isGiftUsed() && !Mage::helper('gifts')->getQuoteQty()){
        	foreach ($quote->getAllItems() as $item) {
        		Mage::getSingleton('checkout/cart')->removeItem($item->getId());
        	}
        	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        	return true;
        }
        foreach ($quote->getAllItems() as $item) { 
			$getGiftsIds = Mage::helper('gifts')->getGiftsIds(1);
			$giftIds 	 = $getGiftsIds['ids'];
			//echo '<pre>';print_r($giftIds);die;
            if(!floatval($item->getPrice()) && in_array($item->getProductId(), $giftIds)){
				if($item->getQty() > 1){
					$item->setQty(1);
				}
			}
			$getGiftsIds2	=	Mage::helper('gifts')->getGiftsIds();
			$giftIds2 	 	= 	$getGiftsIds2['ids'];
			//echo '<pre>';print_r($giftIds2);die;
			if(!count($giftIds2) && Mage::helper('gifts')->isGiftUsed()){
        		if(Mage::helper('gifts')->isGiftUsed() == $item->getProductId())Mage::getSingleton('checkout/cart')->removeItem($item->getId());
        	}
        }
        //Mage::getSingleton('checkout/session')->setCartWasUpdated(true);    
	}

}