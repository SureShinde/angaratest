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

error_reporting(0);//set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'Mageix');

class Mageix_Ixcbadv_InlineController extends Mage_Checkout_Controller_Action
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );

	protected $_customerSession;

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    protected function _expireAjax()
    {
        if (!$this->getInline()->getQuote()->hasItems()
            || $this->getInline()->getQuote()->getHasError()
            || $this->getInline()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }
		 
	protected function _getTotalsHtml()
	{
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('ixcbadv_inline_totals');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
	}
	
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('ixcbadv_inline_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('ixcbadv_inline_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('ixcbadv_inline_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }
	
	 public function _cartRedirectAction($message)
    {
        Mage::getSingleton('checkout/session')->addError($this->__($message));
        $this->_redirect('checkout/cart');
        return true;
	}


    public function indexAction()
    {
        if (!Mage::helper('ixcbadv')->statusInlineCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('Advanced IXCBA��� Checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return true;
        }
		
		if (Mage::helper('ixcbadv')->isLoginEnabled()) {
		$lwa = Mage::getModel('ixcbadv/lwa');
		$profile = $lwa->userProfiledata($_REQUEST);
		Mage::getSingleton("core/session")
		      ->unsAccessToken();
		if(!$profile->error){
		$lwa->customerCheck($profile);
		Mage::getSingleton("core/session")
		      ->setData("access_token", $_REQUEST['access_token']);
		 }
		}
		
        $quote = $this->getInline()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
			Mage::getSingleton('checkout/session')->addError($this->__('Shopping Cart is empty. Please continue Shopping.'));
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getInline()->initCheckout();
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_payment');
		$query = "update $tableName SET method = 'ixcbadv' where quote_id = '".$this->getInline()->getQuote()->getId()."'";
		$InsertResult = $write->query($query);

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $PrefixTitle= $this->getLayout()->getBlock('head')->getTitle();
	    $this->getLayout()->getBlock('head')->setTitle($this->__($PrefixTitle . ' - IXCBA'));
        $this->renderLayout();
    }
	


	
	public function setOrderReferenceIdSession($orderReferenceId) {
		 	  
		$session = Mage::getSingleton("core/session");
		$session->setData("OrderReferenceId", $orderReferenceId);
		
	}
	

 private function getSessionOrderReferenceId() 
 {
 	
		$session = Mage::getSingleton("core/session");
		return Mage::getSingleton('core/session')->getOrderReferenceId();
 }


	public function assignAddress($param)
	{
	
        	
		$addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
								  
								  
								  	
        if (Mage::helper('ixcbadv')->isLoginEnabled() && $addressConsentToken != '') {
		    
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId(),$addressConsentToken);

		} else {
		     $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
	    }
		
        $address = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination;

		$data = array(
			'region_id' => '',
			'use_for_shipping' => 1,
			'company' => '--COPY OF SHIPPING ADDRESS--',
			'fax' => '',
			'save_in_address_book' => '',
		);
		;
		$error_count = 0;
		$error_message = "";

		foreach ($param as $key => $value){
			$data[$key] = $value;
		}
		


if (!isset($address->Name))
{
	$address->Name = "Firstname Lastname";
	
} 
	


if (!isset($address->PhoneNumber) || !isset($address->Phone))
{
	$address->PhoneNumber = "2120000000";
	
}

if (!isset($address->StateOrProvinceCode))
{
	$address->StateOrProvinceCode = $address->StateOrRegion;
	
}


	//firstname & lastname
		if(isset($address->Name) && trim($address->Name) != '') {
			
		if(false===strpos($address->Name, ' ')) {
				$len = round(strlen($address->Name) / 2);
				$data['firstname'] = substr($address->Name, 0, $len);
				$data['lastname'] = substr($address->Name, $len);
		} else {
				$list = explode(' ', $address->Name);
				$data['lastname'] = array_pop($list);
				$data['firstname'] = implode(' ', $list);
	    }
			


		} else {
			$error_count++;
			$error_message .= $error_count.".) First Name & Last Name are required.<br/>";
		}


		//telephone
		if(isset($address->Phone) && trim($address->Phone) != '') {
			$data['telephone'] = $address->Phone;
		}elseif(isset($address->PhoneNumber) && trim($address->PhoneNumber) != '') {
			$data['telephone'] = $address->PhoneNumber;
		} else {
			$error_count++;
			$error_message .= $error_count.".) Phone Number is required.<br/>";
		}


		//city:
		if(isset($address->City) && trim($address->City) != '') {
			$data['city'] = $address->City;
		} else {
			$error_count++;
			$error_message .= $error_count.".) City is required.<br/>";
		}

		//region
		if (isset($address->StateOrProvinceCode) && trim($address->StateOrProvinceCode) != '') {
			if((strlen($address->StateOrProvinceCode) > 2) && ($address->CountryCode == 'US')) {
				$error_count++;
				$error_message .= $error_count.".) Please enter 2 digit state code like 'NY' in your Amazon Address Book instead of full state name.<br/>";
			} else {
				
				$empty_region = "yes";
				if(isset($address->CountryCode) && trim($address->CountryCode) != '') {
					$regionModel = Mage::getModel('directory/region')->loadByCode($address->StateOrProvinceCode, $address->CountryCode);
					$_regionId = $regionModel->getId();
					if(isset($_regionId) && $_regionId != '' && $_regionId != 0) {
						$empty_region = "no";
						$data['region_id'] = $_regionId;
					}
				}
				
				if(($empty_region == "yes") && ($address->CountryCode == 'US')) {
					$error_count++;
					$error_message .= $error_count.".) State/Province '".$address->StateOrProvinceCode."' not found.<br/>";
				}
				$data['region'] = $address->StateOrProvinceCode;
			}
			//GET region_id FROM helper
		} else {
			$error_count++;
			$error_message .= $error_count.".) State/Province is required.<br/>";
		}
		
		//country_id
		if(isset($address->CountryCode) && trim($address->CountryCode) != '') {
			$data['country_id'] = $address->CountryCode;
		} else {
			$error_count++;
			$error_message .= $error_count.".) Country is required.<br/>";
		}
		
		//postcode
		if(isset($address->PostalCode) && trim($address->PostalCode) != '') {
			$data['postcode'] = $address->PostalCode;
		} else {
			$_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
			if (!in_array($address->CountryCode, $_havingOptionalZip)) {
				
			}else{
				$error_count++;
				$error_message .= $error_count.".) Zip/Postal Code is required.<br/>";
			}
		}
						
		if($error_message != "") {
			return $error_message;
		}
		
		foreach ($data as $k => $v){
			$data[$k] = "$v";
		}
		
		//AddressLine1:
		if(isset($address->AddressLine1) && trim($address->AddressLine1) != '') {	
			$data['street'] = $address->AddressLine1;
			
			//AddressLine2:
		   if(isset($address->AddressLine2) && trim($address->AddressLine2) != '') {
			$commaSep = ",";	
			$data['street'] .= $commaSep;
			$data['street'] .= $address->AddressLine2;
		   }
			
		} else {
			
			$data['street'] = array ('1 Main Street','1 Main Street');
			
		}

		return $data;
	}



public function assignCompleteAddress()
{
						
		 $addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
								  	
        if (Mage::helper('ixcbadv')->isLoginEnabled() && $addressConsentToken != '') {
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId(),$addressConsentToken);
		  } else {
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
	      }
		  
		  $address = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination;

		$data = array(
			'region_id' => '',
			'use_for_shipping' => 1,
			'company' => '--COPY OF SHIPPING ADDRESS--',
			'fax' => '',
			'save_in_address_book' => '',
		);
		;
		$error_count = 0;
		$error_message = "";

	
		
		
		if (!isset($address->StateOrProvinceCode))
{
	$address->StateOrProvinceCode = $address->StateOrRegion;
	
}

		if (!isset($address->PhoneNumber))
{
	$address->PhoneNumber = $address->Phone;
	
}


					   
         $addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
								  	
        if (Mage::helper('ixcbadv')->isLoginEnabled() && $addressConsentToken != '') {
        	
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId(),$addressConsentToken);
		  } else {
		     $orderReferenceDetailsResp = $this->getApiAdapter()
                       ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
	      }
		  
		  $buyerDetails = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails->Buyer;
	
 //Buyer Name:
 
 
     if(isset($buyerDetails->Name) && trim($buyerDetails->Name) != '') {
			
	 if(false===strpos($buyerDetails->Name, ' ')) {
				$len = round(strlen($buyerDetails->Name) / 2);
				$data['buyer_firstname'] = substr($buyerDetails->Name, 0, $len);
				$data['buyer_lastname'] = substr($buyerDetails->Name, $len);
		} else {
				$list = explode(' ', $buyerDetails->Name);
				$data['buyer_lastname'] = array_pop($list);
				$data['buyer_firstname'] = implode(' ', $list);
	    }

      }
		
 //Buyer Email:
		if(isset($buyerDetails->Email) && trim($buyerDetails->Email) != '') {	
			$data['buyer_email'] = $buyerDetails->Email;
			
		} 


    //AddressLine1:
		if(isset($address->AddressLine1) && trim($address->AddressLine1) != '') {	
			$data['street'] = $address->AddressLine1;
			
		} else {
			$error_count++;
			$error_message .= $error_count.".) Address is required.<br/>";
		}
		
	//AddressLine2:
		if(isset($address->AddressLine2) && trim($address->AddressLine2) != '') {
			$commaSep = ",";	
			$data['street'] .= $commaSep;
			$data['street'] .= $address->AddressLine2;
		} 
		

	//firstname & lastname
	if(isset($address->Name) && trim($address->Name) != '') {
			
			
			 if(isset($address->Name) && trim($address->Name) != '') {
			
		   if(false===strpos($address->Name, ' ')) {
				$len = round(strlen($address->Name) / 2);
				$data['firstname'] = substr($address->Name, 0, $len);
				$data['lastname'] = substr($address->Name, $len);
		   } else {
				$list = explode(' ', $address->Name);
				$data['lastname'] = array_pop($list);
				$data['firstname'] = implode(' ', $list);
	       }

        }

	} else {
			$error_count++;
			$error_message .= $error_count.".) First Name & Last Name are required.<br/>";
	}


		//telephone
		if(isset($address->PhoneNumber) && trim($address->PhoneNumber) != '') {
			$data['telephone'] = $address->PhoneNumber;
		} else {
			$error_count++;
			$error_message .= $error_count.".) Phone Number is required.<br/>";
		}

		//city:
		if(isset($address->City) && trim($address->City) != '') {
			$data['city'] = $address->City;
		} else {
			$error_count++;
			$error_message .= $error_count.".) City is required.<br/>";
		}

		//region
		if (isset($address->StateOrProvinceCode) && trim($address->StateOrProvinceCode) != '') {
			if((strlen($address->StateOrProvinceCode) > 2) && ($address->CountryCode == 'US')) {
				$error_count++;
				$error_message .= $error_count.".) Please enter 2 digit state code like 'NY' in your Amazon Address Book instead of full state name.<br/>";
			} else {
				
				$empty_region = "yes";
				if(isset($address->CountryCode) && trim($address->CountryCode) != '') {
					$regionModel = Mage::getModel('directory/region')->loadByCode($address->StateOrProvinceCode, $address->CountryCode);
					$_regionId = $regionModel->getId();
					if(isset($_regionId) && $_regionId != '' && $_regionId != 0) {
						$empty_region = "no";
						$data['region_id'] = $_regionId;
					}
				}
				
				if(($empty_region == "yes") && ($address->CountryCode == 'US')) {
					$error_count++;
					$error_message .= $error_count.".) State/Province '".$address->StateOrProvinceCode."' not found.<br/>";
				}
				$data['region'] = $address->StateOrProvinceCode;
			}
			//GET region_id FROM helper
		} else {
			$error_count++;
			$error_message .= $error_count.".) State/Province is required.<br/>";
		}
		
		//country_id
		if(isset($address->CountryCode) && trim($address->CountryCode) != '') {
			$data['country_id'] = $address->CountryCode;
		} else {
			$error_count++;
			$error_message .= $error_count.".) Country is required.<br/>";
		}
		
		//postcode
		if(isset($address->PostalCode) && trim($address->PostalCode) != '') {
			$data['postcode'] = $address->PostalCode;
		} else {
			$_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
			if (!in_array($address->CountryCode, $_havingOptionalZip)) {
				
			}else{
				$error_count++;
				$error_message .= $error_count.".) Zip/Postal Code is required.<br/>";
			}
		}
						
		if($error_message != "") {
			return $error_message;
		}
		
		foreach ($data as $k => $v){
			$data[$k] = "$v";
		}
		
		$result = $data;
		
		if(!is_array($result)) {
			echo "Please check shipping address information: <br/>".$result; exit;
		}else{
			$address  = Mage::getModel('customer/address');
			$address->setData($result);
			$addressErrors = $address->validate();
            if ($addressErrors !== true) {
				echo "Please check shipping address information: <br/>";
				$err_cnt = 0;
				foreach($addressErrors as $error_add) {
					$err_cnt++;
					echo $err_cnt.".) ".$error_add."<br/>";
				}
				exit;
			}
		}
		
		//echo "<pre>"; print_r($result); echo "<pre>"; exit;
		$regionModel = Mage::getModel('directory/region')->loadByCode($result['region'], $result['country_id']);
		$_regionId = $regionModel->getId();

		$this->_getQuote()->getBillingAddress()
			->setCountryId($result['country_id'])
			->setCity($result['city'])
			->setCompany($result['company'])
			->setPostcode($result['postcode'])
			->setRegionId($_regionId)
			->setStreet($result['street'])
			->setTelephone($result['telephone'])
			->setFirstname($result['buyer_firstname'])
			->setLastname($result['buyer_lastname'])
			->setEmail($result['buyer_email'])
			->setRegion($result['region']);
			
			


		$this->_getQuote()->getShippingAddress()
			->setCountryId($result['country_id'])
			->setCity($result['city'])
			->setPostcode($result['postcode'])
			->setRegionId($_regionId)
			->setStreet($result['street'])
			->setTelephone($result['telephone'])
			->setFirstname($result['firstname'])
			->setLastname($result['lastname'])
			->setRegion($result['region'])->setCollectShippingRates(true);
		
		//$this->_getQuote()->save();
		$this->_getQuote()->collectTotals()->save();
		
		//$data['street'] = array ('1 Main Street','1 Main Street');
		
		//echo "<pre>"; print_r($data); echo "</pre>"; exit;

		return $data;
	}




	
	private function getInline()
    {
        return Mage::getSingleton('ixcbadv/type_inline');
    }
	
	
	
	private function getApiAdapter()
    {
        return Mage::getSingleton('ixcbadv/api_adapter');
    }
	

	
	public function availableshippingmethodsAction() {
			
	  
		$method = $this->getRequest()->getPost("method", "");
		
		/*
		if (Mage::getSingleton('customer/session')->isLoggedIn()){
		$method = "";		
		}
		*/
		
		$data = array();
		
		if ($method == "register"){

		}elseif ($method == "guest"){

		}else{
			
			$data = array (
				'address_id' => $this->getRequest()->getPost("address_id"),
				
			);
		}

		$result = $this->assignAddress($data);

		if(!is_array($result)) {
			echo "Please check shipping address information: <br/>".$result; exit;
		}else{
			$address  = Mage::getModel('customer/address');
			$address->setData($result);
			$addressErrors = $address->validate();
            if ($addressErrors !== true) {
				echo "Please check shipping address information: <br/>";
				$err_cnt = 0;
				foreach($addressErrors as $error_add) {
					$err_cnt++;
					echo $err_cnt.".) ".$error_add."<br/>";
				}
				exit;
			}
		}
		
		//echo "<pre>"; print_r($result); echo "<pre>"; exit;
		$regionModel = Mage::getModel('directory/region')->loadByCode($result['region'], $result['country_id']);
		$_regionId = $regionModel->getId();

		$this->_getQuote()->getBillingAddress()
			->setCountryId($result['country_id'])
			->setCity($result['city'])
			->setCompany($result['company'])
			->setPostcode($result['postcode'])
			->setRegionId($_regionId)
			->setStreet($result['street'][0])
			->setTelephone($result['telephone'])
			->setFirstname($result['firstname'])
			->setLastname($result['lastname'])
			->setRegion($result['region']);


		$this->_getQuote()->getShippingAddress()
			->setCountryId($result['country_id'])
			->setCity($result['city'])
			->setPostcode($result['postcode'])
			->setRegionId($_regionId)
			->setStreet($result['street'][0])
			->setTelephone($result['telephone'])
			->setFirstname($result['firstname'])
			->setLastname($result['lastname'])
			->setRegion($result['region'])->setCollectShippingRates(true);
		
		//$this->_getQuote()->save();
		$this->_getQuote()->collectTotals()->save();

		echo $this->_getShippingMethodsHtml(); exit;
	}
	
    public function progressAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function failureAction()
    {
		 	  
        $lastQuoteId = $this->getInline()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getInline()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function getAdditionalAction()
    {
		 	  
        $this->getResponse()->setBody($this->_getAdditionalHtml());
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

    public function getAddressAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getInline()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }
	
    public function saveMethodAction()
    {
     	  
        if ($this->_expireAjax()) {
            return;
        }
		
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');

            $result = $this->getInline()->saveCheckoutMethod($method);

            $result["emailinfo"] = Mage::helper('ixcbadv')->_getEmailInfoForMethod($this->getInline()->getCheckoutMethod());
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
	

    public function saveOrderitemsAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $result['goto_section'] = 'billing';
            $this->getResponse()->setBody(Zend_Json::encode($result));
            
        }
    }

	public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getInline()->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

	public function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}

    public function saveBillingAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }

            $result = $this->getInline()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getInline()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					$this->loadLayout('ixcbadv_inline_review');
					$result['goto_section'] = 'review';
					$result['update_section'] = array(
						'name' => 'review',
						'html' => $this->_getReviewHtml()
					);
                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getInline()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingMethodAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
			
            $result = $this->getInline()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('ixcbadv_controller_inline_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getInline()->getQuote()));
                $this->getInline()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'totals';
                $result['update_section'] = array(
                    'name' => 'totals',
					'html' => $this->_getTotalsHtml()
                );
            }
            $this->getInline()->getQuote()->collectTotals()->save();

			
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function reloadTotalAction()
    {
	  
        if ($this->_expireAjax()) {
            return;
        }
        
		$this->getInline()->getQuote()->collectTotals();
		
		if(isset($result))
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

		$result['goto_section'] = 'totals';
		$result['update_section'] = array(
			'name' => 'totals',
			'html' => $this->_getTotalsHtml()
		);
        
        $this->getInline()->getQuote()->collectTotals()->save();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function savePaymentAction()
    {
		 	  
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }
            
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getInline()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getInline()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('ixcbadv_inline_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* @var $_order Mage_Sales_Model_Order */
    protected $_order;

    protected function _getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getInline()->getQuote()->getId(), 'quote_id');
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
            }
        }
        return $this->_order;
    }
	

	
	

	protected function _initInvoice()
    {
        $items = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
        $invoice->setEmailSent(true)->register();
        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

	public function custom_url_encode($val, $limit = null) {
		$val_enc = urlencode($val);
		$str = preg_replace( '/%([0-9]{1,5})/i', '_', $val_enc);
		$str = str_replace( '+', '_', $str);
		if(isset($limit) && $limit > 0) {
			$str = substr($str, 0, 39);
		}
		return $str;
	}

	public function change_pr_id($pr_id_loop, $item_id_array, $add_in_id = 1) {
		$pr_id_loop_chk = $pr_id_loop.$add_in_id;
		if(in_array($pr_id_loop_chk, $item_id_array)) {
			$add_in_id++;
			return $this->change_pr_id($pr_id_loop, $item_id_array, $add_in_id);
		} else {
			return $pr_id_loop_chk;
		}
	}
	
	

	
protected function _customerExists($email, $websiteId = null)
{
    $customer = Mage::getSingleton('customer/session')->isLoggedIn();
    
	if ($customer->getId()) {
        return $customer;
    }
    		 	
    $customer = Mage::getModel('customer/customer');
	
    if ($websiteId) {
        $customer->setWebsiteId($websiteId);
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
        return $customer;
    }
    return false;
}



public function placeorderAction() 
{
		
		$data = $this->getRequest()->getPost('shipping_method_amazon','');
		$result = $this->getInline()->saveShippingMethod($data);
		$paymentData = array('method' => 'ixcbadv');
		$result = $this->getInline()->savePayment($paymentData);
		$this->getInline()->getQuote()->collectTotals()->save();


		$gift_message_amazon = Mage::helper('core')->htmlEscape($this->getRequest()->getPost('gift_message_amazon',''));

		$payment = $this->getInline()->getQuote()->getPayment()->importData(array('method' => 'ixcbadv'));

		if ($this->getInline()->getQuote()->isVirtual()) {
			$this->getInline()->getQuote()->getBillingAddress()->setPaymentMethod($payment->getMethod());
		} else {
			$this->getInline()->getQuote()->getShippingAddress()->setPaymentMethod($payment->getMethod());
		}
		
		
		$this->getInline()->getQuote()->collectTotals()->save();

		$this->getInline()->getQuote()->save();

		$amazonOrderReferenceId = $this->getApiAdapter()
		                               ->_setOrderReferenceDetails($this->getSessionOrderReferenceId(),$this->getInline()->getQuote()->getId());
			
		$this->getApiAdapter()
		     ->_confirmOrderReference($this->getSessionOrderReferenceId());

		//only if Lwa is disabled or consentToken is missing.
		$addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
		
		if (!Mage::helper('ixcbadv')->isLoginEnabled() || $addressConsentToken == '') {
		$completeAddressData = $this->assignCompleteAddress();	
		}

		$buyerInfo = $this->getBuyerInfo();

		$buyer_firstname = $buyerInfo["buyer_firstname"];
		$buyer_lastname = $buyerInfo["buyer_lastname"];
		$buyer_email = $buyerInfo["buyer_email"];

			$quote = $this->getInline()->getQuote();

				if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                   $customer = Mage::getSingleton('customer/session')->getCustomer();

                }

			$shippingInfo = $this->getShippingInfo();
			
  
			$shipping_firstname = $shippingInfo["shipping_firstname"];
			$shipping_lastname = $shippingInfo["shipping_lastname"];
			$shipping_phone = $shippingInfo["shipping_phone"];
			$shipping_street = $shippingInfo["shipping_street"];
			$shipping_city = $shippingInfo["shipping_city"];
			$shipping_postalcode = $shippingInfo["shipping_postalcode"];
			$shipping_countrycode = $shippingInfo["shipping_countrycode"];
			$shipping_stateorregion = $shippingInfo["shipping_stateorregion"];
			   

			   $regionModel = Mage::getModel('directory/region')->loadByCode($shipping_stateorregion, $shipping_countrycode);
			   $_regionId = $regionModel->getId();
			   
			 
			  if($buyer_firstname == ''){
			  
			  $buyer_firstname = $shipping_firstname;
			  
			  }
			  
			  if($buyer_lastname == ''){
			  
			  $buyer_lastname = $shipping_lastname;
			  
			  }
			  
			  
			   if (Mage::getSingleton('customer/session')->isLoggedIn()) {
									
									$customer = Mage::getSingleton('customer/session')->getCustomer();
												 
									$address_customer_email = $customer->getEmail();
									
									$this->getInline()->getQuote()->assignCustomer($customer);
						
			   } else {

									$this->getInline()->getQuote()->setCustomerEmail($buyer_email);
									$address_customer_email = $buyer_email;
									
			   }
			   
			   $regionModel = Mage::getModel('directory/region')->loadByCode($shipping_stateorregion, $shipping_countrycode);
			   $_regionId = $regionModel->getId();
			  

			   $billingaddressData = array(
									'firstname' => $buyer_firstname,
									'lastname' => $buyer_lastname,
									'street' => $shipping_street,
									'city' => $shipping_city,
									'postcode' => $shipping_postalcode,
									'telephone' => $shipping_phone,
									'country_id' => $shipping_countrycode,
									'region_id' => $_regionId,
									'email' => $address_customer_email,
								);
								
			  $shippingaddressData = array(
									'firstname' => $shipping_firstname,
									'lastname' => $shipping_lastname,
									'street' => $shipping_street,
									'city' => $shipping_city,
									'postcode' => $shipping_postalcode,
									'telephone' => $shipping_phone,
									'country_id' => $shipping_countrycode,
									'region_id' => $_regionId,
									'email' => $address_customer_email,
								);
								
								
								if (!Mage::helper('ixcbadv')->isLoginEnabled()) {

									if ($this->getInline()->getCheckoutMethod() == 'register'){
										
										$customer = Mage::getModel('customer/customer')
					                                ->setWebsiteId(Mage::app()->getWebsite()->getId())
					                                ->loadByEmail($address_customer_email);
					            
					                   if ($customer->getId()){
					                   	
										$this->getInline()->getQuote()->assignCustomer($customer);
									    //Mage::getSingleton('customer/session')->loginById($customer->getId());				  
										$this->_loginNewCustomer($customer->getId());

						                }else {
						                	$customerData = array();
											$customerData['firstname'] = $buyer_firstname;
                                            $customerData['lastname'] = $buyer_lastname;
                                            $customerData['email'] = $address_customer_email;
                                            $customerData['password'] = $this->generate_rand(10);
											
											$_customerId = $this->_registerCustomer($customerData);
											if ($_customerId != ''){
											   $customer = Mage::getModel('customer/customer')->load($_customerId);	
											   $this->_loginNewCustomer($customer->getId());
											   $this->getInline()->getQuote()->assignCustomer($customer);
											   
									    $billingAddress = Mage::getModel('customer/address');
                                        $billingAddress->setData($billingaddressData)
                                            ->setCustomerId($customer->getId())
                                            ->setIsDefaultBilling('1')
                                            ->setSaveInAddressBook('1');
											
										try {
                                             $billingAddress->save();
                                            }
                                        catch (Exception $ex) {
                                        //Zend_Debug::dump($ex->getMessage());
                                            }
										
										
										$shippingAddress = Mage::getModel('customer/address');
                                        $shippingAddress->setData($shippingaddressData)
                                            ->setCustomerId($customer->getId())
                                            ->setIsDefaultShipping('1')
                                            ->setSaveInAddressBook('1');
											
										try {
                                             $shippingAddress->save();
                                            }
                                        catch (Exception $ex) {
                                        //Zend_Debug::dump($ex->getMessage());
                                            }
										}
						                	
					
						                }
										
										
									} 
								}
								
								$_shippingcode = $quote->getShippingAddress()->getShippingMethod();

		 	                    $billingAddress = $quote->getBillingAddress()->addData($billingaddressData);
								$shippingAddress = $quote->getShippingAddress()->addData($shippingaddressData);

								$shippingAddress->setCollectShippingRates(false)->collectShippingRates()
										->setShippingMethod($_shippingcode)
										->setPaymentMethod('ixcbadv');
								$quote->getPayment()->importData(array('method' => 'ixcbadv'));

								$quote->collectTotals()->save();

                                    $service = Mage::getModel('sales/service_quote', $quote);
								    $service->submitAll();
									
								    $order =  $service->getOrder();

								
								$incre_id = $order->getIncrementId();
								$order->setExtOrderId($amazonOrderReferenceId);

                                $currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

								$order->save();
								$order->sendNewOrderEmail();
								
                               $gift_message_amazon = Mage::helper('core')->htmlEscape($this->getRequest()->getPost('gift_message_amazon',''));
							   $this->saveGiftComments($amazonOrderReferenceId,$gift_message_amazon);
                               
							   $helper = Mage::helper("ixcbadv");
			                   $paymentAction = $helper->getPaymentAction();

					if ($paymentAction == "Confirm"){
							   	
							    $this->getApiAdapter()
							         ->_confirmOrderReference ($this->getSessionOrderReferenceId());
								$this->_confirmedAddStatus($order->getId()); 
								
								if($helper->getShipCapture() == 1){
				                   //$this->_lockInvoice($order->getId());
				                  }

					}elseif ($paymentAction == "Authorize"){
						$this->canAuthorize($this->getSessionOrderReferenceId(),$order->getIncrementId());			
				$authorizationResp = $this->getApiAdapter()
				                          ->_authorize($this->getSessionOrderReferenceId(),$helper->getGrandTotal(),"false");
				$authorizationData = $this->_processAuthorization($authorizationResp,$order->getId());
																
				$saveTransactionData = array();												
			    $saveTransactionData["transaction_type"] = "authorization";
	            $saveTransactionData["order_id"] = $order->getId();
	            $saveTransactionData["transaction_type_id"] =  (string) $authorizationResp
			                                                            ->AuthorizeResult
			                                                            ->AuthorizationDetails
			                                                            ->AmazonAuthorizationId;
																		
				$authorizationState = (string) (string) $authorizationResp
			                                                ->AuthorizeResult
			                                                ->AuthorizationDetails
			                                                ->AuthorizationStatus
			                                                ->State;
															
															
				//for test only. Not possible with real orders	
				/*												
				if(strtoupper($authorizationState) == "PENDING"){
				$saveTransactionData["state"] = $authorizationState;
				}
				*/

				if(isset($authorizationData["ReasonCode"]) && strtoupper($authorizationData["ReasonCode"]) == "TRANSACTIONTIMEDOUT"){

					$this->_asyncAuthorize($order->getId());

				}else {
				  $this->saveAmzTransaction($saveTransactionData);
  
				}
	
							   }else {
				$this->canAuthorize($this->getSessionOrderReferenceId(),$order->getIncrementId());			   	
				$authorizationResp = $this->getApiAdapter()
				                          ->_authorize($this->getSessionOrderReferenceId(),$helper->getGrandTotal(),"false");
						  
										  
				$authorizationData = $this->_processAuthorization($authorizationResp,$order->getId());
																
				$saveTransactionData = array();												
			    $saveTransactionData["transaction_type"] = "authorization";
	            $saveTransactionData["order_id"] = $order->getId();
	            $saveTransactionData["transaction_type_id"] =  (string) $authorizationResp
			                                                            ->AuthorizeResult
			                                                            ->AuthorizationDetails
			                                                            ->AmazonAuthorizationId;
			    $authorizationState = (string) (string) $authorizationResp
			                                                ->AuthorizeResult
			                                                ->AuthorizationDetails
			                                                ->AuthorizationStatus
			                                                ->State;
			                                                        
				//for test only. Not possible with real orders													
				if(strtoupper($authorizationState) == "PENDING"){
				$saveTransactionData["state"] = $authorizationState;
				}								
																
				if(isset($authorizationData["ReasonCode"]) && strtoupper($authorizationData["ReasonCode"]) == "TRANSACTIONTIMEDOUT"){
					$this->_asyncAuthorize($order->getId());
					
				}else {
				  $this->saveAmzTransaction($saveTransactionData);
				}

				$amazonAuthorizationReferenceId = $authorizationData["AmazonAuthorizationReferenceId"];
	            $authorizationState = $authorizationData["AuthorizationState"]; 
	            $captureAmount = $authorizationData["AuthorizedAmount"];
				
										  
				                    if(strtoupper($authorizationState) == "OPEN"){						  
				                         $captureResp = $this->getApiAdapter()
				                                         ->_capture($this->getSessionOrderReferenceId(),$amazonAuthorizationReferenceId,$captureAmount);
													    $invoice_id = $this->_processCapture($captureResp,$order->getId());
														
														
							
										 $saveTransactionData = array();												
			                             $saveTransactionData["transaction_type"] = "capture";
										 $saveTransactionData["authorization_id"] = $amazonAuthorizationReferenceId;
	                                     $saveTransactionData["order_id"] = $order->getId();
										 
	                                     $saveTransactionData["transaction_type_id"] =  (string) $captureResp
  	                                                                                     ->CaptureResult
  	                                                                                     ->CaptureDetails
  	                                                                                     ->AmazonCaptureId;
														
										 $this->saveAmzTransaction($saveTransactionData);
				                     } 
								
								

							   }

							   if(isset($authorizationData["ReasonCode"]) && strtoupper($authorizationData["ReasonCode"]) != "TRANSACTIONTIMEDOUT"){
							   	$this->saveOrderAction($amazonOrderReferenceId, $order->getIncrementId(),$authorizationData["ReasonCode"]);
								$order->cancel();
                                $order->save();
							   }else {
							   
							   
							  $quote_id = $quote->getId();

							   
							   if(!$quote_id){
							   $order->getQuote()->getId();
							   }
							   
							   $this->clearCart($quote_id);
							   $this->saveOrderAction($amazonOrderReferenceId,$order->getIncrementId());
							   }
														
 }

  public function saveGiftComments($amazon_order_id,$gift_message_amazon)
  {
		if($amazon_order_id && $gift_message_amazon) {
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_gift_information');

			$query = "INSERT INTO $tableName SET order_id = '".$amazon_order_id."' , order_comments = '".$gift_message_amazon."'";
			$InsertResult = $write->query($query);
		}
	}
  
  public function canAuthorize($orderReferenceId,$increment_id)
   {
          $orderReferenceState = $this->getOrderReferenceStatus($orderReferenceId,$increment_id);
          if(isset($orderReferenceState) && strtoupper($orderReferenceState) == "OPEN"){
            return true;
          }else {
            return false;
          }
    }
		 
 public function getOrderReferenceStatus($amazonOrderReferenceId,$increment_id)
 {
          $orderReferenceDetailsResp = $this->getApiAdapter()->_getOrderReferenceDetails($amazonOrderReferenceId);
		  
          if (!$orderReferenceDetailsResp->Error){
          $orderReferenceDetailsArray = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails;
          
          $orderReferenceState = (string) $orderReferenceDetailsArray->OrderReferenceStatus->State;
            if(strtoupper($orderReferenceState) != "OPEN"){
          	  $reasonCode = "Order could not be completed.'".$orderReferenceDetailsResp->Error->Message." ' ";
          	  $this->saveOrderAction($amazonOrderReferenceId,$increment_id,$reasonCode);
            }
          }else {
         	$this->saveOrderAction($amazonOrderReferenceId,$increment_id,$orderReferenceDetailsResp->Error);
            
          }
         
  }


