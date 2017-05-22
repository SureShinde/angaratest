<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');
class Angara_UtilityBackend_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        // Angara Modification Start
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_review');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
		//return $this->getLayout()->getBlock('root')->toHtml();
		// Angara Modification End
    }

    
	/*
	*	Angara Modification Start
	*/
	public function angaracartsummaryAction()
	{
		$country_id = $this->getRequest()->getParam('country_id');
		$postcode = $this->getRequest()->getParam('postcode');
		$checkout = $this->getOnepage();
		
		$checkout->getQuote()->getShippingAddress()
			->setCountryId($country_id)
			->setCollectShippingRates(true)
			->setPostcode($postcode);
		$checkout->getQuote()->collectTotals();
		
		// new code added by anil to resolve shipping method empty error - 23-10-2012
		$cart = Mage::getSingleton('checkout/cart');
		$quote = $cart->getQuote();
		$method = $quote->getShippingAddress()->getShippingMethod();
		if($method == ''){
			if($country_id == 'US'){	
				$checkout->getQuote()->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
			}else{
				$checkout->getQuote()->getShippingAddress()->setShippingMethod('angnonusflatrate_angnonusflatrate');
			}
		}
		// new code added by anil to resolve shipping method empty error - 23-10-2012
		//echo 'after: '.$method = $quote->getShippingAddress()->getShippingMethod();
		
		
		$checkout->getQuote()->save();
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_cart_totals');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
		echo( $output);		
	}
	
	
	public function angaracartshippingmethodAction()
	{
		$country_id = $this->getRequest()->getParam('country_id');
		$checkout = Mage::getSingleton('checkout/type_onepage');

		$checkout->getQuote()->getShippingAddress()
			->setCountryId($country_id)
			->setCollectShippingRates(true)
			->collectTotals();
		$checkout->getQuote()->save();
		echo $this->_getShippingMethodsHtml();
	}
	// Angara Modification End
	
	
    /**
     * Checkout page
     */
    public function indexAction()
    {
        // Angara Modification Start
		// @ TODO set the correct shipment method
		
		/* Added by hitesh */
		Mage::getSingleton('checkout/session')->setData('shipment',$this->getRequest()->getParam('shipment'));
		$cntrycode = 'US';
		if($this->getRequest()->getParam('shipment') == 'angmexcanflatrate_angmexcanflatrate')
		{
			$cntrycode = 'CA';
		}
		else if($this->getRequest()->getParam('shipment') == 'angnonusflatrate_angnonusflatrate')
		{
			$cntrycode = 'AU';
		}
		else
		{
			$cntrycode = 'US';
		}
		$checkout = Mage::getSingleton('checkout/type_onepage');

		$checkout->getQuote()->getShippingAddress()
			->setCountryId($cntrycode)
			->setCollectShippingRates(true)
			->collectTotals();
		$checkout->getQuote()->save();
		
		$checkout->saveShippingMethod($this->getRequest()->getParam('shipment'));
		/* End by Hitesh*/
		// Angara Modification End
		
		if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    
	// Angara Modification Start
	public function checkoutfixforspdisc()
	{
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote();
		if($cart)
		{
			$cartid = $cart->getId();
			if($cartid)
			{
				$db = Mage::getSingleton('core/resource')->getConnection('core_write');
				if($db)
				{
					$result = $db->query("SELECT * FROM `sales_flat_quote_spdisc` where quoteid='" . $cartid . "'");
					if($result)
					{
						$rows = $result->fetch(PDO::FETCH_ASSOC);
						if($rows) 
						{
							$db->query("update `sales_flat_quote_address` set spdisc='1' where quote_id='" . $cartid . "' and address_type='shipping'");
						}
					}
				}
			}
		}
	}
	// Angara Modification End
	
	
    /**
     * save checkout billing address
     */
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
			// Angara Modification Start
			if(isset($data['customer_password']) && $data['customer_password']!=''){
				$this->getOnepage()->saveCheckoutMethod('register');
			}else{
				$this->getOnepage()->saveCheckoutMethod('guest');
			}
			// Angara Modification End
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
		// Angara Modification Start
		$this->checkoutfixforspdisc();
		// Angara Modification End
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
		// Angara Modification Start
		$this->checkoutfixforspdisc();
		// Angara Modification End
    }

        
    /**
     * Create order action
     */
    public function saveOrderAction()
    {
		
        // Angara Modification Start
		// Start Billing Empty data check - Anil Jain
		$chkBillResponse = $this->checkBillingDataEmpty($this->getOnepage()->getQuote());
		if($chkBillResponse == '1'){
			$result['success'] = false;
			$result['error'] = true;
			$result['error_messages'] = $this->__('We encountered an error while processing your order. Your credit card was not charged. Please check the details entered on this page for accuracy and click "Complete Order".');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
		}
		// End Billing Empty data check - Anil Jain
		
		// Start easy pay - Anil Jain
		$this->easyPayProcessAction($this->getOnepage()->getQuote());		
		// End easy pay - Anil Jain
		// Angara Modification End
		
		if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            //$result['error_messages'] = $e->getMessage();
			
			// Angara Modification Start
			$msg_hp = $e->getMessage();
			$system_error_msg = $e->getMessage();
			if($msg_hp == "Declined")
			{
				$msg_hp = "Sometimes banks block certain credit and debit card numbers for security purposes. We would request you to call the bank to authorize the purchase on Angara and try purchasing again. You can also call us on 1-888-926-4272 or chat with us for further assistance.";
				// Start email alert code - added by anil jain - 02-12-2011
				$this->emailAlertOrderProblem($this->getOnepage()->getQuote(),'Decline',$system_error_msg);
				// End email alert code - added by anil jain - 02-12-2011	
			}elseif($msg_hp == "Referral")
			{
				$msg_hp = "Your transaction is not approved by your card issuing bank. \n\rPlease contact your card issuing bank to provide a verbal authorization. \n\rYou may also try using another card. Once approved, click \"Complete Order\" again. \n\rIf this does not resolve the problem, please contact us.";
				// Start email alert code - added by anil jain - 02-12-2011
				$this->emailAlertOrderProblem($this->getOnepage()->getQuote(),'Referral',$system_error_msg);
				// End email alert code - added by anil jain - 02-12-2011		
			}else{
				// Start email alert code - added by anil jain - 02-12-2011
				$this->emailAlertOrderProblem($this->getOnepage()->getQuote(),'Bounce',$system_error_msg);
				// End email alert code - added by anil jain - 02-12-2011		
			}
            $result['error_messages'] = $msg_hp;
			// Angara Modification End
			
            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    
	// Angara Modification Start
	// Start Easy Pay code
	public function easyPayProcessAction($getQuoteArr=NULL)
    {		
    	//Braintree condition
    	 if (preg_match('/MSIE\s(?P<v>\d+)/i', @$_SERVER['HTTP_USER_AGENT'], $B) && $B['v'] <= 10)
    	 	$btree_flag=1;
    	 else
    	 	$btree_flag=0;
        

		$rPath = '';
		include_once $rPath.'app/code/core/Mage/Easypay/constants.php';
		include_once $rPath.'app/code/core/Mage/Easypay/CallerService.php';
		
		// Billing Information
		$billingArr = $getQuoteArr->getBillingAddress()->getData();
		
		// Card/Payment Information
		$cardPaymentArr = $getQuoteArr->getPayment()->getData();
		$ccardArr = $this->getRequest()->getPost('payment', false);               
		$cardPaymentArr = array_merge($cardPaymentArr, $ccardArr);
		
		// Cart Data
		$cartArr = $getQuoteArr->getAllItems();
		
		//Get required parameters from the web form for the recurring
		$firstName = trim($billingArr['firstname']);
		$lastName = trim($billingArr['lastname']);
		$creditCardOwner = $cardPaymentArr['cc_owner']; // James A;
		$creditCardType = $this->getCardType($cardPaymentArr['cc_type']); // Visa;
		$expDateMonth = $cardPaymentArr['cc_exp_month']; // 11;
		$expDateYear = substr($cardPaymentArr['cc_exp_year'],2,2); // 12;
		if(isset($_SESSION['payment']) && !empty($_SESSION['payment'])){
			$creditCardNumber = base64_decode($_SESSION['payment']['longNumber']); // set in \app\code\core\Mage\Payment\Model\Method\Cc.php
			$cvv2Number = base64_decode($_SESSION['payment']['secNumber']);  // set in \app\code\core\Mage\Payment\Model\Method\Cc.php
		}else{
			$creditCardNumber = '';
			$cvv2Number = '';
		}
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
		
		$email = trim($billingArr['email']);
		$address1 = trim($billingArr['street']);
		$address2 = trim($billingArr['street']);
		$city = trim($billingArr['city']);
		$state = trim($billingArr['region']);
		$zip = trim($billingArr['postcode']);
		$phone = trim($billingArr['telephone']);
		$currencyCode = "USD";
		$temp_price = 1;
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode(); 
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$convFactor = Mage::helper('directory')->currencyConvert($temp_price, $baseCurrencyCode, $currentCurrencyCode);
		
		$emi_profile_id_info = '';
		$emi_type = '';
		$_SESSION['pay_mode']='';
		foreach($cartArr as $item){
			//$cartItemDetail = $item->getBuyRequest()->getData();					
			
			/*if(isset($cartItemDetail['easyopt'])){
				$easypay_flag = $this->isEasyPay($cartItemDetail['easyopt']);
			}else{
				$easypay_flag = 0;	
			}*/
						
			if($item->getNoOfInstallment() > 1){
				$easypay_flag = 1;
			}else{
				$easypay_flag = 0;	
			}
						
			if($easypay_flag=='1'){	
			    $no_of_installment = $item->getNoOfInstallment();	
				
				if($emi_type==''){
					$emi_type = ($no_of_installment);
				}else{
					$emi_type = $emi_type.', '.($no_of_installment);
				}								
				//list($no_of_installment,$itemEasyPrice) = explode('_',$cartItemDetail['easyopt']);
				if(!Mage::helper('core')->isModuleEnabled('Gene_Braintree') || $btree_flag){	
				
				
				$ItemInstallmentAmt = Mage::getBlockSingleton('checkout/cart')->getCartItemInstallmentWithDiscAmount($item->getCalculationPrice(),$item->getNoOfInstallment(),$item->getQty(),$item->getDiscountAmount());			
												
				$item->getProduct()->setName($item->getProduct()->getName().' - '.$this->getItemRecurringInfo($no_of_installment, $ItemInstallmentAmt, $convFactor));
				
				$profileDesc = $item->getProduct()->getName();
				$billingPeriod = 'Month'; // create recurring profile based on Monthly basis deduction
				$billingFrequency = 1; // put frequesncy value
				$totalBillingCycles = $no_of_installment - 1; // Number of month for recurring profile
				
				$profileStartDate = date('mdY',mktime(0,0,0,date("m")+1,date("d"),date("Y"))); 
								
				$amount = $ItemInstallmentAmt / $convFactor;
				$amount = round($amount,2);
				if($amount <= 0) continue;
				$nvpstr="TRXTYPE=".TRXTYPE."&TENDER=".TENDER."&PARTNER=".PAYPAL_PARTNER."&VENDOR=".PAYPAL_VENDOR."&USER=".PAYPAL_USER."&PWD=".PAYPAL_PWD."&ACTION=A&PROFILENAME=".$firstName."".$lastName."&AMT=".$amount."&ACCT=".$creditCardNumber."&EXPDATE=".$padDateMonth.$expDateYear."&CVV2=".$cvv2Number."&START=".$profileStartDate."&PAYPERIOD=MONT&TERM=".$totalBillingCycles."&OPTIONALTRX=A&COMMENT1=".$profileDesc."&FIRSTNAME=".$firstName."&LASTNAME=".$lastName."&EMAIL=".$email."&DESC=".$profileDesc."&STREET=".$address1.", ".$state."&CITY=".$city."&ZIP=".$zip."&PHONENUM=".$phone;
				
				//Mage::log('Easy Pay Request PARAM: '.$nvpstr, null, 'easy_pay.log');
				//echo $nvpstr;exit;
				
				$resArray=hash_call(PAYPAL_RECURRING_URL,$nvpstr);
				if(isset($resArray)){	
					if($resArray["RESULT"] != '0'){
						$RP_response = 'Declined['.serialize( $resArray).']';
						$RP_Profileid = 'NA';
						Mage::log('Easy Pay Request PARAM: '.$nvpstr, null, 'easy_pay.log');
						Mage::log('Easy Pay Request Gateway Response: '.$RP_response.'----'.$RP_Profileid, null, 'easy_pay.log');
					}else{
						
						$RP_response = 'Approved';
						$RP_Profileid = $resArray['PROFILEID'];
					}
					if($RP_Profileid!='NA'){
						if($emi_profile_id_info==''){
							$emi_profile_id_info = $RP_Profileid;
						}else{
							$emi_profile_id_info = $emi_profile_id_info.', '.$RP_Profileid;
						}
					}
				}				
			}// end of braintree if condition
		  }
		}
		$_recInfo_temp = Mage::getBlockSingleton('checkout/cart')->hasRecurringItem();
		if($_recInfo_temp == 1){
			$_SESSION['pay_mode'] = 'EMI'.($emi_type!=''?' ('.$emi_type.')':'');	
		}else{
			$_SESSION['pay_mode'] = 'FULL';
		}
		if($emi_profile_id_info!=''){
			$_SESSION['emi_profile_list'] = $emi_profile_id_info;
			$emi_profile_id_info = 'Profile created on gateway. Profile ID details are: '.$emi_profile_id_info;
			$_SESSION['emi_profile_id_info'] = $emi_profile_id_info;
		}
		//Mage::log('Easy Pay Order Profile detail: '.$emi_profile_id_info, null, 'easy_pay.log');
		return true;
		//Mage::log('Easy Pay Order Profile detail: '.$emi_profile_id_info, null, 'easy_pay.log');
		//exit;
	}
	
	public function isEasyPay($easyopt = NULL)
    {
		list($no_of_installment,$installment_amount) = explode('_',$easyopt);
		if($no_of_installment > 0){
			$easypay_flag = 1;
		}else{
			$easypay_flag = 0;	
		}
		return $easypay_flag;
	}	
	
	public function getItemRecurringInfo($no_of_installment=1, $installment_amount=NULL, $convFactor = NULL)
    {
		if($no_of_installment <= 1){
			$recurringInfo = '';
		}else{
			$amt = $installment_amount / $convFactor;
			$amt = round($amt,2);
			$recurringInfo = 'Selected Pay Option: '.$no_of_installment.' payments of $'.$amt;
		}
		return $recurringInfo;
	}
		
	public function getCardType($cardShortName = NULL)
    {
		switch ($cardShortName) {
			case 'VI': //Visa
				$cardName = 'Visa';
				break;
			case 'AE': //American Express
				$cardName = 'Amex';
				break;
			case 'MC': //MasterCard
				$cardName = 'MasterCard';
				break;
			case 'DI'://Discover
				$cardName = 'Discover';
				break;
			default:
			   $cardName = 'Visa';
		}
		return $cardName;
	}		
	// End Easy Pay Code
	
	// Start email alert code - added by anil jain - 02-12-2011
	public function emailAlertOrderProblem($getQuoteArr=NULL,$email_type='Approved',$system_error_msg=NULL)
    {	
		// Billing Information
		$billingArr = $getQuoteArr->getBillingAddress()->getData();
		//echo '<pre>';print_r($billingArr);echo '</pre>';exit;	
		
		// Card/Payment Information
		$cardPaymentArr = $getQuoteArr->getPayment()->getData();
		//echo '<pre>';print_r($cardPaymentArr);echo '</pre>';exit;		
		
		// Cart Data
		$cartArr = $getQuoteArr->getAllItems();			
		
		$firstName = trim($billingArr['firstname']);
		$lastName = trim($billingArr['lastname']);
		$email = trim($billingArr['email']);
		$address1 = trim($billingArr['street']);
		$address2 = trim($billingArr['street']);
		$city = trim($billingArr['city']);
		$state =trim($billingArr['region']);
		$zip = trim($billingArr['postcode']);
		$country = trim($billingArr['country_id']);
		$phone = trim($billingArr['telephone']);
				
		//	S:VA 
		$domainName	=	Mage::getBaseUrl();
		if(strstr($domainName, 'angara.com')){
			 $to  = 'vaseem.ansari@angara.com, vinod.guneja@angara.com, customer.support@angara.com, receivables@angara.com, hitesh.baid@angara.com';
		}else{
			 $to  = 'qaangara@gmail.com';	
		}
		//	E:VA
		
		// subject
		$subject = 'Order process - '.$email_type.' at '.$domainName.' - '.date('Y-m-d H:i');
		$emi_profile_list = 'NA';
		if(isset($_SESSION['emi_profile_list'])){
			$emi_profile_list = $_SESSION['emi_profile_list'];
			// start profile cancel by api if order decline by system
			include_once 'app/code/core/Mage/Easypay/constants.php';
			include_once 'app/code/core/Mage/Easypay/CallerService.php';
			$profile_cancel_param = "TRXTYPE=R&TENDER=C&PARTNER=".PAYPAL_PARTNER."&VENDOR=".PAYPAL_VENDOR."&USER=".PAYPAL_USER."&PWD=".PAYPAL_PWD."&ACTION=C&ORIGPROFILEID";
			if(trim($emi_profile_list) != ''){
				$profile_arr = explode(',',$emi_profile_list);
				foreach($profile_arr as $profileparam){
					$acturl = $profile_cancel_param.'='.$profileparam;
					$res_arr = hash_call(PAYPAL_RECURRING_URL,trim($acturl));	
					Mage::log('Easy Pay Request Gateway Decline Profile Response: '.$acturl, null, 'easy_pay.log');
					//echo '<br>Response: '.$RP_response;	
				}
			}
			// end profile cancel by api if order decline by system
		}else{
			$emi_profile_list = 'NA';	
		}
		
		// message
		$message = '<p><h3>'.$subject.'</h3></p>
		  <table width="600">
			<tr>
				<td align="left" valign="top" colspan="2"><strong>Customer Billing Information</strong><hr width="100%"></td>				
			</tr>			
			<tr>
				<td align="left" valign="top">Customer Name: </td>
				<td align="left" valign="top">'.$firstName.' '.$lastName.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Email: </td>
				<td align="left" valign="top">'.$email.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Address1: </td>
				<td align="left" valign="top">'.$address1.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Address2: </td>
				<td align="left" valign="top">'.$address2.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">City: </td>
				<td align="left" valign="top">'.$city.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">State: </td>
				<td align="left" valign="top">'.$state.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Country: </td>
				<td align="left" valign="top">'.$country.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Zip: </td>
				<td align="left" valign="top">'.$zip.'</td>
			</tr>
			<tr>
				<td align="left" valign="top">Phone: </td>
				<td align="left" valign="top">'.$phone.'</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2">&nbsp;</td>				
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2"><strong>Shopping Cart Information</strong><hr width="100%"></td>				
			</tr>			
			<tr>
				<td align="left" valign="top" colspan="2">
					<table>';
						$aj = 1;
						foreach($cartArr as $item){
							$product_info = $item->getProduct()->getSku().' : '.$item->getProduct()->getName();									
							$message.='<tr>
									<td align="left" valign="top">Product '.$aj.': </td>
									<td align="left" valign="top">'.$product_info.'</td>				
								</tr>';
							$aj++;	
						}
					$message.='</table>
				</td>				
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2"><strong>EMI Profile Information</strong><hr width="100%"></td>				
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2">'.$emi_profile_list.'</td>				
			</tr>
			<tr>
				<td align="left" valign="top"><strong>Customer Error Message: </strong></td>
				<td align="left" valign="top">'.$system_error_msg.'</td>
			</tr>
			<tr>
				<td align="left" valign="top"><strong>IP Address: </strong></td>
				<td align="left" valign="top">'.@$_SERVER["REMOTE_ADDR"].'</td>
			</tr>
			<tr>
				<td align="left" valign="top"><strong>User Browser Info: </strong></td>
				<td align="left" valign="top">'.@$_SERVER['HTTP_USER_AGENT'].'</td>
			</tr>			
		  </table>';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers		
		$headers .= 'From: Angara Order Technical Department <techsupport@angara.com>' . "\r\n";		
		//echo $message;exit;
		// Mail it
		mail($to, $subject, $message, $headers);
	}
	// End email alert code - added by anil jain - 02-12-2011
	
	// Start check empty billing/shipping data code - added by anil jain - 16-05-2012
	public function checkBillingDataEmpty($getQuoteArr=NULL)
    {
		// Billing Information
		$billingArr = $getQuoteArr->getBillingAddress()->getData();
		
		// Cart Data
		$cartArr = $getQuoteArr->getAllItems();	

		$firstName = trim($billingArr['firstname']);
		$lastName = trim($billingArr['lastname']);
		$email = trim($billingArr['email']);
		$address1 = trim($billingArr['street']);
		$city = trim($billingArr['city']);
		$state =trim($billingArr['region']);
		$zip = trim($billingArr['postcode']);
		$country = trim($billingArr['country_id']);
		$phone = trim($billingArr['telephone']);
		
		if($firstName == '' || $lastName == '' || $address1 == '' || $city == '' || $zip == '' || $address1 == ''){
			$strInfo = 'firstName:'.$firstName.', lastName:'.$lastName.', address1:'.$address1.', city:'.$city.', zip:'.$zip.', address1:'.$address1.', phone:'.$phone.', User Agent:'.$_SERVER['HTTP_USER_AGENT'];
			Mage::log('Billing/Shipping Info Empty: '.$strInfo, null, 'billing_info_empty.log');
			
			$to  = 'techsupport@angara.com';				
			$subject = 'Order process - Billing/Shipping Info Empty Error- '.date('Y-m-d H:i');
			
			$message = '<p><h3>'.$subject.'</h3></p>
			<table width="600">
				<tr>
					<td align="left" valign="top" colspan="2"><strong>Customer Billing Information</strong><hr width="100%"></td>				
				</tr>			
				<tr>
					<td align="left" valign="top" colspan="2">'.$strInfo.'</td>
				</tr>			
				<tr>
					<td align="left" valign="top" colspan="2">&nbsp;</td>				
				</tr>
				<tr>
					<td align="left" valign="top" colspan="2"><strong>Shopping Cart Information</strong><hr width="100%"></td>				
				</tr>			
				<tr>
					<td align="left" valign="top" colspan="2">
						<table>';
							$aj = 1;
							foreach($cartArr as $item){
								$product_info = $item->getProduct()->getSku().' : '.$item->getProduct()->getName();									
								$message.='<tr>
										<td align="left" valign="top">Product '.$aj.': </td>
										<td align="left" valign="top">'.$product_info.'</td>				
									</tr>';
								$aj++;	
							}
						$message.='</table>
					</td>				
				</tr>
				<tr>
					<td align="left" valign="top"><strong>Customer Error Message: </strong></td>
					<td align="left" valign="top">There was an error processing your order. Please try again or contact us.</td>
				</tr>			
			</table>';		
			$headers='MIME-Version: 1.0' . "\r\n";
			$headers.='Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers.='From: Angara Order Technical Department <techsupport@angara.com>' . "\r\n";		
			mail($to, $subject, $message, $headers);
			return '1';
		}else{
			return '0';
		}
	}
	// End check empty billing/shipping data code - added by anil jain - 16-05-2012
	// Angara Modification End
}
