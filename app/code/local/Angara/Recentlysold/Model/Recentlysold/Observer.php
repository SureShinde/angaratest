<?php
class Angara_Recentlysold_Model_Recentlysold_Observer extends Mage_Core_Model_Abstract
{
	public function salesOrderPlaceAfter($observer){
		try{
		$soldItem = Mage::getModel('recentlysold/item');
		
		$order = $observer->getOrder();
		
		if($order){
			
			$soldItem->setBoughtBy($order->getBillingAddress()->getFirstname());
			$rc = $order->getBillingAddress()->getRegionCode();
			$mail = $order->getBillingAddress()->getData('email');
			if(!strpos($mail,'@angara.com')){
				if($rc){
					$soldItem->setLocation($rc.', '.Mage::app()->getLocale()->getCountryTranslation($order->getBillingAddress()->getCountryId()));
				}
				else{
					$soldItem->setLocation(Mage::app()->getLocale()->getCountryTranslation($order->getBillingAddress()->getCountryId()));
				}
				
				$item = $order->getItemsCollection()->getFirstItem();
				
				$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
				
				//var_dump(get_class_methods(get_class($product)));
				
				$soldItem->setProductName($product->getName());
				$soldItem->setPrice($product->getPrice());
				$soldItem->setImage($product->getThumbnail());
				//var_dump($product);
				//var_dump($soldItem->getData());
				
				//exit; //echo 
				$soldItem->setLink($product->getProductUrl());
				$now = Mage::getModel('core/date')->timestamp(time());
				$soldItem->setTime(date('Y-m-d H:i:s', $now));
				if($product->getPrice() >= 70){
					$soldItem->save();
				}
			}
		}
		//exit;
		
		}
		catch(Exception $e){
			# @ todo handle exception here
		}
	}
}