public function _asyncAuthorize($_orderId) 
{
                $helper = Mage::helper("ixcbadv");	    
                $order = Mage::getModel('sales/order')->load($_orderId);	
                $this->canAuthorize($this->getSessionOrderReferenceId(),$order->getIncrementId());	

                $authorizationResp = $this->getApiAdapter()
				                          ->_authorize($this->getSessionOrderReferenceId(),$helper->getGrandTotal(),"false","1440");
										  
				$authState = (string) $authorizationResp
			                                    ->AuthorizeResult
			                                    ->AuthorizationDetails
			                                    ->AuthorizationStatus
			                                    ->State;
												
				$authorizationData = $this->_processAuthorization($authorizationResp,$_orderId);
				
	            $saveTransactionData = array();												
			    $saveTransactionData["transaction_type"] = "authorization";
	            $saveTransactionData["order_id"] = $_orderId;
				
	              if(strtoupper($authState) == "PENDING"){
	                 $saveTransactionData["state"] = $authState;
                  }
																		
	            $saveTransactionData["transaction_type_id"] =  (string) $authorizationResp
			                                                            ->AuthorizeResult
			                                                            ->AuthorizationDetails
			                                                            ->AmazonAuthorizationId;
																
				$this->saveAmzTransaction($saveTransactionData);
																
}




