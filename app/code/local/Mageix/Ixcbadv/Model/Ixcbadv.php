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


class Mageix_Ixcbadv_Model_Ixcbadv extends Mage_Payment_Model_Method_Abstract
{
	/*	
	const PAY_ACTION_SALE = 'Sale';
    const PAY_ACTION_AUTHORIZE = 'Authorize';
    const PAY_ACTION_CONFIRM  = 'Confirm';
	*/
	
	
	protected $_formBlockType = 'ixcbadv/inline_form';
	protected $_code = 'ixcbadv';
	
	protected $_isGateway               = false;
        protected $_canOrder                    = false;
        protected $_canAuthorize                = true;
        protected $_canCapture                  = true;
        protected $_canCapturePartial           = true;
        protected $_canRefund                   = true;
        protected $_canRefundInvoicePartial     = true;
        protected $_canVoid                     = true;
        protected $_canUseCheckout              = false;
        protected $_canFetchTransactionInfo     = true;
        protected $_canCreateBillingAgreement   = false;
        protected $_canReviewPayment            = true;
	
	protected $_canUseInternal = false;
	protected $_isAvailable = null;
	protected $_canUseForMultishipping  = false;
	


    
    public function capture(Varien_Object $payment, $amount)
    {
      $order = $payment->getOrder();
      $order_id = $order->getId();

      if (strtoupper($this->_getPaymentAction()) == "CONFIRM"){
            if ($this->_canAuthorize($order->getExtOrderId())){
               $_authorizationResp = $this->doAuthorize($order->getExtOrderId(),$amount,"false");
				
				if (!$authorizationResp->Error){

                $_authorizationData = $this->_processAuthorization($_authorizationResp,$order->getId());

				$amazonAuthorizationId = (string) $_authorizationResp
                                                    ->AuthorizeResult
			                    ->AuthorizationDetails
			                    ->AmazonAuthorizationId;
				
				$saveTransactionData["transaction_type"] = "authorization";
	            $saveTransactionData["order_id"] = $order_id;
	            $saveTransactionData["transaction_type_id"] =  (string) $_authorizationResp
			                                                            ->AuthorizeResult
			                                                            ->AuthorizationDetails
			                                                            ->AmazonAuthorizationId;
				 

                if(isset($_authorizationData["ReasonCode"]) && strtoupper($_authorizationData["ReasonCode"]) == "TRANSACTIONTIMEDOUT"){
			  $this->_asyncAuthorize($order_id,$amount);	
		    }else {
		      $this->_saveAmzTransaction($saveTransactionData);
		    }
			
		   }

		  }
			
		 if($amazonAuthorizationId != ''){
			if ($this->_canCapture($amazonAuthorizationId,$amount)){
					
                  $captureResp = $this->getApiAdapter()->_capture($order->getExtOrderId(),$amazonAuthorizationId,$amount);
				  $this->_processCapture($captureResp,$order->getId());

				  $saveTransactionData = array();												
			      $saveTransactionData["transaction_type"] = "capture";
				  $saveTransactionData["authorization_id"] = $amazonAuthorizationId;
	              $saveTransactionData["order_id"] = $order->getId();
	              $saveTransactionData["transaction_type_id"] =  (string) $captureResp
  	                                                              ->CaptureResult
  	                                                              ->CaptureDetails
  	                                                              ->AmazonCaptureId;
														
				$this->_saveAmzTransaction($saveTransactionData);
				}
			  
          }
      }elseif (strtoupper($this->_getPaymentAction()) == "AUTHORIZE") {

      	if ($this->_getAuthorizationId($order->getId()) != ''){

				if ($this->_canCapture($this->_getAuthorizationId($order->getId()),$amount)){
					
                  $captureResp = $this->getApiAdapter()->_capture($order->getExtOrderId(),$this->_getAuthorizationId($order->getId()),$amount);
				  $this->_processCapture($captureResp,$order->getId());
				  
				  $saveTransactionData = array();												
			      $saveTransactionData["transaction_type"] = "capture";
				  $saveTransactionData["authorization_id"] = $this->_getAuthorizationId($order->getId());
	              $saveTransactionData["order_id"] = $order->getId();
	              $saveTransactionData["transaction_type_id"] =  (string) $captureResp
  	                                                              ->CaptureResult
  	                                                              ->CaptureDetails
  	                                                              ->AmazonCaptureId;
														
				$this->_saveAmzTransaction($saveTransactionData);
				}
				
          }

      }
      
 }



public function _getAmzAuthorizationId($order_id) 
{

	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_order_transaction');      
    $ordidquery = "SELECT transaction_type_id FROM $tableName WHERE order_id = '".$order_id."'";
    $orderQuery = $read->fetchAll($ordidquery);
    $amazonAuthorizationId = $orderQuery[0]['transaction_type_id'];
				
	return $amazonAuthorizationId;
						  
 }
 


protected function _processCapture($captureResponse,$order_id) 
  {
  	        $amazonCaptureId = (string) $captureResponse
  	                                    ->CaptureResult
  	                                    ->CaptureDetails
  	                                    ->AmazonCaptureId;
										
			$sorp = $this->sorp($amazonCaptureId);

            $captureReferenceId = (string) $captureResponse
                                           ->CaptureResult
                                           ->CaptureDetails
                                           ->CaptureReferenceId;
										   
            $captureState = (string) $captureResponse
                                     ->CaptureResult
                                     ->CaptureDetails
                                     ->CaptureStatus
                                     ->State;
									 
            $capturedAmount = (string) $captureResponse
                                      ->CaptureResult
                                      ->CaptureDetails
                                      ->CaptureAmount
                                      ->Amount;
									  
			$currencyCode = (string) $captureResponse
                                      ->CaptureResult
                                      ->CaptureDetails
                                      ->CaptureAmount
                                      ->CurrencyCode;
                                      
           $creationTimestamp =  (string) $captureResponse
                                           ->CaptureResult
                                           ->CaptureDetails
                                           ->CreationTimestamp;
									  
			if ($captureState != '' ) {
			
			                    $currency_symbol = Mage::app()
			                                       ->getLocale()
			                                       ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
			                                       ->getSymbol();
				
                                $capAmount = $currency_symbol.''.$capturedAmount;
								
								$order = Mage::getModel('sales/order')->load($order_id);
								
								if($sorp != PORS) 
			                                        {
			                                         $this->_logCapture(Mage::helper("ixcbadv")->getMerchantId(), $amazonCaptureId, $capturedAmount, $currencyCode, $order->getExtOrderId(), $creationTimestamp);
			                                        }
				
				                $message_ipn = '';
								$message_ipn .= 'CAPTURE Status:  '. strtoupper($captureState).'  ';
								
								if (strtoupper($captureState) == "DECLINED" || strtoupper($captureState) == "CLOSED") {
								 $reasonCode  = (string) $captureResponse
								                         ->CaptureResult
								                         ->CaptureDetails
								                         ->CaptureStatus
								                         ->ReasonCode;
								 $message_ipn .= '<br/>Reason Code:  '. $reasonCode.'  ';	
								}
								
								$message_ipn .= '<br/> Amazon Capture ID:  '. $amazonCaptureId.'  ';
								$message_ipn .= '<br/> Captured Amount:  '. $capAmount.'  ';
                                
								$order->addStatusToHistory(
										Mage_Sales_Model_Order::STATE_PROCESSING,
										$message_ipn
								);
								
								$order->save();
								
								return $captureState;

								
				}
	
  }
    



public function  _saveAmzTransaction($saveTransactionData) 
{									 
   	if($saveTransactionData["transaction_type"] == "capture"){
		
		$authorizationCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('transaction_type_id',$saveTransactionData["authorization_id"]);
					
	   foreach($authorizationCollection as $authorizationRow){
            $row_id = $authorizationRow->getTransactionId();
		
		//$_captureData = array('invoice_id'=>$saveTransactionData["invoice_id"],
                              //'cm_id'=>$saveTransactionData["transaction_type_id"]);
                              
                $_captureData = array('cm_id'=>$saveTransactionData["transaction_type_id"]);

		$orderIdsModel = Mage::getModel('ixcbadv/transaction')->load($row_id)->addData($_captureData);
		
		try {
               $orderIdsModel->setId($row_id)->save();
            } catch (Exception $e){
              //echo $e->getMessage();
            }
       }
	   
		
	}elseif ($saveTransactionData["transaction_type"] == "refund"){
	
	$captureCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('cm_id',$saveTransactionData["capture_id"]);
					
	   foreach($captureCollection as $captureRow){
            $row_id = $captureRow->getTransactionId();
                              
                $_refundData = array('ref_id'=>$saveTransactionData["refund_id"]);

		$orderIdsModel = Mage::getModel('ixcbadv/transaction')->load($row_id)->addData($_refundData);
		
		try {
               $orderIdsModel->setId($row_id)->save();
            } catch (Exception $e){
              //echo $e->getMessage();
            }
       }

	}else {
										 
   if(isset($saveTransactionData["state"])){
   	
   	Mage::getModel('ixcbadv/transaction')->setOrderId($saveTransactionData["order_id"])
										 ->setTransactionTypeId($saveTransactionData["transaction_type_id"])
										 ->setTransactionType($saveTransactionData["state"])
										 ->save();
	}else {
		
	Mage::getModel('ixcbadv/transaction')->setOrderId($saveTransactionData["order_id"])
	                                     //->setTransactionType($saveTransactionData["transaction_type"])
										 ->setTransactionTypeId($saveTransactionData["transaction_type_id"])
										 ->save();	
	  }
	}				  
 }

    
    public function doAuthorize($reference_id,$amount,$isCapture,$timeout='')
    {
        $authorizationResp = $this->getApiAdapter()->_authorize($reference_id,$amount,$isCapture);
		
        if (!$authorizationResp->Error){
        return $authorizationResp;
	    }else {
	    	Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be authorized.'".$authorizationResp->Error->Message." ' "));	
	    }
    }
    
    
    public function doRefund($amazonCaptureId,$amazonOrderReferenceId,$refundAmount)
    {
        $refundResp = $this->getApiAdapter()->_refund($amazonCaptureId,$amazonOrderReferenceId,$refundAmount);
        
        if (!$refundResp->Error){
        return $refundResp;
	    }else {
	    	Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be refunded.'".$refundResp->Error->Message." ' "));	
	    }
    } 
    


    
    public function _callRefund($order_id,$refundAmount)
    {
        
    	$amazonCaptureId = $this->_getCaptureId($order_id,$refundAmount);

    	if ($this->_canRefund($amazonCaptureId)){
    	$order = Mage::getModel('sales/order')->load($order_id);
        $refundResult = $this->doRefund($amazonCaptureId,$order->getExtOrderId(),$refundAmount);
        $this->_processRefund($refundResult,$order_id,$amazonCaptureId);
        }
    	
    }
    
    
	


