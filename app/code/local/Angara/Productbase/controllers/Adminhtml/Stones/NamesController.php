<?php
class Angara_Productbase_Adminhtml_Stones_NamesController extends Mage_Adminhtml_Controller_Action
{
	
    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('productbase/stones');
    }

    public function indexAction() { 
		$this->_initAction();
		
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones_names'));
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
			$item = Mage::getSingleton('productbase/stone_name')->load($itemId);
			
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
		 $this->_addBreadcrumb('Stone form', 'Stone form');
		 $this->getLayout()->getBlock('head')
			  ->setCanLoadExtJs(true);
		 $this->_addContent($this->getLayout()
			  ->createBlock('productbase/adminhtml_stones_names_edit'))
			  ->_addLeft($this->getLayout()
                  ->createBlock('productbase/adminhtml_stones_names_edit_tabs')
              );
		 $this->renderLayout();
    }
	
	public function deleteAction()
    {
		$itemId      = $this->getRequest()->getParam('id');
		if($itemId){
			try {
				$item = Mage::getSingleton('productbase/stone_name')->load($itemId);
				if($item->getId()){
					 $item->delete();
					 $this->_getSession()->addSuccess($this->__('The stone has been deleted.'));
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
				$stone = Mage::getModel('productbase/stone_name');
				$stone->setData($data);
				if($isEdit){
					$stone->setId($itemId);
				}
				$stone->save();
                $this->_getSession()->addSuccess($this->__('The stone has been saved.'));
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
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones_names'));
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
                    $item = Mage::getSingleton('productbase/stone_name')->load($itemId);
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

	public function massStatusAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('*/*/', array('store'=> $storeId));
    }
	
	/**
     * Validate batch of products before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $productIds
     * @param  int $status
     * @return void
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception($this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.'));
            }
        }
    }
}
