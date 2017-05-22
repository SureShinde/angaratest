<?php

class Angara_Digger_Adminhtml_GrammarController extends Mage_Adminhtml_Controller_Action
{
   protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('digger/fix_time')
                ->_addBreadcrumb('grammar Manager','grammar Manager');
       return $this;
     }
      public function indexAction()
      {
         $this->_initAction();
         $this->renderLayout();
      }
      public function editAction()
      {
           $grammarId = $this->getRequest()->getParam('id');
           $grammarModel = Mage::getModel('digger/grammar')->load($grammarId);
           if ($grammarModel->getId() || $grammarId == 0)
           {
             Mage::register('grammar_data', $grammarModel);
             $this->loadLayout();
             $this->_setActiveMenu('digger/fix_time');
             $this->_addBreadcrumb('grammar Manager', 'grammar Manager');
             $this->_addBreadcrumb('grammar Description', 'grammar Description');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()
                  ->createBlock('digger/adminhtml_grammar_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('digger/adminhtml_grammar_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Rule does not exist');
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
                 $grammarModel = Mage::getModel('digger/grammar');
               if( $this->getRequest()->getParam('id') <= 0 )
                  $grammarModel->setCreatedTime(
                     Mage::getSingleton('core/date')
                            ->gmtDate()
                    );
                  $grammarModel
                    ->addData($postData)
                    ->setUpdateTime(
                             Mage::getSingleton('core/date')
                             ->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                 Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully saved');
                 Mage::getSingleton('adminhtml/session')
                                ->setgrammarData(false);
                 $this->_redirect('*/*/');
                return;
          } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                                  ->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')
                 ->setgrammarData($this->getRequest()
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
                    $grammarModel = Mage::getModel('digger/grammar');
                    $grammarModel->setId($this->getRequest()
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
    