<?php
class Ss_Additionalinformation_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_payments        = null;
	
	public function sendToVerifyAdditionalinformationEmail($orderId, $requestType)
	{
		$storeId = Mage::app()->getStore()->getId();
		$adminSession = Mage::getSingleton('admin/session');
		$_order= Mage::getModel('sales/order')->loadByIncrementId($orderId);
		$customerFirstName = $_order->getCustomerFirstname();
		$customerLastName = $_order->getCustomerLastname();
		$customerOrderEmail = $_order->getCustomerEmail();
		$adminUserId = $adminSession->getUserId();
		$adminUserFirstName = $adminSession->getFirstname();
		$adminUserLastName = $adminSession->getLastname();
		$adminUserName = $adminSession->getUsername();
		// Transactional Email Template's ID
		$templateId = 'additionalinformation_request_email_template';
	 
		// Set sender information          
		$senderName = 'Angara.com';
		$senderEmail = 'customercare-noreply@angara.com';
		$sender = array('name' => $senderName,
					'email' => $senderEmail);
		 
		// Set recepient information
		$recepientEmail = $customerOrderEmail;
		$recepientName = $customerFirstName.' '.$customerLastName;       
		 
		// Get Store ID    
		$store = Mage::app()->getStore()->getId();
	 	
		if($requestType=='scan'){
			$encryptedUrl = Mage::getModel('core/store')->load(1)->getUrl("additionalinformation?param=".Mage::helper('core')->urlEncode($_order->getId())."&key=".Mage::helper('core')->urlEncode($orderId)."&rt=scan",array('_secure'=>true));
		}else{
			$encryptedUrl = Mage::getModel('core/store')->load(1)->getUrl("additionalinformation?param=".Mage::helper('core')->urlEncode($_order->getId())."&key=".Mage::helper('core')->urlEncode($orderId),array('_secure'=>true));
		}
		//echo $encryptedUrl;die;
		//$encryptedUrl = Mage::getUrl().'additionalinformation?param='.Mage::helper('core')->urlEncode($_order->getId()).'&key='.Mage::helper('core')->urlEncode($orderId);
		$paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment($_order))
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
			
		// Set variables that can be used in email template
		$vars = array('url' => $encryptedUrl,
				  	'order_id' => $orderId,
				  	'order'        => $_order,
					'billing'      => $_order->getBillingAddress(),
					'payment_html' => $paymentBlockHtml);
				 
		$translate  = Mage::getSingleton('core/translate');
	 
		// Send Transactional Email
		try {
			Mage::getModel('core/email_template')
				->addBcc('neelam.somani@angara.com')		//	Adding BCC by Vaseem
				->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			$translate->setTranslateInline(true);
			return 1;
		}
		catch(Exception $ex) {
			Mage::getSingleton('core/session')->addError(Mage::helper('additionalinformation')->__('Unable to send email.'));
			return 0;
		}
	}
	
	public function sendOtpAdditionalinformationEmail($recepientEmail,$orderId)
	{
		// Transactional Email Template's ID
		$templateId = 'additionalinformation_otp_email_template';
		$_order= Mage::getModel('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($orderId));
		// Set sender information          
		$senderName = 'Angara.com';
		$senderEmail = 'customercare-noreply@angara.com';
		$sender = array('name' => $senderName,
					'email' => $senderEmail);
		 
		// Get Store ID    
		$store = Mage::app()->getStore()->getId();
		
		$otp = $this->getRandomOtp(9); // Something like Mp2tMpSw
		
		$otpencryptedUrl = Mage::getUrl("additionalinformation/index/otpcheck?key=".$orderId,array('_secure'=>true));
		
		// Set variables that can be used in email template
		$vars = array('otp' => $otp,
					'order'        => $_order,
					'otpurl' => $otpencryptedUrl
					);
				 
		$translate  = Mage::getSingleton('core/translate');
	 
		// Send Transactional Email
		try {
			Mage::getModel('core/email_template')
				->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			$translate->setTranslateInline(true);
			$loadAdditionalinformationModule = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
								->addFieldToFilter('order_increment_id',Mage::helper('core')->urlDecode($orderId))
								->getData();
			$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($loadAdditionalinformationModule[0]['additionalinformation_id']);
			$resonseCustomer->setData('customer_govt_email',$recepientEmail)
							->setData('otp_password',$otp)
							//->setData('flag',1)
							->setData('updated_at',now())
							->save();
			return 1;
		}
		catch(Exception $ex) {
			Mage::getSingleton('core/session')->addError(Mage::helper('additionalinformation')->__('Unable to send email.'));
			return 0;
		}
	}
	
	public function _resetFraudCheck($orderId)
	{
		$_order= Mage::getModel('sales/order')->loadByIncrementId($orderId);
		$customerFirstName = $_order->getCustomerFirstname();
		$customerLastName = $_order->getCustomerLastname();
		$customerOrderEmail = $_order->getCustomerEmail();
		// Transactional Email Template's ID
		$templateId = 'additionalinformation_reset_request_email_template';
	 
		// Set sender information          
		$senderName = 'Angara.com';
		$senderEmail = 'customercare-noreply@angara.com';
		$sender = array('name' => $senderName,
					'email' => $senderEmail);
		 
		// Set recepient information
		$recepientEmail = $customerOrderEmail;
		$recepientName = $customerFirstName.' '.$customerLastName;       
		 
		// Get Store ID    
		$store = Mage::app()->getStore()->getId();
	 	
		$encryptedUrl = Mage::getUrl("additionalinformation?param=".Mage::helper('core')->urlEncode($_order->getId())."&key=".Mage::helper('core')->urlEncode($orderId),array('_secure'=>true));
	
		// Set variables that can be used in email template
		$vars = array('url' => $encryptedUrl,
				  'order_id' => $orderId,
				  'order'        => $_order);
		$translate  = Mage::getSingleton('core/translate');
	 
		// Send Transactional Email
		try {
			Mage::getModel('core/email_template')
				->addBcc('neelam.somani@angara.com')		//	Adding BCC by Vaseem
				->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			$translate->setTranslateInline(true);
			return 1;
		}
		catch(Exception $ex) {
			Mage::getSingleton('core/session')->addError(Mage::helper('additionalinformation')->__('Unable to send email.'));
			return 0;
		}
	}
	
	public function _fraudCheckConfirmation($orderId){
		$_order= Mage::getModel('sales/order')->load($orderId);
		$customerFirstName = $_order->getCustomerFirstname();
		$customerLastName = $_order->getCustomerLastname();
		$customerOrderEmail = $_order->getCustomerEmail();
		// Transactional Email Template's ID
		$templateId = 'additionalinformation_send_confirmation_email_template';
	 
		// Set sender information          
		$senderName = 'Angara.com';
		$senderEmail = 'customercare-noreply@angara.com';
		$sender = array('name' => $senderName,
					'email' => $senderEmail);
		 
		// Set recepient information
		$recepientEmail = $customerOrderEmail;
		$recepientName = $customerFirstName.' '.$customerLastName;       
		 
		// Get Store ID    
		$storeId = Mage::app()->getStore()->getId();
	 	
		//$encryptedUrl = Mage::getUrl("additionalinformation?param=".Mage::helper('core')->urlEncode($_order->getId())."&key=".Mage::helper('core')->urlEncode($orderId),array('_secure'=>true));
	
		// Set variables that can be used in email template
		$vars = array(
				  'order_id' => $_order->getIncrementId(),
				  'order'        => $_order);
		$translate  = Mage::getSingleton('core/translate');
		// Send Transactional Email
		try {
			Mage::getModel('core/email_template')
				->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			$translate->setTranslateInline(true);
			return 1;
		}
		catch(Exception $ex) {
			Mage::getSingleton('core/session')->addError(Mage::helper('additionalinformation')->__('Unable to send email.'));
			return 0;
		}
	}
	
	public function _loadByOrderId($orderId){
		return Mage::getModel('additionalinformation/additionalinformation')->getCollection()
								->addFieldToFilter('order_increment_id',Mage::helper('core')->urlDecode($orderId))
								->getData();
	}
	
	public function _loadByOrderIncrementId($orderId){
		return Mage::getModel('additionalinformation/additionalinformation')->getCollection()
								->addFieldToFilter('order_increment_id',$orderId)
								->getData();
	}
	
	public function getRandomOtp($l, $c = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'){
			for ($s = '', $cl = strlen($c)-1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i);
			return $s;
	}
	
	
	/*********************** GET PAYMENTS ***************************/

    public function getPaymentsCollection($_order)
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection')
                ->setOrderFilter($_order);

            if ($_order->getId()) {
                foreach ($_order->_payments as $payment) {
                    $payment->setOrder($_order);
                }
            }
        }
        return $this->_payments;
    }
	/**
     * Retrieve order payment model object
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function getPayment($_order)
    {
        foreach ($this->getPaymentsCollection($_order) as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        return false;
    }	
}