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


class Mageix_Ixcbadv_CustomerController extends Mage_Core_Controller_Front_Action {

    public function LoginAction()
    {

        $helper = Mage::helper('ixcbadv');
        
        $token = $this->getRequest()->getParam('token', false);
        if(!$token) {
            $this->_afterRedirect();
            return;
        }
        // get profile
        $profile = $helper->getUserProfileFromAccessToken($token);
        // create user if non existent
        if($profile && $profile->user_id) {
            $amazonuser = Mage::getModel('ixcbadv/amazonlogin')->load($profile->user_id, 'amazon_id');
            if(!$amazonuser->getId()) {
                // if not known try to get by email
                $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($profile->email);
                if($customer->getId()) {
                    $customer_id = $customer->getId();
                } else {
                    // create customer
                    if(false===strpos($profile->name, ' ')) {
                        $len = round(strlen($profile->name) / 2);
                        $data['firstname'] = substr($profile->name, 0, $len);
                        $data['lastname'] = substr($profile->name, $len);
                    } else {
                        $list = explode(' ', $profile->name);
                        $data['lastname'] = array_pop($list);
                        $data['firstname'] = implode(' ', $list);
                    }
                    $data['email'] = $profile->email;
                    $data['user_id'] = $profile->user_id;
                    $data['postal_code'] = $profile->postal_code;

                    $customer_id = $this->_createCustomer($data);
                }

                if(false!==$customer_id) {
                    $amazonuser->setCustomerId($customer_id);
                    $amazonuser->setAmazonId($profile->user_id);
                    $amazonuser->save();
                }
            } else {
                $customer_id = $amazonuser->getCustomerId();
            }
        }
        // login user
        if(false!==$customer_id) {
            $this->_loginCustomer($customer_id);
        }
        // redirect (if new to edit profile / if in checkout
        $this->_afterRedirect();
    }

    private function _createCustomer($data)
    {
        $customer = Mage::getModel('customer/customer')
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setPassword(md5(uniqid() . $data['user_id']))
            ->setIsActive(1)
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->setConfirmation(null);
        $customer->save();
        $customer->setConfirmation(null);
        $customer->save();
        $customer->sendNewAccountEmail(
            'registered',
            '',
            Mage::app()->getStore()->getId()
        );
        $id = $customer->getId();
        if(is_numeric($id) && $id>0) {
            return $id;
        }
        return false;
    }

    private function _loginCustomer($customer_id) {
        $session = Mage::getSingleton('customer/session');
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if($customer->getId()) {
            $session->setCustomerAsLoggedIn($customer);
        }
    }

    private function _afterRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
            $referer = Mage::helper('core')->urlDecode($referer);
            if ((strpos($referer, Mage::app()->getStore()->getBaseUrl()) === 0)
                    || (strpos($referer, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)) {
                $session->setBeforeAuthUrl($referer);
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            }
        } else {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }
}