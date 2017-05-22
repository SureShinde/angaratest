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

class Mageix_Ixcbadv_Block_Inline_Head extends Mage_Core_Block_Template {
	public function getScript(){
		$head = $this->helper('ixcbadv')->getAllConfigVars();
		
		
$getConfigVar = Mage::helper('ixcbadv')->getAllConfigVars();
$merchantId = Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
$clientId = Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getClientId());

		
		if(isset($head['sandbox_mode']) && $head['sandbox_mode'] == 1) {
			
			if(isset($head['country']) && $head['country'] == 'UK') {
	return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/js/Widgets.js?sellerId=".$merchantId."'></script><script>jQuery.noConflict();</script>";
			} elseif(isset($head['country']) && $head['country'] == 'DE') {
	return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js?sellerId=".$merchantId."'></script><script>jQuery.noConflict();</script>";
			} else { // Default US Urls
	return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js?sellerId=".$merchantId."'><script>jQuery.noConflict();</script>";
	
	
	
	
		
			}


		}else{
			
			if(isset($head['country']) && $head['country'] == 'UK') {
				return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/js/Widgets.js?sellerId=".$merchantId."'></script><script>jQuery.noConflict();</script>";
			} elseif(isset($head['country']) && $head['country'] == 'DE') {
				return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js?sellerId=".$merchantId."'></script><script>jQuery.noConflict();</script>";
			} else {
				return "<script type=\"text/javascript\">window.onAmazonLoginReady = function() {amazon.Login.setClientId('".$clientId."');};</script><script src='https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js?sellerId=".$merchantId."'></script><script>jQuery.noConflict();</script>";
			}

		}
	}
}