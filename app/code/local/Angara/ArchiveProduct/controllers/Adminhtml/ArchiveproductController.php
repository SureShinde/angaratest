<?php
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php';
class Angara_ArchiveProduct_Adminhtml_ArchiveProductController extends Mage_Adminhtml_Catalog_ProductController
{	
	/**
     * Delete product action
     */
    public function archiveAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $sku = $product->getSku();
            try {
                $product->archive();
                $this->_getSession()->addSuccess($this->__('The product has been archived.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }
	
	public function massArchiveAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getModel('catalog/product')->load($productId);
						/* Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product)); */
                        Mage::dispatchEvent('catalog_controller_product_archive', array('product' => $product));
                        $product->archive();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been archived.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/catalog_product/index/');
    }
}