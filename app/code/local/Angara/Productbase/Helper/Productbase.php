<?php
class Angara_Productbase_Helper_Productbase extends Mage_Core_Helper_Abstract {
    public static function findMetalFullName($default_metal, $option_metal){
		$gold_quality = "14k";
		if(stripos($default_metal, '14k')){
			$gold_quality = "14k";
		}else if(stripos($default_metal, '18k')){
			$gold_quality = "18k";
		}else if(stripos($default_metal, '10k')){
			$gold_quality = "10k";
		}
		
		if($option_metal == "Yellow Gold"){
			return $gold_quality." Yellow Gold";
		}else if($option_metal == "White Gold" || $option_metal == "WG"){
			return $gold_quality." White Gold";
		}else if($option_metal == "Rose Gold"){
			return $gold_quality." Rose Gold";
		}else if($option_metal == "SL"){
			return "Silver";
		}else if($option_metal == "PT"){
			return "Platinum";
		}else{
			return $option_metal;
		}	
	}
	
	public function saveCustomOption($product, $title, array $optionData, array $values = array())
	{
		Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		/*if (!$product) {
			throw new Exception('Can not find product ');
		}*/
 
		$defaultData = array(
			'type'			=> 'field',
			'is_require'	=> 0,
			'price'			=> 0,
			'price_type'	=> 'fixed',
		);
 
		$data = array_merge($defaultData, $optionData, array(
													'product_id' 	=> $product->getId(),
													'title'			=> $title,
													'values'		=> $values,
												));
 
		//$product->setHasOptions(1)->save();										
		$option = Mage::getModel('catalog/product_option')->setData($data)->setProduct($product)->save();
 
		return $option;
	}
}
