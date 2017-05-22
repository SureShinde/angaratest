<?php
/**
 * Sharecart Extension
 *
 * @category   Sutunam
 * @package    Sharecart
 * @author     Sututeam 
 */

class Sutunam_Sharecart_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'sutunamsharcart/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'sutunamsharcart/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'sutunamsharcart/email/email_template';
    
    
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }


    /**
     * Post Action
     */
    public function postAction()
    {
        $session = $this->_getSession();
        
        $params = $this->getRequest()->getParams();
        
        if(!isset($params['emailshare'])){
            $session->addError(Mage::helper('sharecart')->__('Email is required. Please enter email!'));
            $this->_redirect('checkout/cart');
            return;
        }
        
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($params['emailshare']);
        try {
            //save sharecart
            $sharecart = Mage::getModel('sharecart/cart');
            if($customer->getId()){
                $data['user_id'] = $customer->getId();
            }else{
                $data['user_id'] = 0;
            } 
            
            $data['items'] = serialize(Mage::helper('sharecart')->getItems());
            
            $sharecart->setData($data);
            
            $sharecart->save();
            
            $url = Mage::helper('sharecart')->generateUrlShare($sharecart);
            
            $emailData = new Varien_Object();
            $emailData->setData(array('url' => $url));
            
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            //send email to friend
            $mailTemplate = Mage::getModel('core/email_template');
		//echo  Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);die;
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $params['emailshare'],
                    null,
                    array('data' => $emailData)
                );

            if (!$mailTemplate->getSentSuccess()) {
                throw new Exception();
            }

            $translate->setTranslateInline(true);

            $this->_getSession()->setSharem($this->__('The cart has been shared.'));
            $this->_redirect('checkout/cart');

            return;
            
        } catch (Exception $exc) {            
            $this->_getSession()->setSharee($this->__('Cannot share cart.'));
			Mage::logException($exc);
            $this->_redirect('checkout/cart');
            return;
        }
    }   

    
    public function getShareUrlAction()
    {        
        $emailshare = $this->getRequest()->getParam('emailshare');        
        
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($emailshare);

        try {
            //save sharecart
            $sharecart = Mage::getModel('sharecart/cart');
            if($customer->getId()){
                $data['user_id'] = $customer->getId();
            }else{
                $data['user_id'] = 0;
            }            
            $data['items'] = serialize(Mage::helper('sharecart')->getItems());
            
            $sharecart->setData($data);
            
            $sharecart->save();
            
            $url = Mage::helper('sharecart')->generateUrlShare($sharecart);
            $result = array(
                'error' => false,
                'message' => Mage::helper('sharecart')->__('Link to share to your friend:') . '<br/>' . $url
                );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
            
        } catch (Exception $exc) {            
            $result = array(
                'error' => true,
                'message' => Mage::helper('sharecart')->__('Cannot share cart!')
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }
    }    

    
    /**
     * Add cart to cart of friend
     */
    public function cartAction()
    {

        $crypt = $this->getRequest()->getParam('crypt');
        
        if (!strlen((string) $crypt)) {
            $this->_redirect('checkout/cart');
            return;
        }        

        $shareCart = Mage::helper('sharecart')->getShareCartByCode($crypt);
        
        if (!$shareCart->getId()) {
            $this->_redirect('checkout/cart');
            return;
        }
        
        try {
            $customerSession = $this->_getCustomerSession();
            
            $cart   = $this->_getCart();
            $items = unserialize($shareCart->getItems());
            if(is_array($items)){
                foreach ($items as $item){
                    $params = array();
                    if(is_array($item['option'])){
                        $params = array_merge($params, $item['option']);                    
                    }                
                    $product = Mage::getModel('catalog/product')->load($item['product_id']);
                    if($product->getId()){
                        $params['qty'] = $item['qty'];
                        $cart->addProduct($product, $params);
                        if (!empty($params['related_product'])) {
                            $cart->addProductsByIds(explode(',', $params['related_product']));
                        }                    

                        $this->_getSession()->setCartWasUpdated(true);

                    }else{
                        continue;
                    }
                    unset($params);                
                }
            }
            $cart->save();
			Mage::getSingleton('Angara_Angaracart_Model_Cart_Observer')->checkoutCartUpdateItemsAfter('');

        }catch (Exception $e){
            $this->_getSession()->addException($exc, $this->__('Cannot add cart of your friends.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_getSession()->addSuccess($this->__('Shopping cart of your friend is added to your cart!'));
        $this->_redirect('checkout/cart');
        return;
    }
}