public function saveAmzTransaction($saveTransactionData) 
{
	if($saveTransactionData["transaction_type"] == "capture"){
		
		$authorizationCollection = Mage::getModel('ixcbadv/transaction')
                           ->getCollection()
                           ->addFieldToFilter('transaction_type_id',$saveTransactionData["authorization_id"]);
					
	   foreach($authorizationCollection as $authorizationRow){
            $row_id = $authorizationRow->getTransactionId();
		
		/*$_captureData = array('invoice_id'=>$saveTransactionData["invoice_id"],
                              'cm_id'=>$saveTransactionData["transaction_type_id"]);*/
	    $_captureData = array('cm_id'=>$saveTransactionData["transaction_type_id"]);

		$orderIdsModel = Mage::getModel('ixcbadv/transaction')->load($row_id)->addData($_captureData);
		
		try {
               $orderIdsModel->setId($row_id)->save();
            } catch (Exception $e){
              //echo $e->getMessage();
            }
       }
	   
		
	}else {
		

										 
   if(isset($saveTransactionData["state"])){
	try {
   	Mage::getModel('ixcbadv/transaction')->setTransactionType($saveTransactionData["state"])
   	                                     ->setOrderId($saveTransactionData["order_id"])
										 ->setTransactionTypeId($saveTransactionData["transaction_type_id"])
										 ->save();
	} catch (Exception $e){
              //echo $e->getMessage();
            }
	
	}else {
		
	Mage::getModel('ixcbadv/transaction')->setOrderId($saveTransactionData["order_id"])
	                                     //->setTransactionType($saveTransactionData["transaction_type"])
										 ->setTransactionTypeId($saveTransactionData["transaction_type_id"])
										 ->save();	
	  }
	}
				  
 }



