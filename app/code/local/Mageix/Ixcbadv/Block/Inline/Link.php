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
 
class Mageix_Ixcbadv_Block_Inline_Link extends Mage_Core_Block_Template
{
    public function getIxcbadvCheckoutUrl($issecure = null)
    {
		if(isset($issecure) && $issecure == 'secure') {
			return $this->getUrl('ixcbadv/inline', array('_secure'=>true));
		} else {
			return $this->getUrl('ixcbadv/inline', array('_secure'=>false));
		}
    }

    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function isPossibleIxcbadvCheckout()
    {
        return $this->helper('ixcbadv')->canCartCheckout();
    }

	public function earlyPos()
    {
        return ($this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_BEFORE);

    }
    public function afterPos()
    {
        return (!$this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_AFTER);
    }
	public function allConfigVars() 
	{
		return $this->helper('ixcbadv')->getAllConfigVars();
	}

	public function getWidget ($jswidgetname , $div, $element, $urlPath, $checkouturl, $type) {
	$merchantId = Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
        $sizeval = Mage::getStoreConfig('ixcbadv/mageix_button/button_size_ixcbadv');
        $colorval = Mage::getStoreConfig('ixcbadv/mageix_button/button_color_ixcbadv');
		return "
			if (window['$jswidgetname'] == undefined){
					function inlinecallback(){
						window.location = '".$checkouturl."';
					}
			$jswidgetname = new AmazonWidgets ('".$merchantId."');
			$jswidgetname.createInlineWidget('".$urlPath."', inlinecallback, 'checkout', '".$sizeval."', '".$colorval."', '".$type."', '".$checkouturl."');
			$jswidgetname.renderWidget($jswidgetname.inlineWidget,'$div');
			}
				";
	}
	
	
	function getApaButton ($jswidgetname , $div, $element, $urlPath, $buttonType, $checkouturl, $sizeval, $colorval) {
	        
		$merchantId = Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
                $sizeval = Mage::getStoreConfig('ixcbadv/mageix_button/button_size_ixcbadv');
                $colorval = Mage::getStoreConfig('ixcbadv/mageix_button/button_color_ixcbadv');
                
                $isLoginEnabled = Mage::helper("ixcbadv")->isLoginEnabled();
                
                if(Mage::helper('ixcbadv')->isLoginEnabled()){
                
                $scope = "profile payments:widget payments:shipping_address";
                
                }else {
                
                $scope = "payments:widget";
                
                }
                
		return "
			if (window['$jswidgetname'] == undefined){
					function inlinecallback(){
						window.location = '".$checkouturl."';
					}
		$jswidgetname = new AmazonWidgets ('".$merchantId."');
		$jswidgetname.createInlineButton('".$div."', '".$buttonType."',  '".$sizeval."', '".$colorval."', '".$checkouturl."', '".$scope."');
		$jswidgetname.renderWidget($jswidgetname.inlineButton,'$div');
			}
				";
	}
		
	
	
    }

