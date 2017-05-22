<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

class Angara_Gifts_IndexController extends Mage_Core_Controller_Front_Action {
	public function addAction()
    {	
        if ($this->getRequest()->isGet()) {
            $gift_id = $this->getRequest()->getParam('gift', 0);
            if($gift_id){
            	if(!Mage::helper('gifts')->isGiftUsed() && in_array($gift_id, Mage::helper('gifts')->getGiftsIds())){
            		$quote = Mage::getSingleton('checkout/session')->getQuote();
                	$cart = Mage::getModel('checkout/cart');
        			$product = new Mage_Catalog_Model_Product();
        			$product->load($gift_id);

        			$cart->addProduct($product, 1);
        			$cart->save();
        		
        			foreach ($quote->getAllItems() as $item) {
                                    
                   		if ($item->getProductId() == $gift_id) {
                       		$item->setCustomPrice(1);
                       		$item->setOriginalCustomPrice(1);
                   		}
                	}
                	
                	$cart->init();
                	$cart->save();          
        		
        			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        		}
            }
        }
		//	If we are calling the addAction from checkout/cart without ajax then we need to redirect user back to cart
		$mode = $this->getRequest()->getParam('mode', 0);
		if($mode=='d'){
	        $this->_redirect('checkout/cart');
		}
    }
	
	//	Function to add gift products to cart by using fancycart from product page
	public function ajaxAction()
    {	
		$html = Mage::getSingleton('core/layout')->createBlock('core/template')->setTemplate('gifts/gifts.phtml')->toHtml();
		$this->getResponse()->setBody($html);
    }
}