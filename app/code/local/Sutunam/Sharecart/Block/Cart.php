<?php
class Sutunam_Sharecart_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{    
    public function isEnabelShareCart()
    {
        return Mage::getStoreConfig(Sutunam_Sharecart_Model_Config::XML_PATH_SHARECART_ENABEL);
    }
    
    public function getSharePostUrl()
    {     
        return Mage::getUrl('sharecart/index/post');
    }
    
    public function getShareUrl()
    {        
        return Mage::getUrl('sharecart/index/getShareUrl');
    }
}