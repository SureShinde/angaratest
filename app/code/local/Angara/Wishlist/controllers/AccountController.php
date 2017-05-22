<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';

class Angara_Wishlist_AccountController extends Mage_Customer_AccountController{
	
	protected $_validActions = array('create','login','logoutSuccess','forgotpassword','forgotpasswordpost','confirm','confirmation','resetpassword','resetpasswordpost');
	protected $_customActions = array('login','create','forgotpassword');

	/*
		Checking preDispatch
	*/
    public function preDispatch(){
		$action = $this->getRequest()->getActionName();
		if (preg_match('/^('.$this->_getCustomActions().')/i', $action)){
			$this->getRequest()->setActionName($this->_validActions[1]);		//	check the action, if the requested action doesn't meet the actions defined above then cal parent function
		}
		parent::preDispatch();
		
		/**
		* Parent check is complete, reset request action name to origional value
		*/
		if ($action != $this->getRequest()->getActionName()){
			$this->getRequest()->setActionName($action);
		}
		
		if (!$this->getRequest()->isDispatched()) {
			return;
		}
		
		if (!preg_match('/^('.$this->_getValidActions().')/i', $action)) {
			if (!$this->_getSession()->authenticate($this)) {
				$this->setFlag('', 'no-dispatch', true);
			}
		} else {
			$this->_getSession()->setNoReferer(true);
		}
	}
	 
	
	/*
		
	*/
	protected function _getValidActions(){
		return implode("|", array_merge($this->_validActions, $this->_customActions));
	}


	/**
	  * Gets custom action names and returns them as a pipe separated string
	  *
	  * @return string
	*/
	protected function _getCustomActions() {
		return implode("|", $this->_customActions);
	}
	

	/*
		Customer Login Action
	*/
	public function loginAction(){
		if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
             //$response['url'] = $this->getUrl('*/*/');
             return;
        }
        $session 		= 	$this->_getSession();
		$redirectURL	=	Mage::app()->getRequest()->getServer('HTTP_REFERER');		//	Creating redirect url after item added in wishlist
		$session->setAfterAuthUrl($redirectURL);
		
