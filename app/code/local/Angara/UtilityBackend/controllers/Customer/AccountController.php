<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Customer').DS.'AccountController.php');
class Angara_UtilityBackend_Customer_AccountController extends Mage_Customer_AccountController
{
	/**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
			//	S:VA
            //$session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
			$session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        //	S:VA
						/*$referer = Mage::getModel('core/url')
                            ->getRebuiltUrl(Mage::helper('core')->urlDecode($referer));*/
						$referer = $this->_getModel('core/url')
                            ->getRebuiltUrl( $this->_getHelper('core')->urlDecodeAndEscape($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
					//	S:VA
                    //$session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
					$session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl( $this->_getHelper('customer')->getDashboardUrl());
        }/*else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }*/ else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
		// Start Angara Modification
		$chkredirectUrlLogin = stripos($_SERVER['HTTP_REFERER'], 'customer/account/login');
		$chkredirectUrlRegister = stripos($_SERVER['HTTP_REFERER'], 'customer/account/create');	
		
		$wishlist_session = Mage::getSingleton('wishlist/session');
		
		if ($chkredirectUrlLogin !== false || $chkredirectUrlRegister !== false) {
			if($wishlist_session->getAddWishlistUrl()){
				$wishlist_session->unsAddWishlistUrl();
			}
			$this->_redirectUrl($session->getBeforeAuthUrl(true));
		}else{
			if($wishlist_session->getAddWishlistUrl()){
				$tmp_addwishlisturl = $wishlist_session->getAddWishlistUrl();	
				$wishlist_session->unsAddWishlistUrl();
				$this->_redirectUrl($tmp_addwishlisturl);
			}else{
				if($wishlist_session->getAddWishlistUrl()){
					$wishlist_session->unsAddWishlistUrl();
				}
				$this->_redirectUrl($_SERVER['HTTP_REFERER']);
			}	
		}
		// End Angara Modification
    }   
	
	
	/**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);
		
		//	S:VA
		$params 		= 	Mage::app()->getRequest()->getParams(); 
		$customerEmail	=	$params['email'];
		$tempArray		=	explode('@',$customerEmail);
		if(is_array($tempArray)){
			$customerEmailDomain	=	$tempArray[1];	
		}
		$notAllowedDomains	=	array('qq.com');
		if(in_array($customerEmailDomain, $notAllowedDomains)){
			$errors	=	array('Sorry! You are not allowed to create an account with us. Please contact to our support team.');
		}
		//	E:VA
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        return $errors;
    }
	
	/**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
		else{
			$customer->setIsSubscribed('');
		}
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }
}
