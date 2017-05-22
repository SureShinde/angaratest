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

class Mageix_Ixcbadv_Adminhtml_IxcbadvxmlController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('ixcbadv/items')->_addBreadcrumb(Mage :: helper('ixcbadv')->__('Search Processing XML'), Mage :: helper('ixcbadv')->__('Search Processing XML'));
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','ixcbadv', array('template' => 'ixcbadv/xml.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}



protected function _isCurl() {
	
	if (function_exists('curl_version')){
	return "TRUE";
  }else {
  	return "FALSE";
	
  }
	
}



	public function getDetailsAction() {
		if ($this->getRequest()->isPost()) {

			$order_id = $this->getRequest()->getPost('order_id', '');
			$action_id = $this->getRequest()->getPost('action_id', '');
			$xml_to_search = "";
			$key_id = "";
			$show_tags = "yes";
			if($action_id == "order_xml") {
				$xml_to_search = $order_id.".xml";
				$show_tags = "no";
			} elseif($action_id == "authorize_xml") {
				$xml_to_search = $order_id."_authorize.xml";
				$key_id = "PaymentAuthorize";
			} elseif($action_id == "capture_xml") {
				$xml_to_search = $order_id."_capture.xml";
				$key_id = "PaymentCapture";
			} elseif($action_id == "refund_xml") {
				$xml_to_search = $order_id."_refund.xml";
				$key_id = "PaymentRefund";
			} elseif($action_id == "cancel_xml") {
				$xml_to_search = $order_id."_cancel.xml";
				$key_id = "PaymentCancel";
			} elseif($action_id == "mws_.xml") {
				$reference_id = $this->getRequest()->getPost('reference_id', '');
				$xml_to_search = $reference_id.".xml";
			} else {
				echo "Your Action not mapped. We cannot find your xml.";
				return;
			}
			$root_dir_path = Mage::getBaseDir();
			$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';
			$mageix_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'mageix';
			$ixcbadv_dir_path = $mageix_dir_path.DIRECTORY_SEPARATOR.'ixcbadv';
			$order_dir_path = $ixcbadv_dir_path.DIRECTORY_SEPARATOR.$order_id;
			if (!is_dir($order_dir_path)) { echo "Order details does not exists."; return; }

			$xmlfilepath = $order_dir_path.DIRECTORY_SEPARATOR.$xml_to_search;
			$collect_xml = "";
			if (file_exists($xmlfilepath)) {
				$fd = fopen ($xmlfilepath, "r");
				// initialize a loop to go through each line of the file
				while (!feof ($fd)) {
					
					$buffer = fgetcsv($fd, 4096); // declare an array to hold all of the contents of each
					if(count($buffer) > 0) {
						for ($i = 0; $i < count($buffer); ++$i) {
							$collect_xml .= Mage::getModel('ixcbadv/encryption')->decrypt($buffer[$i]);
						}
						if($action_id != "order_xml") {
							$collect_xml .= "\n";
						}
					}
				}
			}else{
				echo "Xml details of this Order does not exists."; return;
			}

			if($collect_xml != "") {
				//echo "<pre>"; echo htmlentities($collect_xml); echo "</pre>";
				if($show_tags == "yes") {
					if($action_id == "mws_xml") {
						
						// Formatting MWS Xml below.

						$array_mws = $this->xml2array($collect_xml);
						$message_type = "";
						
						if(isset($array_mws['AmazonEnvelope']['MessageType']) && $array_mws['AmazonEnvelope']['MessageType'] != '') {
							foreach($array_mws['AmazonEnvelope']['MessageType'] as $key_add => $val_add) {
								$message_type = $val_add;
							}
						}
						echo "<br/><b>Type : $message_type</b><br/>";
						
						if($message_type == 'OrderAcknowledgement') {
							if(isset($array_mws['AmazonEnvelope']['Message']['OrderAcknowledgement']) && $array_mws['AmazonEnvelope']['Message']['OrderAcknowledgement'] != '') {
								foreach($array_mws['AmazonEnvelope']['Message']['OrderAcknowledgement'] as $key_add => $val_add) {
									echo "<br/>".$key_add." => ".$val_add['value'];
								}
							}
						}elseif($message_type == 'OrderFulfillment') {
							if(isset($array_mws['AmazonEnvelope']['Message']['OrderFulfillment']) && $array_mws['AmazonEnvelope']['Message']['OrderFulfillment'] != '') {
								foreach($array_mws['AmazonEnvelope']['Message']['OrderFulfillment'] as $key_add => $val_add) {
									if($key_add == 'FulfillmentData') {
										echo "<br/><br/><b>$key_add</b><br/>";
										foreach($val_add as $val_add_key => $val_add_val) {
											echo "<br/>".$val_add_key." => ".$val_add_val['value'];
										}
									}elseif($key_add == 'Item') {
										echo "<br/><br/><b>$key_add</b><br/>";
										//echo "<pre>"; print_r($val_add); echo "</pre>";
										foreach($val_add as $val_add_key => $val_add_val) {
											if (is_int($val_add_key)) {
												foreach($val_add_val as $val_add_val_key => $val_add_val_val) {
													echo "<br/>".$val_add_val_key." => ".$val_add_val_val['value'];
												}
											}else{
												echo "<br/>".$val_add_key." => ".$val_add_val['value'];
											}
										}
									}else{
										echo "<br/>".$key_add." => ".$val_add['value'];
									}

								}
							}
						}elseif($message_type == 'OrderAdjustment') {
							//echo "<pre>"; print_r($array_mws); echo "</pre>";
							if(isset($array_mws['AmazonEnvelope']['Message']['OrderAdjustment']) && $array_mws['AmazonEnvelope']['Message']['OrderAdjustment'] != '') {
								foreach($array_mws['AmazonEnvelope']['Message']['OrderAdjustment'] as $key_add => $val_add) {
									if($key_add == 'AdjustedItem') {
										echo "<br/><br/><b>$key_add</b><br/>";
										foreach($val_add as $val_add_key => $val_add_val) {
											if($val_add_key == 'ItemPriceAdjustments') {
												echo "<br/><b>".$val_add_key."</b>";
												foreach($val_add_val['Component'] as $itempriceadjustments_val) {
													if($itempriceadjustments_val['Type']['value'] == 'ShippingTax') continue;
													echo "<br/>".$itempriceadjustments_val['Type']['value']." => ".$itempriceadjustments_val['Amount']['value'];
												}
											}elseif($val_add_key == 'PromotionAdjustments') {
												echo "<br/><b>".$val_add_key."</b>";
												$pr_key = '';
												$pr_val = '';
												foreach($val_add_val['Component'] as $promotionadjustments_val) {
													if($pr_key == '') {
														$pr_key = $promotionadjustments_val['value'];
													}else{
														$pr_val = $promotionadjustments_val['value'];
													}
												}
												echo "<br/>".$pr_key." => ".$pr_val;
											}else{
												echo "<br/>".$val_add_key." => ".$val_add_val['value'];
											}
										}
									}else{
										echo "<br/>".$key_add." => ".$val_add['value'];
									}
								}
							}else{
								$array_mws_multiple = $array_mws;
								foreach($array_mws_multiple['AmazonEnvelope']['Message'] as $array_mws) {
									foreach($array_mws['OrderAdjustment'] as $key_add => $val_add) {
										if($key_add == 'AdjustedItem') {
											echo "<br/><br/><b>$key_add</b><br/>";
											foreach($val_add as $val_add_key => $val_add_val) {
												if($val_add_key == 'ItemPriceAdjustments') {
													echo "<br/><b>".$val_add_key."</b>";
													foreach($val_add_val['Component'] as $itempriceadjustments_val) {
														if($itempriceadjustments_val['Type']['value'] == 'ShippingTax') continue;
														echo "<br/>".$itempriceadjustments_val['Type']['value']." => ".$itempriceadjustments_val['Amount']['value'];
													}
												}elseif($val_add_key == 'PromotionAdjustments') {
													echo "<br/><b>".$val_add_key."</b>";
													$pr_key = '';
													$pr_val = '';
													foreach($val_add_val['Component'] as $promotionadjustments_val) {
														if($pr_key == '') {
															$pr_key = $promotionadjustments_val['value'];
														}else{
															$pr_val = $promotionadjustments_val['value'];
														}
													}
													echo "<br/>".$pr_key." => ".$pr_val;
												}else{
													echo "<br/>".$val_add_key." => ".$val_add_val['value'];
												}
											}
										}else{
											echo "<br/>".$key_add." => ".$val_add['value'];
										}
									}
								}
							}
						}

						//echo "<pre>"; print_r($array_mws); echo "</pre>";
						//echo "<pre>"; echo htmlentities($collect_xml); echo "</pre>";

					} else {
						echo "<br/><b>Amazon Order ID:</b> ".$order_id."<br/>";
						
						// Formatting ShipNotification XML below.
						//echo htmlentities($collect_xml);

						$array_notification_data = $this->xml2array($collect_xml);

						if(isset($array_notification_data[$key_id]['NotificationReferenceId']) && $array_notification_data[$key_id]['NotificationReferenceId'] != '') {
							foreach($array_notification_data[$key_id]['NotificationReferenceId'] as $key_add => $val_add) {
								echo "<br/>NotificationReferenceId => ".$val_add;
							}
						}
						if(isset($array_notification_data[$key_id]['ProcessedOrder']['OrderDate']) && $array_notification_data[$key_id]['ProcessedOrder']['OrderDate'] != '') {
							foreach($array_notification_data[$key_id]['ProcessedOrder']['OrderDate'] as $key_add => $val_add) {
								echo "<br/>OrderDate => ".$val_add;
							}
						}
						$DisplayableShippingLabel = "";
						if(isset($array_notification_data[$key_id]['ProcessedOrder']['DisplayableShippingLabel']) && $array_notification_data[$key_id]['ProcessedOrder']['DisplayableShippingLabel'] != '') {
							foreach($array_notification_data[$key_id]['ProcessedOrder']['DisplayableShippingLabel'] as $key_add => $val_add) {
								$DisplayableShippingLabel = $val_add;
							}
						}
						
						$amt_amzn = 0; $order_total_items = 0;
						$prinicipal_amt = 0; $shipping_amt = 0; $tax_amt = 0; $shipping_tax_amt = 0; 
						$prinicipalpromo_amt = 0; $shippingpromo_amt = 0; $counter_loop = 1;
						foreach($array_notification_data[$key_id]['ProcessedOrder']['ProcessedOrderItems']['ProcessedOrderItem'] as $key_num => $val_num) {
							if (is_int($key_num)) {
								echo "<br/><br/><b>Order Item : $counter_loop</b><br/>";
								echo "<br/>AmazonOrderItemCode => ".$val_num['AmazonOrderItemCode']['value'];
								echo "<br/>SKU => ".$val_num['SKU']['value'];
								echo "<br/>Title => ".$val_num['Title']['value'];
								echo "<br/>Description => ".$val_num['Description']['value'];
								echo "<br/>Price => ".$val_num['Price']['Amount']['value'];
								echo "<br/>Quantity => ".$val_num['Quantity']['value'];
								echo "<br/>Condition => ".$val_num['Condition']['value'];

								foreach($val_num['ItemCharges']['Component'] as $com_key => $com_val) {
									switch($com_val['Type']['value']) {
										case 'Principal':
											echo "<br/>Principal => ".$com_val['Charge']['Amount']['value'];
										break;
										case 'Shipping':
											echo "<br/>Shipping => ".$com_val['Charge']['Amount']['value'];
										break;
										case 'Tax':
											echo "<br/>Tax => ".$com_val['Charge']['Amount']['value'];
										break;
										case 'PrincipalPromo':
											echo "<br/>PrincipalPromo => ".$com_val['Charge']['Amount']['value'];
										break;
										default:
									}
								}
								if($DisplayableShippingLabel) {
									echo "<br/>DisplayableShippingLabel => ".$DisplayableShippingLabel;
								}
								$order_total_items++;
								$positive_total = $prinicipal_amt + $shipping_amt + $tax_amt + $shipping_tax_amt;
								$negative_total = $prinicipalpromo_amt + $shippingpromo_amt;
								$iopn_data['OrderTotal'] += $positive_total - $negative_total;
								$counter_loop++;
							}else{
								if($key_num == 'AmazonOrderItemCode') {
									echo "<br/><br/><b>Order Item : $counter_loop</b><br/>";
									echo "<br/>AmazonOrderItemCode => ".$val_num['value'];
								}elseif($key_num == 'SKU') {
									echo "<br/>SKU => ".$val_num['value'];
								}elseif($key_num == 'Title') {
									echo "<br/>Title => ".$val_num['value'];
								}elseif($key_num == 'Description') {
									echo "<br/>Description => ".$val_num['value'];
								}elseif($key_num == 'Quantity') {
									echo "<br/>Quantity => ".$val_num['value'];
								}elseif($key_num == 'Price') {
									echo "<br/>Price => ".$val_num['Amount']['value'];
								}elseif($key_num == 'Condition') {
									echo "<br/>Condition => ".$val_num['value'];
								}elseif($key_num == 'ItemCharges') {
									foreach($val_num['Component'] as $com_key => $com_val) {
										switch($com_val['Type']['value']) {
											case 'Principal':
												echo "<br/>Principal => ".$com_val['Charge']['Amount']['value'];
												$amt_amzn++;
											break;
											case 'Shipping':
												echo "<br/>Shipping => ".$com_val['Charge']['Amount']['value'];
												$amt_amzn++;
											break;
											case 'Tax':
												echo "<br/>Tax => ".$com_val['Charge']['Amount']['value'];
												$amt_amzn++;
											break;
											case 'PrincipalPromo':
												echo "<br/>PrincipalPromo => ".$com_val['Charge']['Amount']['value'];
												$amt_amzn++;
											break;
											default:
										}
									}
								}
								if($amt_amzn == '6') {
									if($DisplayableShippingLabel) {
										echo "<br/>DisplayableShippingLabel => ".$DisplayableShippingLabel;
									}
									$positive_total = $prinicipal_amt + $shipping_amt + $tax_amt + $shipping_tax_amt;
									$negative_total = $prinicipalpromo_amt + $shippingpromo_amt;
									$iopn_data['OrderTotal'] += $positive_total - $negative_total;
									$amt_amzn = 0;
									$prinicipal_amt = 0; $shipping_amt = 0; $tax_amt = 0; $shipping_tax_amt = 0;
									$prinicipalpromo_amt = 0; $shippingpromo_amt = 0; 
									$order_total_items++;
								}
							}
							$iopn_data['OrderTotalItems'] = $order_total_items;
						}

						//echo "<pre>"; print_r($array_notification_data); echo "</pre>";
					}
				} else {
					echo "<br/><b>Amazon Order ID:</b> ".$order_id."<br/>";
					// Formatting Cart XML below.

					$xml_data_items = "";
					$xml_data_promotion = "";

					$xml_data = explode("===== Promotional Cart Data Start =====", $collect_xml);
					
					if(isset($xml_data[0]) && $xml_data[0] != '')
					$xml_data_items = $xml_data[0];

					if(isset($xml_data[1]) && $xml_data[1] != '')
					$xml_data_promotion = $xml_data[1];
					
					if($xml_data_items != '') {
						$xml_data_items_br = nl2br($xml_data_items);
						$xml_data_items_exp = explode("<br />", $xml_data_items_br);
						
						$keys_allowed = array('Title', 'SKU', 'Description', 'Amount', 'CurrencyCode', 'Quantity', 'URL', 'Condition', 'ServiceLevel', 'DisplayableShippingLabel', '');
						$purchaseitem_array = array();
						$additional_key = 'UnitPrice.';

						foreach($xml_data_items_exp as $xml_data_items_val) {
							if($xml_data_items_val != '') {
								$xml_data_items_val_exp = explode("=>", $xml_data_items_val);
								
								if(isset($xml_data_items_val_exp[0]) && trim($xml_data_items_val_exp[0]) != '') {
									$key = trim($xml_data_items_val_exp[0]);
									$val = trim($xml_data_items_val_exp[1]);
									$key_exp = explode(".", $key);
									
									// little logic changed for additional_key calculating on November 24 2012 start
									$additional_key = $key_exp[count($key_exp)-2].".";
									// little logic changed for additional_key calculating on November 24 2012 end

									if((isset($key_exp[2]) && trim($key_exp[2]) != '') && (isset($val) && trim($val) != '') && in_array(end($key_exp), $keys_allowed)) {
										if(in_array($key_exp[2], $purchaseitem_array)) {
											
											if(end($key_exp) == 'Amount' || end($key_exp) == 'CurrencyCode') {
												echo $additional_key.end($key_exp)." => ".$val."<br/>";
											}else{
												echo end($key_exp)." => ".$val."<br/>";
											}
											if(end($key_exp) == 'CurrencyCode' && $additional_key == 'UnitPrice.') {
												$additional_key = 'Tax.';
											}elseif(end($key_exp) == 'CurrencyCode' && $additional_key == 'Tax.') {
												$additional_key = 'Shipping.';
											}elseif(end($key_exp) == 'CurrencyCode' && $additional_key == 'Shipping.') {
												$additional_key = 'UnitPrice.';
											}
											
										}else{
											$purchaseitem_array[$key_exp[2]] = $key_exp[2];
											echo "<br/><b>PurchaseItem.".$key_exp[2]."</b><br/>";
											echo end($key_exp)." => ".$val."<br/>";
										}
									}
								}

							}
						}

					}

					if(trim($xml_data_promotion) != '') {
						
						$xml_data_promotion_br = nl2br($xml_data_promotion);
						$xml_data_promotion_exp = explode("<br />", $xml_data_promotion_br);
						
						foreach($xml_data_promotion_exp as $xml_data_promotion_val) {
							if($xml_data_promotion_val != '') {
								$xml_data_promotion_val_exp = explode("=>", $xml_data_promotion_val);
								
								if(isset($xml_data_promotion_val_exp[0]) && trim($xml_data_promotion_val_exp[0]) != '') {
									$key = trim($xml_data_promotion_val_exp[0]);
									$val = trim($xml_data_promotion_val_exp[1]);
									$key_exp = explode(".", $key);
									
									if((isset($key_exp[2]) && trim($key_exp[2]) != '') && (isset($val) && trim($val) != '') && in_array(end($key_exp), $keys_allowed)) {
										if(in_array($key_exp[2], $purchaseitem_array)) {
											echo end($key_exp)." => ".$val."<br/>";
										}else{
											$purchaseitem_array[$key_exp[2]] = $key_exp[2];
											echo "<br/><b>Promotion Data</b><br/>";
										}
									}
								}
							}
						}
						//echo "<pre>"; echo $xml_data_promotion; echo "</pre>";
					}
					//echo "<pre>"; print_r($xml_data); echo "</pre>";
				}
			}
			//echo $this->getRequestStatus($reference_id);
		}
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