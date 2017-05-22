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

class Mageix_Ixcbadv_Block_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{

	protected function _prepareLayout()
    {
       	if($this->getOrder()->getPayment()->getMethod() && $this->getOrder()->getPayment()->getMethod() == 'ixcbadv') {

			$onclick = "ixcbadvDisableCancelOrderOnline()&submitAndReloadArea($('creditmemo_item_container'),'".$this->getUpdateUrl()."')";
			$this->setChild(
				'update_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
					'label'     => Mage::helper('sales')->__('Update Qty\'s'),
					'class'     => 'update-button',
					'onclick'   => $onclick,
				))
			);

			if ($this->getCreditmemo()->canRefund()) {
				if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
					$this->setChild(
						'submit_button',
						$this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
							'label'     => Mage::helper('sales')->__('Refund Online'),
							'class'     => 'save submit-button',
							'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
						))
					);
				}
				$this->setChild(
					'submit_offline',
					$this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
						'label'     => Mage::helper('sales')->__('Refund Online'),
						'class'     => 'save submit-button',
						'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
					))
				);

			}
			else {
				$this->setChild(
					'submit_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
						'label'     => Mage::helper('sales')->__('Refund Online'),
						'class'     => 'save submit-button',
						'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
					))
				);
			}

		}else{
			parent::_prepareLayout();
		}
    }

}
