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


class Mageix_Ixcbadv_Model_Observer
{
   

	public function cancel(Varien_Object $payment)
    {
    			
     $order_id = $payment->getEvent()->getPayment()->getOrder()->getId();
	 
	 $ordercancel = Mage::getModel('sales/order')->load($order_id);	
	 
	 if ($ordercancel->getPayment()->getMethod() != 'ixcbadv'  ||  $ordercancel->hasInvoices() ) {
             return;
     }	

		     $amazonrderId = $ordercancel->getExtOrderId();
		     
		     
		     $increId = $ordercancel->getIncrementId();

			  $cancelOrderReferenceResp = $this->_getApiAdapter()
		                                       ->_cancelOrderReference($amazonrderId);
        
        $requestId = (string) $cancelOrderReferenceResp->ResponseMetadata->RequestId;

	
	$message_ipn = '';
	$message_ipn .= 'Order Reference CANCELED.';
	$message_ipn .= '<br/> Request Id:  '. $requestId.'  ';
	

	$ordercancel->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED,$message_ipn);
								
	$ordercancel->save();

    	
}
	



  private function _getApiAdapter()
    {
        return Mage::getSingleton('ixcbadv/api_adapter');
    }
 		

	function positivePrice($price) {

		if(!isset($price) || $price < 0) {
			$price = 0;
		}
		return $price;
		
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

	public function formatAmount($amount)
    {
		 
        return Mage::helper('ixcbadv')->formatAmount($amount);
		
    }

	public function getCountry()
    {
		  
         return Mage::getStoreConfig('payment/ixcbadv/country'); 
		 
    }

	public function getCurrencyFormat()
    {
		   
		  $country = $this->getCountry();
		  
		  if($country == "US" )
			 return "USD";
		  else if($country == "UK" )
			 return "GBP";
		  else if($country == "DE" )
			 return "EUR" ;
		  else
			 Mage::log("Invalid country Configured , please correct the configuration"); 
		
   }
   
   public function saveXmlIxcbadv($xml, $amazon_order_id, $feed_id = '') {
   	    
 
			if(is_array($xml)) {
				$ixcbadv_xml_data = '';
				foreach($xml as $xml_key => $xml_val) {
					if($xml_key == '' && $xml_val == '') {
						$ixcbadv_xml_data .= "===== Promotional Cart Data Start ===== \n";	
					}else{
						$ixcbadv_xml_data .= "$xml_key => $xml_val \n";
					}
				}
			}else{
				$ixcbadv_xml_data = $xml;
			}
			
			$root_dir_path = Mage::getBaseDir();
			$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';

			$mageix_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'mageix';
			if (!is_dir($mageix_dir_path)) { mkdir($mageix_dir_path); }

			$ixcbadv_dir_path = $mageix_dir_path.DIRECTORY_SEPARATOR.'ixcbadv';
			if (!is_dir($ixcbadv_dir_path)) { mkdir($ixcbadv_dir_path); }

			$order_dir_path = $ixcbadv_dir_path.DIRECTORY_SEPARATOR.$amazon_order_id;
			if (!is_dir($order_dir_path)) { mkdir($order_dir_path); }
			
			if($feed_id == '') $feed_id = $amazon_order_id;
			$xmlfilepath = $order_dir_path.DIRECTORY_SEPARATOR.$feed_id.".xml";
			$fh = fopen($xmlfilepath, 'w');
			
			$ixcbadv_xml_data = Mage::getModel('ixcbadv/encryption')->encrypt($ixcbadv_xml_data);
			$ixcbadv_xml_data = utf8_encode(trim($ixcbadv_xml_data));

			fwrite($fh, $ixcbadv_xml_data);
			fclose($fh);
				

   }

   
   public function ixcbadvXmlExists() {
   	     
		$root_dir_path = Mage::getBaseDir();
		$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';
		
		$mageix_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'mageix';
		if (!is_dir($mageix_dir_path)) { return; }

		$ixcbadv_dir_path = $mageix_dir_path.DIRECTORY_SEPARATOR.'ixcbadv';
		if (!is_dir($ixcbadv_dir_path)) { return; }

		$results = scandir($ixcbadv_dir_path);

		foreach ($results as $result) {
			if ($result === '.' or $result === '..') continue;

			if (is_dir($ixcbadv_dir_path .DIRECTORY_SEPARATOR. $result)) {
				$delete_xmls = "yes";
				$order_obj = Mage::getModel('sales/order')->loadByAttribute('ext_order_id', $result);
				if ($order_obj->getId()) {
					$order_date = strtotime($order_obj->getCreatedAt());
					$i = Mage::getStoreConfig('payment/ixcbadv/delete_xml_period');
					$order_date = strtotime(date("Y-m-d", strtotime($order_obj->getCreatedAt())) . " +".$i."days");
					$today_date = time();
					if($order_date > $today_date) {
						$delete_xmls = "no";
					}
				}
				if($delete_xmls == "yes") {
					$this->rmdir_files($ixcbadv_dir_path .DIRECTORY_SEPARATOR. $result);
				}
			}
		}
		
		
	}

	public function rmdir_files($dir) {
 
		$dh = opendir($dir);
		if ($dh) {
			while($file = readdir($dh)) {
				if (!in_array($file, array('.', '..'))) {
					if (is_file($dir.DIRECTORY_SEPARATOR.$file)) {
						unlink($dir.DIRECTORY_SEPARATOR.$file);
					}
					else if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
						$this->rmdir_files($dir.DIRECTORY_SEPARATOR.$file);
					}
				}
			}
			rmdir($dir);
		}
		
		
	}
	
	
	 
	
    
    public function saveXmlIx($xml, $amazon_order_id, $feed_id = '') {


		if(is_array($xml)) {
			$ixcbadv_xml_data = '';
			foreach($xml as $xml_key => $xml_val) {
				if($xml_key == '' && $xml_val == '') {
					$ixcbadv_xml_data .= "===== Promotional Cart Data Start ===== \n";	
				}else{
					$ixcbadv_xml_data .= "$xml_key => $xml_val \n";
				}
			}
		}else{
			$ixcbadv_xml_data = $xml;
		}
		
		$root_dir_path = Mage::getBaseDir();
		$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';

		$mageix_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'mageix';
		if (!is_dir($mageix_dir_path)) { mkdir($mageix_dir_path); }

		$ixcbadv_dir_path = $mageix_dir_path.DIRECTORY_SEPARATOR.'ixcbadv';
		if (!is_dir($ixcbadv_dir_path)) { mkdir($ixcbadv_dir_path); }

		$order_dir_path = $ixcbadv_dir_path.DIRECTORY_SEPARATOR.$amazon_order_id;
		if (!is_dir($order_dir_path)) { mkdir($order_dir_path); }
		
		if($feed_id == '') $feed_id = $amazon_order_id;
		$xmlfilepath = $order_dir_path.DIRECTORY_SEPARATOR.$feed_id.".xml";
		$fh = fopen($xmlfilepath, 'w');
		
		$ixcbadv_xml_data = Mage::getModel('ixcbadv/encryption')->encrypt($ixcbadv_xml_data);

		$ixcbadv_xml_data = utf8_encode(trim($ixcbadv_xml_data));

		fwrite($fh, $ixcbadv_xml_data);
		fclose($fh);		

	}
    
    
    


	function generate_rand($l) { 
	  //this is string you can add more sysmbols
	  $c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"; 
	  srand((double)microtime()*1000000); 
	  $rand = '';
	  for($i=0; $i<$l; $i++) { 
		  $rand.= $c[rand()%strlen($c)]; 
	  } 
	  return $rand; 
	}

}

