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

class Mageix_Ixcbadv_IpnController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
		$ipnData = json_decode(file_get_contents('php://input'), true);
		$ipnData = json_decode($ipnData[Message], true);

	        $methodVars = Mage::helper('ixcbadv')->getAllConfigVars();
			$merchantId = Mage::helper('core')->decrypt($methodVars['merchantid']);
			$accessKeyID = Mage::helper('core')->decrypt($methodVars['accesskeyid']);
			$secretKeyID = Mage::helper('core')->decrypt($methodVars['secretkeyid']);
			
			$helper = Mage::helper("ixcbadv");
			
			$hmac_sha1_algorithm = 'sha1';
			$email_body_user = '';
			$notification_data = '';
			$notification_type = '';
			$uuid_request = '';
			$timestamp_request = '';
			$signature_request = '';

			foreach($ipnData as $key => $val) {
				$email_body_user .= htmlentities($key).' => '.htmlentities($val)."<br/>";
				if(strtolower($key) == 'notificationreferenceid') { $notificationReferenceId = $val; }
				if(strtolower($key) == 'notificationtype') { $notification_type = $val; }
				if(strtolower($key) == 'releaseenvironment') { $releaseEnvironment = $val; }
				if(strtolower($key) == 'marketplaceid') { $marketplaceId = $val; }
				if(strtolower($key) == 'releaseenvironment') { $releaseEnvironment = $val; }
				if(strtolower($key) == 'sellerid') { $sellerId = $val; }
				if(strtolower($key) == 'notificationdata') { $notification_data = $val; }
				if(strtolower($key) == 'version') { $version = $val; }
				if($key == 'UUID') { $uuid_request = $val; }
				elseif($key == 'Timestamp') { $timestamp_request = $val; }
				elseif($key == 'SignatureVersion') { $signatureVersion = $val; }
				elseif($key == 'Signature') { $signature_request = $val; }
				
			}

			$merge_request = $uuid_request.$timestamp_request;
			$rawhmac_request = hash_hmac($hmac_sha1_algorithm, $merge_request, $secretKeyID, true);
			$base64_encode_rawhmac = base64_encode($rawhmac_request);
			
			$currentTimezone = @date_default_timezone_get();
			@date_default_timezone_set('GMT');
			$date_gmt = date('Y-m-d H:i:s');
			@date_default_timezone_set($currentTimezone);
			$minutes_diff = round(abs(strtotime($date_gmt) - strtotime($timestamp_request)) / 60,2);
			
			
			if ($notification_type == "PaymentAuthorize") {
			
			$array_notification_data = $this->xml2array($notification_data);
			//$authorizationReferenceId = $array_notification_data['AuthorizationNotification']['AuthorizationDetails']['AuthorizationReferenceId'] ['value'];
		$amazonAuthorizationId = $array_notification_data['AuthorizationNotification']['AuthorizationDetails']['AmazonAuthorizationId'] ['value'];
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_order_transaction');        
        $statequery = "SELECT transaction_type FROM $tableName WHERE transaction_type_id = '".$amazonAuthorizationId."'";
        $stateQu = $read->fetchAll($statequery);
        $local_authorization_state = $stateQu[0]['transaction_type'];

		if (strtoupper($local_authorization_state) == "PENDING" || strtoupper($local_authorization_state) == "OPEN") {

	         $authorizationDetailsResp = $this->getApiAdapter()->_getAuthorizationDetails($amazonAuthorizationId);
			
			 $authorizationState = (string) $authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationStatus
			                                                    ->State;
			
			        $authorizedAmount = (string) $authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationAmount
			                                                    ->Amount;
			        if ($authorizationState != '' ) {
			        	
						$read = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_order_transaction');        
                        $ordidquery = "SELECT order_id FROM $tableName WHERE transaction_type_id = '".$amazonAuthorizationId."'";
                        $orderQuery = $read->fetchAll($ordidquery);
                        $order_id = $orderQuery[0]['order_id'];

				$order = Mage::getModel('sales/order')->load($order_id);

				$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
				
                                $authAmount = $currency_symbol.''.$authorizedAmount;



								$message_ipn = '';
								$message_ipn .= 'AUTHORIZATION Status:  '. strtoupper($authorizationState).'  ';
								
							if (strtoupper($authorizationState) == "OPEN"){
							$orderStatus = $order->getStatus();
							 }
								
						        if (strtoupper($authorizationState) == "DECLINED") {
                                                                 $orderStatus = Mage_Sales_Model_Order::STATE_CANCELED;
                                                                 
                                                                 $reasonCode = (string) $authorizationDetailsResp
			                                                    ->GetAuthorizationDetailsResult
			                                                    ->AuthorizationDetails
			                                                    ->AuthorizationStatus
			                                                    ->ReasonCode;
								 $message_ipn .= '<br/>Reason Code:  '. $reasonCode.'  ';	
								}
								
								$message_ipn .= '<br/> Amazon Authorization ID:  '. $amazonAuthorizationId.'  ';
								$message_ipn .= '<br/> Authorized Amount:  '. $authAmount.'  ';
								//$message_ipn .= '<br/> by IPN';
								if(!isset($orderStatus) || $orderStatus == ''){
								$orderStatus = $order->getStatus();
								}
                                
								$order->addStatusToHistory(
										$orderStatus,
										$message_ipn
								);
								
						if (strtoupper($authorizationState) == "DECLINED") {
								$order->cancel();
						}
								
								$order->save();
								$this->getResponse()->setHttpResponseCode(200);
								
								$saveTransactionData["notification_type"] = "PaymentAuthorize";
                                $saveTransactionData["authorization_id"] = $amazonAuthorizationId;
                                $saveTransactionData["state"] = $authorizationState;
								
								$this->updateAmzTransaction($saveTransactionData);
								
					    //capture		
						if(strtoupper($authorizationState) == "OPEN"){
							
							 if(strtoupper($helper->getPaymentAction()) != "AUTHORIZE"){
							
							    $splitArr = explode('-A', $amazonAuthorizationId);
								$amazonOrderReference =  $splitArr[0];
				  
				                 $captureResp = $this->getApiAdapter()
				                                         ->_capture($amazonOrderReference,$amazonAuthorizationId,$authorizedAmount);
														 
													      $this->_processCapture($captureResp,$order->getId()); 
														  

														  
										$amzCaptureId = (string) $captureResp
  	                                                                     ->CaptureResult
  	                                                                     ->CaptureDetails
  	                                                                      ->AmazonCaptureId;
																	 
									    $captureState = (string) $captureResp
			                                                    ->CaptureResult
								                                ->CaptureDetails
								                                ->CaptureStatus
			                                                    ->State;
			
			                           $capturedAmount = (string) $captureResp
			                                                    ->CaptureResult
			                                                    ->CaptureAmount
			                                                    ->Amount;
							
										 $saveTransactionData = array();												
			                             $saveTransactionData["notification_type"] = "PaymentCapture";
										 $saveTransactionData["authorization_id"] = $amazonAuthorizationId;
                                         $saveTransactionData["capture_id"] = $amzCaptureId;
										 		
										 $this->updateAmzTransaction($saveTransactionData);
										 
										 if(strtoupper($helper->getPaymentAction()) == "SALE"){
											$this->_createInvoice($order_id);
										 }
										 
								}		 

				         }
	
			       }

				
			
	
	       }
	       
	  }elseif ($notification_type == "PaymentRefund") {
			
			
			$array_notification_data = $this->xml2array($notification_data);
			$amazonRefundId = $array_notification_data['RefundNotification']['RefundDetails']['AmazonRefundId']['value'];
			$refundDetailsResp = $this->getApiAdapter()->_getRefundDetails($amazonRefundId);
			
			 $refundState = (string) $refundDetailsResp
			                                                    ->GetRefundDetailsResult
			                                                    ->RefundDetails
			                                                    ->RefundStatus
			                                                    ->State;
			
			        $refundAmount = (string) $refundDetailsResp
			                                                    ->GetRefundDetailsResult
			                                                    ->RefundDetails
			                                                    ->RefundAmount
			                                                    ->Amount;

            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_order_transaction');        
            $ordidquery = "SELECT order_id FROM $tableName WHERE ref_id = '".$amazonRefundId."'";
            $orderQuery = $read->fetchAll($ordidquery);
            $order_id = $orderQuery[0]['order_id'];
				
			$order = Mage::getModel('sales/order')->load($order_id);
                     
			$currency_code = Mage::app()->getStore()-> getCurrentCurrencyCode();
			$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
				
		$refundState = strtoupper($refundState);
	
	
	$refundAmount = $currency_symbol.''.$refundAmount;

	$message_ipn = '';
	$message_ipn .= 'Transaction REFUND. Status: '. $refundState.' ';
	$message_ipn .= '<br/> Amazon Refund Id:  '. $amazonRefundId.'  ';
	$message_ipn .= '<br/> Refund Amount:  '.$refundAmount.'  ';

	$order->addStatusToHistory($order->getStatus(),$message_ipn);
								
	$order->save();
		
		$this->getResponse()->setHttpResponseCode(200);
		
		
		 }


    }


 protected function _createInvoice($order_id) 
 {
		$order = Mage::getModel('sales/order')->load($order_id);
 
            try {
               if(!$order->canInvoice()) {
                  Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
                }
  
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
  
               if (!$invoice->getTotalQty()) {
                   Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
               }
  
               $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

               $invoice->register();
               $transactionSave = Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder());
  
               $transactionSave->save();
           }
          catch (Mage_Core_Exception $e) {
  
           }

	return $invoice->getId();
 }

