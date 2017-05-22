<?php
      
class Unbxdsearch_Search_Model_Layerfilterattribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
        /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
    	 
    	error_log("asdsd");
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
	    			$data=parent::_getItemsData();
	    		}
	    	}
   		
	    return $data;
	 }
}
