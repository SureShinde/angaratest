<?php
class Angara_UtilityBackend_Helper_Data extends Mage_Core_Helper_Abstract
{

	/*
		return mandrill Api Key
	*/
	public function getMandrillApiKey(){
		$mandrillApiKey	=	Mage::getStoreConfig('angarainfo/mandrill_settings/mandrill_api_key');
		if($mandrillApiKey==''){
			$mandrillApiKey	=	'k93tI_1-pNTbT9bFTZjD-g';
		}
		return $mandrillApiKey;
	}

	public function getRefererUrl() {
        $refererUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }
        if ($url = Mage::app()->getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }

        $refererUrl = Mage::helper('core')->escapeUrl($refererUrl);

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Check url to be used as internal
     *
     * @param   string $url
     * @return  bool
     */
    protected function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {
            /**
             * Url must start from base secure or base unsecure url
             */
            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
    }
	
	
	
	/*
		Global function added to send emails using Transactional Templates created in admin
	*/
	public function sendTransactionalEmail($templateId, $toEmail, $templateVariables){
		$mailTemplate        = 	Mage::getModel('core/email_template');
		$translate           = 	Mage::getSingleton('core/translate');
		//$templateId          = 	5;		//	Template Id from System -> Transactional Emails
		$template_collection = 	$mailTemplate->load($templateId);
		$template_data       = 	$template_collection->getData();
		//echo '<pre>';print_r($template_data);
		
		if(!empty($template_data)){
			//$mailSubject= 'Subject';   					//	The Template Subject will be taken in sent email
			$from_email = 	Mage::getStoreConfig('trans_email/ident_general/name');	//	'vaseem.ansari@angara.com';		//	from sendor email
			$from_name  = 	Mage::getStoreConfig('trans_email/ident_general/email');	//	'Vaseem';						//	from sender name
			$sender     = 	array('name'=> $from_name,
								'email' => $from_email);
		
			//$templateVariables       = array('customerName' => 'Vasim Ansari');
			$storeId    = 	Mage::app()->getStore()->getStoreId();
			$model 		= 	$mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
			$email 		= 	$toEmail;	//'vaseemansari007@gmail.com';			//	receiver email
			$name  		= 	'Alam Ramesh';						//	receiver name
			//echo '<pre>';print_r($templateVariables);die;
			//Mage::log('templateVariables->'.Zend_Debug::dump($templateVariables, null, false)."\n\r", null, 'emails.log');	
			try {
				$model->sendTransactional($templateId, $sender, $email, $name, $templateVariables, $storeId);
				/*if(!$mailTemplate->getSentSuccess()) {
					echo "Something went wrong...<br>";
				} else {
					echo "Message sent to ".$email."!!!<br>";
				}*/
				$translate->setTranslateInline(true);
			} catch(Exception $e) {
			   Mage::logException($e)  ;
			}
		}
	}

}
	 