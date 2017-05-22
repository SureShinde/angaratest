<?php
/**
 * @name         :  Magento Facebook App
 * @version      :  1.1
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  6 October 2011
 * 
 * */
class Apptha_Facebookapp_Model_Observer extends Varien_Object
{

   
    public $appId;
    public $myUrl;
    public $appSecret;
    public $_websiteCollection;
    
    /* get the appid and secret key from the admin configuration */

    public function _construct()
    {
        parent::_construct();
        $this->appId = Mage::getStoreConfig('facebookapp/faceconnect/facebook_app_id');
        $this->appSecret = Mage::getStoreConfig('facebookapp/faceconnect/facebook_app_secret');
        $this->_websiteCollection = Mage::app()->getWebsites();
        require_once 'facebook/facebook.php';
    }

    /*This event triggers after the product save */
    /* function :get the product details and post the product in facebook app wall */

    public function catalog_product_save_after($observer)
    {
        
        $product = $observer->getProduct();
        
        $product_website = $product->getWebsiteIds();
        foreach($product_website as $website)
         {
              $website_name[] = strtoupper($this->getwebsite_name($website));
         }
         
         if(in_array('FACEBOOK', $website_name))
         {
          
             $_categories = $product->getCategoryIds();
            if ($product->getStatus() == 1)
             {
                $_categories = $product->getCategoryIds();
                if (count($_categories) > 0)
                {
                    $_category = Mage::getModel('catalog/category')->load($_categories[0]);
                    // $product_url = $this->getUrl($_category->getUrlPath()).$product->getUrlPath();
                    $url_key = $product->getUrlKey();
                    if (empty($url_key))
                    {
                        $product_url = $product->getName() . '.html';
                    }
                    else
                    {
                        $product_url = $url_key . '.html';
                    }
                }
                else
                {
                    $url_key = $product->getUrlKey();
                    if (empty($url_key))
                    {
                        $product_url = $product->getName() . '.html';
                    }
                    else
                    {
                        $product_url = $url_key . '.html';
                    }
                }

            $product_name = $product->getname();
            $product_description = $product->getdescription();
            $model = Mage::getModel('catalog/product');
            $_product = $model->load($product->getId());
            $imagepath = $product->getSmallImage();          
            if($imagepath == 'no_selection')
            {
              $picture = 'http://fbrell.com/f8.jpg';
            }
            else
            {
                $picture = Mage::helper('catalog/image')->init($product, 'small_image');
            }
            $facebook = new Facebook(array(
                        'appId' => "$this->appId",
                        'secret' => "$this->appSecret",
                        'cookie' => true,
                    ));
            $loginUrl = $facebook->getLoginUrl(array(
                        'canvas' => 1,
                        'fbconnect' => 0,
                        'req_perms' => 'email,read_stream,read_friendlists,manage_friendlists,user_about_me,publish_stream,offline_access',
                        'next' => 'APP CANVAS URL IN FACEBOOK',
                        'cancel_url' => 'URL WHERE TO REDIRECT WHEN ACCESS NOT GRANTED',
                    ));
            $session = $facebook->getSession();
            $getName =  $facebook->api(array(
                        "method"    => "fql.query",
                        "query"     => "select name from user where uid=me()"
                    ));
            $getappName =  $facebook->api(array(
                        "method"    => "fql.query",
                        "query"     => "select display_name from application where app_id=$this->appId"
                    ));
           // $user_name = $getName[0]['name'];
            $app_name = $getappName[0]['display_name'];
            //$token = $this->get_app_token($this->appId, $this->appSecret);//call the get_app_token function to get the get
            $token = '';
            /* redirect url when click the product in facebook wall */
            $myUrl = Mage::getStoreConfig('facebookapp/faceconnect/facebook_app_path') . 'index.php/' . $product_url;
            
            $attachment = array('name' => $product_name,
                'caption' => $product_name,
                'description' => $product_description,
                'link' => "$myUrl",
                'access_token' => $token,
                'picture' => "$picture",
                'message' => "$app_name has added a new product to its store.",
            );
            /* api to post the product in the wall */
            //$result = $facebook->api('/'.$this->appId.'/feed/', 'post', $attachment);
          }
      }
        return;
    }

    /*function: get the apptoken of the facebook app */

    public function get_app_token($appid, $appsecret)
        {
            //$ch = curl_init();
            $url = "https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id=$appid&client_secret=$appsecret";
            $r = file_get_contents($url);
            /*$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1');
            $r = curl_exec($ch);*/
            $access_token = substr($r, 13);
            
            return $access_token = substr($r, 13);
        }

        /*This event triggers after the order save */
        /* function :get the product details of this order and post the product in its wall and app using friends wall */