protected function _processCapture($captureResponse,$order_id) 
  {
  	        $amazonCaptureId = (string) $captureResponse
  	                                    ->CaptureResult
  	                                    ->CaptureDetails
  	                                    ->AmazonCaptureId;

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
									  
			if ($captureState != '' ) {
			
			                    $currency_symbol = Mage::app()
			                                       ->getLocale()
			                                       ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
			                                       ->getSymbol();
				
                                $capAmount = $currency_symbol.''.$capturedAmount;
								
								$order = Mage::getModel('sales/order')->load($order_id);
				
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
							
								
				}
	
  }




 public function updateAmzTransaction($saveTransactionData) 
{
	if($saveTransactionData["notification_type"] == "PaymentAuthorize"){
		
		$authorizationCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('transaction_type_id',$saveTransactionData["authorization_id"]);
					
	   foreach($authorizationCollection as $authorizationRow){
            $row_id = $authorizationRow->getTransactionId();
	        $_stateData = array('state'=>$saveTransactionData["state"]);

		$orderIdsModel = Mage::getModel('ixcbadv/transaction')->load($row_id)->addData($_stateData);
		
		try {
               $orderIdsModel->setId($row_id)->save();
            } catch (Exception $e){
              //echo $e->getMessage();
            }
       }
	   
		
	}
	
	if($saveTransactionData["notification_type"] == "PaymentCapture"){
		
		$authorizationCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('transaction_type_id',$saveTransactionData["authorization_id"]);
					
	   foreach($authorizationCollection as $authorizationRow){
            $row_id = $authorizationRow->getTransactionId();
	        $_captureData = array('cm_id'=>$saveTransactionData["capture_id"]);

		$orderIdsModel = Mage::getModel('ixcbadv/transaction')->load($row_id)->addData($_captureData );
		
		try {
               $orderIdsModel->setId($row_id)->save();
            } catch (Exception $e){
              //echo $e->getMessage();
            }
       }
	   
		
	}
				  
 }


    
    public function getApiAdapter()
         {
          return Mage::getModel('ixcbadv/api_adapter');
         }
		 
		 
    
    function xml2array($contents, $get_attributes=1) {

		if(!$contents) return array();
		if(!function_exists('xml_parser_create')) {
			return array();
		}
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct( $parser, $contents, $xml_values );
		xml_parser_free( $parser );

		if(!$xml_values) return;
		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();
		$current = &$xml_array;

		foreach($xml_values as $data) {
			unset($attributes,$value);
			extract($data);
			$result = '';
			if($get_attributes) {
				$result = array();
				if(isset($value)) $result['value'] = $value;
				if(isset($attributes)) {
					foreach($attributes as $attr => $val) {
						if($get_attributes == 1) $result['attr'][$attr] = $val;
					}
				}
			} elseif(isset($value)) {
				$result = $value;
			}

			if($type == "open") {
				$parent[$level-1] = &$current;
				if(!is_array($current) or (!in_array($tag, array_keys($current)))) {
					$current[$tag] = $result;
					$current = &$current[$tag];
				} else {
					if(isset($current[$tag][0])) {
						array_push($current[$tag], $result);
					} else {
						$current[$tag] = array($current[$tag],$result);
					}
					$last = count($current[$tag]) - 1;
					$current = &$current[$tag][$last];
				}
			} elseif($type == "complete") {
				if(!isset($current[$tag])) { 
					$current[$tag] = $result;
				} else {
					if((is_array($current[$tag]) and $get_attributes == 0)
					or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
						array_push($current[$tag],$result);
					} else {
						$current[$tag] = array($current[$tag],$result);
					}
				}
			} elseif($type == 'close') {
				$current = &$parent[$level-1];
			}
		}
		return($xml_array);
	}
}