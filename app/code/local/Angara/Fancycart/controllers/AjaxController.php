<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Angara_Fancycart_AjaxController extends Mage_Checkout_CartController
{		
	public function getItemsCountAction(){
		echo Mage::helper('checkout/cart')->getSummaryCount();
	}
	
	protected function _goBack()
    {
		// nowhere to go
		echo Mage::helper('checkout/cart')->getSummaryCount();
    }
	
}
?>
