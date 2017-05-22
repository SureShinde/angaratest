<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class  Angara_Customerreview_Model_Observer
{

   public function salesOrderStatusAfter($observer)
    {
		 $order = $observer->getEvent()->getOrder();
		 $status = $observer->getEvent()->getStatus();
		 $state = $observer->getEvent()->getState();
		 
		 $followupMailsCount = 5;
		 $followupSchedule[] = '+7 days';
		 $followupSchedule[] = '+10 days';
		 $followupSchedule[] = '+13 days';
		 $followupSchedule[] = '+16 days';
		 $followupSchedule[] = '+19 days';
		 
		 if($state=='complete'){
			for($i=0;$i<$followupMailsCount; $i++){
				$mod = Mage::getModel('customerreview/customerreviewlog');
				$mod->setEmail($order->getCustomerEmail())
					->setSequenceNumber($i+1)
					->setOrderId($order->getId())
					->setDate(date('Y-m-d H:m:s', strtotime($followupSchedule[$i])))
					->save();
			}	
		 }
    } 	
	
   public function orderCompletionFollowupAfter($observer)
    {
		 $orderId = $observer->getEvent()->getOrderid();
		 $email = $observer->getEvent()->getEmail();
		 $followupObjectId = $observer->getEvent()->getFollowupobjectid();
		 $sequenceNumber=$observer->getEvent()->getSequencenumber();
 
				$mod = Mage::getModel('customerreview/customerreviewlog');
				$mod->setEmail($email)
					->setSequenceNumber($sequenceNumber)
					->setOrderId($orderId)
					->setDate(now())//->setDate(date('Y-m-d H:m:s', strtotime($followupSchedule[$i])))
					->setFollowupObjectId($followupObjectId)
					->setStatus('R')
					->setRuleId( Mage::helper('customerreview/data')->getOrderCompletionRuleId())
					->save();			
    } 		
}