     public function checkout_type_onepage_save_order($observer)
       {
        $event = $observer->getEvent();
        $_order = $event->getOrder();
        $_quote = $event->getQuote();
        $customer = Mage::getModel('customer/customer')->load($_order->getCustomerId());
        $total = $_quote->getBaseGrandTotal();
        $items = $_order->getAllItems();
        $itemTotal = count($items);

        $facebook = new Facebook(array(
                    'appId' => "$this->appId",
                    'secret' => "$this->appSecret",
                    'cookie' => true,
                ));
        $loginUrl = $facebook->getLoginUrl(array(
                    'canvas' => 1,
                    'fbconnect' => 0,
                    'req_perms' => 'publish_stream,offline_access,manage_pages',
                    'next' => 'APP CANVAS URL IN FACEBOOK',
                    'cancel_url' => 'URL WHERE TO REDIRECT WHEN ACCESS NOT GRANTED',
                ));
        $session = $facebook->getSession();
        $getappName =  $facebook->api(array(
                    "method"    => "fql.query",
                    "query"     => "select display_name from application where app_id=$this->appId"
                ));
         $app_name = $getappName[0]['display_name'];
        foreach ($items as $itemId => $item)
         {
            $product = $item->getProduct();
            $_categories = $product->getCategoryIds();
            if ($product->getStatus() == 1)
             {
                $_categories = $product->getCategoryIds();
                if (count($_categories) > 0)
                {
                    $_category = Mage::getModel('catalog/category')->load($_categories[0]);
                    $product_url = $this->getUrl($_category->getUrlPath()) . $product->getUrlPath();
                }
                else
                {
                    $product_url = $this->getUrl($product->getUrlPath());
                }
            }
            $product_description = $product->getdescription();
            $product_name = $product->getname();
            $picture = Mage::helper('catalog/image')->init($product, 'small_image');
            if(empty($picture))
            {
              $picture = 'http://fbrell.com/f8.jpg';
            }
            if ($session)
            {
                $myUrl = Mage::getStoreConfig('facebookapp/faceconnect/facebook_app_path') . 'index.php/' . $product_url;
                $fbToken = $session['access_token'];
                $attachment = array('name' => $product_name,
                    'caption' => 'Just purchased this product',
                    'description' => $product_description,
                    'link' => $myUrl,
                    'access_token' => $fbToken,
                    'picture' => "$picture",
                    'message' => "This is a great product from $app_name.  I would strongly recommend everyone to purchase it from $app_name",
                );
                /* get the friend id of the appusers */
                $friend_appuser = $facebook->api(array('method' => 'friends.getAppUsers'));
                foreach ($friend_appuser as $app_users)
                {
                    $result = $facebook->api('/' . $app_users . '/feed/', 'post', $attachment);
                }
                /* api to post the product in the wall */
                $result = $facebook->api('/me/feed/', 'post', $attachment);
            }
        }
        return;
    }

    /*This event triggers  before the controller action predispatch */
    /* function :get the email id of the facebook login user and add as customer in magento site if email is not already register */

    public function catalog_product_new_action($observer)
    {

        $facebook = new Facebook(array(
                    'appId' => "$this->appId",
                    'secret' => "$this->appSecret",
                    'cookie' => true,
                ));
        $loginUrl = $facebook->getLoginUrl(array(
                    'canvas' => 1,
                    'fbconnect' => 0,
                    'req_perms' => 'email,read_stream,read_friendlists,manage_friendlists,user_about_me,publish_stream,offline_access',
                    'next' => 'APP CANVAS URL IN FACEBOOK',
                    'cancel_url' => 'URL WHERE TO REDIRECT WHEN ACCESS NOT GRANTED',
                ));
        $session = $facebook->getSession();
      
        if($session)
        {
            $checkIfUserLikePage =  $facebook->api(array(
                "method"    => "fql.query",
                "query"     => "select first_name,last_name,email from user where uid=me()"
                    ));
            $customer = Mage::getModel('customer/customer');
            /* check the user stands on facebook site*/
           if(strtoupper(Mage::app()->getStore()->getWebsite()->getName())=='FACEBOOK')
            {

            if($checkIfUserLikePage[0]['email'])
            {
            $customer
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($checkIfUserLikePage[0]['email']);
            if($customer->getId()){
                $randomPassword = $customer->generatePassword(8);
                  $customer->setId($customer->getId())
                                       ->setSkipConfirmationIfEmail($checkIfUserLikePage[0]['email'])
                                            ->setFirstname($checkIfUserLikePage[0]['first_name'])
                                            ->setLastname($checkIfUserLikePage[0]['last_name'])
                                            ->setEmail($checkIfUserLikePage[0]['email']);
                  $customer->save();
                  $this->getcustomer_session()->setCustomerAsLoggedIn($customer);

            }
            else
            {
                  $randomPassword = $customer->generatePassword(8);
                  $customer->setId(null)
                                       ->setSkipConfirmationIfEmail($checkIfUserLikePage[0]['email'])
                                            ->setFirstname($checkIfUserLikePage[0]['first_name'])
                                            ->setLastname($checkIfUserLikePage[0]['last_name'])
                                            ->setEmail($checkIfUserLikePage[0]['email'])
                                            ->setPassword($randomPassword)
                                            ->setConfirmation($randomPassword)
                                            ->setFacebookUid($facebook->getUser());
                  $customer->save();
                  $this->getcustomer_session()->setCustomerAsLoggedIn($customer);
           }
            }
        }
        }
    return;
    }

    /*Get the customer session */

    private function getcustomer_session()
	{
		return Mage::getSingleton('customer/session');
	}

    /*Get the Website name using the website id*/

    public function getwebsite_name($websiteId)
    {
        foreach ($this->_websiteCollection as $website)
        {
            if ($website->getId() == $websiteId)
            {
                return $website->getName();
            }
        }
        return null;
    }
    
}