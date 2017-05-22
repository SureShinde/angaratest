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

class Mageix_Ixcbadv_Adminhtml_ScratchpadController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('ixcbadv/items')
		                   ->_addBreadcrumb(Mage :: helper('ixcbadv')->__('Scratchpad'), Mage :: helper('ixcbadv')->__('Scratchpad'));
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','ixcbadv', array('template' => 'ixcbadv/scratchpad.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}
	
	
	public function getDetailsAction() {
		if ($this->getRequest()->isPost()) {
			$api_id = $this->getRequest()->getPost('api_id', '');

			if($api_id == "authorize"){
				$apiParams = array();
				$apiParams["authorization_amount"] = $this->getRequest()->getPost('authorization_amount', '');
				$apiParams["reference_id"] = $this->getRequest()->getPost('reference_id', '');
				
				echo $this->_callApi($apiParams,"Authorize");	
			}
			
			if($api_id == "getauthorizationdetails"){
				$apiParams = array();
				$apiParams["authorization_id"] = $this->getRequest()->getPost('authorization_id', '');
				
				echo $this->_callApi($apiParams,"GetAuthorizationDetails");
			}
			
			if($api_id == "closeauthorization"){
				
				$apiParams = array();
				$apiParams["authorization_id"] = $this->getRequest()->getPost('authorization_id', '');
				
				echo $this->_callApi($apiParams,"CloseAuthorization");	
			}
			
			if($api_id == "capture"){
				
				$apiParams = array();
				$apiParams["authorization_id"] = $this->getRequest()->getPost('authorization_id', '');
				$apiParams["capture_amount"] = $this->getRequest()->getPost('capture_amount', '');
				
				echo $this->_callApi($apiParams,"Capture");	
			}
			
			if($api_id == "getcapturedetails"){
				
				$apiParams = array();
				$apiParams["capture_id"] = $this->getRequest()->getPost('capture_id', '');
				
				echo $this->_callApi($apiParams,"GetCaptureDetails");
			}

            if($api_id == "refund"){
            		
            	$apiParams = array();
				$apiParams["capture_id"] = $this->getRequest()->getPost('capture_id', '');
				$apiParams["refund_amount"] = $this->getRequest()->getPost('refund_amount', '');
				
				echo $this->_callApi($apiParams,"Refund");
			}
			
			 if($api_id == "getrefunddetails"){

				$apiParams = array();
				$apiParams["refund_id"] = $this->getRequest()->getPost('refund_id', '');
				
				echo $this->_callApi($apiParams,"GetRefundDetails");
	
			}


		}
	}

	public function _callApi($apiParams,$api)
	{	
					
	    if($api == "Authorize"){
		  	
	      $authorizationAmount = $apiParams["authorization_amount"];
		  $amazonOrderReferenceId = $apiParams["reference_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_authorize($amazonOrderReferenceId,$authorizationAmount,"false");
		}
		
		if($api == "GetAuthorizationDetails"){
		  	
	      $amazonAuthorizationId = $apiParams["authorization_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_getAuthorizationDetails($amazonAuthorizationId);
		}
		
		if($api == "CloseAuthorization"){
		  	
	      $amazonAuthorizationId = $apiParams["authorization_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_closeAuthorization($amazonAuthorizationId);
		}
		
		if($api == "Capture"){
		  	
	      $amazonAuthorizationId = $apiParams["authorization_id"];
		  $captureAmount = $apiParams["capture_amount"];
		  $amazonOrderReferenceId  = $apiParams["reference_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_capture($amazonOrderReferenceId,$amazonAuthorizationId,$captureAmount);
				          
		  $this->_logPadCapture($apiResp,$amazonOrderReferenceId);

		}
		
		if($api == "GetCaptureDetails"){
		  	
	      $amazonCaptureId = $apiParams["capture_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_getCaptureDetails($amazonCaptureId);
		}
		
		if($api == "Refund"){
		  	
	      $amazonCaptureId = $apiParams["capture_id"];
		  $refundAmount = $apiParams["refund_amount"];
		  $amazonOrderReferenceId  = $apiParams["reference_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_refund($amazonCaptureId,$amazonOrderReferenceId,$refundAmount) ;
		}
		
		if($api == "GetRefundDetails"){
		  	
	      $amazonRefundId = $apiParams["refund_id"];
		  			
		  $apiResp = $this->_getApiAdapter()
				          ->_getRefundDetails($amazonRefundId);
		}

		

	$apiRespArray = $this->_xml2json($apiResp);
	
	unset($api);
	unset($apiParams);

	return $apiRespArray;
	}
	
	
	
 function _xml2json($xml)
{
    $arr = array();
    if (is_object($xml)) {
    foreach ($xml->children() as $r)
    {
        $t = array();
        if(count($r->children()) == 0)
        {
            $arr[$r->getName()] = strval($r);
        }
        else
        {
            $arr[$r->getName()][] = $this->_xml2json($r);
        }
    }
   }

    return json_encode($arr);
}


  private function _logPadCapture($captureResponse,$reference_id)
    {

       $helper = Mage::helper("ixcbadv");
  	        $ixcbadvMod = $helper->getIxModel();
  	        $amazonCaptureId = (string) $captureResponse
  	                                    ->CaptureResult
  	                                    ->CaptureDetails
  	                                    ->AmazonCaptureId;
  	        $sorp = $ixcbadvMod->sorp($amazonCaptureId);

             $captureReferenceId = (string) $captureResponse
                                           ->CaptureResult
                                           ->CaptureDetails
                                           ->CaptureReferenceId;
                                           
             $creationTimestamp =  (string) $captureResponse
                                           ->CaptureResult
                                           ->CaptureDetails
                                           ->CreationTimestamp;
										   
            $captureState = (string) $captureResponse
                                     ->CaptureResult
                                     ->CaptureDetails
                                     ->CaptureStatus
                                     ->State;
                                     
            $currencyCode = (string) $captureResponse
                                      ->CaptureResult
                                      ->CaptureDetails
                                      ->CaptureAmount
                                      ->CurrencyCode;
									 
            $capturedAmount = (string) $captureResponse
                                      ->CaptureResult
                                      ->CaptureDetails
                                      ->CaptureAmount
                                      ->Amount;
                                      
                                    if($sorp == PORS) 
			               {
			                 $ixcbadvMod->_logCapture($helper->getMerchantId(), $amazonCaptureId, $capturedAmount, $currencyCode, $reference_id, $creationTimestamp);
			               }
                                      
      }



	

    private function _getApiAdapter()
    {
        return Mage::getSingleton('ixcbadv/api_adapter');
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