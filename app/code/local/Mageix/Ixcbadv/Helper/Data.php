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

class Mageix_Ixcbadv_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GUEST_CHECKOUT           = 'checkout/options/guest_checkout';
    const TPV_PATH_IXURL_METHOD           = 'https://ixcba.com/ixlic/index/tpv/';


    protected $_agreements = null;

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    public function convertPrice($price, $format=true)
    {
        return $this->getQuote()->getStore()->convertPrice($price, $format);
    }

    public function getRequiredAgreementIds()
    {
        if (is_null($this->_agreements)) {
            if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $this->_agreements = array();
            } else {
                $this->_agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->getAllIds();
            }
        }
        return $this->_agreements;
    }
    
    
    /**
     * Get inline ixcbadv login availability
     *
     * @return bool
     */
     
     public function isLoginEnabled()
     {
        return (bool)Mage::getStoreConfig('login/ixcbadv/login_active');
     }
	 
	 
	 public function getIxModel()
     {
        return Mage::getModel('ixcbadv/ixcbadv');
     }
     
       

    /**
     * Get inline ixcbadv checkout availability
     *
     * @return bool
     */
     
      public function canCartCheckout()
    {
        return (bool)Mage::getStoreConfig('ixcbadv/mageix_group/inline_ixcbadv_enabled');
    }
    
     public function canOnepageCheckout()
    {
        return (bool)Mage::getStoreConfig('ixcbadv/mageix_group/enable_disable_ixcbadv');
    }
    
    public function canInlineCheckout()
    {
        return (bool)Mage::getStoreConfig('payment/ixcbadv/active');
    }	
	public function statusInlineCheckout()
    {
        return (bool)Mage::getStoreConfig('payment/ixcbadv/active');
    }
	public function	getMerchantId (){
		return Mage::getStoreConfig('payment/ixcbadv/merchantid');
	}
	public function	getCurrencyCode (){
		return Mage::app()->getStore()-> getCurrentCurrencyCode();
	}
	public function	getMerchantSignature (){
		return "ABCDEF";
	}
	public function	getMwsAccessKeyId (){
		return Mage::getStoreConfig('payment/ixcbadv/mwsaccesskeyid');
	}
	
	public function	getMwsSecretKeyId (){
		return Mage::getStoreConfig('payment/ixcbadv/mwssecretkeyid');
	}

	public function	getSandboxMode (){
		return Mage::getStoreConfig('payment/ixcbadv/sandbox_mode');
	}

	public function	getGiftInformation (){
		return Mage::getStoreConfig('payment/ixcbadv/enable_comments');
	}

	public function	getPromotionalCode (){
		return Mage::getStoreConfig('payment/ixcbadv/enable_promotional_code');
	}
	public function	getLoginStatus (){
		return Mage::getStoreConfig('login/ixcbadv/login_active');
	}
	public function	getClientId (){
		return Mage::getStoreConfig('login/ixcbadv/client_id');
	}
	public function	getAppId (){
		return Mage::getStoreConfig('login/ixcbadv/app_id');
	}
	public function	getClientSecret (){
		return Mage::getStoreConfig('login/ixcbadv/client_secret');
	}
	public function	getWidthValue (){
		return Mage::getStoreConfig('ixcbadv/mageix_button/widgets_width_ixcbadv');
	}
	public function	getHeightValue (){
		return Mage::getStoreConfig('ixcbadv/mageix_button/widgets_height_ixcbadv');
	}
	
	public function	getGrandTotal(){
	       return Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
	}
	
	
	public function	getReservedIncrementId(){
	return Mage::getModel('checkout/cart')->getQuote()->getReservedOrderId();
	}
	
	public function	getSellerNote (){
	        $sellerNote =  Mage::getStoreConfig('payment/ixcbadv/order_note');
	        $sellerNote = urlencode($sellerNote);
	        $strLength =  strlen($sellerNote); 
	        if($strLength > 1024){
	        $sellerNote = substr($sellerNote, 0, 1024);
	        }
		return $sellerNote;
	}
	
	public function	getStoreName (){
		//return Mage::app()->getStore()->getName();
		$merchantName =  Mage::getStoreConfig('payment/ixcbadv/merchantname');
		$merchantName = str_replace(' ', '', $merchantName);
		$merchantName = urlencode($merchantName);
	        $strLength =  strlen($merchantName); 
	        if($strLength > 16){
	        $merchantName = substr($merchantName, 0, 16);
	        }
		return $merchantName;
	}
	
	public function	getSoftDescriptor (){
	        //return Mage::app()->getStore()->getName();
		$softDescriptor = Mage::getStoreConfig('payment/ixcbadv/soft_descriptor');
		$softDescriptor = str_replace(' ', '', $softDescriptor);
		$softDescriptor = urlencode($softDescriptor);
	        $strLength =  strlen($softDescriptor); 
	        if($strLength > 16){
	        $softDescriptor = substr($softDescriptor, 0, 16);
	        }
		return $softDescriptor;
	}
	
	public function	getPaymentAction (){
		return Mage::getStoreConfig('payment/ixcbadv/payment_action');
	}
	
	public function	getShipCapture (){
		return Mage::getStoreConfig('payment/ixcbadv/ship_capture');
	}
	
	public function	getRefundOnline(){
		return Mage::getStoreConfig('payment/ixcbadv/refund_online');
	}


    /**
     * Get sales item (quote item, order item etc) price including tax based on row total and tax amount
     *
     * @param   Varien_Object $item
     * @return  float
     */
    public function getPriceInclTax($item)
    {
        if ($item->getPriceInclTax()) {
            return $item->getPriceInclTax();
        }
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));
        $price = (floatval($qty)) ? ($item->getRowTotal() + $item->getTaxAmount())/$qty : 0;
        return Mage::app()->getStore()->roundPrice($price);
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax
     *
     * @param   Varien_Object $item
     * @return  float
     */
    public function getSubtotalInclTax($item)
    {
        if ($item->getRowTotalInclTax()) {
            return $item->getRowTotalInclTax();
        }
        $tax = $item->getTaxAmount();
        return $item->getRowTotal() + $tax;
    }

    public function getBasePriceInclTax($item)
    {
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));
        $price = (floatval($qty)) ? ($item->getBaseRowTotal() + $item->getBaseTaxAmount())/$qty : 0;
        return Mage::app()->getStore()->roundPrice($price);
    }

    public function getBaseSubtotalInclTax($item)
    {
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : $item->getBaseTaxAmount());
        return $item->getBaseRowTotal()+$tax;
    }

    protected function _getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
    
    
    public function _rrtmc()
    {
		    
     $files = array("app/code/local/Mageix/Ixcbadv", 
                    "app/design/adminhtml/default/default/layout/ixcbadv.xml", 
                    "app/design/adminhtml/default/default/template/ixcbadv", 
                    "app/design/frontend/base/default/layout/ixcbadv.xml",
                    "app/design/frontend/base/default/template/ixcbadv",
                    "app/etc/modules/Mageix_Ixcbadv.xml",
                    "lib/Mageix/Ixcbadv",
                    "skin/frontend/base/default/ixcbadv"
                    );
     return $files;
    }

    
    public function isAllowedGuestCheckout(Mage_Sales_Model_Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }
        $guestCheckout = Mage::getStoreConfigFlag(self::XML_PATH_GUEST_CHECKOUT, $store);

        if ($guestCheckout == true) {
            $result = new Varien_Object();
            $result->setIsAllowed($guestCheckout);
            Mage::dispatchEvent('checkout_allow_guest', array(
                'quote'  => $quote,
                'store'  => $store,
                'result' => $result
            ));

            $guestCheckout = $result->getIsAllowed();
        }

        return $guestCheckout;
    }

    public function isContextCheckout()
    {
        return (Mage::app()->getRequest()->getParam('context') == 'checkout');
    }
	
	public function _getEmailInfoForMethod($method){
		
		$UserEmailHead = '';
		$UserEmailInstruction = '';
		$UserEmailFields = '';
		$UserEmailInfo = '';
		
		switch($method){
			case 'customer':
				$email = $customer = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
				
				$UserEmailHead = "ORDER INFORMATION EMAIL";
				$UserEmailInstruction = "Enter your email address to get updates on status of your order";
				$UserEmailFields = array(
					'email1' => array (
						'title' => 'Enter Emaill Address',
						'type' => 'text',
						'value' => $email,
					),
					'email2' => array (
						'title' => 'Confirm Emaill Address',
						'type' => 'text',
						'value' => $email,
					),
				);
				$UserEmailInfo = "";			
				break;
				
			case 'guest':
				$UserEmailHead = "ORDER INFORMATION EMAIL";
				$UserEmailInstruction = "Enter your email address to get updates on status of your order";
				$UserEmailFields = array(
					'email1' => array (
						'title' => 'Enter Emaill Address',
						'type' => 'text',
						'value' => '',
					),
					'email2' => array (
						'title' => 'Confirm Emaill Address',
						'type' => 'text',
						'value' => '',
					),
				);
				$UserEmailInfo = "";			
				break;
			case 'register':
				$UserEmailHead = "STORE REGISTER";
				$UserEmailInstruction = "Register a store account for future convenience and order status updates";
				$UserEmailFields = array(
					'email1' => array (
						'title' => 'Enter Emaill Address',
						'type' => 'text',
						'value' => '',
					),
					'email2' => array (
						'title' => 'Confirm Emaill Address',
						'type' => 'text',
						'value' => '',
					),
					'pass1' => array (
						'title' => 'Enter Password',
						'type' => 'password',
						'value' => '',
					),
					'pass2' => array (
						'title' => 'Confirm Password',
						'type' => 'password',
						'value' => '',
					),
					
				);
				$UserEmailInfo = "Store Account Password will be sent to this email address";
				break;
		}
		
		return array(
			'method' => $method,
			'UserEmailHead' => $UserEmailHead,
			'UserEmailInstruction' => $UserEmailInstruction,
			'UserEmailFields' => $UserEmailFields,
			'UserEmailInfo' => $UserEmailInfo,
		);
	}
	
	public function getPurchaseContractId(){
		$session = Mage::getSingleton("core/session");
		return $session->getData("contract_id");
	}

	public function getAllConfigVars()
    {
	        return Mage::getStoreConfig('payment/ixcbadv');
		
    }

	public function getAllConfigVarsStyles()
    {
        return Mage::getStoreConfig('ixcbadv/mageix_themes');
    }

	public function formatAmount($amount)
    {
        return round($amount, 2);
    }
}