	/**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
		$currentUrl = Mage::helper("core/url")->getCurrentUrl();
        if (is_null($this->_isAvailable)) {
        $enable_disable_ixcbadv = Mage::helper("ixcbadv")->canInlineCheckout();
			if($enable_disable_ixcbadv == "1") {
				$custom_val = true;
			}else{
				if(preg_match("/ixcbadv/is", $currentUrl)){
					$custom_val = true;
				}else{
					$custom_val = false;
				}
			}
			$this->_isAvailable = $custom_val;
            $this->_canUseCheckout = $custom_val;
        }
        return $this->_isAvailable;
    }


	public function validate(){
		parent::validate();
		$getIxcbadvvartocheck = '';
		$info = $this->getInfoInstance();
		$getIxcbadvvartocheck = $info->getIxcbadvvartocheck();
		if(isset($getIxcbadvvartocheck) && $getIxcbadvvartocheck == 'yes') {
			Mage::throwException("Please click on Pay with Amazon button to use this option.");
		}
	}
	
	
	
	 public function getApiAdapter()
         {
          return Mage::getModel('ixcbadv/api_adapter');
         }
         
         
         public function _canAuthorize($orderReferenceId)
         {
          $orderReferenceState = $this->_getOrderReferenceStatus($orderReferenceId);
          if(isset($orderReferenceState) && strtoupper($orderReferenceState) == "OPEN"){
            return true;
          }else {
            return false;
          }
         }
		 
		 
		 
		 public function _canRefund($amazonCaptureId)
         {
          
             if (!$this->_isRefundOnline()){
                 return false;
             }
          
          $_captureDetailsResp = $this->getApiAdapter()->_getCaptureDetails($amazonCaptureId);
		  $captureState = $_captureDetailsResp->GetCaptureDetailsResult->CaptureDetails->CaptureStatus->State;
		  
		  if(strtoupper($captureState) == "PENDING"){
				
			 Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be refunded. Capture ID ' ".$amazonCaptureId." ' is '".strtoupper($captureState)." ' "));	
				
			}elseif((strtoupper($captureState) == "DECLINED") || (strtoupper($captureState) == "CLOSED")){
				
				$reasonCode = (string) $_captureDetailsResp
			                            ->GetAuthorizationDetailsResult
			                            ->AuthorizationDetails
			                            ->AuthorizationStatus
			                            ->ReasonCode;
																
			 Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be refunded. Capture ID ' ".$amazonCaptureId." ' is '".strtoupper($captureState)." ' Reason Code:'".$reasonCode." "));
				
			}
		  
		  $capturedAmount = (string)  $_captureDetailsResp->GetCaptureDetailsResult->CaptureDetails->CaptureAmount->Amount;
		  
		  if($this->_checkAmounts($capturedAmount,$amount)){
			return true;
		  }
		  
         }

        
		
		 public function _checkAmounts($capturedAmount,$amount)
		 {
				$division = 15 / 100;
				$division = round($division, 2);
				$fifteenpercent = $division * $capturedAmount;
				$maxPercentAmount = $fifteenpercent + $capturedAmount;
				
				$plusSeventyFive = 75 + $capturedAmount;
				
				if($plusSeventyFive < $maxPercentAmount){
					if($amount <= $plusSeventyFive){
						return true;
					}	
				}elseif ($plusSeventyFive > $maxPercentAmount){
					if($amount <= $maxPercentAmount){
						return true;
					}
				}else {
					 Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be refunded. Refund amount ' ".$amount." ' must not exceed 15% or $75 (whichever is less) above the captured amount:' ".$capturedAmount." ' "));
				}
		 	
			
		 }
		 
		 
		 
		 public function _canCapture($authorization_id,$amount)
		 {
		 	$_authorizationDetailsResp = $this->getApiAdapter()->_getAuthorizationDetails($authorization_id);
			
			$authorizationState = (string) $_authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationStatus
			                                                    ->State;
																
			if(strtoupper($authorizationState) == "PENDING"){
			 $message = "This invoice could not be captured. Authorization ID ' ".$authorization_id." ' is '".strtoupper($authorizationState)." '  This capture will be attempted automatically once the authorization is ready for capture. ";
			 Mage::getSingleton('adminhtml/session')->addError($message);	
			 
			}elseif((strtoupper($authorizationState) == "DECLINED") || (strtoupper($authorizationState) == "CLOSED")){
				
				$reasonCode = (string) $_authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationStatus
			                                                    ->ReasonCode;
																
			 Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be captured. Authorization ID ' ".$authorization_id." ' is '".strtoupper($authorizationState)." ' Reason Code:'".$reasonCode." "));
				
			}
																
																
			$authorizedAmount = (string) $_authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationAmount
			                                                    ->Amount;
																
			if($authorizedAmount != '' && $authorizedAmount >= $amount){
				
				return true;
				
			}else {
				Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be captured. Capture amount ' ".$amount." ' must be equal or less than the authorized amount '".$authorizedAmount." ' "));
			}
			
		 	
		 }
         
         
         public function _getAuthorizationId($order_id)
         {
            /*	
			$authorizationCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('order_id',$order_id);
            */
            //direct access is quicker than loading model
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_order_transaction');        
            $authquery = "SELECT transaction_type_id FROM $tableName WHERE order_id = '".$order_id."'";
            $authIdQuery = $read->fetchAll($authquery);
            $authorization_id = $authIdQuery[0]['transaction_type_id'];
            
