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

class Mageix_Ixcbadv_Block_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    protected function _getOrdersTrackingCode()
    {
		if((isset($_GET['amazonorderid']) && $_GET['amazonorderid'] != '') && Mage::getStoreConfig('payment/ixcbadv/enable_ga') == 1) {
			$collection = Mage::getResourceModel('sales/quote_collection')
				->addFieldToFilter('reserved_order_id', array('in' => $_GET['amazonorderid']))
			;
			$result = array();
			foreach ($collection as $quote) {
				if ($quote->getIsVirtual()) {
					$address = $quote->getBillingAddress();
					$tax_amount = $quote->getBillingAddress()->getBaseTaxAmount();
					$shipping_amount = '0';
				} else {
					$address = $quote->getShippingAddress();
					$tax_amount = $quote->getShippingAddress()->getBaseTaxAmount();
					$shipping_amount = $quote->getShippingAddress()->getBaseShippingAmount();
				}
				$result[] = sprintf("_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);",
					$quote->getReservedOrderId(),
					$this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
					$quote->getBaseGrandTotal(),
					$tax_amount,
					$shipping_amount,
					$this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCity())),
					$this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getRegion())),
					$this->jsQuoteEscape(Mage::helper('core')->escapeHtml($address->getCountry()))
				);
				foreach ($quote->getAllVisibleItems() as $item) {
					$result[] = sprintf("_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
						$quote->getReservedOrderId(),
						$this->jsQuoteEscape($item->getSku()), $this->jsQuoteEscape($item->getName()),
						null, // there is no "category" defined for the order item
						$item->getBasePrice(), $item->getQty()
					);
				}
				$result[] = "_gaq.push(['_trackTrans']);";
			}
			return implode("\n", $result);
		}else{
			return parent::_getOrdersTrackingCode();
		}
    }
}

