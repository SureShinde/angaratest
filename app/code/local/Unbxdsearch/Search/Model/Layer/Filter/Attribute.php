<?php
      
class Unbxdsearch_Search_Model_Layer_Filter_Attribute extends AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute
{
        /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
    	 
    	
    	$attribute = $this->getAttributeModel();    	 
    	$this->_requestVar = $attribute->getAttributeCode();
        $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('adjnav')->getCacheKey($this->_requestVar);																																																																																															
   		$data = $this->getLayer()->getAggregator()->getCacheData($key);
   		
	   		
	    	if ($data == null) {
	    		$data=array();
		    		if(Mage::registry('collector')){
		    	
		    		$filters=Mage::registry('collector')->getFacets();
		    
		    		
		    		$data=array();
		    		foreach($filters as $filter){
		    			
		    			if($filter->getName()==$attribute->getName()){
		    				
		    				$items=$filter->getItems();
		    				foreach($items as $item){
		    					
		    					$options=$attribute->getFrontend()->getSelectOptions();
		    				    foreach($options as $option){
		    				    	 				    	
		    				    	if(strtolower($option['label'])==strtolower($item->getLabel())){
		    				    			
		    				    			$value=$option['value'];
		    				    			$name=$option['label'];
		    				    			
		    				    	}
		    				    }
		    				    
		    							$data[]=array(
		    							"value"=>$value,
		    							"label"=>$name,
		    							"count"=>$item->getCount(),
		    					);
		    				}
		    				
		    				
		    			}
		    
		    		}
				
		    	       
		    		}
	    		else{
	    			
	    			$options = $attribute->getFrontend()->getSelectOptions();
	    			
	    			$optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
	    					$attribute,
	    					$this->_getBaseCollectionSql()
	    			);
	    			
	    			foreach ($options as $option) {
	    				if (is_array($option['value'])) {
	    					continue;
	    				}
	    				if (Mage::helper('core/string')->strlen($option['value'])) {
	    					// Check filter type
	    					if ($attribute->getIsFilterable() == self::OPTIONS_ONLY_WITH_RESULTS) {
	    						if (!empty($optionsCount[$option['value']])) {
	    							$data[] = array(
	    									'label' => $option['label'],
	    									'value' => $option['value'],
	    									'count' => $optionsCount[$option['value']],
	    							);
	    						}
	    					}
	    					else {
	    						$data[] = array(
	    								'label' => $option['label'],
	    								'value' => $option['value'],
	    								'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
	    						);
	    					}
	    				}
	    			}
	    			
	    		
	    			 
	    		}
	    		$currentIds = Mage::helper('adjnav')->getParam($attribute->getAttributeCode());
	    		$tags = array(
	    				Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $currentIds,
	    		);
	    		 
	    		$tags = $this->getLayer()->getStateTags($tags);
	    		$this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
	    	}
   		
	    return $data;
	 }
}
