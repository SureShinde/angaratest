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

class Mageix_Ixcbadv_Adminhtml_IxcbadvController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('ixcbadv/items')->_addBreadcrumb(Mage :: helper('ixcbadv')->__('Search Processing Status'), Mage :: helper('ixcbadv')->__('Search Processing Status'));
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','ixcbadv', array('template' => 'ixcbadv/index.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}
	

		 
	

	public function getDetailsAction() {
		if ($this->getRequest()->isPost()) {
            $reference_id = $this->getRequest()->getPost('reference_id', '');
			echo $this->getRequestStatus($reference_id);
		}
	}

	public function getRequestStatus($referenceId)
	{
		$country = Mage::getStoreConfig('payment/ixcbadv/country');
		$application_name = 'Advanced Ixcbadv by Mageix';
		$application_version = '12.0.0';
		$serviceUrl = Mage::getStoreConfig('payment/ixcbadv/mws_service_url_'.$country);
		
		$merchant_id = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/ixcbadv/merchantid'));		
		$MwsAccessKey = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/ixcbadv/mwsaccesskeyid'));
		$MwsSecretKey = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/ixcbadv/mwssecretkeyid'));		
		$marketplace_id = Mage::getStoreConfig('payment/ixcbadv/marketplace_id_'.$country);
			
		$config = array (
		  'ServiceURL' => $serviceUrl,
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		);

		$service = new Mageix_Ixcbadv_MarketplaceWebService_Client(
			 $MwsAccessKey ,
			 $MwsSecretKey ,
			 $config ,
			 $application_name ,
			 $application_version
		);

		$feedHandle = @fopen('php://memory','rw+');

		$parameters = array (
		  'Marketplace' => $marketplace_id,
		  'Merchant' => $merchant_id,
		  'FeedSubmissionId' => $referenceId,
		  'FeedSubmissionResult' => $feedHandle
		);

		$request = new Mageix_Ixcbadv_MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);
		
		$processingStatus = false;
		try {
			$response = $service->getFeedSubmissionResult($request);
		} catch (Mageix_Ixcbadv_MarketplaceWebService_Exception $ex) {
			
			if($ex->getRequestId())		$msg .= '<strong>Request Id: </strong> ' .$ex->getRequestId();
			if($ex->getErrorType())		$msg .= '<span class="separator">|</span> <strong>Error: </strong> '.$ex->getErrorType();
			if($ex->getStatusCode())	$msg .= '<span class="separator">|</span> <strong>Status Code: </strong> '.$ex->getStatusCode();
			if($ex->getMessage())		$msg .= '<br />'.$ex->getMessage();
			//if($ex->getErrorCode())	$msg .= '<br/>'.$ex->getErrorCode();
			//if($ex->getXML()) $msg .= '<br/>'.htmlentities($ex->getXML());

			@fclose($feedHandle);
			return "<br/><fieldset><ul class='note-list'><li>".$msg."</li></ul></fieldset>"; //returns false in case of request still in pending status
		}
		
		$xml_feed = stream_get_contents($feedHandle);
		@fclose($feedHandle);
		//$status_api = "<pre>"; $status_api .= htmlentities($xml_feed); $status_api .= "</pre>"; return $status_api;
		$xml_array = $this->xml2array($xml_feed);
		
		$status_api = "<br/><fieldset><ul class='note-list'>"; 
		$counter = 0;

		if($xml_array['AmazonEnvelope']['Message']['ProcessingReport']) {
			foreach($xml_array['AmazonEnvelope']['Message']['ProcessingReport'] as $key => $value) {
				if($key == 'ProcessingSummary') {
					$status_api .= "<li>";
					foreach($xml_array['AmazonEnvelope']['Message']['ProcessingReport']['ProcessingSummary'] as $key_ps => $value_ps) {
						if(isset($value_ps['value']) && $value_ps['value'] != '' && $value_ps['value'] != '0') {
							if($counter == 0) {
								$status_api .= "<strong>$key_ps: </strong> " .$value_ps['value'];
							}else{
								$status_api .= "<span class='separator'>|</span> <strong>$key_ps: </strong> " .$value_ps['value'];
							}
							$counter++;
						}
					}
					$status_api .= "</li>";
				} elseif($key == 'Result') {
					$single_display = 'no';
					foreach($value as $key_result => $value_result) {
						if (is_int($key_result)) {
						}else{
							$single_display = 'yes';
						}
					}
					if($single_display == 'yes') {
						$status_api .= "<li>";
					}
					foreach($value as $key_result => $value_result) {
						if (is_int($key_result)) {
							$status_api .= "<li>";
							foreach($value_result as $key_all => $value_all) {
								if($key_all == 'ResultCode') {
									$status_api .= "<strong>Result: </strong> " .$value_all['value'];
								} elseif($key_all == 'ResultMessageCode') {
									$status_api .= "<span class='separator'>|</span> <strong>MessageCode: </strong> " .$value_all['value'];
								} elseif($key_all == 'ResultDescription') {
									$status_api .= "<br /> " .$value_all['value'];
								}
							}
							$status_api .= "</li>";
						}else{
							if($key_result == 'ResultCode') {
								$status_api .= "<strong>Result: </strong> " .$value_result['value'];
							} elseif($key_result == 'ResultMessageCode') {
								$status_api .= "<span class='separator'>|</span> <strong>MessageCode: </strong> " .$value_result['value'];
							} elseif($key_result == 'ResultDescription') {
								$status_api .= "<br /> " .$value_result['value'];
							}
						}
					}
					if($single_display == 'yes') {
						$status_api .= "</li>";
					}
				}
			}
		}

		$status_api .= "</ul></fieldset>";
		
		return $status_api;
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