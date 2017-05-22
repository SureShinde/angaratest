<?php
class Angara_Productbase_Adminhtml_ProductsController extends Mage_Adminhtml_Controller_Action
{
	
    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('productbase/products');
    }

    public function indexAction() { 
		$this->_initAction();
		
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_products'));
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
        $productId  = (int) $this->getRequest()->getParam('id');

        if ($productId) {	//edit mode
			$product = Mage::getSingleton('catalog/product')->load($productId);
			$angara_product = Mage::getModel('productbase/product')->getCollection()->getItemByColumnValue('sku',$product->getSku());
			
			if ($angara_product->getId())
            {
				 Mage::register('product_data', $angara_product);
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Product does not exist');
                 $this->_redirect('*/*/');
            }
        }
		// new mode           
		$this->loadLayout();
		$this->_setActiveMenu('productbase/products');
		$this->_addBreadcrumb('Productbase Manager', 'Productbase Manager');
		 $this->_addBreadcrumb('Product pricing form', 'Product pricing form');
		 $this->getLayout()->getBlock('head')
			  ->setCanLoadExtJs(true);
		 $this->_addContent($this->getLayout()
			  ->createBlock('productbase/adminhtml_products_edit'))
			  ->_addLeft($this->getLayout()
                  ->createBlock('productbase/adminhtml_products_edit_tabs')
              );
		 $this->renderLayout();
    }
	
	 /**
     * Save product action
     */
    public function saveAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
				Mage::getModel('productbase/products')->save($data);
                $this->_getSession()->addSuccess($this->__('The product pricing data has been saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                    Mage::register('product_data', $data);
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
                'id'    => $productId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
	

    public function resetpricesAction(){
		try{
			$errors = Mage::getModel('productbase/products')->resetPricing();
			if(count($errors)>0){
				$this->_getSession()->addError($this->__('Reset pricing execution may have stopped.'));
				foreach($errors as $error){
					$this->_getSession()->addError($error);
				}
			}else{
				$this->_getSession()->addSuccess($this->__('Prices were updated successfully. Some products may have been disabled for which automatic pricing is invalid.'));
			}
		}
		catch (Exception $e)
		{
			Mage::logException($e);
			$this->_getSession()->addError($e->getMessage());
			//echo $e->getMessage();
		}
		$this->_redirect('*/*/index');
	}
    
	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_products'));
        $this->renderLayout();
    }
	
	public function massPricingAction()
    {
		$errors = 0;
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        }
        else {
			foreach ($productIds as $productId) {
				try {
					
						$product = Mage::getModel('catalog/product')->load($productId);
						Mage::getModel('productbase/products')->resetProduct($product);
					
				} catch (Exception $e) {
					$errors++;
					$this->_getSession()->addError($e->getMessage());
				}
			}
			if($errors==0){
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($productIds))
                );
			}
        }
       $this->_redirect('*/*/index');
    }
	
	public function massDebugAction()
    {
		$errors = 0;
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        }
        else {
			foreach ($productIds as $productId) {
				try {
						$product = Mage::getSingleton('catalog/product')->load($productId);
						Mage::getModel('productbase/products')->resetProduct($product, true);
					
				} catch (Exception $e) {
					$errors++;
					$this->_getSession()->addError($e->getMessage());
				}
			}
			if($errors==0){
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been checked.', count($productIds))
                );
			}
        }
       //$this->_redirect('*/*/index');
    }
	
	public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')->load($productId);
					Mage::getModel('productbase/products')->deleteProduct($product->getSku());
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($productIds))
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

    //public function massStatusAction()
//    {
//        $productbaseIds = $this->getRequest()->getParam('productbase');
//        if(!is_array($productbaseIds))
//        {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
//        }
//        else
//            try
//            {
//                foreach ($productbaseIds as $productbaseId)
//                {
//                    $productbase = Mage::getSingleton('productbase/rule')
//                        ->load($productbaseId)
//                        ->setIsActive($this->getRequest()->getParam('status'))
//                        ->save();
//                }
//                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($productbaseIds)));
//            }
//            catch (Exception $e)
//            {
//                Mage::logException($e);
//                $this->_getSession()->addError($e->getMessage());
//            }
//        $this->_redirect('*/*/index');
//    }

    /*public function exportCsvAction()
    {
        $fileName   = 'productbase.csv';
        $content    = $this->getLayout()->createBlock('productbase/adminhtml_productbase_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'productbase.xml';
        $content    = $this->getLayout()->createBlock('productbase/adminhtml_productbase_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function categoriesAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productbase/adminhtml_rule_edit_tab_categories')->toHtml()
        );
    }

    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productbase/adminhtml_rule_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('productbase/items');
    }*/
}
