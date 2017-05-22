<?php
class Paypal_InContext_Model_Observer{
    
    //AUEPTY9XMYCCC
    public function checkoutCartAddProductComplete($observer){
        if (!Mage::getStoreConfig('payment/incontext/enable') || !Mage::helper('incontext')->isMobile()){
            return;
        }
        
        $request = $observer->getRequest();
        $returnUrl = $request->getParam('return_url', false);
        if (preg_match('/express\/start/i', $returnUrl)){           
            $request->setParam('return_url', Mage::getUrl('checkout/cart', array('_secure'=>true)));            
        }         
    }
}