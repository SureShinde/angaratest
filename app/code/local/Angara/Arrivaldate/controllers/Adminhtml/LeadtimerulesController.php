<?php

class Angara_Arrivaldate_Adminhtml_LeadtimerulesController extends Mage_Adminhtml_Controller_Action
{
  
     protected function _initAction()
    {
		
        $this->loadLayout()->_setActiveMenu('arrivaldate/fix_time')
                ->_addBreadcrumb('Leadtime Manager','Leadtime Manager');				
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
           $daterulesModel = Mage::getModel('arrivaldate/leadtimerules')->load($daterulesId);
           if ($daterulesModel->getId() || $daterulesId == 0)
           {
             Mage::register('leadtimerules_data', $daterulesModel);
             $this->loadLayout();
             $this->_setActiveMenu('arrivaldate/fix_time');
             $this->_addBreadcrumb('leadtimerules Manager', 'leadtimerules Manager');
             $this->_addBreadcrumb('leadtimerules Description', 'leadtimerules Description');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()
                  ->createBlock('arrivaldate/adminhtml_leadtimerules_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('arrivaldate/adminhtml_leadtimerules_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Leadtime rules does not exist');
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
                 $daterulesModel = Mage::getModel('arrivaldate/leadtimerules');
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
                    $daterulesModel = Mage::getModel('arrivaldate/leadtimerules');
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
    