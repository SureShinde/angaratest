<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Contacts').DS.'IndexController.php');
class Angara_UtilityBackend_Contacts_IndexController extends Mage_Contacts_IndexController
{

	/*
	*	Angara Modification Start
	*/
    //	S:VA	Action added to show download guide form
	public function downloadAction() {	
		$this->loadLayout();			 
        $this->renderLayout();
	}
	
	//	S:VA	Action added to save the data from download guide form
	public function downloadPostAction() {		
		$params 	= 	Mage::app()->getRequest()->getParams();
		//pr($params);
		$session 	= 	Mage::getSingleton('core/session');
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('email_address')) != '')
		{
			$email  	= 	(string)$this->getRequest()->getParam('email_address');
			$fname 		= 	(string)$this->getRequest()->getParam('fname');
			$lname 		= 	(string)$this->getRequest()->getParam('lname');
			if (!Zend_Validate::is($email, 'EmailAddress') )
			{	
				$session->addError($this->__('Please provide a correct email address.'));
				$this->_redirect('*/*/download');
				return;
			} else {
				//	Insert the data in table
				$formType	=	'Download Guide';
				$str 		=	"('$formType', '$fname', '$lname', '$email')";
				$connection = 	Mage::getSingleton('core/resource')->getConnection('core_read');
				$sql 		=  "insert into custom_form (form_type, first_name, last_name, email) values ".$str;	
				$connection->query($sql);
				//$session->addSuccess($this->__('Thanks for participation '));		
				$homeurl 	= 	Mage::getBaseUrl();		
				
				Mage::getSingleton('core/session')->setData('dwn_process',true);		//	set the session
				
				//$this->_redirect('*/*/download?s=1#dwn');
				$this->_redirectURL($homeurl.'contacts/index/download/s/1#dwn');
				return;					
			}
		} else {		
			$session->addError($this->__('Please provide a correct email address.'));
			$this->_redirect('*/*/download');
			return;	
		}			
	}
	
	
	public function referredAction()
    {
		$params = $this->getRequest()->getParams();
		if(!empty($params))
		{
			$email = $params['email'];
			$friendemail = $params['friendemail'];
			if(!empty($friendemail))
			{
				$fname = $params['fname'];
				if (Zend_Validate::is($email, 'EmailAddress') && Zend_Validate::is($friendemail, 'EmailAddress'))
				{
					$mcapi= new MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
					$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');
					if($mcapi->listsForEmail($email))
					{					
					}
					else
					{						
						$merge_vars = array(
						'FNAME'=>$fname);
						$mcapi->listSubscribe("89665bc355", $email, $merge_vars, $email_type='html', $double_optin=false, $update_existing=false,$replace_interests=false, $send_welcome=false);
						$message = array('to' => array(array('email' => $email, 'name' => $fname)),
											'merge_vars' => array(array(
												'rcpt' => $email,
												'vars' =>
												 array())));										
						$template_content = array();						
						$template_name = 'Welcome_Email'; 															
						$mandrill->messages->sendTemplate($template_name, $template_content, $message);	
					}
					$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$sql = "update refer_friend set subscribed = 1 where email = '$friendemail' and friend_email = '$email'";				
					$connection->query($sql);
					//$this->_redirectUrl('http://localhost/contacts/index/refer/');
				}	
			}
		}
		$this->loadLayout();			 
		$this->getLayout()->getBlock('referredForm')
        ->setFormAction( Mage::getUrl('*/*/postreferred') );		
        $this->renderLayout();
    }
	public function referAction()
	{	
		$this->loadLayout();			 
		$this->getLayout()->getBlock('referForm')
        ->setFormAction( Mage::getUrl('*/*/postrefer') );		
        $this->renderLayout();
	}
	public function postreferredAction()
    {		
		$session = Mage::getSingleton('core/session');
		$mcapi= new MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
		$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('refemail')) != '')
		{
			$email  = (string)$this->getRequest()->getParam('refemail');
			$fname = (string)$this->getRequest()->getParam('reffname');
			$email1 = (string)$this->getRequest()->getParam('refemail1');
			$fname1 = (string)$this->getRequest()->getParam('reffname1');
			$friendsEmails = array();
			$friendsFnames = array();
			if (!Zend_Validate::is($email, 'EmailAddress') || !Zend_Validate::is($email1, 'EmailAddress'))
			{	
				$session->addError($this->__('Please provide your correct email address'));
				$this->_redirect('*/*/refer');
				return;				
			}			
			else
			{
				$friendsEmails[1] = $email1;
				if($fname1 != 'Friend 1 Name')
				{
					$friendsFnames[1] = $fname1;
				}				
				for($emailentry =2;$emailentry < 6 ; $emailentry ++)
				{	
					$nextemail = (string)$this->getRequest()->getParam('refemail'.$emailentry);			
					$nextfname = (string)$this->getRequest()->getParam('reffname'.$emailentry);
					if (Zend_Validate::is($nextemail, 'EmailAddress'))
					{			
						$friendsEmails[$emailentry] = (string)$this->getRequest()->getParam('refemail'.$emailentry);
						$name = 'Friend '.$emailentry.' Name';				
						if($nextfname != $name)
						{					
							$friendsFnames[$emailentry] = (string)$this->getRequest()->getParam('reffname'.$emailentry);
						}
					}
				}
				if($mcapi->listsForEmail($email))
				{
				}
				else
				{
					$merge_vars = array(
					'FNAME'=>$fname);
					$mcapi->listSubscribe("89665bc355", $email, $merge_vars, $email_type='html', $double_optin=false, $update_existing=false,$replace_interests=false, $send_welcome=false);
					$message = array('to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										 array())));										
					$template_content = array();						
					$template_name = 'Welcome_Email'; 															
					$mandrill->messages->sendTemplate($template_name, $template_content, $message);		
				}
				// email for participation
				$message = array('to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										 array())));										
				$template_content = array();				
				$template_name = 'ThankYou4Participation_Refer2Win'; 															
				$mandrill->messages->sendTemplate($template_name, $template_content, $message);	
				$homeurl = Mage::getBaseUrl();	
				foreach($friendsEmails as $key => $friendsEmail)
				{
					$str =$str.",('$email','$friendsEmail',0)";
									
					$confirmationurl = "$homeurl"."contacts/index/referred/?friendemail=$email&email=$friendsEmail&fname=$friendsFnames[$key]";
					$message = array('to' => array(array('email' => $friendsEmail, 'name' => $friendsFnames[$key])),
									'merge_vars' => array(array(
										'rcpt' => $friendsEmail,
										'vars' =>
										array(
											array(
												'name' => 'FRIENDNAME',
												'content' => $fname),
											array(
												'name' => 'URL',
												'content' => $confirmationurl)										
									))));														
					$template_content = array();					
					$template_name = 'YourFriendReferredYou_Refer2Win'; 															
					$mandrill->messages->sendTemplate($template_name, $template_content, $message);						
				}				
				$str = substr($str, 1);	
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$sql =  "insert into refer_friend (email, friend_email, subscribed) values ".$str;	
				$connection->query($sql);
				//$session->addSuccess($this->__('Thanks for participation '));				
				$this->_redirectURL("$homeurl"."/contacts/index/referred/?submit=1");
				return;					
			}
		}
		else
		{			
			$session->addError($this->__('Please provide your correct email address'));
			$this->_redirect('*/*/refer');
			return;	
		}			
	}
	
	public function postreferAction()
    {		
		$session = Mage::getSingleton('core/session');
		$mcapi= new MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
		$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('refemail')) != '')
		{
			$email  = (string)$this->getRequest()->getParam('refemail');
			$fname = (string)$this->getRequest()->getParam('reffname');
			$email1 = (string)$this->getRequest()->getParam('refemail1');
			$fname1 = (string)$this->getRequest()->getParam('reffname1');
			$friendsEmails = array();
			$friendsFnames = array();
			if (!Zend_Validate::is($email, 'EmailAddress') || !Zend_Validate::is($email1, 'EmailAddress'))
			{	
				$session->addError($this->__('Please provide your correct email address'));
				$this->_redirect('*/*/refer');
				return;				
			}			
			else
			{
				$friendsEmails[1] = $email1;
				if($fname1 != 'Friend 1 Name')
				{
					$friendsFnames[1] = $fname1;
				}				
				for($emailentry =2;$emailentry < 6 ; $emailentry ++)
				{	
					$nextemail = (string)$this->getRequest()->getParam('refemail'.$emailentry);			
					$nextfname = (string)$this->getRequest()->getParam('reffname'.$emailentry);
					if (Zend_Validate::is($nextemail, 'EmailAddress'))
					{			
						$friendsEmails[$emailentry] = (string)$this->getRequest()->getParam('refemail'.$emailentry);
						$name = 'Friend '.$emailentry.' Name';				
						if($nextfname != $name)
						{					
							$friendsFnames[$emailentry] = (string)$this->getRequest()->getParam('reffname'.$emailentry);
						}
					}
				}
				if($mcapi->listsForEmail($email))
				{
				}
				else
				{
					$merge_vars = array(
					'FNAME'=>$fname);
					$mcapi->listSubscribe("89665bc355", $email, $merge_vars, $email_type='html', $double_optin=false, $update_existing=false,$replace_interests=false, $send_welcome=false);
					$message = array('to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										 array())));										
					$template_content = array();						
					$template_name = 'Welcome_Email'; 															
					$mandrill->messages->sendTemplate($template_name, $template_content, $message);		
				}
				// email for participation
				$message = array('to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										 array())));										
				$template_content = array();				
				$template_name = 'ThankYou4Participation_Refer2Win'; 															
				$mandrill->messages->sendTemplate($template_name, $template_content, $message);	
				$homeurl = Mage::getBaseUrl();	
				foreach($friendsEmails as $key => $friendsEmail)
				{
					$str =$str.",('$email','$friendsEmail',0)";
									
					$confirmationurl = "$homeurl"."contacts/index/referred/?friendemail=$email&email=$friendsEmail&fname=$friendsFnames[$key]";
					$message = array('to' => array(array('email' => $friendsEmail, 'name' => $friendsFnames[$key])),
									'merge_vars' => array(array(
										'rcpt' => $friendsEmail,
										'vars' =>
										array(
											array(
												'name' => 'FRIENDNAME',
												'content' => $fname),
											array(
												'name' => 'URL',
												'content' => $confirmationurl)										
									))));														
					$template_content = array();					
					$template_name = 'YourFriendReferredYou_Refer2Win'; 															
					$mandrill->messages->sendTemplate($template_name, $template_content, $message);						
				}				
				$str = substr($str, 1);	
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$sql =  "insert into refer_friend (email, friend_email, subscribed) values ".$str;	
				$connection->query($sql);
				//$session->addSuccess($this->__('Thanks for participation '));				
				$this->_redirectURL("$homeurl"."/contacts/index/refer/?submit=1");
				return;					
			}
		}
		else
		{			
			$session->addError($this->__('Please provide your correct email address'));
			$this->_redirect('*/*/refer');
			return;	
		}			
	}	
	public function subscribeAction()
	{	
		$this->loadLayout();			 
		$this->getLayout()->getBlock('subscribeForm')
        ->setFormAction( Mage::getUrl('*/*/postsub') );		
        $this->renderLayout();
	}
	public function postsubAction()
    {		
		$session = Mage::getSingleton('core/session');
		if($this->getRequest()->isPost() && trim($this->getRequest()->getPost('subemail')) != '')
		{
			$email  = (string)$this->getRequest()->getParam('subemail');			
			if (!Zend_Validate::is($email, 'EmailAddress'))
			{				
				$session->addSuccess($this->__('Please provide correct email address'));
				$this->_redirect('*/*/subscribe');
				return;				
			}
			else
			{
				$fname=(string)$this->getRequest()->getParam('subfname');
				$lname=(string)$this->getRequest()->getParam('sublname');
				$bmonth=$this->getRequest()->getParam('subbirthmonth');
				$bdate=$this->getRequest()->getParam('subbirthdate');
				$amonth=$this->getRequest()->getParam('subanniversarymonth');
				$adate=$this->getRequest()->getParam('subanniversarydate');
				$gender=(string)$this->getRequest()->getParam('subgender');
				$status=(string)$this->getRequest()->getParam('substatus');	
				if( ($bmonth=='MM' && $bdate=='DD') || ($bmonth==02&& ($bdate==30 ||$bdate == 31)) || ($bmonth==04 && $bdate==31) || ($bmonth==04 && $bdate==31) || ($bmonth==09 && $bdate==31) || ($bmonth==11 && $bdate==31)) 
					{
						$bdate='';
						$bmonth='';
					}
				if( ($amonth=='MM' && $adate=='DD') ||($amonth==02 && ($adate==30 ||$adate == 31)) || ($amonth==04 && $adate==31) || ($amonth==04 && $adate==31) || ($amonth==09 && $adate==31) || ($amonth==11 && $adate==31)) 
					{
						$adate='';
						$amonth='';
					}
				$merge_vars = array(
				'FNAME'=>$fname,
				'LNAME'=>$lname, 
				'ANNIVER'=>$amonth.'/'.$adate,
				'BDAY'=>$bmonth.'/'.$bdate,				
				'GENDER'=>$gender,
				'STATUS'=>$status);					
				$mcapi= new MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
				$result=$mcapi->listSubscribe("3a6089e27a", $email, $merge_vars, $email_type='html', $double_optin=false, $update_existing=false,$replace_interests=false, $send_welcome=false);				
				if($result)
				{
					$session->addSuccess($this->__('Thank You for Subscribing. Please check your inbox for exciting new offers on great products.'));
					$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');	
					$message = array('to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										 array())));										
					$template_content = array();					       
					$template_name = 'Welcome_Email'; 															
					$mandrill->messages->sendTemplate($template_name, $template_content, $message);
				}
				else
				{
					$session->addError($this->__('This email address is already subscribed.'));		
				}
				$this->_redirectUrl(Mage::getBaseUrl());
				return;	   
			}			
		}	
		$this->_redirect("*/*/postsub");			
        return;		
	}
	

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
				// Angara Modification Start
                //$this->_redirect('*/*/');
				 $this->_redirect('thank-you.html');
				// Angara Modification End
				
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }
	/*
	*	Angara Modification Ends
	*/

}
