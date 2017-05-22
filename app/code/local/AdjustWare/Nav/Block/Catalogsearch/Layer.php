<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalogsearch/Layer.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Catalogsearch_Layer extends AdjustWare_Nav_Block_Catalog_Layer_View
{

    public function getLayer()
    {
        return Mage::getSingleton('catalogsearch/layer');
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $availableResCount = (int) Mage::app()->getStore()
            ->getConfig(Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT );

        if (!$availableResCount
            || ($availableResCount>=$this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }
    
    
    protected function createCategoriesBlock(){
        $categoryBlock = $this->getLayout()
            ->createBlock('adjnav/catalog_layer_filter_categorysearch')
            ->setLayer($this->getLayer())
            ->init();
        $this->setChild('category_filter', $categoryBlock);
    }
} 