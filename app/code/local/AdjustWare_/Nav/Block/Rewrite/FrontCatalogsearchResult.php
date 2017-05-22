<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogsearchResult.data.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Rewrite_FrontCatalogsearchResult extends Mage_CatalogSearch_Block_Result
{
    /**
     * Retrieve Search result list HTML output, wrapped with <div>
     *
     * @return string
     */
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }
    
    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */ 
    public function setListCollection()
    {
            $this->getListBlock()
               ->setCollection($this->_getProductCollection());
        return $this;
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) 
        {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if(!$this->getResultCount())
        {
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }    
        return $html;
    }
} 