<?php

/**
 * Out of Stock Subscription index controller
 *
 * @category    Angara
 * @package     Angara_OutofStockSubscription
 * @author      Asheesh Singh
 */
class Angara_OutofStockSubscription_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{ 
		$productId = $this->getRequest()->getPost('product');
		$email = $this->getRequest()->getPost('subscription_email');
		if ($email && $productId) {
			Mage::getModel('outofstocksubscription/info')->saveSubscrition($productId, $email);
			if($this->getRequest()->isAjax()) {
				$data['success'] = 1;
				$data['message'] = $this->__('Thank you. We will notify once the product is available.');
				Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($data));
				Mage::app()->getFrontController()->getResponse()->sendResponse();
				die;
			} else {
				$this->_getSession()->addSuccess($this->__('Thank you. We will notify once the product is available.'));		
				$product = Mage::getModel('catalog/product')->load($productId);
				$url = $product->getData('url_path');
				$this->_redirect($url);
			}
		}
		else {
			if($this->getRequest()->isAjax()) {
				$data['success'] = 0;
				$data['message'] = $this->__('Please enter a valid email address.');
				Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($data));
				Mage::app()->getFrontController()->getResponse()->sendResponse();
				die;
			} else {
				$this->_redirect('');
			}
		}		
	}
	
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}