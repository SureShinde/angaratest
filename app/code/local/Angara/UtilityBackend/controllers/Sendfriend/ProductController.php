<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Email to a Friend Product Controller
 *
 * @category    Mage
 * @package     Mage_Sedfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once(Mage::getModuleDir('controllers','Mage_Sendfriend').DS.'ProductController.php');
class Angara_UtilityBackend_Sendfriend_ProductController extends Mage_Sendfriend_ProductController
{
    /**
     * Show Send to a Friend Form
     *
     */
    public function sendAction()
    {
        $product    = $this->_initProduct();
        //$model      = $this->_initSendToFriendModel();

        if (!$product) {
            $this->_forward('noRoute');
            return;
        }

        /*if ($model->getMaxSendsToFriend() && $model->isExceedLimit()) {
            Mage::getSingleton('catalog/session')->addNotice(
                $this->__('The messages cannot be sent more than %d times in an hour', $model->getMaxSendsToFriend())
            );
        }*/

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        Mage::dispatchEvent('sendfriend_product', array('product' => $product));
        $data = Mage::getSingleton('catalog/session')->getSendfriendFormData();
        if ($data) {
            Mage::getSingleton('catalog/session')->setSendfriendFormData(true);
            $block = $this->getLayout()->getBlock('sendfriend.send');
            if ($block) {
                $block->setFormData($data);
            }
        }

        $this->renderLayout();
    }
	
	/**
     * Send Email Post Action
     *
     */
    public function sendmailAction()
    {
		if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/send', array('_current' => true));
        }
		
        $product    = $this->_initProduct();
        $model      = $this->_initSendToFriendModel();
        $data       = $this->getRequest()->getPost();

        if (!$product || !$data) {
            $this->_forward('noRoute');
            return;
        }

        $categoryId = $this->getRequest()->getParam('cat_id', null);
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')
                ->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
        }
		
        $model->setSender($this->getRequest()->getPost('sender'));
        $model->setRecipients($this->getRequest()->getPost('recipients'));
        $model->setProduct($product);
				
		try {
            $validate = $model->validate();
			if ($validate === true) {
                $model->send();
                /*Mage::getSingleton('catalog/session')->addSuccess($this->__('The link to a friend was sent.'));
                $this->_redirectSuccess($product->getProductUrl());
                return;*/
				echo $this->__('Email sent successfully.');
            }
            else {
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        //Mage::getSingleton('catalog/session')->addError($errorMessage);
						echo $errorMessage;
                    }
                }
                else {
                   //Mage::getSingleton('catalog/session')->addError($this->__('There were some problems with the data.'));
				   echo $this->__('There were some problems with the data.');
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalog/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('catalog/session')
                ->addException($e, $this->__('Some emails were not sent.'));
        }

        // save form data
        Mage::getSingleton('catalog/session')->setSendfriendFormData($data);

        //$this->_redirectError(Mage::getURL('*/*/send', array('_current' => true)));
    }
} ?>