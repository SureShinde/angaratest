<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */
 
class Angara_Gifts_Block_Gifts extends Mage_Core_Block_Template {

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getQuoteQty() {
        return Mage::helper('gifts')->getQuoteQty();
    }
    
    public function getQuoteTotal() {
        return Mage::helper('gifts')->getQuoteTotal();
    }
    
    public function getGiftProducts(){
    	$ids = Mage::helper('gifts')->getGiftsIds();
		$ruletitle	=	$ids['ruletitle'];
		$ids = $ids['ids'];
		//echo '<pre>';print_r($ids);die;
    	$products = array();
    	if(is_array($ids) && count($ids)){
			$i=0;
    		foreach($ids as $id){
				if($i<3){			//	Show only 3 products
    				array_push($products, Mage::getModel('catalog/product')->load($id));
				}
				$i++;
    		}
    	}
    	//return $products;
		return array("products" => $products, "ruletitle" => $ruletitle );	
    }    
	
	public function getGiftProductsUpperRange(){
    	$ids = Mage::helper('gifts')->getGiftsIdsUpperRange();
		$ruletitle	=	$ids['ruletitle'];
		$ids = $ids['ids'];
    	$products = array();
    	if(is_array($ids) && count($ids)){
			$j=0;
    		foreach($ids as $id){
    			if($j<3){		//	Show only 3 products
					array_push($products, Mage::getModel('catalog/product')->load($id));
				}
				$j++;
    		}
    	}
    	//return $products;
		return array("products" => $products, "ruletitle" => $ruletitle );	
    }
	
}