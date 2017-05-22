<?php
class Angara_CustomEmails_Model_Observer
{
    public function invoicedStatusChange($event)
    {
        $order 			= 	$event->getOrder();
		$orderOldState	=	$event->getEvent()->getData('data_object')->getOrigData('state');
		$orderNewState	=	$event->getEvent()->getData('data_object')->getData('state');
		Mage::log('Order old state is '.$orderOldState.' and new state is '.$orderNewState.' for order id '.$order->getIncrementId(), null, 'order_status.log');

        if ( $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED && ($orderOldState != $orderNewState) ){
		//if ( $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED  ){
			Mage::log('Sending order cancel email for order id '.$order->getIncrementId(), null, 'order_status.log');
            $this->_sendStatusMail($order);
		}
    }
 
    private  function _sendStatusMail($order)
    {
        $emailTemplate  = Mage::getModel('core/email_template');
 		
		if ($order->getCustomerIsGuest()) {
			$emailTemplate->loadDefault('order_status_change_email_guest');
			$emailTemplateVariables['billing'] 	= 	$order->getBillingAddress();
			$customerName						=	$order->getBillingAddress()->getName();
		} else {
			$emailTemplate->loadDefault('order_status_change_email');
			$customerName						=	$order->getCustomerName();
		}

		//$emailSubject	=	"Angara order # ".$order->getIncrementId()." cancellation update";
		$emailSubject	=	"Angara order # ".$order->getIncrementId()." update confirmation.";
        $emailTemplate->setTemplateSubject($emailSubject);
 
        // Get General email address (Admin->Configuration->General->Store Email Addresses)
		$salesData['email'] 	= 	Mage::getStoreConfig('trans_email/ident_sales/email');
		$salesData['name'] 		= 	Mage::getStoreConfig('trans_email/ident_sales/name');
		
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);
 		
		$emailTemplateVariables['order'] 	= 	$order;
		$emailTemplateVariables['customer_name'] 	= 	$customerName;
		
		try{
			$emailTemplate->send($order->getCustomerEmail(), $customerName, $emailTemplateVariables);
			//$emailTemplate->send('vaseem.ansari@angara.com', $order->getStoreName(), $emailTemplateVariables);
			Mage::getSingleton('core/session')->addSuccess('The order cancelled email has been sent to customer.');
		}catch(Mage_Core_Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMmessage());
		}catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('There has been some error. Please check the error log.'));
            Mage::logException($e);
		}
    }
}
?>