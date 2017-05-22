<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Attribute.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter_attribute.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_attribute';
    }
    
    public function getVar(){
        return $this->_filter->getRequestVar();
    }
    
    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
             )); 
        }
        
        return $url;
    }
    
    public function getHtmlId($item)
    {
        return $this->getVar() . '-' . $item->getValueString();        
    }
    
    public function isSelected($item)
    {
        $ids = (array)$this->_filter->getActiveState();
        return in_array($item->getValueString(), $ids);        
    }
    
    public function getItemsArray()
    {   
        $items = array();
		
		//$_items = ($this->getVar() == 'filterable_metal_types' || $this->getVar() == 'filterable_stone_grades' || $this->getVar() == 'filterable_carat_weight_ranges' || $this->getVar() == 'mother_stone_count') ? $this->getItems() : $this->getItemsSortedByCountDesc();
		
		//	S:VA
		$_items = ( ($this->getVar() == 'filterable_stone_names') ? $this->getItemsSortedByCountDesc() : $this->getItems() );
		//	E:VA
				
        foreach ($_items as $_item)
        {
			if($_item->getCount() > 0){
				$htmlParams = 'id="' . $this->getHtmlId($_item) . '" ';
				$href = $this->htmlEscape($currentUrl = Mage::app()->getRequest()->getBaseUrl());
				$href .= $this->getRequestPath();
				$params = Mage::helper('adjnav')->getParams();
				if (isset($params[$this->getVar()]) && $this->getVar() != 'filterable_metal_types'){
					$values = explode('-', $params[$this->getVar()]);
					if(($key = array_search($_item->getValueString(), $values)) !== false){
						unset($values[$key]);
					}
					else{
						$values[] = $_item->getValueString();
					}
					$params[$this->getVar()] = implode('-', array_unique($values));	            	
				}
				else{
					if($params[$this->getVar()] == $_item->getValueString()){
						$params[$this->getVar()] = '';
					}
					else{
						$params[$this->getVar()] = $_item->getValueString();
					}
				}
				
				if ($params = http_build_query($params))
				{
					$href .= '?' . $params;
				}		
				/*if(strpos($href,'order')===false){
					$href = $href."&order=position&dir=asc";
				}*/
							
				$htmlParams .= 'href="' . $href . '" ';
					
				
				$htmlParams .= 'class="adj-nav-attribute ' 
							. ($this->isSelected($_item) ? 'adj-nav-attribute-selected ' : '') . '" ';
				
				$icon = '<div class="fis-'.$this->getVar().' fi-'.$this->getVar().'-'.strtolower( preg_replace('/\W/ ','-',$this->htmlEscape($_item->getLabel()))).' catalog-fi"></div>';
				/*$icon = '<div class="catalog-filter-icon filter-icon-'.$this->getVar().'-'.strtolower( str_ireplace(' ','-',$this->htmlEscape($_item->getLabel()))).'-64"></div>';*/
				
				$label = $this->htmlEscape($_item->getLabel());
				
				if($this->getName() == 'Stone Count') {
					if($label == '1') {
						$label = $label . " Stone";
					}
					else {
						$label = $label . " Stones";
					}
				}
			
$items[] = '<div class="padding-type-2 pull-left catalog-filter-item"><div class="'. ($this->isSelected($_item) ? 'padding-type-3 showcase-border-thick border-active active':'padding-type-5').'"><a onclick="categoryNavFilterSelect(\''.$this->getName().' | '.$_item->getLabel().'\');return false;" '.$htmlParams.'>'.$icon.$label.' <small class="text-light">(' .  $_item->getCount() .')</small></a></div></div>';


			}
        }
        
        return $items;
    }
    
    /**
     * Will return GET part of the request
     *
     *	@return string
     */    
    public function getRequestPath()
    {
    	$request = Mage::app()->getRequest();
    	
    	$requestPath = '';
		
    	if ($request->isXmlHttpRequest()){
    		$requestPath = Mage::getSingleton('core/session')->getRequestPath();
			//	S:VA
			if( rtrim(Mage::getBaseUrl(), '/').$requestPath !=  Mage::app()->getRequest()->getServer('HTTP_REFERER') ){
				$requestPath = '';
			}else{
    			$requestPath = Mage::getSingleton('core/session')->getRequestPath();
			}
			//	E:VA
    	}else{
    		Mage::getSingleton('core/session')->setRequestPath($requestPath = $request->getRequestString());
    	}
    	
    	return $this->htmlEscape($requestPath);
    }
} 
