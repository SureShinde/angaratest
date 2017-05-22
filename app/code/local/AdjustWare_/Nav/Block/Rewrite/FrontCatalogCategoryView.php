<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogCategoryView.data.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Rewrite_FrontCatalogCategoryView extends Mage_Catalog_Block_Category_View
{
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        if ($this->getCurrentCategory()->getIsAnchor()){
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }
        return $html;
    }   

    public function getCmsBlockHtml()
    {
        if (parent::isContentMode())
        {
            return Mage::helper('adjnav')->wrapProducts(parent::getCmsBlockHtml());
        } 
        return parent::getCmsBlockHtml();
    }
} 