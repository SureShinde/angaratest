<?php

class Angara_Fancycart_Block_View extends Mage_Checkout_Block_Cart {
	public function getSVars(){
		return Mage::getModel('omniture/sitecatalyst')->getSVars();
	}
	
	public function getCartScript(){
		return Mage::getModel('omniture/sitecatalyst')->getCartScript();
	}
}

?>
