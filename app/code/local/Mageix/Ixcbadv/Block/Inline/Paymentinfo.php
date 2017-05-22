<?php   
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLC's  End User License Agreement
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://ixcba.com/index.php/license-guide/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@mageix.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * 
 * Magento Mageix IXCBADV Module
 *
 * @category	Checkout & Payments, Customer Registration & Login
 * @package 	Mageix_Ixcbadv
 * @copyright   Copyright (c) 2014 -  Mageix LLC 
 * @author      Brian Graham
 * @license	    http://ixcba.com/index.php/license-guide/   End User License Agreement
 *
 *
 *
 * Magento Mageix IXCBA Module
 * 
 * @category   Checkout & Payments
 * @package	   Mageix_Ixcba
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @author     Brian Graham
 * @license   http://mageix.com/index.php/license-guide/   End User License Agreement
 */

class Mageix_Ixcbadv_Block_Inline_Paymentinfo extends Mage_Core_Block_Template{   
	
	public function getMerchantid (){
		return Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
	}
	public function getWidthvalue (){
		return Mage::helper("ixcbadv")->getWidthValue();
	}
	public function getHeightvalue (){
		return Mage::helper("ixcbadv")->getHeightValue();
	}
	
	public function getDisplayMode() {		
		if ( Mage::app()->getRequest()->getActionName() == "confirm"){
			return "Read";
		}
		return "";
	}
	

	
	
	public function getPaymentWidget(){
		$displayMode = "";
		
		if ( Mage::app()->getRequest()->getActionName() == "confirm"){
			$displayMode = "displayMode: 'Read',";
		}

		$merchantId = Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
		$sizeval = Mage::getStoreConfig('ixcbadv/mageix_button/button_size_ixcbadv');
        $colorval = Mage::getStoreConfig('ixcbadv/mageix_button/button_color_ixcbadv');
		
		$amazonOrderReferenceId = Mage::getSingleton(‘core/session’)->getOrderReferenceId();
		
		return "<div id='AmzWalletWidget'></div>
					<script>
						new OffAmazonPayments.Widgets.Wallet({
  sellerId: '".$merchantId."',
  amazonOrderReferenceId: '".$amazonOrderReferenceId."',  
  
  design: {
    size : {width:'".$widthval."', height:'".$heightval."'}
  },

  onPaymentSelect: function(orderReference) {
 
    
  },
  onError: function(error) {
    // your error handling code
  }
}).bind('AmzWalletWidget');
					</script>";
	}
	
}