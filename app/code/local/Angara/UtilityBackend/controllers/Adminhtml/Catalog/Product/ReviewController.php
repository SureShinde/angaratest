<?php 

/**
 * @rewrite by Asheesh
 */ 
  
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'Product'.DS.'ReviewController.php');
class Angara_UtilityBackend_Adminhtml_Catalog_Product_ReviewController extends Mage_Adminhtml_Catalog_Product_ReviewController
{
	public function exportCsvAction()
	{
		$fileName   = 'review.csv';
		$grid       = $this->getLayout()->createBlock('adminhtml/review_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 
	
	public function exportExcelAction()
	{
		$fileName   = 'review.xml';
		$grid       = $this->getLayout()->createBlock('adminhtml/review_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
}
