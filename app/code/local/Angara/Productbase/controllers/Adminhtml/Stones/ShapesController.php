<?php
class Angara_Productbase_Adminhtml_Stones_ShapesController extends Mage_Adminhtml_Controller_Action
{
	
    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('productbase/shapes');
    }

    public function indexAction() { 
		$this->_initAction();
		
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones_shapes'));
		$this->renderLayout();
	}
	
	/**
     * Create new product page
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
	
	/**
     * Product edit form
     */
    public function editAction()
    {
        $itemId  = (int) $this->getRequest()->getParam('id');

        if ($itemId) {	//edit mode
			$item = Mage::getSingleton('productbase/stone_shape')->load($itemId);
			
			if ($item->getId())
            {
				 Mage::register('item_data', $item);
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Item does not exist');
                 $this->_redirect('*/*/');
            }
        }
		// new mode           
		$this->loadLayout();
		$this->_setActiveMenu('productbase/stones');
		$this->_addBreadcrumb('Productbase Manager', 'Productbase Manager');
		 $this->_addBreadcrumb('Shape form', 'Shape form');
		 $this->getLayout()->getBlock('head')
			  ->setCanLoadExtJs(true);
		 $this->_addContent($this->getLayout()
			  ->createBlock('productbase/adminhtml_stones_shapes_edit'))
			  ->_addLeft($this->getLayout()
                  ->createBlock('productbase/adminhtml_stones_shapes_edit_tabs')
              );
		 $this->renderLayout();
    }
	
	public function deleteAction()
    {
		$itemId      = $this->getRequest()->getParam('id');
		if($itemId){
			try {
				$item = Mage::getSingleton('productbase/stone_shape')->load($itemId);
				if($item->getId()){
					 $item->delete();
					 $this->_getSession()->addSuccess($this->__('The shape has been deleted.'));
				}
			}
			catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
		}
		$this->_redirect('*/*/', array('store'=>$storeId));
    }
	
	 /**
     * Save product action
     */
    public function saveAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $itemId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
				$stone = Mage::getModel('productbase/stone_shape');
				$stone->setData($data);
				if($isEdit){
					$stone->setId($itemId);
				}
				$stone->save();
                $this->_getSession()->addSuccess($this->__('The shape has been saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                    Mage::register('item_data', $data);
                $redirectBack = true;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $itemId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
    
	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones_shapes'));
        $this->renderLayout();
    }
	
	public function massDeleteAction()
    {
        $itemIds = $this->getRequest()->getParam('item');
        if (!is_array($itemIds)) {
            $this->_getSession()->addError($this->__('Please select item(s).'));
        }
        else {
            try {
                foreach ($itemIds as $itemId) {
                    $item = Mage::getSingleton('productbase/stone_shape')->load($itemId);
					$item->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($itemIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
       $this->_redirect('*/*/index');
    }
}
