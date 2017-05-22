<?php 

/**
 * @rewrite by Asheesh
 */ 
  
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php');
class Angara_UtilityBackend_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
	
	/**
	 * Get matchingband products grid and serializer block
	 */
	public function matchingbandAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.matchingband')
			->setProductsMatchingband($this->getRequest()->getPost('products_matchingband', null));
		$this->renderLayout();
	}
	
	/**
	 * Get matchingband products grid
	 */
	public function matchingbandGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.matchingband')
			->setProductsRelated($this->getRequest()->getPost('products_matchingband', null));
		$this->renderLayout();
	}


    /**
     * Initialize product before saving
	 * @modifyBy Asheesh
     */
    protected function _initProductSave()
    {
		$links = $this->getRequest()->getPost('links');
		$product = parent::_initProductSave();
		// added by Hitesh for matching bands
		if (isset($links['matchingband']) && !$product->getMatchingbandReadonly()) {
			$product->setMatchingbandLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['matchingband']));
		}
        return $product;
    }
}
