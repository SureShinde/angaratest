<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Price.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{
    private $_style;
    
    public function __construct()
    {
        parent::__construct();
        $this->_style = Mage::getStoreConfig('design/adjnav/price_style');
        $this->setTemplate('adjnav/filter_price_' . $this->_style . '.phtml');
        
        $this->_filterModelName = 'adjnav/catalog_layer_filter_price';
    }
    
    public function getVar(){
        return $this->_filter->getRequestVar();
    }
    
    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
//        if ('slider' != $this->_style && !empty($query[$this->getVar()])){
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
            )); 
        }
        return $url;
    }
    
    public function isSelected($item)
    {
        return ($item->getValueString() == $this->_filter->getActiveState());        
    }
    
    public function getSymbol()
    {
        $s = $this->getData('symbol');
        if (!$s){
            $code = Mage::app()->getStore()->getCurrentCurrencyCode();
            $s = trim(Mage::app()->getLocale()->currency($code)->getSymbol());
            
            $this->setData('symbol', $s);
        }
        return $s;
    }
} 