            return $authorization_id;

         }
         
         public function _getCaptureId($order_id,$refundAmount)
         {
            $captureCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('order_id',$order_id);
                           
            foreach($captureCollection as $captureInstance){
                   $capture_id = $captureInstance->getCmId();
                   $captureDetailsResp = $this->getApiAdapter()->_getCaptureDetails($capture_id);

                   $captureState = (string) $captureDetailsResp
			                                      ->GetCaptureDetailsResult
			                                      ->CaptureDetails
			                                      ->CaptureStatus
			                                      ->State;
		   $captureAmount = (string) $captureDetailsResp
			                                       ->GetCaptureDetailsResult
			                                       ->CaptureDetails
			                                       ->CaptureAmount
			                                       ->Amount;
			                                       
	           $refundedAmount = (string) $captureDetailsResp
			                                       ->GetCaptureDetailsResult
			                                       ->CaptureDetails
			                                       ->RefundedAmount
			                                       ->Amount;
		                                          
		   $capturedBalance = $captureAmount - $refundedAmount;
		   
			                                       
			                                       
	           if (strtoupper($captureState) == "COMPLETED" && $capturedBalance >= $refundAmount) {
	              return $capture_id;
	           }
                   
            }

         }
         
         
          public function _isShipCapture()
         {
           if (Mage::helper('ixcbadv')->getShipCapture() == 1) {
            return true;
           }else {
            return false;
           }
         }
         
         
          public function _isRefundOnline()
         {
           if (Mage::helper('ixcbadv')->getRefundOnline() == 1) {
            return true;
           }else {
            return false;
           }
         }
         
         
          public function _getPaymentAction()
         {
           return Mage::helper('ixcbadv')->getPaymentAction(); 
         }
		 
		 
		 public function sorp($id_string) 
         {
		  $idPrefix = substr($id_string,0,1);
          return strtoupper($idPrefix);
         }
         
         
         protected function _processRefund($refundReferenceDetails,$order_id,$amazonCaptureId)
         {
         
         $order = Mage::getModel('sales/order')->load($order_id);
				
	 $currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
         
         $amazonRefundId = (string) $refundReferenceDetails->RefundResult->RefundDetails->AmazonRefundId;
	 $refundAmount = (string) $refundReferenceDetails->RefundResult->RefundDetails->RefundAmount->Amount;
	 $refundState = (string) $refundReferenceDetails->RefundResult->RefundDetails->RefundStatus->State;
	 $refundState = strtoupper($refundState);
	 $refundAmount = $currency_symbol.''.$refundAmount;
	 
	  if ($refundState != '' ) {
	
	     $message_ipn = '';
	     $message_ipn .= 'Transaction REFUND. Status: '. $refundState.' ';
	     $message_ipn .= '<br/> Amazon Refund Id:  '. $amazonRefundId.'  ';
	     $message_ipn .= '<br/> Refund Amount:  '.$refundAmount.'  ';

	     $order->addStatusToHistory($order->getStatus(),$message_ipn);
								
	     $order->save();
	  }
	  
	  
	  $saveTransactionData["transaction_type"] = "refund";
          $saveTransactionData["capture_id"] = $amazonCaptureId;
          $saveTransactionData["refund_id"] = $amazonRefundId;
          $this->_saveAmzTransaction($saveTransactionData);
         
         }
         
         
         protected function _processAuthorization($authorizationResponse,$order_id) 
         {
	
  	        $authorizationReferenceId = (string) $authorizationResponse
  	                                            ->AuthorizeResult
  	                                            ->AuthorizationDetails
  	                                            ->AuthorizationReferenceId;
												
												
			$amazonAuthorizationReferenceId = (string) $authorizationResponse
			                                    ->AuthorizeResult
			                                    ->AuthorizationDetails
			                                    ->AmazonAuthorizationId;
												
			$authorizationState = (string) $authorizationResponse
			                                    ->AuthorizeResult
			                                    ->AuthorizationDetails
			                                    ->AuthorizationStatus
			                                    ->State;
												
			$authorizedAmount = (string) $authorizationResponse
			                                    ->AuthorizeResult
			                                    ->AuthorizationDetails
			                                    ->AuthorizationAmount
			                                    ->Amount;

			
		    $authorizationData = array();
			$authorizationData["AmazonAuthorizationReferenceId"] = $amazonAuthorizationReferenceId;
	        $authorizationData["AuthorizationState"] = $authorizationState; 
	        $authorizationData["AuthorizedAmount"] = $authorizedAmount;

			if ($authorizationState != '' ) {
				
				$order = Mage::getModel('sales/order')->load($order_id);
				
				$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
				
                $authAmount = $currency_symbol.''.$authorizedAmount;



								$message_ipn = '';
								$message_ipn .= 'AUTHORIZATION Status:  '. strtoupper($authorizationState).'  ';
								
						if (strtoupper($authorizationState) == "DECLINED" || strtoupper($authorizationState) == "CLOSED") {
						
						
								 $reasonCode  = (string) $authorizationResponse
								                         ->AuthorizeResult
								                         ->AuthorizationDetails
								                         ->AuthorizationStatus
								                         ->ReasonCode;
                      
								 $authorizationData["ReasonCode"] = $reasonCode;
								 $message_ipn .= '<br/>Reason Code:  '. $reasonCode.'  ';	
								}
								
								$message_ipn .= '<br/> Amazon Authorization ID:  '. $amazonAuthorizationReferenceId.'  ';
								$message_ipn .= '<br/> Authorized Amount:  '. $authAmount.'  ';
								
                                
								$order->addStatusToHistory(
										Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
										$message_ipn
								);
								
								$order->save();
	
			}

                 return $authorizationData;
	
               }
  
            public function _asyncAuthorize($order_id,$amount) 
            {
			
			$order = Mage::getModel('sales/order')->load($order_id);		  
		    $_authorizationResp = $this->getApiAdapter()->_authorize($order->getExtOrderId(),$amount,"false","1440");

			
			$_authState =  (string) $_authorizationResp
			                                     ->AuthorizeResult
			                                     ->AuthorizationDetails
			                                     ->AuthorizationStatus
			                                     ->State;
			
	        $this->_processAuthorization($_authorizationResp,$order_id);	

				$saveTransactionData = array();												
			    $saveTransactionData["transaction_type"] = "authorization";
	            $saveTransactionData["order_id"] = $order_id;
	            $saveTransactionData["transaction_type_id"] =  (string) $_authorizationResp
			                                                            ->AuthorizeResult
			                                                            ->AuthorizationDetails
			                                                            ->AmazonAuthorizationId;
				if(strtoupper($_authState) == "PENDING"){													
                   $saveTransactionData["state"] = $_authState;
				}
			                                                            
																
			$this->_saveAmzTransaction($saveTransactionData);
																
            }

       public function _logCapture($sellerId, $amazonCaptureId, $capturedAmount, $currencyCode, $referenceId, $creationTimestamp)
            {
            
	
	      $domain_url = Mage::getUrl();
	      $parse_domain_name = parse_url($domain_url);
	      $domain_name = $parse_domain_name['host'];
	      
	      $sellerId = Mage::helper('core')->decrypt($sellerId);
					
	       $logdata = array('domain_name' => $domain_name, 'frontend_url' => $domain_url, 'seller_id' => $sellerId, 'marketplace_id' => MARKETPLACE_ID,  'amazon_capture_id' => $amazonCaptureId,  'amount' => $capturedAmount, 'currency_code' => $currencyCode, 'reference_id' => $referenceId, 'timestamp' => $creationTimestamp, 'platform_id' => PLATFORM_ID);

            $lic_url_possibilities = array();
			
	        $lic_url_possibilities[] = TPV_PATH_IXURL_METHOD;

            foreach($lic_url_possibilities as $url) {
			
			$ch = curl_init ($url);
               curl_setopt ($ch, CURLOPT_POST, true);
               curl_setopt ($ch, CURLOPT_POSTFIELDS, $logdata);
               curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
               $returndata = curl_exec ($ch);
               
               if(isset($returndata) && strtoupper($returndata) == 'SUCCESS')
	         {
		  break;
		  			
	         }

		curl_close($ch);
		
            }
    
        }
            
         
        
        public function _getOrderReferenceStatus($amazonOrderReferenceId)
        {
          $orderReferenceDetailsResp = $this->getApiAdapter()->_getOrderReferenceDetails($amazonOrderReferenceId);
		  
          if (!$orderReferenceDetailsResp->Error){
          $orderReferenceDetailsArray = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails;
          
          $orderReferenceState = (string) $orderReferenceDetailsArray->OrderReferenceStatus->State;
          if(strtoupper($orderReferenceState) != "OPEN"){
          	Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be authorized. Order reference state:'".$orderReferenceState." ' "));
          }
          return (string) $orderReferenceDetailsArray->OrderReferenceStatus->State;
          }
         else {
            Mage::throwException(Mage::helper('ixcbadv')->__("This order cannot be authorized.'".$orderReferenceDetailsResp->Error->Message." ' "));
            
          }
         
        }
		
		
	
	


}
?>
