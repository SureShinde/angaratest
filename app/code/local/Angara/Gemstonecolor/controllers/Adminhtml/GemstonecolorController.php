<?php
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php';
class Angara_Gemstonecolor_Adminhtml_GemstonecolorController extends Mage_Adminhtml_Catalog_ProductController
{
	
	/**
	 * Get gemstonecolor products grid and serializer block
	 */
	public function gemstonecolorAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('adminhtml.catalog.product.edit.tab.gemstonecolor')
			->setProductsGemstonecolor($this->getRequest()->getPost('products_gemstonecolor', null));
		$this->renderLayout();
	}
	
	/**
	 * Get gemstonecolor products grid
	 */
	public function gemstonecolorGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('adminhtml.catalog.product.edit.tab.gemstonecolor')
			->setProductsGemstonecolor($this->getRequest()->getPost('products_gemstonecolor', null));
		$this->renderLayout();
	}
	
	public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('gemstonecolor/adminhtml_system_config_frontend_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

}
