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

require_once 'Mage/Customer/controllers/AccountController.php';
class Mageix_Ixcbadv_Customer_AccountController extends Mage_Customer_AccountController
{
         
   /**
     * Overriding defaults redirect URL 
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();        
        
		if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl() ) {

			// Set default URL to redirect customer to
			$session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

			// Redirect customer to the last page visited after logging in
			if ($session->isLoggedIn())
			{
				if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
					if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
						$referer = Mage::helper('core')->urlDecode($referer);
						if ($this->_isUrlInternal($referer)) {
							$session->setBeforeAuthUrl($referer);
						}
					}
				}
			} else {
				$session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
			}
		}        
        if(preg_match("/inline/i", $session->getBeforeAuthUrl())) {
			$session->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
		}
        $this->_redirectUrl($session->getBeforeAuthUrl(true));        
    }
}

