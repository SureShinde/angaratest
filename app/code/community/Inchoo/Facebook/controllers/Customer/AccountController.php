<?php
/**
 * Facebook Customer account controller
 *
 * @category    Inchoo
 * @package     Inchoo_Facebook
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Facebook_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
	protected $_cookieCheckActions = array('connect');
	
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('facebook/config')->isEnabled()) {
            $this->norouteAction();
            return;
        }
    }	

	public function connectAction()
    {
    	
    	if(!$this->_getSession()->validate()) {
    		$this->_getCustomerSession()->addError($this->__('Facebook connection failed.'));
    		$this->_redirect('*/*');
    		return;
    	}
    	
    	//login or connect
    	
    	$customer = Mage::getModel('customer/customer');
    	
    	$collection = $customer->getCollection()
    	 			->addAttributeToFilter('facebook_uid', $this->_getSession()->getUid())
    				->setPageSize(1);
    				
    	if($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        
        if($this->_getCustomerSession()->isLoggedIn()) {
        	$collection->addFieldToFilter('entity_id', array('neq' => $this->_getCustomerSession()->getCustomerId()));
        }
        
        $uidExist = (bool)$collection->count();
        
        if($this->_getCustomerSession()->isLoggedIn() && $uidExist) {
        	$existingCustomer = $collection->getFirstItem();
			$existingCustomer->setFacebookUid('');
        	$existingCustomer->getResource()->saveAttribute($existingCustomer, 'facebook_uid');
        }
        	
		if($this->_getCustomerSession()->isLoggedIn()) {
       		$currentCustomer = $this->_getCustomerSession()->getCustomer();
 			$currentCustomer->setFacebookUid($this->_getSession()->getUid());
			$currentCustomer->getResource()->saveAttribute($currentCustomer, 'facebook_uid');        	
			
			$this->_getCustomerSession()->addSuccess(
				$this->__('Your Facebook account has been successfully connected. Now you can fast login using Facebook anytime. Exclusive offer for you: Use discount code CLASSIC for 10% off + free gift(s).')
			);				
			if(Mage::getSingleton('customer/session')->getBeforeAuthUrl())
			{
				$url = Mage::getSingleton('customer/session')->getBeforeAuthUrl();				
				$this->_redirectUrl($url);
				return;
			}
			else
			{				
				$this->_redirectReferer();
				return;
			}
        }
        
        if($uidExist) {
        	$uidCustomer = $collection->getFirstItem();
        	//additional fix:
			if($uidCustomer->getConfirmation()){
				$uidCustomer->setConfirmation(null);
				Mage::getResourceModel('customer/customer')->saveAttribute($uidCustomer, 'confirmation');
			}
			//
			$this->_getCustomerSession()->setCustomerAsLoggedIn($uidCustomer);
			$this->_getCustomerSession()->addSuccess(
				$this->__('Your Facebook account has been successfully connected. Now you can fast login using Facebook anytime. Exclusive offer for you: Use discount code CLASSIC for 10% off + free gift(s).')
			);
			if(Mage::getSingleton('customer/session')->getBeforeAuthUrl())
			{
				$url = Mage::getSingleton('customer/session')->getBeforeAuthUrl();				
				$this->_redirectUrl($url);
				return;
			}
			else
			{				
				$this->_redirectReferer();
				return;
			}
			
        }
        
		
        //let's go with e-mail
        
        try {
        	$standardInfo = $this->_getSession()->getClient()->call("/me");
        	
		} catch(Mage_Core_Exception $e) {
    		$this->_getCustomerSession()->addError(
    			$this->__('Facebook connection failed.') .
    			' ' . 
    			$this->__('Service temporarily unavailable.')
    		);
    		$this->_redirectReferer();
    		return;    		
    	}
		
    	//@todo: check are first_name and last_name always there
		if(!isset($standardInfo['email'])) {
    		$this->_getCustomerSession()->addError(
    			$this->__('Facebook connection failed.') .
    			' ' .
				$this->__('Email address is required.')
    		);
    		$this->_redirectReferer();
    		return;
		}
		
		$customer
			->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
			->loadByEmail($standardInfo['email']);
		
		if($customer->getId()){
			$customer->setFacebookUid($this->_getSession()->getUid());
			Mage::getResourceModel('customer/customer')->saveAttribute($customer, 'facebook_uid');
			
			if($customer->getConfirmation()){
				$customer->setConfirmation(null);
				Mage::getResourceModel('customer/customer')->saveAttribute($customer, 'confirmation');
			}
			
			$this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
			$this->_getCustomerSession()->addSuccess(
				$this->__('Your Facebook account has been successfully connected. Now you can fast login using Facebook anytime. Exclusive offer for you: Use discount code CLASSIC for 10% off + free gift(s).')
			);
			if(Mage::getSingleton('customer/session')->getBeforeAuthUrl())
			{
				$url = Mage::getSingleton('customer/session')->getBeforeAuthUrl();				
				$this->_redirectUrl($url);
				return;
			}
			else
			{				
				$this->_redirectReferer();
				return;
			}
			
			
		}
		
		//registration needed
		
		$randomPassword = $customer->generatePassword(8);
		
		$customer	->setId(null)
					->setSkipConfirmationIfEmail($standardInfo['email'])
					->setFirstname($standardInfo['first_name'])
					->setLastname($standardInfo['last_name'])
					->setEmail($standardInfo['email'])
					->setPassword($randomPassword)
					->setConfirmation($randomPassword)
					->setFacebookUid($this->_getSession()->getUid());

		//FB: Show my sex in my profile
		
		
		//FB: Show my full birthday in my profile
       
		
		//$customer->getGroupId(); // needed in 1.3.x.x ?
		
		//for future versions and easy mods ;)
		if ($this->getRequest()->getParam('is_subscribed', false)) {
			$customer->setIsSubscribed(1);
		}
		
		//registration will fail if tax required, also if dob, gender aren't allowed in profile
		$errors = array();
		$validationCustomer = $customer->validate();
		if (is_array($validationCustomer)) {
				$errors = array_merge($validationCustomer, $errors);
		}
		$validationResult = count($errors) == 0;

		if (true === $validationResult) {
			$customer->save();
			
			$this->_getCustomerSession()->addSuccess(
				$this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
				'. ' . 
				$this->__('Your Facebook account has been successfully connected. Now you can fast login using Facebook anytime. Exclusive offer for you: Use discount code CLASSIC for 10% off + free gift(s).')
			);
			
			$customer->sendNewAccountEmail();
			
			$this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
			$this->_redirectReferer();
			return;
		
		//else set form data and redirect to registration
		} else {
 			$this->_getCustomerSession()->setCustomerFormData($customer->getData());
 			$this->_getCustomerSession()->addError($this->__('Facebook profile can\'t provide all required info, please register and then connect with Facebook for fast login.'));
			if (is_array($errors)) {
				foreach ($errors as $errorMessage) {
					$this->_getCustomerSession()->addError($errorMessage);
				}
			}
			
		$this->_redirectReferer();
			
		}

    }
	
	private function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
    
	private function _getSession()
	{
		return Mage::getSingleton('facebook/session');
	}
	
	 
		
		
    
	
	
}
