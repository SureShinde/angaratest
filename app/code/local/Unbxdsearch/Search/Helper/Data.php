<?php
class Unbxdsearch_Search_Helper_Data extends Mage_CatalogSearch_Helper_Data
{
    public function getFilter()
    {
  	return $this->_getRequest()->getParam("filter");
	}
	
	public function getCategory()
	{
		return $this->_getRequest()->getParam("cat");	
	}
public function getFacetList(){
		
		
		$facetlist=array();
		$filterableAttributes=Mage::getSingleton('catalog/layer')->getFilterableAttributes();
		
		$alist=array("weight","metal_type","metal",'jewelry_type','emb_quality_grade2','de_style','de_stone_shape','de_stone_type');
		foreach ($filterableAttributes as $attribute) {
			
		if(in_array( $attribute->getName(),$alist)){
				if(array_key_exists($attribute->getAttributeCode(),$this->_getRequest()->getParams())){
		
						$facetlist[$attribute->getName()]=$this->_getRequest()->getParams($attribute->getAttributeCode());
				}
			}
		}
		
		return $facetlist;
		
	}
	public function getRequestVar($requestvar){
		return $this->_getRequest()->getParam($requestvar);
	}
	
}

?>
