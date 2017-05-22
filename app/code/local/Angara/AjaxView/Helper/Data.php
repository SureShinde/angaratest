<?php
class Angara_AjaxView_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function noOfRelatedProducts()
    {
        return Mage::getStoreConfig('ajaxview/seo/no_of_related_products');
		//ajaxview is the name of the module
		//seo is the name of the group in etc/system.xml
		//no_of_related_products is the name of the field in etc/system.xml
		//Calling of this function can be done anywhere like this 	- $noOfRelatedProductsToShow	=	Mage::Helper('ajaxview')->noOfRelatedProducts();
		
    }
	
	public function getCartTotal(){
		return $grandtotalwithout_easy 	= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
		//return $totals 			= 	Mage::getSingleton('checkout/session')->getQuote()->getTotals(); 
	}
}
	 