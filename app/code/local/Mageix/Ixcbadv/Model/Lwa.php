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
class Mageix_Ixcbadv_Model_Lwa extends Mage_Core_Model_Abstract
{
     public function _construct()
    {    
        $this->_init('ixcbadv/lwa');
    }
    
    public function userProfiledata($data)
    {
        //check if the extension is in Sandbox or Production mode, and then use the correct endpoint.
		if(Mage::helper('ixcbadv')->getSandboxMode()){
			$url_send = "https://api.sandbox.amazon.com/user/profile";
		}
		else{
			$url_send = "https://api.amazon.com/user/profile";
		}
		$helper = Mage::helper("ixcbadv");
		$clientId = Mage::helper('core')->decrypt($helper->getClientId());
		$c = curl_init('https://api.amazon.com/auth/o2/tokeninfo?access_token='
		  . urlencode($_REQUEST['access_token']));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$r = curl_exec($c);
		curl_close($c);
		$d = json_decode($r);
		if ($d->aud != $clientId) {
		  // the access token does not belong to us
		  header('HTTP/1.1 404 Not Found');
		  echo $clientId;
		  echo 'Page not found';
		  exit;
		}
		// exchange the access token for user profile
		$c = curl_init($url_send);
		curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer '
		  . $_REQUEST['access_token']));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$r = curl_exec($c);
		curl_close($c);
		$d = json_decode($r);
		return $d;
    }
	public function customerCheck($profile)
	{
			if($profile && $profile->user_id) {

                                       $amazonAccountId = Mage::helper('core')->encrypt(base64_encode($profile->user_id));
                                       $amazonUserCollection = Mage::getModel('ixcbadv/lwa')
                                                               ->getCollection()
                                                               ->addFieldToFilter('amazon_user_id', $amazonAccountId);

                                        foreach($amazonUserCollection as $amazonUser){
                                        $customer_id = $amazonUser->getCustomerId();
                                         }

                                        $customer = Mage::getModel('customer/customer')
                                                   ->setWebsiteId(Mage::app()->getWebsite()->getId())
                                                   ->load($customer_id);

					if(!$customer->getId()){
					
					$customer = Mage::getModel('customer/customer')
					            ->setWebsiteId(Mage::app()->getWebsite()->getId())
					            ->loadByEmail($profile->email);
					            
					        if ($customer->getId()){
					        
					        try
                                                  {
                                                  $amazonAccountId = Mage::helper('core')->encrypt(base64_encode($profile->user_id));
						  Mage::getModel('ixcbadv/lwa')
                                                      ->setCustomerId($customer->getId())
                                                      ->setAmazonUserId($amazonAccountId)
                                                      ->save();
                                                  }
                                                  catch(Exception $e){
                                                  $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                                                  $tbl_qry = "CREATE TABLE IF NOT EXISTS `ixcbadv_amazon_user` (`ID` int(11) NOT NULL AUTO_INCREMENT,`customer_id` varchar(255) NOT NULL,`amazon_user_id` varchar(255) NOT NULL,PRIMARY KEY (`ID`), UNIQUE KEY `unq_customer`,(`customer_id`,`amazon_user_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$write->query($tbl_qry);
			$amazonAccountId = Mage::helper('core')->encrypt(base64_encode($profile->user_id));
						  Mage::getModel('ixcbadv/lwa')
                                                      ->setCustomerId($customer->getId())
                                                      ->setAmazonUserId($amazonAccountId)
                                                      ->save();
                                                  
                                                  }
						
						
						}
					}
					
					if($customer->getId()) {
						$customer_id = $customer->getId();
					} else {
					
							
        
						// create customer
						if(false===strpos($profile->name, ' ')) {
							$len = round(strlen($profile->name) / 2);
							$data['firstname'] = substr($profile->name, 0, $len);
							$data['lastname'] = substr($profile->name, $len);
						} else {
							$list = explode(' ', $profile->name);
							$data['lastname'] = array_pop($list);
							$data['firstname'] = implode(' ', $list);
						}
						$data['email'] = $profile->email;
						$data['user_id'] = $profile->user_id;
						$data['postal_code'] = $profile->postal_code;

						$customer_id = $this->_createCustomer($data);

						if ($customer_id != ''){
						
						$amazonAccountId = Mage::helper('core')->encrypt(base64_encode($profile->user_id));
						$LwaModel = Mage::getModel('ixcbadv/lwa')
                                                           ->setCustomerId($customer_id)
                                                           ->setAmazonUserId($amazonAccountId)
                                                           ->save();
						}	
					}
			}
			// login user
			if(false!==$customer_id) {
				$this->_loginCustomer($customer_id);
			}
			// redirect (if new to edit profile / if in checkout
			return 1;
		//	$this->_afterRedirect();
	}
	public function _createCustomer($data)
    {
        $customer = Mage::getModel('customer/customer')
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
	    //->setAmazonId($data['user_id'])
            ->setPassword(md5(uniqid() . $data['user_id']))
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

    public function _loginCustomer($customer_id) {
        $session = Mage::getSingleton('customer/session');
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if($customer->getId()) {
            $session->setCustomerAsLoggedIn($customer);
        }
    }

    public function _afterRedirect($referurl)
    {
        $session = Mage::getSingleton('customer/session');

        if ($referer = $referurl) {
            $referer = Mage::helper('core')->urlDecode($referer);
            if ((strpos($referer, Mage::app()->getStore()->getBaseUrl()) === 0)
                    || (strpos($referer, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)) {
                $session->setBeforeAuthUrl($referer);
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            }
        } else {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        return $session->getBeforeAuthUrl(true);
    }
}