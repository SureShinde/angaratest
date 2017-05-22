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


class Mageix_Ixcbadv_Model_Order_Payment extends Mage_Sales_Model_Order_Payment
{
	
     
     public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
            $this->setCreatedInvoice($invoice);
            return $this; // @see Mage_Sales_Model_Order_Invoice::capture()
        }
        $amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
        $order = $this->getOrder();
        $payment_method = $order->getPayment()->getMethod();

        // prepare parent transaction and its amount
        $paidWorkaround = 0;
        if (!$invoice->wasPayCalled()) {
            $paidWorkaround = (float)$amountToCapture;
        }
        $this->_isCaptureFinal($paidWorkaround);

        $this->_generateTransactionId(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction()
        );

        Mage::dispatchEvent('sales_order_payment_capture', array('payment' => $this, 'invoice' => $invoice));

        /**
         * Fetch an update about existing transaction. It can determine whether the transaction can be paid
         * Capture attempt will happen only when invoice is not yet paid and the transaction can be paid
         */
        if ($invoice->getTransactionId()) {
            $this->getMethodInstance()
                ->setStore($order->getStoreId())
                ->fetchTransactionInfo($this, $invoice->getTransactionId());
        }
        $status = true;
        if (!$invoice->getIsPaid() && !$this->getIsTransactionPending()) {
            // attempt to capture: this can trigger "is_transaction_pending"
            $this->getMethodInstance()->setStore($order->getStoreId())->capture($this, $amountToCapture);

            $transaction = $this->_addTransaction(
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
                $invoice,
                true
            );

            if ($this->getIsTransactionPending()) {
                $message = Mage::helper('sales')->__('Capturing amount of %s is pending approval on gateway.', $this->_formatPrice($amountToCapture));
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                if ($this->getIsFraudDetected()) {
                    $status = Mage_Sales_Model_Order::STATUS_FRAUD;
                }
                $invoice->setIsPaid(false);
            } else { // normal online capture: invoice is marked as "paid"
                if($payment_method != 'ixcbadv') {
                $message = Mage::helper('sales')->__('Captured amount of %s online.', $this->_formatPrice($amountToCapture));
                }
                $state = Mage_Sales_Model_Order::STATE_PROCESSING;
                $invoice->setIsPaid(true);
                $this->_updateTotals(array('base_amount_paid_online' => $amountToCapture));
            }
            if ($order->isNominal()) {
                $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
            } else {
                $message = $this->_prependMessage($message);
                $message = $this->_appendTransactionToMessage($transaction, $message);
            }
            $order->setState($state, $status, $message);
            $this->getMethodInstance()->processInvoice($invoice, $this); // should be deprecated
            return $this;
        }
        Mage::throwException(
            Mage::helper('sales')->__('The transaction "%s" cannot be captured yet.', $invoice->getTransactionId())
        );
    }
     
     
public function refund($creditmemo)
    {
        $baseAmountToRefund = $this->_formatAmount($creditmemo->getBaseGrandTotal());
        $order = $this->getOrder();
		$payment_method = $order->getPayment()->getMethod();

        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND);

        // call refund from gateway if required
        $isOnline = false;
        $gateway = $this->getMethodInstance();
        $invoice = null;
		
		if($payment_method == 'ixcbadv') {
		  $isIxcbadvOnline = Mage::getModel('ixcbadv/ixcbadv')->_isRefundOnline();	
			if ($isIxcbadvOnline) {
			//Mage::getModel('ixcbadv/ixcbadv')->_callRefund($order->getId(),$baseAmountToRefund);
			}
			
		}else {
		
        if ($gateway->canRefund() && $creditmemo->getDoTransaction()) {
            $this->setCreditmemo($creditmemo);
            $invoice = $creditmemo->getInvoice();
            if ($invoice) {
                $isOnline = true;
                $captureTxn = $this->_lookupTransaction($invoice->getTransactionId());
                if ($captureTxn) {
                    $this->setParentTransactionId($captureTxn->getTxnId());
                }
                $this->setShouldCloseParentTransaction(true); // TODO: implement multiple refunds per capture
                try {
                    $gateway->setStore($this->getOrder()->getStoreId())
                        ->processBeforeRefund($invoice, $this)
                        ->refund($this, $baseAmountToRefund)
                        ->processCreditmemo($creditmemo, $this)
                    ;
                } catch (Mage_Core_Exception $e) {
                    if (!$captureTxn) {
                        $e->setMessage(' ' . Mage::helper('sales')->__('If the invoice was created offline, try creating an offline creditmemo.'), true);
                    }
                    throw $e;
                }
            }
        }
       }

        // update self totals from creditmemo
        $this->_updateTotals(array(
            'amount_refunded' => $creditmemo->getGrandTotal(),
            'base_amount_refunded' => $baseAmountToRefund,
            'base_amount_refunded_online' => $isOnline ? $baseAmountToRefund : null,
            'shipping_refunded' => $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => $creditmemo->getBaseShippingAmount(),
        ));

        // update transactions and order state
        $transaction = $this->_addTransaction(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
            $creditmemo,
            $isOnline
        );
		
		
		
   if($payment_method == 'ixcbadv') {
		$isIxcbadvOnline = Mage::getModel('ixcbadv/ixcbadv')->_isRefundOnline();	
			if ($isIxcbadvOnline) {
			Mage::getModel('ixcbadv/ixcbadv')->_callRefund($order->getId(),$baseAmountToRefund);
            $message = Mage::helper('sales')->__('Refunded amount of %s online.', $this->_formatPrice($baseAmountToRefund));
        } else {
            $message = $this->hasMessage() ? $this->getMessage()
                : Mage::helper('sales')->__('Refunded amount of %s offline.', $this->_formatPrice($baseAmountToRefund));
        }
		
		
	} else {
			
        if ($invoice) {
            $message = Mage::helper('sales')->__('Refunded amount of %s online.', $this->_formatPrice($baseAmountToRefund));
        } else {
            $message = $this->hasMessage() ? $this->getMessage()
                : Mage::helper('sales')->__('Refunded amount of %s offline.', $this->_formatPrice($baseAmountToRefund));
        }
	}
		
		
        $message = $message = $this->_prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);

        Mage::dispatchEvent('sales_order_payment_refund', array('payment' => $this, 'creditmemo' => $creditmemo));
        return $this;
    }
    

}
