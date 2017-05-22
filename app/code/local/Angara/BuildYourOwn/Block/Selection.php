<?php   
class Angara_BuildYourOwn_Block_Selection extends Mage_Core_Block_Template
{
	private $_jewelryModel;
	public function getJewelryModel(){
		if(!$this->_jewelryModel){
			$this->_jewelryModel = Mage::registry('byoJewelryModel');
		}
		return $this->_jewelryModel;
	}
	
	public function getDiamond(){
		return $this->getJewelryModel()->getDiamondSelected();
	}
	
	public function getDiamondPair(){
		return $this->getJewelryModel()->getDiamondPairSelected();
	}
	
	public function getSetting(){
		$settingDetails = $this->getJewelryModel()->getSettingSelected();
		if($settingDetails){
			$productId = $settingDetails['simpleProductId'];
			$product = Mage::getModel('catalog/product')->load($productId);
			if($product->getId()){
				return $product;
			}
		}
		return false;
	}
}







