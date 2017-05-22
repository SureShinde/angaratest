<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/View.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Catalog_Layer_View extends AdjustWare_Nav_Block_Rewrite_FrontCatalogLayerView
{
    protected $_filterBlocks = null;
    
    public function getStateInfo()
    {
        $hlp = Mage::helper('adjnav');
        
        $ajaxUrl = '';
        if ($hlp->isSearch()){
            $ajaxUrl = Mage::getUrl('adjnav/ajax/search');
        }
        elseif ($cat = $this->getLayer()->getCurrentCategory()){
            $ajaxUrl = Mage::getUrl('adjnav/ajax/category', array('id'=>$cat->getId()));
        }
        $ajaxUrl = $hlp->stripQuery($ajaxUrl);
        
        //it could be search, home or category
        $url     = $hlp->getContinueShoppingUrl();
        
        $pageKey = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
        $queryStr = $hlp->getParams(true, $pageKey);
        if ($queryStr)
            $queryStr = substr($queryStr,1);
       
	   if (false !== strpos($url, '?'))
        {
            $url = substr($url, 0, strpos($url, '?'));
        }
        $this->setClearAllUrl($hlp->getClearAllUrl($url));

        return array($url, $queryStr, $ajaxUrl);
    }

    public function bNeedClearAll()
    {
        return Mage::helper('adjnav')->bNeedClearAll();
    }
    
    protected function _prepareLayout()
    {    	
    	// Notifies Magento Booster that the Layered Navigation is loaded    	
        Mage::register('adjustware_layered_navigation_view', true, true);
    	
        // get current category ID
        
        $category = Mage::registry('current_category');
        
        if ($category)
        {
            $iCurrentCatId = $category->getId();
        }
        else 
        {
            $iCurrentCatId = null;
        }
        
        // get last cat ID
        
        $sessionObject = Mage::getSingleton('catalog/session');
        
        if ($sessionObject AND $iLastCatId = $sessionObject->getAdjnavLastCategoryId())
        {
            if ($iCurrentCatId != $iLastCatId)
            {
                Mage::register('adjnav_new_category', true);         
            }
        }
        
        $sessionObject->setAdjnavLastCategoryId($iCurrentCatId);
        
        //preload setting    
        $this->setIsRemoveLinks(Mage::getStoreConfig('design/adjnav/remove_links'));

        //blocks    
        $this->createCategoriesBlock();
        
        $filterableAttributes = $this->_getFilterableAttributes();
        
        // we rewrite this piece of code
        // to make sure price filter is applied last
        $blocks = array();        
        foreach ($filterableAttributes as $attribute)
        {
            $blockType = 'adjnav/catalog_layer_filter_attribute';
            
            if ($attribute->getFrontendInput() == 'price') 
            {
                $blockType = 'adjnav/catalog_layer_filter_price';
            }
            
            $name = $attribute->getAttributeCode() .'_filter';
            
            $blocks[$name] = $this->getLayout()->createBlock($blockType)
                ->setLayer($this->getLayer())
                ->setAttributeModel($attribute);
            
            $this->setChild($name, $blocks[$name]);
        }
        
        foreach ($blocks as $name=>$block)
        {
                $block->init();
        }
        
        $this->getLayer()->apply();
        $this->getLayer()->getProductCollection();
        return Mage_Core_Block_Template::_prepareLayout();
    }  
    
    protected function createCategoriesBlock(){
        if ('none' != Mage::getStoreConfig('design/adjnav/cat_style'))
        {
            $categoryBlock = $this->getLayout()->createBlock('adjnav/catalog_layer_filter_category')
                ->setLayer($this->getLayer())
                ->init();
            $this->setChild('category_filter', $categoryBlock);
        }
    }
    
    public function getFilters()
    {
        if (is_null($this->_filterBlocks)){
            $this->_filterBlocks = parent::getFilters();
            
            $val = Mage::getConfig()->getNode('modules/AdjustWare_Icon/active');
    	    if ((string)$val == 'true'){
    	       Mage::helper('adjicon')->addIconsToFilters($this->_filterBlocks);
    	    }            
        }       
        
        return $this->_filterBlocks;
    }
    
    protected function _toHtml(){
        $html = parent::_toHtml();  
        if (!Mage::app()->getRequest()->isXmlHttpRequest()){
            $html = '<div id="adj-nav-navigation">' . $html . '</div>';
        }
        return $html; 
    }

} 