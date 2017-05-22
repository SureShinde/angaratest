<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Homepage.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php  
// wrapper for product list on home page
class AdjustWare_Nav_Block_Homepage extends AdjustWare_Nav_Block_List
{
    protected function _prepareLayout()
    {
        $staticBlock = $this->getLayout()
            ->createBlock('cms/block', 'adj_nav_homepage')
            ->setBlockId('adj_nav_homepage');
        if ($staticBlock){
            $this->insert($staticBlock);
        }
        
        $productsBlock = $this->getLayout()
            ->createBlock('catalog/product_list', 'product_list')
            //->setColumnsCount(4)  
            ->setTemplate('catalog/product/list.phtml');
        //@todo  check gift registry compatibility     
        if ($productsBlock)
            $this->insert($productsBlock);

        return parent::_prepareLayout();
    } 
    
    protected function _toHtml()
    {
        $hlp = Mage::helper('adjnav');
        
        $html = $this->getChildHtml('adj_nav_homepage');
        if ($html && !$hlp->getParams()){
            $html = $hlp->wrapHomepage($html);
        }
        else{
            $html = parent::_toHtml();
        }
        
        return $html;
    }    
    
 