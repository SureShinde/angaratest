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
class Apptha_Facebookapp_IndexController extends Mage_Core_Controller_Front_Action {

    public function _construct()
    {
        parent::_construct();
        $this->appId = Mage::getStoreConfig('facebookapp/general/facebook_app_id');
        $this->appSecret = Mage::getStoreConfig('facebookapp/general/facebook_app_secret');
        require 'facebook/facebook.php';
    }
    public function indexAction()
    {

        $product_id = $this->getRequest()->getParam('id');
        $this->loadLayout();
        $this->renderLayout();
    }
     public function suggest_productAction()
    {

        // Create our Application instance (replace this with your appId and secret).
       // $product_id = $this->getRequest()->getPost('postid', false);
        $friends_id = $this->getRequest()->getPost('ids', false);
        $product_id = $this->getRequest()->getPost('postid');
        $message = $this->getRequest()->getPost('message');
        $model = Mage::getModel('catalog/product');
        $_product = $model->load($product_id);
        $product_name = $_product->getName();
        $product_description = $_product->getDescription(); 
        $_product->getProductUrl();
        $_categories = $_product->getCategoryIds();
        if (count($_categories) > 0)
        {
            $_category = Mage::getModel('catalog/category')->load($_categories[0]);
            $product_url = $_product->getUrlPath();
        }
        else
        {
            $product_url = $_product->getUrlPath();
        }

        $picture = $_product->getImageUrl();
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
        if ($session)
            {
                $myUrl = Mage::getStoreConfig('facebookapp/general/facebook_app_path') . 'index.php/' . $product_url;
                $fbToken = $session['access_token'];
                $attachment = array('name' => $product_name,
                    'caption' => 'I bought s new product from groupclone',
                    'message' => $message,
                    'description' => $product_description,
                    'link' => $myUrl,
                    'access_token' => $fbToken,
                    'picture' => "$picture",
                );
                //$friend_appuser = $facebook->api(array('method' => 'friends.getAppUsers'));
                foreach ($friends_id as $users)
                {
                    $result = $facebook->api('/' . $users . '/feed/', 'post', $attachment);
                }
                //$result = $facebook->api('/me/feed/', 'post', $attachment);
                echo '<script>self.parent.tb_remove();</script>';
            }
    }

}
