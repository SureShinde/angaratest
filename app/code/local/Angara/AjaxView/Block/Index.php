<?php   
//class Angara_AjaxView_Block_Index extends Mage_Core_Block_Template{   
class Angara_AjaxView_Block_Index extends Mage_Catalog_Block_Product_Abstract{   

	//	Creating a function here can be directly used on phtml files 
	function getRelatedProductsIds(){
		return Mage::registry('related_products');
	}
}