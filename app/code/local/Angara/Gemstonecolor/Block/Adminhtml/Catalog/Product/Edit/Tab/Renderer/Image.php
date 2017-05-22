<?php 
class Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row){
		if($row->getId()!=''){
			$image = $row->getImage(); 
			$productMediaConfig = Mage::getModel('catalog/product_media_config');			
			$url = $productMediaConfig->getMediaUrl($image);
			return '<img width="100" src="'. $url . '" title="'.basename($image).'" >';
		} else {
    		 return '';
		} 
	}
}