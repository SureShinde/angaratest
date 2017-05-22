<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

class Angara_Gifts_Helper_Data extends Mage_Core_Helper_Abstract{

	 public function getQuoteTotal(){
	 	$quote = Mage::getSingleton('checkout/session')->getQuote();
	 	return $quote->getGrandTotal();
	 }
	 
	 public function getQuoteQty(){
	 	$quote = Mage::getSingleton('checkout/session')->getQuote();
	 	$qty = !is_null($quote->getItemsSummaryQty()) ? $quote->getItemsSummaryQty() : 0;
	 	if($this->isGiftUsed())$qty-=1;
	 	return $qty; 
	 }
	 
	 public function getRules($recordsFor){
	 	$store_id = Mage::app()->getStore()->getId();
	 	$collection = Mage::getModel('gifts/gifts')->getCollection();
		
	 	$total 	= 	!is_null($this->getQuoteTotal()) ? $this->getQuoteTotal() : 0;
		//	Patch added to calculate the free gifts as per the total price in cart
		if($total>0){
			$total	=	Mage::getBlockSingleton('checkout/cart')->getCartFinalTotal();
		}
		//echo '<br>total 1->'.$total;
		if($recordsFor!=''){
			$total = 	$total + 1000;
		}
		//	Bug fix if cart have total above 3000 so we need to add 1000 more so that can show higher range
		if( $total>3000 && $total<5000 && $recordsFor!='' ){
			$total = 	$total + 1000;
		}
		//echo '<br>total 2->'.$total;
        $collection ->addFieldToFilter('store', array('in' => array($store_id, 0)))
   					->addFilter('status', 1)
   					->addFieldToFilter('qty', array('lteq' => $this->getQuoteQty()))
   					->addFieldToFilter('amount', array('lteq' => $total));
   		
		$collection->getSelect()->order('amount DESC');
		$collection->getSelect()->limit(1);
		//$collection	->getSelect()->where('qty <=' . $this->getQuoteQty() . ' OR amount <=' . $total);
		//$collection	->getSelect()->where( 'amount <=' . $total);
		//$collection->load(1);die;	
		//echo '<pre>'; print_r($collection);die;
   		return $collection;
	 }
	 
	 public function getAllRules(){
	 	$store_id = Mage::app()->getStore()->getId();
	 	$collection = Mage::getModel('gifts/gifts')->getCollection();
        $collection ->addFieldToFilter('store', array('in' => array($store_id, 0)))
   					->addFilter('status', 1);
   		return $collection;
	 }
	 
	 public function isGiftUsed(){
	 	$gift_ids = $this->getGiftsIds(1);
		$gift_ids = $gift_ids['ids'];
		//echo '<pre>';print_r($gift_ids);die;
	 	if(is_array($gift_ids) && count($gift_ids)){
	 		$quote = Mage::getSingleton('checkout/session')->getQuote();
	 		foreach ($quote->getAllItems() as $item){
				//echo '<br>'.$item->getProductId();
				//echo '<br>'.$item->getSku();
				$giftCardSku	=	$this->giftCardSku();
                //if (in_array($item->getProductId(), $gift_ids)  && !floatval($item->getBasePrice())){
				if (in_array($item->getProductId(), $gift_ids) || in_array($item->getSku(), $giftCardSku) ){
                    return $item->getProductId();
                }
            }
	 	}
	 	else return false;
	 }
	 
