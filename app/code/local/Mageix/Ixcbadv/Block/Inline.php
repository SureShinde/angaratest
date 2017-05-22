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


class Mageix_Ixcbadv_Block_Inline extends Mageix_Ixcbadv_Block_Inline_Abstract
{
    public function getInlineSteps()
    {
        $steps = array();
	
        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }
		$stepCodes = array('orderitems', 'accountlogin', 'billing', 'shipping', 'review');
	
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }
	
    public function getInlineActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'orderitems' : 'login';
    }
	
	public function allConfigVars() 
	{
		return $this->helper('ixcbadv')->getAllConfigVarsStyles();
	}

	public function getGiftInformation() 
	{
		return $this->helper('ixcbadv')->getGiftInformation();
	}

	public function getPromotionalCode() 
	{
		return $this->helper('ixcbadv')->getPromotionalCode();
	}

	public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }
	
    public function getFirstname()
    {
        $firstname = $this->getAddress()->getFirstname();
        if (empty($firstname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getFirstname();
        }
        return $firstname;
    }
	
    public function getLastname()
    {
        $lastname = $this->getAddress()->getLastname();
        if (empty($lastname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getLastname();
        }
        return $lastname;
    }
	
    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }
	
    public function getSaveUrl()
    {
    }
	
    protected function _getTaxvat()
    {
        if (!$this->_taxvat) {
            $this->_taxvat = $this->getLayout()->createBlock('customer/widget_taxvat');
        }
	
        return $this->_taxvat;
    }
	
    public function isTaxvatEnabled()
    {
        return $this->_getTaxvat()->isEnabled();
    }
	
    public function getTaxvatHtml()
    {
        return $this->_getTaxvat()
            ->setTaxvat($this->getQuote()->getCustomerTaxvat())
            ->setFieldIdFormat('billing:%s')
            ->setFieldNameFormat('billing[%s]')
            ->toHtml();
    }
	
	public function getCurQuoteId()
	{
		return $this->getQuote()->getId();
	}
	
	public function getShippingMethodsUrl(){
		return Mage::getUrl('ixcbadv/inline/availableshippingmethods');
	}
	
	public function getMerchantid() {
		return Mage::helper('core')->decrypt(Mage::helper("ixcbadv")->getMerchantId());
	}
	
	public function getEmailInfo(){
		return Mage::helper('ixcbadv')->_getEmailInfoForMethod($this->getInline()->getCheckoutMethod());
	}
	
    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }
	
	public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }
	
    public function getInline()
    {
        return Mage::getSingleton('ixcbadv/type_inline');
    }
	
	public function getCheckoutMethod(){
		return $this->getInline()->getCheckoutMethod();
	}
	
	public function renderTotal($total, $area = null, $colspan = 1)
    {
        $code = $total->getCode();
        if ($total->getAs()) {
            $code = $total->getAs();
        }
		return $this->_getTotalRenderer($code)
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }
	
	protected function _getTotalRenderer($code)
    {
        $blockName = $code.'_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/ixcbadv/quote/totals/{$code}/renderer");
       
        
            if ($config) {
                $block = (string) $config;
            }else{
				$config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/renderer");
				if ($config) {
					$block = (string) $config;
				}
			}
	
            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        $block->setTotals($this->getTotals());
        
        
        return $block;
    }
	
	public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }
	
	public function getImgProduct($id)
    {
        return Mage::getModel("catalog/product")->load($id);
    }
	
	public function getFormatPrice($price)
	{
		return Mage::helper('core')->currency($price);
	}
	
	
    

     function getAddressWidget ($jswidgetname,$getShippingMethodsUrl, $shipping_methods , $widthval, $heightval, $displayMode, $orderReferenceId, $div, $merchantId) {
		
		
		return "
			if (window['$jswidgetname'] == undefined){
					function inlinecallback(){
						window.location = '".$checkouturl."';
					}
					$jswidgetname = new AmazonWidgets ('".$merchantId."');
					$jswidgetname.createAddressWidget('".$getShippingMethodsUrl."', '".$shipping_methods."', '".$widthval."', '".$heightval."', '".$displayMode."','".$orderReferenceId."', '". $div."');
					$jswidgetname.renderWidget($jswidgetname.inlineWidget,'$div');
			}
				";
	}
	 
	 
	 
	 
	 function getWalletWidget ($jswidgetname,$widthval, $heightval, $displayMode, $orderReferenceId, $div, $merchantId) {
		
		
		return "
			if (window['$jswidgetname'] == undefined){
					function inlinecallback(){
						window.location = '".$checkouturl."';
					}
					$jswidgetname = new AmazonWidgets ('".$merchantId."');
					$jswidgetname.createWalletWidget('".$widthval."', '".$heightval."', '".$displayMode."', '".$orderReferenceId."', '". $div."');
					$jswidgetname.renderWidget($jswidgetname.inlineWidget,'$div');
			}
				";
	}
	

}

