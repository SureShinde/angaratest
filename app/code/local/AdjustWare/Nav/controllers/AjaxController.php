<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/controllers/AjaxController.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function categoryAction()
    {
        // init category
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $this->_forward('noRoute'); 
            return;
        }
		
		if($categoryId == 470){	// build your own ring category hardcoded for faster performance
			$byoJewelryModel = Mage::getModel('buildyourown/jewelry_ring');
			Mage::register('byoJewelryModel', $byoJewelryModel);
		}
		else if($categoryId == 469){	// build your own pendant category hardcoded for faster performance
			$byoJewelryModel = Mage::getModel('buildyourown/jewelry_pendant');
			Mage::register('byoJewelryModel', $byoJewelryModel);
		}

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        Mage::register('current_category', $category);
        
        try
		{
			if ($category->getId() && $category->getCustomDesign()) {
    			Mage::getModel('catalog/design')->applyDesign($category, Mage_Catalog_Model_Design::APPLY_FOR_CATEGORY);
			}
			
			$this->loadLayout();
		}
		catch (Varien_Exception $e)
		{
			if ((NULL !== strpos($e->getMessage(), 'addColumnCountLayoutDepend')) && version_compare(Mage::getVersion(), '1.3.2', '<'))
			{
				// We shouldn`t do anything if method Mage_Catalog_Block_Product_Abstract::addColumnCountLayoutDepend
				// is called. It doesn`t exist in magento version lower than 1.3.2.
			}
			else
			{
				throw $e;				
			}
		}
        
        $response = array();
		$noFilters = (int) $this->getRequest()->getParam('noFilters', false);
		if(!$noFilters)
        	$response['layer']    = $this->getLayout()->getBlock('layer')->toHtml();
        $response['products'] = $this->getLayout()->getBlock('root')->toHtml();  
        
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }
    
    public function searchAction()
    {
        $this->loadLayout();
        
        $response = array();
        $response['layer']    = $this->getLayout()->getBlock('layer')->toHtml();
        $response['products'] = $this->getLayout()->getBlock('root')->setIsSearchMode()->toHtml();  
        
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
        
    }
    
} 