		$params 		= 	Mage::app()->getRequest()->getParams(); 
		$productId		=	$params['product_id'];
		//	Creating wishlist add url
		$wishlistAddUrl	=	Mage::getBaseUrl().'customer/account';
		if($productId){
			$wishlistAddUrl	=	Mage::getBaseUrl(). 'wishlist/index/add/product/' .$productId;
		}

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
					//$message = Mage::helper('customer')->__('You have been logged in successfully. You are being redirected...');
					$response['message'] = $message;
                    $response['success'] = true;
                    $response['url'] 	 = $wishlistAddUrl;	//	Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                    
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s" class="text-underline">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    //$session->addError($message);
                    $response['message'] = $message;
                    $response['success'] = false;
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                //$session->addError($this->__('Login and password are required.'));
                $message = 'Login and password are required.';
                $response['success'] = false;
                $response['message'] = $message;
            }
        }
		$this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
        //$this->_loginPostRedirect();
	}
	
	
	/*
    	Create Account Action
    */
    public function createAction()
    {
		$session = $this->_getSession();
		if ($session->isLoggedIn()) {
			$this->_redirect('*/*/');
			return;
		}
		$session->setEscapeMessages(true); // prevent XSS injection in user input
		if ($this->getRequest()->isPost()) {
			$errors = array();

			if (!$customer = Mage::registry('current_customer')) {
				$customer = Mage::getModel('customer/customer')->setId(null);
			}

			/* @var $customerForm Mage_Customer_Model_Form */
			$customerForm = Mage::getModel('customer/form');
			$customerForm->setFormCode('customer_account_create')
				->setEntity($customer);

			$customerData = $customerForm->extractData($this->getRequest());

			/**
			 * Initialize customer group id
			 */
			$customer->getGroupId();

			$password = $this->getRequest()->getPost('password');
			//	S:VA we can set a secure password as well
			/*if(!Mage::getStoreConfig('fastregistration/general/show_password')){
				$password = Mage::helper('core')->getRandomString(8,
					Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
					. Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
					. Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
					. Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS);
			}*/
			
			$redirectURL	=	Mage::app()->getRequest()->getServer('HTTP_REFERER');		//	Creating redirect url after item added in wishlist
			$session->setAfterAuthUrl($redirectURL);
			
			$params 		= 	Mage::app()->getRequest()->getParams(); 
			$productId		=	$params['product_id'];
			//	Creating wishlist add url
			$wishlistAddUrl	=	Mage::getBaseUrl().'customer/account';
			if($productId){
				$wishlistAddUrl	=	Mage::getBaseUrl(). 'wishlist/index/add/product/' .$productId;
			}
			
			try {
				$customerErrors = $customerForm->validateData($customerData);
				if ($customerErrors !== true) {
					$errors = array_merge($customerErrors, $errors);
				} else {
					$customerForm->compactData($customerData);
					$customer->setPassword($password);
					$customer->setConfirmation($password);
				}

				$validationResult = count($errors) == 0;
				$response = array();

				if (true === $validationResult) {
					$customer->save();

					Mage::dispatchEvent('customer_register_success',
						array('account_controller' => $this, 'customer' => $customer)
					);

					if ($customer->isConfirmationRequired()) {
						$customer->sendNewAccountEmail(
							'confirmation',
							$session->getBeforeAuthUrl(),
							Mage::app()->getStore()->getId()
						);
						
						$response['success'] = true;
						$response['message'] = $this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s" class="text-underline">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail()));
						$response['url'] = $redirectURL;	//	Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
					} else {
						$session->setCustomerAsLoggedIn($customer);
						$url = $this->_welcomeCustomer($customer);
						
						$response['success'] = true;
						$response['url'] = $wishlistAddUrl;	//	Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
						$response['message'] = '';	//$this->__('You are successfully registered');
					}
				} else {
					$session->setCustomerFormData($this->getRequest()->getPost());
					if (is_array($errors)) {
						$response['success'] = false;
						foreach ($errors as $errorMessage) {
							$response['message'] .= $errorMessage;
						}
					} else {
						$response['success'] = false;
						$response['message'] = $this->__('Invalid customer data');
					}
				}
			} catch (Mage_Core_Exception $e) {
				$session->setCustomerFormData($this->getRequest()->getPost());
				if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
					$url = Mage::getUrl('customer/account/forgotpassword');
					$response['success'] = false;
					$response['message'] = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s" class="text-underline">click here</a> to get your password and access your account.', $url);
				} else {
					$response['success'] = false;
					$response['message'] = $e->getMessage();
				}
			} catch (Exception $e) {
				$response['success'] = false;
				$response['message'] = $this->__('Cannot save the customer.');
			}
		}
		$this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
		//$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
    }


	/*
		Forgot Password Action
	*/
    public function forgotPasswordAction(){
    	
    $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                //$this->_getSession()->addError($this->__('Invalid email address.'));
                // $this->_redirect('*/*/forgotpassword');
                $response['url'] = Mage::getUrl('*/*/forgotpassword');
                $response['message'] = $this->__('Invalid email address.');
                $response['success'] = false; 
                $this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
                return;


            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    //$this->_redirect('*/*/forgotpassword');
                    $response['url'] = Mage::getUrl('*/*/forgotpassword');
                	$response['message'] = $exception->getMessage();
                	$response['success'] = false; 
                	$this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
                    return;
                }
            }
           // $this->_getSession()
             //   ->addSuccess(Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email)));
            //$this->_redirect('*/*/');
            //$response['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $response['message'] = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
 			$response['success'] = true; 
            $this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
            return;
        } else {
            //$this->_getSession()->addError($this->__('Please enter your email.'));
            //$this->_redirect('*/*/forgotpassword');
            $response['url'] = Mage::getUrl('*/*/forgotpassword');
            $response['message'] = $this->__('Please enter your email.');
            $response['success'] = false; 
            $this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
            return;
        }
        
        $this->getResponse()->setBody(Mage::helper( 'core')->jsonEncode( $response ));
    }
    
}