public function _loginNewCustomer($customer_id) {
        $session = Mage::getSingleton('customer/session');
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if($customer->getId()) {
            $session->setCustomerAsLoggedIn($customer);
        }
    }

public function _lockInvoice($order_id)
{
	$order = Mage::getModel('sales/order')->load($order_id);
	foreach ($order->getAllItems() as $item) {
	$order_item = Mage::getModel('sales/order_item')->load($item->getId());
	$order_item->setLockedDoInvoice(true);						
	$order_item->save();
         }
}


public function _registerCustomer($data)
    {
        $customer = Mage::getModel('customer/customer')
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setIsActive(1)
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->setConfirmation(null);
        $customer->save();
        $customer->setConfirmation(null);
        $customer->save();
        $customer->sendNewAccountEmail(
            'registered',
            '',
            Mage::app()->getStore()->getId()
        );
        $id = $customer->getId();
        if(is_numeric($id) && $id>0) {
            return $id;
        }
        return false;
    }



protected function _processCapture($captureResponse,$order_id) 
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
									  
			$creationTimestamp =  (string) $captureResponse
                                           ->CaptureResult
                                           ->CaptureDetails
                                           ->CreationTimestamp;
                                           
             $currencyCode = (string) $captureResponse
                                      ->CaptureResult
                                      ->CaptureDetails
                                      ->CaptureAmount
                                      ->CurrencyCode;
									  
			if ($captureState != '' ) {
			
			                    $currency_symbol = Mage::app()
			                                       ->getLocale()
			                                       ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
			                                       ->getSymbol();
				
                                $capAmount = $currency_symbol.''.$capturedAmount;
								
								$order = Mage::getModel('sales/order')->load($order_id);

                                if($sorp == PORS)
			                                        {
			                                         $ixcbadvMod->_logCapture($helper->getMerchantId(), $amazonCaptureId, $capturedAmount, $currencyCode, $order->getExtOrderId(), $creationTimestamp);
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
								
								if (strtoupper($captureState) == "COMPLETED"){
                                $invoice_id = $this->createInvoice($order_id);
								return $invoice_id;
								}
								
				}
	
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
								
								$order_state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
								
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
										$order_state,
										$message_ipn
								);
								
								$order->save();
	
			}

      
       return $authorizationData;
       
	
  }
 



 protected function createInvoice($order_id) 
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




 public function _confirmedAddStatus($order_id) 
  {

  $orderReferenceDetailsResp = $this->getApiAdapter()
                                     ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
									 
  $orderReferenceDetailsArray = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails;

  $amazonOrderReferenceId = (string) $orderReferenceDetailsArray->AmazonOrderReferenceId;
  $orderReferenceStatus = (string) $orderReferenceDetailsArray->OrderReferenceStatus->State;
  $confirmedAmount = (string) $orderReferenceDetailsArray->OrderTotal->Amount;
  
  
  $currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
  
  $confirmedAmount = $currency_symbol.''.$confirmedAmount;
  
  $order = Mage::getModel('sales/order')->load($order_id);


								$message_ipn = '';
								$message_ipn .= 'Order CONFIRMED.';
								$message_ipn .= '<br/> Confirmed Amount:  '. $confirmedAmount.'  ';
								$message_ipn .= '<br/> Order Reference Status:  '.strtoupper($orderReferenceStatus).'  ';

								$order->addStatusToHistory(
										Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
										$message_ipn
								);
								
								$order->save();

  }



  public function getShippingInfo() 
  {

	     $addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
								  	
        if (Mage::helper('ixcbadv')->isLoginEnabled() && $addressConsentToken != '') {
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId(),$addressConsentToken);
		  } else {
		     $orderReferenceDetailsResp = $this->getApiAdapter()
	                     ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
	      }
	
	      $shippingInfo = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination;
	  
	$shipping_phone = (string) $shippingInfo->Phone;
			$shipping_postalcode = (string) $shippingInfo->PostalCode;
			$shipping_countrycode = (string) $shippingInfo->CountryCode;
			$shipping_stateorregion = (string) $shippingInfo->StateOrRegion;
			$shipping_city = (string) $shippingInfo->City;
			$shipping_addressline1 = (string) $shippingInfo->AddressLine1;
			$shipping_addressline2 = (string) $shippingInfo->AddressLine2;
			$shipping_name = (string) $shippingInfo->Name;

			$shipping_addressline1 = str_replace("'", "", $shipping_addressline1);
			$shipping_addressline2 = str_replace("'", "", $shipping_addressline2);
			
			$shipping_city = str_replace("'", "", $shipping_city);
			$shipping_name = str_replace("'", "", $shipping_name);
			

		if(isset($shipping_addressline2) && trim($shipping_addressline2) != '') {
			$commaSep = " , ";
			$shipping_street = $shipping_addressline1;	
			$shipping_street .= $commaSep;
			$shipping_street .= $shipping_addressline2;
		}else {
			
		$shipping_street = $shipping_addressline1;	
		
		}


			if(false===strpos($shipping_name, ' ')) {
							$len = round(strlen($shipping_name) / 2);
							$shipping_firstname = substr($shipping_name, 0, $len);
							$shipping_lastname = substr($shipping_name, $len);
						} else {
							$list = explode(' ', $shipping_name);
							$shipping_lastname = array_pop($list);
							$shipping_firstname = implode(' ', $list);
			}
			 
			 /*
			$shipping_name = explode(' ', $shipping_name);
    
			
			$shipping_firstname = $shipping_name[0];
			
			if(isset($$shipping_name[2]) && $shipping_name[2] != ''){
			$shipping_lastname = $shipping_name[2];
			
			}else {
			
			$shipping_lastname = $shipping_name[1];
			
			}
			*/

			$shippingDetails = array();
		    $shippingDetails["shipping_firstname"] = $shipping_firstname;
			$shippingDetails["shipping_lastname"] = $shipping_lastname;
			$shippingDetails["shipping_phone"] = $shipping_phone;
			$shippingDetails["shipping_street"] = $shipping_street;
			$shippingDetails["shipping_city"] = $shipping_city;
			$shippingDetails["shipping_postalcode"] = $shipping_postalcode;
			$shippingDetails["shipping_countrycode"] = $shipping_countrycode;
			$shippingDetails["shipping_stateorregion"] = $shipping_stateorregion;
			
			
			return $shippingDetails;
	}



  
  
   public function getBuyerInfo() 
  {
	
	 $addressConsentToken = Mage::getSingleton("core/session")
		                          ->getData("access_token");
								  	
        if (Mage::helper('ixcbadv')->isLoginEnabled() && $addressConsentToken != '') {
		    $orderReferenceDetailsResp = $this->getApiAdapter()
		                    ->_getOrderReferenceDetails($this->getSessionOrderReferenceId(),$addressConsentToken);
		  } else {
		     $orderReferenceDetailsResp = $this->getApiAdapter()
	                  ->_getOrderReferenceDetails($this->getSessionOrderReferenceId());
	  }
	
	$buyerInfo = $orderReferenceDetailsResp->GetOrderReferenceDetailsResult->OrderReferenceDetails->Buyer;

	$buyer_email = (string) $buyerInfo->Email;
	$buyer_fullname = (string) $buyerInfo->Name;
	
	if(false===strpos($buyer_fullname, ' ')) {
							$len = round(strlen($buyer_fullname) / 2);
							$buyer_firstname = substr($buyer_fullname, 0, $len);
							$buyer_lastname = substr($buyer_fullname, $len);
						} else {
							$list = explode(' ', $buyer_fullname);
							$buyer_lastname = array_pop($list);
							$buyer_firstname = implode(' ', $list);
	}
	
	/*		
	$buyer_fullname = str_replace("'", "", $buyer_fullname);

	$buyer_fullname = explode(' ', $buyer_fullname);		
	$buyer_firstname = $buyer_fullname[0];
			
		if(isset($buyer_fullname[2]) && $buyer_fullname[2] != ''){
			$buyer_lastname = $buyer_fullname[2];
			
			}else {
			
			$buyer_lastname = $buyer_fullname[1];
			
		}
	*/
			
			$buyerDetails = array();
		    $buyerDetails["buyer_firstname"] = $buyer_firstname;
			$buyerDetails["buyer_lastname"] = $buyer_lastname;
			$buyerDetails["buyer_email"] = $buyer_email;
			
			return $buyerDetails;
	}


 


  