	 public function getGiftsIds($param = 0){
		//echo 'param->'.$param;
	 	$ids = array();
	 	if($param)$rules = $this->getAllRules();
	 	else $rules = $this->getRules();
		//echo $rules->getSize(); die;			//	it will give total no of rules created in admin
   		if($rules->getSize()){
   			foreach($rules as $rule){
				//echo '<pre>'; print_r($rule->getData()); die;		//	it will give each rule complete details from db
				//echo Mage::getModel('gifts/product')->getCollection()->addFilter('gift_id', $rule->getId())->getSize(); die;
   				if(Mage::getModel('gifts/product')->getCollection()->addFilter('gift_id', $rule->getId())->getSize()){		//	it will give no of products assigned to this rule
   					$products_model = Mage::getModel('gifts/product')->getCollection()->addFieldToFilter('gift_id', $rule->getId())->load();
                    foreach ($products_model as $product) {
                    	//if(Mage::getModel('catalog/product')->load($product->getProductId())->isSalable()){
						if(Mage::getModel('catalog/product')->load($product->getProductId())){	
                        	$quote = Mage::getSingleton('checkout/session')->getQuote();
                        	$gift_push = 1;
                        	if(!$param){
                        		foreach ($quote->getAllItems() as $item){
									//echo $item->getPrice();die;
                        			if($item->getProductId() == $product->getProductId() && floatval($item->getPrice())){
                        				$gift_push = 0;
                        				break;
                        			} 
                        		}
                        	}
							
                        	if($gift_push)array_push($ids, $product->getProductId());
                        }
                    }
					$ruletitle[]	=	$rule->getTitle();
					//echo '<pre>'; print_r($ids); die;
   				}
   			}
			//echo '<pre>'; print_r($ids); //die;
   		}
   		//return array_unique($ids);	
		return array( "ids" => array_unique($ids), "ruletitle" => $ruletitle);	
	 }
	 
	 public function getGiftsIdsUpperRange($param = 0){
		//echo $this->getQuoteTotal(); 
		$total	=	Mage::getBlockSingleton('checkout/cart')->getCartFinalTotal();
		//if($this->getQuoteTotal() > '5000'){
		if($total > '5000'){
			return false;	 
		}
	 	$ids = array();
		//echo '<br>param->'.$param;
	 	if($param){
			$rules = $this->getAllRules();
		}else {
			$rules = $this->getRules('UpperRange');
		}
		//echo $rules->getSize(); die;		
   		if($rules->getSize()){
   			foreach($rules as $rule){
				//echo '<pre>'; print_r($rule->getData()); die;
   				if(Mage::getModel('gifts/product')->getCollection()->addFilter('gift_id', $rule->getId())->getSize()){
   					$products_model = Mage::getModel('gifts/product')->getCollection()->addFieldToFilter('gift_id', $rule->getId())->load();
                    foreach ($products_model as $product) {
                    	//if(Mage::getModel('catalog/product')->load($product->getProductId())->isSalable()){
							if(Mage::getModel('catalog/product')->load($product->getProductId())){
                        	$quote = Mage::getSingleton('checkout/session')->getQuote();
                        	$gift_push = 1;
                        	if(!$param){
                        		foreach ($quote->getAllItems() as $item){
                        			if($item->getProductId() == $product->getProductId() && floatval($item->getPrice())){
                        				$gift_push = 0;
                        				break;
                        			} 
                        		}
                        	}
                        	if($gift_push)array_push($ids, $product->getProductId());
                        }
                    }	//	end foreach
					$ruletitle[]	=	$rule->getTitle();
					//echo '<pre>'; print_r($ids); die;
   				}
   			}
   		}
   		//return array_unique($ids);	
		return array( "ids" => array_unique($ids), "ruletitle" => $ruletitle);	
	 }
	 
	//	Function to check if module is enabled in backend
	public function getGiftsStatus()
    {
        return Mage::getStoreConfig('gifts/settings/enabled');
    }
	
	//	Function to check Gift Card SKUs
	public function giftCardSku()
    {
		return $giftSkuArray	=	array('AGIF00100', 'AGIF01000', 'AGIF10000', 'AGIF00150', 'AGIF01500', 'AGIF00200', 'AGIF02000', 'AGIF00250', 'AGIF02500', 'AGIF00300', 'AGIF03000', 'AGIF00350', 'AGIF00400', 'AGIF00500', 'AGIF05000', 'AGIF00750');
	}
	
}