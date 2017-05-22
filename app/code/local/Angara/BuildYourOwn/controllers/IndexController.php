<?php
class Angara_BuildYourOwn_IndexController extends Mage_Core_Controller_Front_Action
{
	public function diamondDetailsAction() {
		$id = (int)$this->getRequest()->getParam('diamondId');
		if($id){
			$resultDiamond = Mage::getModel('buildyourown/list')->getDiamondDetails($id);
			if($resultDiamond && isset($resultDiamond['diamond'])){
				$resultDiamond['diamond']['current_price'] = Mage::helper('core')->currency($resultDiamond['diamond']['total_sales_price_in_currency'],true,false);
				echo json_encode($resultDiamond['diamond']);
			}
		}
	}
	
	public function selectSettingAction(){
		$settingParams = $this->getRequest()->getParams();
		$byoJewelryType = $this->getRequest()->getParam('byoJewelryType');
		$centerDiamondSize = $this->getRequest()->getParam('centerDiamondSize');
		//if($byoJewelryType){
			$byoModel = Mage::getModel('buildyourown/jewelry_'.$byoJewelryType);
			if($byoModel){
				$byoModel->selectSetting($settingParams);
				$byoModel->setupUserPreferredCenterDiamondSize($centerDiamondSize);
			}
		//}
		$this->_forward('index',$byoJewelryType);
	}
	
}