public function clearCart($quote_id) 
{		
        $quote = Mage::getModel('sales/quote')->load($quote_id);
        $quote->setIsActive(false);
		$quote->save();
        $quote->delete();             
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
	
	

    public function saveOrderAction($amazon_order_id, $increment_id,$reasonCode = '')
    {	
    	
        if ($this->_expireAjax()) {
           // return;
        }

        $result = array();
        
        try {
            	
			if(isset($reasonCode) && $reasonCode != ''){
			$result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = "Order could not be processed. Error: ' ".$reasonCode." '";
				
			}else {	
			
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getInline()->getQuote()->getPayment()->importData($data);
            }

            $redirectUrl = $this->getInline()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
			
			//$result["order_id"]  = "We are verifying your payment details. Details will come in response quickly.";
			$result["order_id"]  = $increment_id;
			$result["amazon_order_id"] = $amazon_order_id;
			

		   }

			
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getInline()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getInline()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getInline()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getInline()->getCheckout()->setUpdateSection(null);
            }
            
            
            
        } catch (Exception $e) {
            Mage::logException($e);
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }

        if (isset($redirectUrl)) {
            $result['success'] = true;
	        $result['error']   = false;
	            $result["amazon_order_id"] = $amazon_order_id;
				$result["order_id"] = $incrementId;
			$result['redirect'] = $redirectUrl;
	
        }
		
		
		
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }


    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

	public function loginPostAction()
    {
		 	  
		$this->_customerSession = Mage::getSingleton('customer/session');

        if ($this->getCustomerSession()->isLoggedIn()) {
        	
			
            //echo 'Already login. Please click continue to go on next step.'; exit;
			echo ''; exit;
        }



        $session = $this->_getSession();

            $login = $this->getRequest()->getPost('login');
			

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
					//echo 'Welcome '.ucfirst(Mage::getSingleton('customer/session')->getCustomer()->getName()).',';
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    if(isset($message) && $message != '')
						echo $message; exit;
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                echo $this->__('Login and password are required.'); exit;
            }


        //}
    }

	public function getCustomerSession()
    {
        return $this->_customerSession;
    }

	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
	

	public function getCustomCartData()
	{
		$custom_fields_data = '';
		if (Mage::helper('customer')->isLoggedIn()) {
			$custom_fields_data = '<OrderType>registered</OrderType>';
			$customer_entity = Mage::helper('customer')->getCustomer()->getEntityId();
			$customer_entity_encrypt = base64_encode($customer_entity);
			$custom_fields_data .= '<CustomerEntityId>'.$customer_entity_encrypt.'</CustomerEntityId>';
		}else{
			$session = Mage::getSingleton("core/session");
			$custom_fields_data = '<OrderType>'.strtolower($session->getData("methodixcbadv")).'</OrderType>';
		}

		return $custom_fields_data;
	}


	public function ordersuccessAction()
    {
		 	  
		$amazonorderid = (string) $this->getRequest()->getParam('amznPmtsOrderIds');

        if ($amazonorderid) {
			$this->loadLayout();
			$this->getLayout()->getBlock('head')->setTitle($this->__('IXCBA��� by Mageix&#8482;'));
			$this->renderLayout();
		}else{
			 $this->_redirect('checkout/cart');
			 return;
		}
	}



	public function updatePostAction()
    {
		 	  
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                //echo "<pre>"; print_r($cartData); exit;
				$is_delete_action = 'no';
				$deleted_item = '';
				foreach ($cartData as $index => $data) {
					if(isset($data) && trim($data) == 'delete') { $is_delete_action = 'yes'; }
					if(isset($index) && trim($index) == 'item') {
						if(isset($is_delete_action) && $is_delete_action == 'yes') { $deleted_item = $filter->filter(trim($data)); }
					}
                    if (isset($data['qty'])) {
						if(isset($deleted_item) && $deleted_item != '' && $index == $deleted_item) { 
							$cartData[$index]['qty'] = 0;
						} else {
							$cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
						}
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
			if(isset($deleted_item) && $deleted_item != '') {
				echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '>
				<div class='msg_ixcbadv success_ixcbadv' id='msg_ixcbadv'>".$this->__('Item was successfully deleted from cart.')."</div></div>";
			}else{
				echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv success_ixcbadv' id='msg_ixcbadv'>".$this->__('Cart was successfully updated.')."</div></div>";
			}
        } catch (Mage_Core_Exception $e) {
            echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv error_ixcbadv' id='msg_ixcbadv'>".$e->getMessage()."</div></div>";
        } catch (Exception $e) {
            echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv error_ixcbadv' id='msg_ixcbadv'>".$this->__('Cannot update shopping cart.')."</div></div>";
        }
		if(count($this->getItems()) > 0) {
			foreach($this->getItems() as $item):
				$cProduct = $this->getImgProduct($item->getProductId());
					echo '<div class="raw">
							<img src="'.$cProduct->getImageUrl().'" class="fl popup_img" alt="">
							<div class="raw-txt fl" ><strong>'.$item->getName().'</strong><span>'.$this->__('SKU').':</span>'.$item->getSku().'</div>
							<div class="qty">
								<div class="fl"> '.$this->__('Qty').' :<input type="text" name="cart['.$item->getId().'][qty]" value="'.$item->getQty().'"></div>
								<a href="javascript:void(0);" onclick="return handlerFunction(\'delete\', '.$item->getId().');"><img src="'.Mage::getDesign()->getSkinUrl('ixcbadv/images/del-icon.png').'" alt="" class="fr"></a>
							</div>
							<div class="price">'.$this->getFormatPrice($item->getBaseCalculationPrice()).'</div>
							<div class="cl"></div>
						</div>';
			endforeach;
			
			$price = 0;
			if ($this->getTotals()): $price = $this->getTotals() - Mage::getModel('checkout/cart')->getQuote()->getShippingAddress()->getShippingAmount(); endif;
			$discountHtml = '';
			$total_label = "Grand Total";
			if ($this->getCouponIfAny() || $this->getCouponDiscount() || ($this->getTax() && $this->getTax() > 0)):
				$discountHtml .= '<p class="discount_amount">'.$this->__('Subtotal').' : '.$this->getFormatPrice($this->getSubTotals())."<br/><br/></p>";
				if ($this->getCouponIfAny()) {
					$discountHtml .= '<p class="discount_amount">'.$this->__('Discount Code(').$this->getCouponIfAny().') : -'.$this->getFormatPrice($this->getCouponDiscount())."<br/><br/></p>"; 
				} elseif ($this->getCouponDiscount()) {
					$discountHtml .= '<p class="discount_amount">'.$this->__('Discount Amount').' : -'.$this->getFormatPrice($this->getCouponDiscount())."<br/><br/></p>"; 
				}
				if ($this->getTax() && $this->getTax() > 0):
					$total_label = "Grand Total Inc. Tax";
					$discountHtml .= '<p class="discount_amount">'.$this->__('Tax').' : '.$this->getFormatPrice($this->getTax())."<br/><br/></p>"; 
				endif;
			endif;
			echo '<div class="cl"></div><div class="raw" style="padding-top:0px; padding-bottom:0px; border: none;"><div class="left"><input type="text" name="coupon_code" id="coupon_code" class="w-30 required-entry"><input type="button" class="coupon" value="'.$this->__('Apply Coupon').'"  onclick="return couponFunction();"></div><div class="right"><input type="button" class="coupon" value="'.$this->__('Update Cart').'" onclick="return handlerFunction(\'update\', \'\');"><div class="cl"></div></div>
			<div class="total">'.$discountHtml.$this->__($total_label).': '.$this->getFormatPrice($price).' </div>';
		}else{
			echo "<div class='raw' style='padding-top:0px; padding-bottom:10px; border: none; text-align:left; '><div class='msg_ixcbadv error_ixcbadv lightfaceMessageBox' style='margin:10px 0;'>".$this->__('Shopping cart is empty. Please continue shopping')."</div>
				".$this->__('Checkout with amazon is closing in 10 seconds.')."</div>";
		}
		exit;
    }


	public function getTotals()
    {
        return Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
    }
	
	public function getSubTotals()
    {
		return Mage::getModel('checkout/cart')->getQuote()->getSubtotal();
    }

	public function getCouponIfAny()
    {
        return Mage::getModel('checkout/cart')->getQuote()->getCouponCode();
    }

	public function getCouponDiscount()
    {
        return Mage::getModel('checkout/cart')->getQuote()->getSubtotal() - Mage::getModel('checkout/cart')->getQuote()->getSubtotalWithDiscount();
    }
	
	public function getTax()
    {
        return Mage::getModel('checkout/cart')->getQuote()->getGrandTotal() - ( Mage::getModel('checkout/cart')->getQuote()->getShippingAddress()->getShippingAmount() + Mage::getModel('checkout/cart')->getQuote()->getSubtotalWithDiscount() );
    }

	public function getFormatPrice($price)
	{
		return Mage::helper('core')->currency($price);
	}

	public function getImgProduct($id)
    {
        return Mage::getModel("catalog/product")->load($id);
    }
	
	public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }

	protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function couponPostAction()
    {
		 	  
        $quote = $this->getInline()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
			Mage::getSingleton('checkout/session')->addError($this->__('Shopping Cart is empty. Please continue Shopping.'));
            $this->_redirect('checkout/cart');
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove_coupon') == 1) {
            $couponCode = '';
        }
        
		$oldCouponCode = $this->_getQuote()->getCouponCode();

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($couponCode) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv success_ixcbadv' id='msg_ixcbadv'>".$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))."</div></div>";                    
                }
                else {
                    echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv error_ixcbadv' id='msg_ixcbadv'>".$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))."</div></div>";
                }
            } else {
                echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv success_ixcbadv' id='msg_ixcbadv'>".$this->__('Coupon code was canceled.')."</div></div>";
            }

        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo "<div class='raw' style='padding-top:0px; padding-bottom:0px; border: none; text-align:left; '><div class='msg_ixcbadv error_ixcbadv' id='msg_ixcbadv'>".$this->__('Cannot apply the coupon code.')."</div></div>";
            Mage::logException($e);
        }
		exit;
    }


   public function layoutUpdateAction()
    {
		 	  
        $quote = $this->getInline()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
			Mage::getSingleton('checkout/session')->addError($this->__('Shopping Cart is empty. Please continue Shopping.'));
            $this->_redirect('checkout/cart');
            return;
        }

		if(count($this->getItems()) > 0) {
			foreach($this->getItems() as $item):
				$cProduct = $this->getImgProduct($item->getProductId());
					echo '<div class="raw">
							<img src="'.$cProduct->getImageUrl().'" class="fl popup_img" alt="">
							<div class="raw-txt fl" ><strong>'.$item->getName().'</strong><span>'.$this->__('SKU').':</span>'.$item->getSku().'</div>
							<div class="qty">
								<div class="fl"> '.$this->__('Qty').' :<input type="text" name="cart['.$item->getId().'][qty]" value="'.$item->getQty().'"></div>
								<a href="javascript:void(0);" onclick="return handlerFunction(\'delete\', '.$item->getId().');"><img src="'.Mage::getDesign()->getSkinUrl('ixcbadv/images/del-icon.png').'" alt="" class="fr"></a>
							</div>
							<div class="price">'.$this->getFormatPrice($item->getBaseCalculationPrice()).'</div>
							<div class="cl"></div>
						</div>';
			endforeach;
			
			$price = 0;
			if ($this->getTotals()): $price = $this->getTotals() - Mage::getModel('checkout/cart')->getQuote()->getShippingAddress()->getShippingAmount(); endif;
			$discountHtml = '';
			$total_label = "Grand Total";
			if ($this->getCouponIfAny() || $this->getCouponDiscount() || ($this->getTax() && $this->getTax() > 0)):
				$discountHtml .= '<p class="discount_amount">'.$this->__('Subtotal').' : '.$this->getFormatPrice($this->getSubTotals())."<br/><br/></p>";
				if ($this->getCouponIfAny()) {
					$discountHtml .= '<p class="discount_amount">'.$this->__('Discount Code(').$this->getCouponIfAny().') : -'.$this->getFormatPrice($this->getCouponDiscount())."<br/><br/></p>"; 
				} elseif ($this->getCouponDiscount()) {
					$discountHtml .= '<p class="discount_amount">'.$this->__('Discount Amount').' : -'.$this->getFormatPrice($this->getCouponDiscount())."<br/><br/></p>"; 
				}
				if ($this->getTax() && $this->getTax() > 0):
					$total_label = "Grand Total Inc. Tax";
					$discountHtml .= '<p class="discount_amount">'.$this->__('Tax').' : '.$this->getFormatPrice($this->getTax())."<br/><br/></p>"; 
				endif;
			endif;
			echo '<div class="cl"></div><div class="raw" style="padding-top:0px; padding-bottom:0px; border: none;"><div class="left"><input type="text" name="coupon_code" id="coupon_code" class="w-30 required-entry"><input type="button" class="coupon" value="'.$this->__('Apply Coupon').'"  onclick="return couponFunction();"></div><div class="right"><input type="button" class="coupon" value="'.$this->__('Update Cart').'" onclick="return handlerFunction(\'update\', \'\');"><div class="cl"></div></div>
			<div class="total">'.$discountHtml.$this->__($total_label).': '.$this->getFormatPrice($price).' </div>';
		}
		exit;
    }


	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
    public function forgotPostAction()
    {
		 	  
        $email = (string) $this->getRequest()->getPost('forgot-email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                echo $this->__('Invalid email address.'); exit;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
					if(Mage::getVersion() <= '1.5.1.0') {
						$newPassword = $customer->generatePassword();
						$customer->changePassword($newPassword, false);
						$customer->sendPasswordReminderEmail();
					} else {
						$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
						$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
						$customer->sendPasswordResetConfirmationEmail();
					}
                } catch (Exception $exception) {
                    echo $exception->getMessage(); exit;
                }
            }
            echo $this->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email)); exit;
        } else {
            echo $this->__('Please enter your email.'); exit;
        }
    }
	
	public function allConfigVars() 
	{
		return Mage::helper('ixcbadv')->getAllConfigVars();
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

	public function inlinePostAction()
    {
		return;
	}

}