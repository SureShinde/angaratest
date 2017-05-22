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

require_once(Mage::getBaseDir('lib').DS.'Mageix'.DS.'Ixcbadv'.DS.'library'.DS.'class.oap.php');

class Mageix_Ixcbadv_Model_Api_Adapter
{
    
 
     public function _getOrderReferenceDetails($amazonOrderReferenceId,$addressConsentToken = '') 
     {

		
		if ($amazonOrderReferenceId){
		
		        $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key;
	        $paramArray["AmazonOrderReferenceId"] = $amazonOrderReferenceId; 
	        $paramArray["SellerId"] = $seller_id;
			if ($addressConsentToken != ''){
			$paramArray["AddressConsentToken"] = $addressConsentToken;
		    }

			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$orderReferenceDetailsResp = @$oap->postAction($paramArray,$urlArray,"GetOrderReferenceDetails");
            
        
			if (isset($orderReferenceDetailsResp->Error)) {
			
	
			$api_name = "GetOrderReferenceDetails";
			$error_type = (string) $orderReferenceDetailsResp->Error->Type;
			$error_code = (string) $orderReferenceDetailsResp->Error->Code;
			$error_message = (string) $orderReferenceDetailsResp->Error->Message;
			$request_id = (string) $orderReferenceDetailsResp->RequestID;

			if ($request_id == ''){
			
			$request_id = (string) $orderReferenceDetailsResp->RequestId;
			
			}

			$this->update_ixcbadv_log('_getOrderReferenceDetails', $error_type, $error_code, $error_message, $request_id);
			}
			
		   
		    return $orderReferenceDetailsResp;
			
		}else{
			return "NO_ORDER_REFERENCE_ID";
		}
	}
	
	
	public function _authorize($amazonOrderReferenceId,$authorizeAmount,$captureNow,$timeout='') 
        {
        
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	        $seller_id = $amazonCredentialsArray["seller_id"];
	        $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	        $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		    $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];

			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);
			
			if(isset($timeout) && $timeout != ''){
			   $authorizeParams = $this->prepareAuthorizeParams($amazonOrderReferenceId,$authorizeAmount,$captureNow,$timeout);
			}else {
			   $authorizeParams = $this->prepareAuthorizeParams($amazonOrderReferenceId,$authorizeAmount,$captureNow);
		    }

			$authorizationResp = @$oap->postAction($authorizeParams,$urlArray,"Authorize");
			
                        if (isset($authorizationResp->Error)) {
	
			$api_name = "Authorize";
			$error_type = (string) $authorizationResp->Error->Type;
			$error_code = (string) $authorizationResp->Error->Code;
			$error_message = (string) $authorizationResp->Error->Message;
			$request_id = (string) $authorizationResp->RequestID;
			if ($request_id == ''){
			
			$request_id = (string) $authorizationResp->RequestId;
			
			}	

			$this->update_ixcbadv_log('_authorize', $error_type, $error_code, $error_message, $request_id);
                        }
                  
	   return $authorizationResp;

        }
        
        
        public function _capture($amazonOrderReferenceId,$amazonAuthorizationId,$captureAmount) 
        {
        
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	        $seller_id = $amazonCredentialsArray["seller_id"];
	        $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	        $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		    $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];


			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);
			
			$captureParams = $this->prepareCaptureParams($amazonOrderReferenceId,$amazonAuthorizationId,$captureAmount);

			$captureResp = @$oap->postAction($captureParams,$urlArray,"Capture");

                        if (isset($captureResp->Error)) {
			
	
			$api_name = "Capture";
			$error_type = (string) $captureResp->Error->Type;
			$error_code = (string) $captureResp->Error->Code;
			$error_message = (string) $captureResp->Error->Message;
			$request_id = (string) $captureResp->RequestID;
			if ($request_id == ''){
			
			$request_id = (string) $captureResp->RequestId;
			
			}	

			$this->update_ixcbadv_log('_capture', $error_type, $error_code, $error_message, $request_id);
                        }

			                     
	   return $captureResp;

        }
        
        
        
        
        private function prepareAuthorizeParams($amazonOrderReferenceId,$authorizeAmount,$captureNow,$timeout = '') 
        {
		
	$helper = Mage::helper("ixcbadv");	
	$authorizeParams = array ();	
	
	$authorizeParams["AWSAccessKeyId"] = Mage::helper('core')->decrypt($helper->getMwsAccessKeyId());
	$authorizeParams["AmazonOrderReferenceId"] = $amazonOrderReferenceId;
	$authorizeParams["SellerId"] = Mage::helper('core')->decrypt($helper->getMerchantId());

	$authorizeParams["AuthorizationAmount.CurrencyCode"] = $helper->getCurrencyCode();
	$authorizeParams["AuthorizationAmount.Amount"] = $authorizeAmount;
	

	if ($timeout != ''){
		
	 $authorizeParams["TransactionTimeout"] = $timeout;	
	}
	
	$lenLimit = 32;
	
	$strLen = strlen($amazonOrderReferenceId);
	$strLen = $strLen + 1;
	
	$leftChar = $lenLimit - $strLen;
	
	$randCode = substr(md5(uniqid()),0,$leftChar);
	$authorizationReferenceId = $amazonOrderReferenceId.'_'.$randCode;
	
	if ($helper->getSellerNote() != ''){
		$found = stripos($helper->getSellerNote(), 'SandboxSimulation');
		if($found !== false){
		  $authorizeParams["SellerAuthorizationNote"] = $helper->getSellerNote();	
	    }
	}
	
	$authorizeParams["AuthorizationReferenceId"] = $authorizationReferenceId;
	$authorizeParams["CaptureNow"] = $captureNow;
	
	if ($captureNow == "true" && $helper->getSoftDescriptor() != ''){
	$softDescriptor = str_replace(' ', '', $helper->getSoftDescriptor());
	$authorizeParams["SoftDescriptor"] = $softDescriptor;
	}
	
        return $authorizeParams;	
	
	
        }
        
        
        private function prepareCaptureParams($amazonOrderReferenceId,$amazonAuthorizationId,$captureAmount) 
        {
		
	$helper = Mage::helper("ixcbadv");	
	$captureParams = array ();	
	
	$captureParams["AWSAccessKeyId"] = Mage::helper('core')->decrypt($helper->getMwsAccessKeyId());
	$captureParams["SellerId"] = Mage::helper('core')->decrypt($helper->getMerchantId());
	$captureParams["CaptureAmount.CurrencyCode"] = $helper->getCurrencyCode();
	$captureParams["CaptureAmount.Amount"] = $captureAmount;
	$captureParams["AmazonAuthorizationId"] = $amazonAuthorizationId;
	
	$lenLimit = 32;
	
	$strLen = strlen($amazonOrderReferenceId);
	$strLen = $strLen + 1;
	
	$leftChar = $lenLimit - $strLen;
	
	$randCode = substr(md5(uniqid()),0,$leftChar);
	$captureReferenceId = $amazonOrderReferenceId.'_'.$randCode;
	
	$captureParams["CaptureReferenceId"] = $captureReferenceId;
	
	if ($helper->getSoftDescriptor() != ''){
	 $softDescriptor = str_replace(' ', '', $helper->getSoftDescriptor());
	 $captureParams["SoftDescriptor"] = $softDescriptor;
	}
	
        return $captureParams;	
	
	
        }
	
	
	
	
	public function _getAuthorizationDetails($amazonAuthorizationId) 
	{

			$amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key;
	                $paramArray["AmazonAuthorizationId"] = $amazonAuthorizationId; 
	                $paramArray["SellerId"] = $seller_id;

			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$authorizationDetailsResp = @$oap->postAction($paramArray,$urlArray,"GetAuthorizationDetails");
			
			if (isset($authorizationDetailsResp->Error)) {
			
	
			$api_name = "GetAuthorizationDetails";
			$error_type = (string) $authorizationDetailsResp->Error->Type;
			$error_code = (string) $authorizationDetailsResp->Error->Code;
			$error_message = (string) $authorizationDetailsResp->Error->Message;
			$request_id = (string) $authorizationDetailsResp->RequestID;

			if ($request_id == ''){
			
			$request_id = (string) $authorizationDetailsResp->RequestId;
			
			}

			$this->update_ixcbadv_log('_getAuthorizationDetails', $error_type, $error_code, $error_message, $request_id);
			}

			return $authorizationDetailsResp;
	
	  }

	  
	  
	  public function _closeAuthorization($amazonAuthorizationId) 
	{

			$amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key;
	                $paramArray["AmazonAuthorizationId"] = $amazonAuthorizationId; 
	                $paramArray["SellerId"] = $seller_id;

			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$closeAuthorizationResp = @$oap->postAction($paramArray,$urlArray,"CloseAuthorization");

			if (isset($closeAuthorizationResp->Error)) {
			
	
			$api_name = "CloseAuthorization";
			$error_type = (string) $closeAuthorizationResp->Error->Type;
			$error_code = (string) $closeAuthorizationResp->Error->Code;
			$error_message = (string) $closeAuthorizationResp->Error->Message;
			$request_id = (string) $closeAuthorizationResp->RequestID;

			if ($request_id == ''){
			
			$request_id = (string) $closeAuthorizationResp->RequestId;
			
			}

			$this->update_ixcbadv_log('_closeAuthorization', $error_type, $error_code, $error_message, $request_id);
			}

			return $closeAuthorizationResp;
	
	  }
	  
	  
	  
	  public function _getCaptureDetails($amazonCaptureId) 
	  {
	  		$amazonCredentialsArray = $this->_getAmazonCredentials();
	  	

                        $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key;
	                $paramArray["AmazonCaptureId"] = $amazonCaptureId; 
	                $paramArray["SellerId"] = $seller_id;

			$oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$captureDetailsResp = @$oap->postAction($paramArray,$urlArray,"GetCaptureDetails");

			if (isset($captureDetailsResp->Error)) {
			
	
			$api_name = "GetCaptureDetails";
			$error_type = (string) $captureDetailsResp->Error->Type;
			$error_code = (string) $captureDetailsResp->Error->Code;
			$error_message = (string) $captureDetailsResp->Error->Message;
			$request_id = (string) $captureDetailsResp->RequestID;

			if ($request_id == ''){
			
			$request_id = (string) $captureDetailsResp->RequestId;
			
			}

			$this->update_ixcbadv_log('_getCaptureDetails', $error_type, $error_code, $error_message, $request_id);
			}
			
			return $captureDetailsResp;
	
	  }
	  
	  
	  public function _refund($amazonCaptureId,$amazonOrderReferenceId,$refundAmount) 
	  {

			$amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$currency_code = Mage::app()->getStore()-> getCurrentCurrencyCode();
			$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
			
			$lenLimit = 32;
	
	                $strLen = strlen($amazonOrderReferenceId);
	                $strLen = $strLen + 1;
	
	                $leftChar = $lenLimit - $strLen;
	
	                $randCode = substr(md5(uniqid()),0,$leftChar);
	                $refundReferenceId = $amazonOrderReferenceId.'_'.$randCode;
	
	                $paramArray = array();
			        $paramArray["AWSAccessKeyId"] = $mws_access_key; 
	                $paramArray["SellerId"] = $seller_id;
	                $paramArray["AmazonCaptureId"] = $amazonCaptureId;
	                $paramArray["RefundAmount.Amount"] = $refundAmount;
	                $paramArray["RefundAmount.CurrencyCode"] = $currency_code;
	                $paramArray["RefundReferenceId"] = $refundReferenceId;

	                $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$refundResult = @$oap->postAction($paramArray,$urlArray,"Refund");

			if (isset($refundResult->Error)) {
			
			$api_name = "Refund";
			$error_type = (string) $refundResult->Error->Type;
			$error_code = (string) $refundResult->Error->Code;
			$error_message = (string) $refundResult->Error->Message;
			$request_id = (string) $refundResult->RequestId;
			
			if ($request_id == ''){
			
			$request_id = (string) $refundResult->RequestID;
			
			}	

			$this->update_ixcbadv_log('_refundAmount', $error_type, $error_code, $error_message, $request_id);
			}
			

			return $refundResult;
	
	   }
	  
	  
	   public function _getRefundDetails($amazonRefundId) 
	   {
	         $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	         $seller_id = $amazonCredentialsArray["seller_id"];
	         $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	         $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		     $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key; 
	        $paramArray["SellerId"] = $seller_id;
	        $paramArray["AmazonRefundId"] = $amazonRefundId;
			
	        $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$refundDetailsResult = @$oap->postAction($paramArray,$urlArray,"GetRefundDetails");

			if (isset($refundDetailsResult->Error)) {
			
	
			$api_name = "GetRefundDetails";
			$error_type = (string) $refundDetailsResult->Error->Type;
			$error_code = (string) $refundDetailsResult->Error->Code;
			$error_message = (string) $refundDetailsResult->Error->Message;
			$request_id = (string) $refundDetailsResult->RequestId;
			
			if ($request_id == ''){
			
			$request_id = (string) $refundDetailsResult->RequestID;
			
			}	

			$this->update_ixcbadv_log('_getRefundDetails', $error_type, $error_code, $error_message, $request_id);
			}

			return $refundDetailsResult;
	
	  
	   
	   }
	  
	  
	  
	  
	   public function _confirmOrderReference ($amazonOrderReferenceId) 
           {
 
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	        $seller_id = $amazonCredentialsArray["seller_id"];
	        $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	        $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		    $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			$path = $urlarray["path"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key; 
	        $paramArray["SellerId"] = $seller_id;
	        $paramArray["AmazonOrderReferenceId"] = $amazonOrderReferenceId;
	                
	        $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$confirmOrderReferenceResult = @$oap->postAction($paramArray,$urlArray,"ConfirmOrderReference");

			if (isset($confirmOrderReferenceResult->Error)) {
			
	
			$api_name = "ConfirmOrderReference";
			$error_type = (string) $confirmOrderReferenceResult->Error->Type;
			$error_code = (string) $confirmOrderReferenceResult->Error->Code;
			$error_message = (string) $confirmOrderReferenceResult->Error->Message;
			$request_id = (string) $confirmOrderReferenceResult->RequestID;
			
			if ($request_id == ''){
			
			$request_id = (string) $confirmOrderReferenceResult->RequestId;
			
			}	

			$this->update_ixcbadv_log('_confirmOrderReference', $error_type, $error_code, $error_message, $request_id);
			}
				
           
          }
          
          
          public function _setOrderReferenceDetails($amazonOrderReferenceId,$_quoteId) 
          {
            			
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	        $seller_id = $amazonCredentialsArray["seller_id"];
	        $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	        $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		    $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			$path = $urlarray["path"];
	                
	        $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

	        $orderParams = $this->_prepareOrderParams($_quoteId,$customInformation = '');

			$orderReferenceDetailsResult = @$oap->postAction($orderParams,$urlArray,"SetOrderReferenceDetails");
			



		            if (isset($orderReferenceDetailsResult->Error)) {
			
	
			       $api_name = "SetOrderReferenceDetails";
			       $error_type = (string) $orderReferenceDetailsResult->Error->Type;
			       $error_code = (string) $orderReferenceDetailsResult->Error->Code;
			       $error_message = (string) $orderReferenceDetailsResult->Error->Message;
			       $request_id = (string) $orderReferenceDetailsResult->RequestID;
			       
			         if ($request_id == ''){
			
			             $request_id = (string) $orderReferenceDetailsResult->RequestId;
			
			         }	

			      $this->update_ixcbadv_log('_setOrderReferenceDetails', $error_type, $error_code, $error_message, $request_id);
		            }
		
		
		              $orderReferenceStatus = (string) $orderReferenceDetailsResult
		                             ->SetOrderReferenceDetailsResult
		                             ->OrderReferenceDetails
		                             ->OrderReferenceStatus
		                             ->State;
		
		              if ($orderReferenceStatus == "Draft"){
	
		              $orderReferenceConstraints = (string) $orderReferenceDetailsResult
		                             ->SetOrderReferenceDetailsResult
		                             ->OrderReferenceDetails
		                             ->Constraints
		                             ->Constraint[0]
		                             ->ConstraintID;
				 
				 if (!isset($orderReferenceConstraints) || $orderReferenceConstraints == ''){
				 	
				   return (string) $orderReferenceDetailsResult->SetOrderReferenceDetailsResult->OrderReferenceDetails->AmazonOrderReferenceId;
					
				 }
			 }
			 	
          }
          
          
          public function _closeOrderReference($amazonOrderReferenceId) 
          {
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			$path = $urlarray["path"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key; 
	                $paramArray["SellerId"] = $sellerId;
	                $paramArray["AmazonOrderReferenceId"] = $amazonOrderReferenceId;
	                
	                $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$closeOrderReferenceResult = @$oap->postAction($paramArray,$urlArray,"CloseOrderReference");
			
			if (isset($closeOrderReferenceResult->Error)) {
			
			$api_name = "CloseOrderReference";
			$error_type = (string) $closeOrderReferenceResult->Error->Type;
			$error_code = (string) $closeOrderReferenceResult->Error->Code;
			$error_message = (string) $closeOrderReferenceResult->Error->Message;
			$request_id = (string) $closeOrderReferenceResult->RequestID;
			
			if ($request_id == ''){
			
			$request_id = (string) $closeOrderReferenceResult->RequestId;
			
			}	

			$this->update_ixcbadv_log('_closeOrderReference', $error_type, $error_code, $error_message, $request_id);
			}
          
          }
          
          public function _cancelOrderReference($amazonOrderReferenceId) 
          {
            $amazonCredentialsArray = $this->_getAmazonCredentials();
		       
	                $seller_id = $amazonCredentialsArray["seller_id"];
	                $mws_access_key = $amazonCredentialsArray["mws_access_key"]; 
	                $mws_secret_key = $amazonCredentialsArray["mws_secret_key"];
		        $aws_sandbox = $amazonCredentialsArray["sandbox_mode"];

			$urlArray = $this->getAmazonEnvironmentArray();
			$urlArray["access_key"] = $mws_access_key;
			$urlArray["secret_key"] = $mws_secret_key;
			
			$url = $urlArray["endpoint"];
			$urlhost = $urlArray["urlhost"];
			$path = $urlarray["path"];
			
			$paramArray = array();
			$paramArray["AWSAccessKeyId"] = $mws_access_key; 
	                $paramArray["SellerId"] = $seller_id;
	                $paramArray["AmazonOrderReferenceId"] = $amazonOrderReferenceId;
	                
	                $oap = new OAP($mws_access_key, $mws_secret_key, $url, $urlhost);

			$cancelOrderReferenceResult = @$oap->postAction($paramArray,$urlArray,"CancelOrderReference");

			if (isset($cancelOrderReferenceResult->Error)) {
			
	
			$api_name = "CancelOrderReference";
			$error_type = (string) $cancelOrderReferenceResult->Error->Type;
			$error_code = (string) $cancelOrderReferenceResult->Error->Code;
			$error_message = (string) $cancelOrderReferenceResult->Error->Message;
			$request_id = (string) $cancelOrderReferenceResult->RequestID;
			
			if ($request_id == ''){
			
			$request_id = (string) $cancelOrderReferenceResult->RequestId;
			
			}	

			$this->update_ixcbadv_log('_cancelOrderReference', $error_type, $error_code, $error_message, $request_id);
			}
			
			return $cancelOrderReferenceResult;
          
          }

          
          
          private function _prepareOrderParams($_quoteId,$customInformation = '') 
          {

		
	  $helper = Mage::helper("ixcbadv");	
	  $orderParams = array ();	
	
	  $orderParams["AmazonOrderReferenceId"] = Mage::getSingleton('core/session')->getOrderReferenceId();
	  $orderParams["SellerId"] = Mage::helper('core')->decrypt($helper->getMerchantId());
	  $orderParams["OrderReferenceAttributes.OrderTotal.CurrencyCode"] = $helper->getCurrencyCode();
	  $orderParams["OrderReferenceAttributes.OrderTotal.Amount"] = $helper->getGrandTotal();

    /*
	  $sellerNote = trim($helper->getSellerNote());
			$limit = 1024;
			if (strlen($sellerNote) > $limit) {
				$sellerNote = substr($sellerNote, 0, strrpos(substr($sellerNote, 0, $limit), ' '));
			}
	*/
	
	
	  if ($helper->getSellerNote() != ''){
	      $found = stripos($helper->getSellerNote(), 'SandboxSimulation');
		if($found === false){
	      $orderParams["OrderReferenceAttributes.SellerNote"] = $helper->getSellerNote();
		}
	  }
	
	  //$storeName = urlencode($helper->getStoreName());
	  $orderParams["OrderReferenceAttributes.SellerOrderAttributes.StoreName"] = $helper->getStoreName();

	
	  if (isset($customInformation) && $customInformation != ''){
	  $orderParams["OrderReferenceAttributes.SellerOrderAttributes.CustomInformation"] = $customInformation;
	  }
	
	
          return $orderParams;
		
	  }
	  
	
	  private function getAmazonEnvironmentArray() 
          {

		$aws_sandbox = Mage::helper("ixcbadv")->getSandboxMode();
				
	    if($aws_sandbox == 1) 
			{
					$url = 'https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/';
					$urlhost = 'mws.amazonservices.com';
					$path = '/OffAmazonPayments_Sandbox/2013-01-01/';
					$region = 'NA';
					$environment = 'SANDBOX';
				
			} else {
					$url = 'https://mws.amazonservices.com/OffAmazonPayments/2013-01-01/';
					$urlhost = 'mws.amazonservices.com';
					$path = '/OffAmazonPayments/2013-01-01/';
					$region = 'NA';
					$environment = 'LIVE';
				
			}
		
		
		$urlArray = array();
		$urlArray["endpoint"] = $url;
		$urlArray["urlhost"] = $urlhost;
		$urlArray["path"] = $path;
		
		return $urlArray;
          }
		  
          
          private function _getAmazonCredentials() 
          {
 	
		$helper = Mage::helper("ixcbadv");
		
		$amazonCredentialsArray = array();
		$amazonCredentialsArray["seller_id"] = Mage::helper('core')->decrypt($helper->getMerchantId());
	        $amazonCredentialsArray["mws_access_key"] = Mage::helper('core')->decrypt($helper->getMwsAccessKeyId()); 
	        $amazonCredentialsArray["mws_secret_key"] = Mage::helper('core')->decrypt($helper->getMwsSecretKeyId());
		$amazonCredentialsArray["sandbox_mode"] = $helper->getSandboxMode();
		
		return $amazonCredentialsArray;
          }
		  
		  private function _getHelper() 
          {
          	return Mage::helper("ixcbadv");
			
		  }
		  
		  
	
	public function update_ixcbadv_log($api_name, $error_type, $error_code, $error_message, $request_id) 
	{
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$now = Mage::getModel('core/date')->timestamp(time());
		$date_time = date('Y-m-d H:i:s', $now);
		try {
			$ixcbadv_error_log = Mage::getSingleton('core/resource')->getTableName('ixcbadv_error_log');
			$query = "INSERT INTO $ixcbadv_error_log SET api_name = '".$api_name."', description = '".$error_message."', error_type = '".$error_type."', error_code = '".$error_code."', request_id = '".$request_id."', created_date = '".$date_time."'";
			$InsertResult = $write->query($query);
		} catch (Exception $e) {
			$tbl_qry = "CREATE TABLE IF NOT EXISTS `ixcbadv_error_log` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `api_name` varchar(255) NOT NULL, `description` longtext NOT NULL, `error_type` varchar(255) NOT NULL, `error_code` varchar(255) NOT NULL, `request_id` varchar(255) NOT NULL, `created_date` datetime NOT NULL, PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$write->query($tbl_qry);
			$ixcbadv_error_log = Mage::getSingleton('core/resource')->getTableName('ixcbadv_error_log');
			$query = "INSERT INTO $ixcbadv_error_log SET api_name = '".$api_name."', description = '".$error_message."', error_type = '".$error_type."', error_code = '".$error_code."', request_id = '".$request_id."', created_date = '".$date_time."'";
			$InsertResult = $write->query($query);
		}
	
	}


}