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


class Mageix_Ixcbadv_Model_Encryption extends Mage_Core_Model_Encryption
{
    protected $_key = "zXKG3fvu2tsSRBSBXsD4KamU9QRSeytOQQMGpYeaFrv";

    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }
    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return Varien_Crypt_Mcrypt
     */
    protected function _getCrypt($key = null)
    {
        $key = $this->_key;
        if (!$this->_crypt) {
            if (null === $key) {
                $key = (string)Mage::getConfig()->getNode('global/crypt/key');
            }
            $this->_crypt = Varien_Crypt::factory()->init($key);
        }
        return $this->_crypt;
    }
}

