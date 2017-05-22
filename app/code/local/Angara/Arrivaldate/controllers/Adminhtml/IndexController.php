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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2012 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Angara_Arrivaldate_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
   protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('arrivaldate/set_time')
                ->_addBreadcrumb('Arrivaldate Manager','Arrivaldate Manager');
				
       return $this;
     }
      public function indexAction()
      {
			
         $this->_initAction();
         $this->renderLayout();		 
      }
      public function editAction()
      {
           $daterulesId = $this->getRequest()->getParam('id');
           $daterulesModel = Mage::getModel('arrivaldate/daterules')->load($daterulesId);
           if ($daterulesModel->getId() || $daterulesId == 0)
           {
             Mage::register('daterules_data', $daterulesModel);
             $this->loadLayout();
             $this->_setActiveMenu('arrivaldate/set_time');
             $this->_addBreadcrumb('daterules Manager', 'daterules Manager');
             $this->_addBreadcrumb('daterules Description', 'daterules Description');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()
                  ->createBlock('arrivaldate/adminhtml_daterules_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('arrivaldate/adminhtml_daterules_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Daterules does not exist');
                 $this->_redirect('*/*/');
            }
       }
       public function newAction()
       {
          $this->_forward('edit');
       }
       public function saveAction()
       {
         if ($this->getRequest()->getPost())
         {
           try {
                 $postData = $this->getRequest()->getPost();
                 $daterulesModel = Mage::getModel('arrivaldate/daterules');
               if( $this->getRequest()->getParam('id') <= 0 )
                  $daterulesModel->setCreatedTime(
                     Mage::getSingleton('core/date')
                            ->gmtDate()
                    );
                  $daterulesModel
                    ->addData($postData)
                    ->setUpdateTime(
                             Mage::getSingleton('core/date')
                             ->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                 Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully saved');
                 Mage::getSingleton('adminhtml/session')
                                ->setsynonymData(false);
                 $this->_redirect('*/*/');
                return;
          } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                                  ->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')
                 ->setsynonymData($this->getRequest()
                                    ->getPost()
                );
                $this->_redirect('*/*/edit',
                            array('id' => $this->getRequest()
                                                ->getParam('id')));
                return;
                }
              }
              $this->_redirect('*/*/');
            }
          public function deleteAction()
          {
              if($this->getRequest()->getParam('id') > 0)
              {
                try
                {
                    $daterulesModel = Mage::getModel('arrivaldate/daterules');
                    $daterulesModel->setId($this->getRequest()
                                        ->getParam('id'))
                              ->delete();
                    Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully deleted');
                    $this->_redirect('*/*/');
                 }
                 catch (Exception $e)
                  {
                           Mage::getSingleton('adminhtml/session')
                                ->addError($e->getMessage());
                           $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                  }
             }
            $this->_redirect('*/*/');
       }
      
      
}
?>
    