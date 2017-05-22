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

class Mageix_Ixcbadv_Model_Adminhtml_Source_Widgetswidth {


	public function toOptionArray()
	{
		return array(
			array('value' => '400px', 'label' => Mage::helper('adminhtml')->__('400px')),
			array('value' => '425px', 'label' => Mage::helper('adminhtml')->__('425px')),
			array('value' => '450px', 'label' => Mage::helper('adminhtml')->__('450px')),
			array('value' => '475px', 'label' => Mage::helper('adminhtml')->__('475px')),
			array('value' => '500px', 'label' => Mage::helper('adminhtml')->__('500px')),
			array('value' => '525px', 'label' => Mage::helper('adminhtml')->__('525px')),
			array('value' => '550px', 'label' => Mage::helper('adminhtml')->__('550px')),
			array('value' => '575px', 'label' => Mage::helper('adminhtml')->__('575px')),
			array('value' => '600px', 'label' => Mage::helper('adminhtml')->__('600px')),
		);
	}
}



?>