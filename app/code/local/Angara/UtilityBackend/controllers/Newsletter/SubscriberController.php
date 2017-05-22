<?php 

/**
 * @rewrite by Asheesh
 */ 
  
require_once(Mage::getModuleDir('controllers','Mage_Newsletter').DS.'SubscriberController.php');
class Angara_UtilityBackend_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController
{
    // Angara Modification Start
	public function msgAction()
	{
		if(isset($_SESSION['newslettermsg']))
		{
			echo $_SESSION['newslettermsg'];
			unset($_SESSION['newslettermsg']);
		}
		else
		{
			echo 'NA';
		}
		exit;
	}
	// Angara Modification End
	/**
      * New subscription action
      */
	
	public function newajaxAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
			// Angara Modification Start
			$newslettertype		= (string) $this->getRequest()->getPost('newslettertype');
			$errflag = 0;
			// Angara Modification End
            try {
				
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    echo 'Please enter a valid email address.';
					exit;
                }
				
				// Angara Modification Start
				// HPRAHI Codes				
				$cust = Mage::getModel('newsletter/subscriber')->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
				$cust = $cust->loadByEmail($email);
				if($cust->getSubscriberEmail())
				{
					$status = $cust->getStatus();
					if($status == '3')
					{
						$cust = $cust->setStatus('1');
						$cust->save();
						//$session->addSuccess($this->__('Thank you for your subscription.'));
						//$_SESSION['newslettermsg'] = '1:' . $newslettertype . ':Thank you for your subscription.';
						echo 'Thank you for subscribing. Check you email for 15% OFF on your first purchase.';
						$session->setEmailSubscribed(true);
						Mage::dispatchEvent('newsletter_subscriber_event', array('email' => $email ,'id' => $cust->getId() ));
						//$this->_redirectReferer();	
						return;
					}
					else if($status == '2')
					{
						echo ($this->__('Your subscription has not been confirmed yet.'));
						return;
					}
					else if($status == '1')
					{
						echo ($this->__('This email address is already subscribed.'));
						return;
					}
					else
					{
						echo ($this->__('There was a problem with the subscription.'));
						return;
					}
					
				}				
				// HPRAHI Code Ends
				// Angara Modification End
				
                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    echo ($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
					return;
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    echo ($this->__('This email address is already assigned to another user.'));
					return;
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
					// Angara Modification Start
					//$session->addSuccess($this->__('Confirmation request has been sent.'));
					echo 'Confirmation request has been sent.';
					return;
					// Angara Modification End
                }
                else {
                    // Angara Modification Start
					//$session->addSuccess($this->__('Thank you for your subscription.'));
					echo 'Thank you for subscribing. Check you email for 15% OFF on your first purchase.';
					$session->setEmailSubscribed(true);
					return;
					// Angara Modification End
                }
            }
            catch (Mage_Core_Exception $e) {
				// Angara Modification Start
				//$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
				echo $e->getMessage();
				$errflag = 1;
				// Angara Modification End
            }
            catch (Exception $e) {
                // Angara Modification Start
				//$session->addException($e, $this->__('There was a problem with the subscription.'));
				$_SESSION['newslettermsg'] = '0:' . $newslettertype . ':There was a problem with the subscription.';
				$errflag = 1;
				// Angara Modification End
            }
        }
		else{
			echo 'Please enter email address.';
			exit;
		}
		// Angara Modification Start
		if($newslettertype == '11-Military-Offer' && $errflag == 0){
			header("Refresh:0;URL='http://www.angara.com/'");exit;			
		}else{
			//$this->_redirectReferer();
		}
       	// Angara Modification End
    }
	  
	  
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
			// Angara Modification Start
			$newslettertype		= (string) $this->getRequest()->getPost('newslettertype');
			$errflag = 0;
			// Angara Modification End
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }
				
				// Angara Modification Start
				// HPRAHI Codes				
				$cust = Mage::getModel('newsletter/subscriber')->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
				$cust = $cust->loadByEmail($email);
				if($cust->getSubscriberEmail())
				{
					$status = $cust->getStatus();
					if($status == '3')
					{
						$cust = $cust->setStatus('1');
						$cust->save();
						//$session->addSuccess($this->__('Thank you for your subscription.'));
						$_SESSION['newslettermsg'] = '1:' . $newslettertype . ':Thank you for your subscription.';
						Mage::dispatchEvent('newsletter_subscriber_event', array('email' => $email ,'id' => $cust->getId() ));
						
						$this->_redirectReferer();	
						return;
					}
					else if($status == '2')
					{
						Mage::throwException($this->__('Your subscription has not been confirmed yet.'));
					}
					else if($status == '1')
					{
						Mage::throwException($this->__('This email address is already subscribed.'));
					}
					else
					{
						Mage::throwException($this->__('There was a problem with the subscription.'));
					}
					
				}				
				// HPRAHI Code Ends
				// Angara Modification End
				
                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
					// Angara Modification Start
					//$session->addSuccess($this->__('Confirmation request has been sent.'));
					$_SESSION['newslettermsg'] = '1:' . $newslettertype . ':Confirmation request has been sent.';
					// Angara Modification End
                }
                else {
                    // Angara Modification Start
					//$session->addSuccess($this->__('Thank you for your subscription.'));
					$_SESSION['newslettermsg'] = '1:' . $newslettertype . ':Thank you for your subscription.';
					// Angara Modification End
                }
            }
            catch (Mage_Core_Exception $e) {
				// Angara Modification Start
				//$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
				$_SESSION['newslettermsg'] = '0:' . $newslettertype . ':' . $e->getMessage();
				$errflag = 1;
				// Angara Modification End
            }
            catch (Exception $e) {
                // Angara Modification Start
				//$session->addException($e, $this->__('There was a problem with the subscription.'));
				$_SESSION['newslettermsg'] = '0:' . $newslettertype . ':There was a problem with the subscription.';
				$errflag = 1;
				// Angara Modification End
            }
        }
		// Angara Modification Start
		if($newslettertype == '11-Military-Offer' && $errflag == 0){
			header("Refresh:0;URL='http://www.angara.com/'");exit;			
		}else{
			$this->_redirectReferer();	
		}
       	// Angara Modification End
    }

    /**
     * Subscription confirm action
     */
    public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');

            if($subscriber->getId() && $subscriber->getCode()) {
                if($subscriber->confirm($code)) {
                    $session->addSuccess($this->__('Your subscription has been confirmed.'));
                } else {
                    $session->addError($this->__('Invalid subscription confirmation code.'));
                }
            } else {
                $session->addError($this->__('Invalid subscription ID.'));
            }
        }

        $this->_redirectUrl(Mage::getBaseUrl());
    }
	
	public function confirmedAction()
    {
		$params		= $this->getRequest()->getParams();
		$session 	= Mage::getSingleton('core/session');
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('email')) != '') {
			$email  = (string) $this->getRequest()->getParam('email');			
			$returnUrl = (string) $this->getRequest()->getParam('return_url');
			
			try {
					if (!Zend_Validate::is($email, 'EmailAddress')) {
						$session->addError($this->__('Please enter a valid email address.'));
					}
					
					$subscriber = Mage::getModel('newsletter/subscriber')->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
					$subscriber = $subscriber->loadByEmail($email);
					// code for mailchimp tracking of subscribers :Start
					$sessionId = $session->getSessionId(); 
					$mailchimp = Mage::getModel('abandoncartmailchimp/session');		
							$mailchimp->setSessionId($sessionId);
							$mailchimp->setVisitorEmail($email);												
							$mailchimp->setCreatedAt(now());					
							$mailchimp->save();
					// code for mailchimp tracking of subscribers :End
					if($subscriber->getSubscriberEmail())	//	email exist in magento (user subscribed earlier)
					{
						$status = $subscriber->getStatus();
						if($status == '3')		//	Status = Unsubscribed
						{
							$newSubscriber	=	1;
							$subscriber = $subscriber->setStatus('1');
							$subscriber->save();
							//:RV - $session->addSuccess($this->__('Congratulations! You&#39;re about to save 15%. Enter code: <b class="apricot-text">EMLNZ</b> at checkout.'));
							//$session->addSuccess($this->__('Thank you for subscribing. Please check your inbox for the coupon.'));
							Mage::getSingleton('core/session')->setData('subspopmsg', '<span class="text-up">Thank you for subscribing.</span><span class="text-down">Please check your inbox for the coupon.</span>');
							Mage::dispatchEvent('newsletter_subscriber_event', array('email' => $email ,'id' => $subscriber->getId() ));
						}
						else if($status == '2')//	Status = Not Activated
						{
							$session->addError($this->__('Your subscription has not been confirmed yet.'));
						}
						else if($status == '1')//	Status = Subscribed
						{
							$session->addError($this->__('This email address is already subscribed.'));
						}
						else
						{
							$session->addError($this->__('There was a problem with the subscription.'));
						}
						
					}
					else{
						$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
						if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
							$session->addSuccess($this->__('Confirmation request has been sent.'));
						}
						else {
							$newSubscriber	=	1;
							//:RV - $session->addSuccess($this->__('Congratulations! You&#39;re about to save 15%. Enter code: <b class="apricot-text">EMLNZ</b> at checkout.'));
							//$session->addSuccess($this->__('Thank you for subscribing. Please check your inbox for the coupon.'));
							Mage::getSingleton('core/session')->setData('subspopmsg', '<span class="text-up">Thank you for subscribing.</span><span class="text-down">Please check your inbox for the coupon.</span>');
						}
					}
				}
				catch (Mage_Core_Exception $e) {
					$session->addError($e, $this->__('There was a problem with the subscription.'));
				}
				catch (Exception $e) {
					$session->addError($e, $this->__('There was a problem with the subscription.'));
				}
							
			// S:VA		
			Mage::getSingleton('core/session')->setData('frm_submit',true);		//	set the session
			
			//	Modifying return url so that we can set the channel
			if($newSubscriber	==	1)	{
				$parts 					= parse_url($returnUrl);
				parse_str($parts['query'], $query);
				$param['cid'] 			= 'em-ln';
				$param['resetChannel'] 	= 'true';
				$parts['query'] 		= http_build_query($param);
				$queryString			=	'?' . urldecode($parts['query']);
				if($returnUrl){
					$this->_redirectUrl($returnUrl . $queryString);
				}else{
					$this->_redirectUrl(Mage::getBaseUrl() . $queryString);
				}
			}else{
				if($returnUrl)
					$this->_redirectUrl($returnUrl);
				else
					$this->_redirectUrl(Mage::getBaseUrl());
			}
		}
		else{
			$session->addError($this->__('Please enter a valid email address.'));
		}
    }
	
	public function confirmedAjaxAction()
    {
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('email')) != '') {
			$email  = (string) $this->getRequest()->getParam('email');
			$newsletterType = (string) $this->getRequest()->getParam('newslettertype');
			$session = Mage::getSingleton('core/session');
			
			try {
				if (!Zend_Validate::is($email, 'EmailAddress')) {
					echo $this->__('Please enter a valid email address.');
				}
				else{
					$subscriber = Mage::getModel('newsletter/subscriber')->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
					$subscriber = $subscriber->loadByEmail($email);
					
					// code for mailchimp tracking of subscribers :Start
					$sessionId = $session->getSessionId(); 
					$mailchimp = Mage::getModel('abandoncartmailchimp/session');		
							$mailchimp->setSessionId($sessionId);
							$mailchimp->setVisitorEmail($email);												
							$mailchimp->setCreatedAt(now());					
							$mailchimp->save();
					// code for mailchimp tracking of subscribers :End
					
					if($subscriber->getSubscriberEmail()) 
					{
						$status = $subscriber->getStatus();
						if($status == '3')
						{
							$subscriber = $subscriber->setStatus('1');
							$subscriber->save();
							if($newsletterType == '04-mini-promotion'){
								echo $this->__('Thank You! You have access to this year’s lowest prices.');
							}
							else{
								echo $this->__('Thank You for Subscribing. Please check your <br />inbox for exciting new offers on great products.');
							}
							Mage::dispatchEvent('newsletter_subscriber_event', array('email' => $email ,'id' => $subscriber->getId() ));
						}
						else if($status == '2')
						{
							echo $this->__('Your subscription has not been confirmed yet.');
						}
						else if($status == '1')
						{
							echo $this->__('This email address is already subscribed.');
						}
						else
						{
							echo $this->__('There was a problem with the subscription.');
						}
						
					}
					else{
						$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
						if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
							echo $this->__('Confirmation request has been sent.');
						}
						else {
							if($newsletterType == '04-mini-promotion'){
								echo $this->__('Thank You! You have access to this year’s lowest prices.');
							}
							else{
								echo $this->__('Thank You for Subscribing. Please check your <br />inbox for exciting new offers on great products.');
							}
						}
					}
				}
			}
			catch (Mage_Core_Exception $e) {
				echo $this->__('There was a problem with the subscription.');
			}
			catch (Exception $e) {
				echo $this->__('There was a problem with the subscription.');
			}
		}
		else{
			echo $this->__('Please enter a valid email address.');
		}
    }

    /**
     * Unsubscribe newsletter
     */
    public function unsubscribeAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $session = Mage::getSingleton('core/session');
            try {
                Mage::getModel('newsletter/subscriber')->load($id)
                    ->setCheckCode($code)
                    ->unsubscribe();
                $session->addSuccess($this->__('You have been unsubscribed.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the un-subscription.'));
            }
        }
        $this->_redirectReferer();
    }
	
	// code for mailchimp unsubscribing in mailchimp lists 
	public function unsubAction()
	{
		$params = $this->getRequest()->getParams();
		$email = $params['email'];
		unset($params['email']);		
		try 
		{	
			$session = Mage::getSingleton('core/session');
			$mcapi= new MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
			$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');
			$mandrill->rejects->add($email);			
			if($mcapi->listsForEmail($email))
			{				
				$lists = $mcapi->listsForEmail($email);					
				foreach($lists as $list)
				{							
					$mcapi->listUnsubscribe($list, $email, $delete_member=false, $send_goodbye=false, $send_notify=false);							
					
				}
				$session->addSuccess($this->__('You have been unsubscribed.'));
				$this->_redirectUrl(Mage::getBaseUrl());
			}
			else
			{
				$session->addError($this->__('You are not a subscriber.'));				
			}
		}		
		 catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
            }
        catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the un-subscription.'));
            }
			$this->_redirectUrl(Mage::getBaseUrl());			
	}

	public function emailSubscribeAction()
    {
        try {
            $email=$this->getRequest()->getParam('email');
            $location=$this->getRequest()->getParam('location');
            $orderid=$this->getRequest()->getParam('order_id');

            $session     = Mage::getSingleton('core/session');
            $subscriber = Mage::getModel('newsletter/subscriber')
                ->loadByEmail($email);
            
            $order=Mage::getModel('sales/order')->loadByIncrementId($orderid);
            if($order)
            	$order_email=$order->getBillingAddress()->getEmail();
     
            
            if ($subscriber->getId()) {
                $session->addError($this->__('This email address is already subscribed.'));
                $this->_redirect("/");

            } else if($order && $order->getBillingAddress()->getEmail()==$email){
                
                $subscriber->subscribe($email,$location);
               
                Mage::getSingleton('core/session')->setData('subspopmsg', '<span class="text-up">Thank you for subscribing.</span><span class="text-down">Please check your inbox for the coupon.</span>');
                
                $this->_redirect("/");

            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
    
    public function emailFreeShippingAction()
	{
		try {
            $email=$this->getRequest()->getPost('email');
            $error = false;
            
            if (!Zend_Validate::is($this->getRequest()->getPost('email'), 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('firstname'), 'NotEmpty')) {
                $error = true;
            }

            if (!Zend_Validate::is($this->getRequest()->getPost('lastname'), 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('zi'), 'NotEmpty')) {
                $error = true;
            }
            if(($this->getRequest()->getPost('DOBMonth')=='2' || $this->getRequest()->getPost('DOBMonth')=='4' || $this->getRequest()->getPost('DOBMonth')=='6' || $this->getRequest()->getPost('DOBMonth')=='9' || $this->getRequest()->getPost('DOBMonth')=='11') ||  ($this->getRequest()->getPost('Month')=='2' || $this->getRequest()->getPost('Month')=='4' || $this->getRequest()->getPost('Month')=='6' || $this->getRequest()->getPost('Month')=='9' || $this->getRequest()->getPost('Month')=='11'))
            {
            	if($this->getRequest()->getPost('DOBDay')=='31' || $this->getRequest()->getPost('Day')=='31')
            	{
               		 $error = true;
            	}
            }
       
            

            if ($error) {
            	throw new Exception();
            }
            else
            {
            	$firstname=$this->getRequest()->getPost('firstname');
            	$lastname=$this->getRequest()->getPost('lastname');
            	$gender=$this->getRequest()->getPost('gender');
            	$dobMonth=$this->getRequest()->getPost('DOBMonth');
            	$dobDay=$this->getRequest()->getPost('DOBDay');
            	$dobYear=$this->getRequest()->getPost('DOBYear');
            	$rel=$this->getRequest()->getPost('relationship');
            	$month=$this->getRequest()->getPost('Month')?$this->getRequest()->getPost('Month'):'';
            	$day=$this->getRequest()->getPost('Day')?$this->getRequest()->getPost('Day'):'';
            	$year=$this->getRequest()->getPost('Yr')?$this->getRequest()->getPost('Yr'):'';
            	$zip=$this->getRequest()->getPost('zi');
            	$dob=$dobYear.'-'.$dobMonth.'-'.$dobDay;
            	if($year && $day && $month)
            		$dow=$year.'-'.$month.'-'.$day;
            	else
            		$dow=NULL;
            	
            	$subscriber = Mage::getModel('newsletter/subscriber')
                ->loadByEmail($email);
                $coreResource = Mage::getSingleton('core/resource');
			$write=$coreResource->getConnection('core_write');
                if ($subscriber->getId())
                {
                	$query="UPDATE newsletter_subscriber SET subscriber_firstname ='$firstname' ,subscriber_lastname ='$lastname',gender='$gender',birthday='$dob',relationship_status='$rel',wedding_date='$dow',zip='$zip',free_shipping='1' WHERE subscriber_email='$email'";
                	$write->query($query);
                	Mage::dispatchEvent('newsletter_subscriber_save_before', array('email' => $email));
                	/*Mage::getSingleton('customer/session')->addSuccess(Mage::helper('core')->__('Thank you for your time. Make your next purchase through one of our emails and unlock Free Express Shipping.'));*/
                	Mage::getSingleton('core/session')->setData('subspopmsg', '<span class="text-up">Thank you for your time.</span><span class="text-down">Make your next purchase through one of our emails and unlock Free Express Shipping.</span>');
                }

                 $this->_redirect("/");
            	
            }

            
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError(Mage::helper('core')->__('Unable to submit your request. Please, try again later'));
            $this->_redirectReferer();

           
        }
		
		
	}
	public function shippingdetailUpdateAction()
	{
		try 
		{
			$error = false;
            $email=$this->getRequest()->getPost('email');
            $tracking=$this->getRequest()->getPost('cid');
            $state_country=$this->getRequest()->getParam('billing');
            //tracking if url contains ?cid="em-" parameter
            if($email && $tracking)
            {
            	$string=$tracking;
		        $arr = explode("-", $string, 2);
		        $first = $arr[0];
		        if($first!="em")
		        	$error = true;	 
            }
            else
            	$error = true;
           
            $subscriber = Mage::getModel('newsletter/thanksgivingdata')
			            ->getCollection()
			            ->addFieldToFilter('subscriber_email', $email)
			            ->getFirstItem();
            $subscriber_newsletter = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            
            //server side validation of entries

            if (!Zend_Validate::is($this->getRequest()->getPost('email'), 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('firstname'), 'NotEmpty')) {
                $error = true;
                
            }

            if (!Zend_Validate::is($this->getRequest()->getPost('lastname'), 'NotEmpty')) {
                $error = true;
                
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('street'), 'NotEmpty')) {
                $error = true;
                
            }
             if (!Zend_Validate::is($this->getRequest()->getPost('city'), 'NotEmpty')) {
                $error = true;
               
            }
             if (Zend_Validate::is($state_country['region_id'], 'NotEmpty')) {
             	$region = Mage::getModel('directory/region')->load($state_country['region_id']);
                $state_name = $region->getName();
                
                
            }
            else if (Zend_Validate::is($state_country['region'], 'NotEmpty')) {
                $state_name = $state_country['region'];
                
            }
            else
            {
            	$error = true;
            	
            }
            	
            if (Zend_Validate::is($state_country['country_id'], 'NotEmpty')) {
             	$country = Mage::getModel('directory/country')->loadByCode($state_country['country_id']);
                $country_name= $country->getName();
                
            }
            else
            {
            	$error = true;
                
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('phone'), 'NotEmpty')) {
                $error = true;
                
            }
            if (!Zend_Validate::is($this->getRequest()->getPost('zi'), 'NotEmpty')) {
                $error = true;
                
            }
           
            // checking if subscriber had already updated entry in  table and it exist in newsletter_subscriber table.
            $sub_date=$subscriber_newsletter->getCaptureDate();
            $sub_time=$subscriber_newsletter->getCaptureTime();
			$arr_t = explode(":", $sub_time, 2);
			$arr_d = explode("-", $sub_date, 3);
			$day1 = $arr_d[0];
			$day2 = $arr_d[1];
			$day3 = $arr_d[2];
			if(!($day1==2016 && $day2==11 && $day3==24))
			{
				$error = true;
			}
            
            if($subscriber->getId() || !($subscriber_newsletter->getId()))
            {
            	$error = true;
            	
            }
            if ($error) {
            	throw new Exception();
            }
            else
            {
				$firstname=$this->getRequest()->getPost('firstname');
            	$lastname=$this->getRequest()->getPost('lastname');
            	$street=$this->getRequest()->getPost('street');
            	$apartment=$this->getRequest()->getPost('apartment');
            	$city=$this->getRequest()->getPost('city');
            	$state=$state_name;
            	$country=$country_name;
            	$phone=$this->getRequest()->getPost('phone');
            	$zi=$this->getRequest()->getPost('zi');
            	//entering values into database in a new table 
                $coreResource = Mage::getSingleton('core/resource');
			    $write=$coreResource->getConnection('core_write');
               
            	$query="INSERT INTO newsletter_thanksgivingdata VALUES ('','$email','$firstname','$lastname','$street','$apartment','$city','$state','$country','$zi','$phone')";
            	$write->query($query);
                // redirecting user to a specific landing page
                $baseurl=Mage::getBaseUrl();
                Mage::app()->getFrontController()->getResponse()
                ->setRedirect($baseurl.'thanksgiving-thankyou.html',301)
                ->sendResponse();
                 exit;
            }   
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError(Mage::helper('core')->__('Unable to submit your request. Please, try again later'));
            $this->_redirectReferer(); 
        }
		
    }
    
}