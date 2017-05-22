<?php
abstract class Angara_BuildYourOwn_Model_Jewelry extends Mage_Core_Model_Abstract
{
	abstract public function getJewelryType();
	abstract protected function _getByoSession();
	
	abstract protected function _getByoConfiguration();
	
	public function hasDiamondPair(){
		$byoSession = $this->_getByoSession();
		return ($byoSession && !empty($byoSession['selections']['diamondPair']));
	}
	
	public function getUserStartWithPreference(){
		$byoSession = $this->_getByoSession();
		return $byoSession['userStartWithPreference'];
	}
	
	public function isDiamondPairRequired(){
		$byoConfiguration = $this->_getByoConfiguration();
		return ($byoConfiguration && !empty($byoConfiguration['isDiamondPairRequired']));
	}
	
	public function isDiamondRequired(){
		$byoConfiguration = $this->_getByoConfiguration();
		return ($byoConfiguration && !empty($byoConfiguration['isDiamondRequired']));
	}
	
	public function hasDiamondSelected(){
		return (bool)$this->getDiamondSelected();
	}	
	
	public function hasSettingSelected(){
		return is_array($this->getSettingSelected());
	}
			
	public function getDiamondSelected(){
		$byoSession = $this->_getByoSession();
		return $byoSession['selections']['diamond'];
	}
	
	public function getSettingSelected(){
		$byoSession = $this->_getByoSession();
		return $byoSession['selections']['setting'];
	}
	
	public function getUserSelections(){
		$byoSession = $this->_getByoSession();
		return $byoSession['selections'];
	}
	
	public function selectDiamond($diamondId){
		$diamond = false;
		if($diamondId){
			$diamond = Mage::getModel('buildyourown/list')->getDiamondDetails($diamondId);
		}
		$byoSession = $this->_getByoSession();
		$byoSession['selections']['diamond'] = $diamond;
		$this->_setByoSession($byoSession);
	}
	
	public function selectDiamondPair($diamondIdPairs){
		$diamondPairs = array();
		if($diamondIdPairs){
			foreach($diamondIdPairs as $diamondId){
				$diamondPairs[] = Mage::getModel('buildyourown/list')->getDiamondDetails($diamondId);
			}
		}
		else{
			$diamondPairs = false;
		}
		$byoSession = $this->_getByoSession();
		$byoSession['selections']['diamondPair'] = $diamondPairs;
		$this->_setByoSession($byoSession);
	}
	
	public function selectSetting($settingDetails){
		$byoSession = $this->_getByoSession();
		$byoSession['selections']['setting'] = $settingDetails;
		$this->_setByoSession($byoSession);
	}
	
	public function getRemoveSettingUrl(){
		return '/buildyourown/'.$this->getJewelryType().'/removeSetting';
	}
	
	public function getRemoveDiamondUrl(){
		return '/buildyourown/'.$this->getJewelryType().'/removeDiamond';
	}
	
	public function getRemoveDiamondPairUrl(){
		return '/buildyourown/'.$this->getJewelryType().'/removeDiamondPair';
	}
	
	public function getUserFilters(){
		$byoSession = $this->_getByoSession();
		return $byoSession['appliedFilters'];
	}
	
	public function setupUserPreferredCenterDiamondSize($centerDiamondSize){
		if($centerDiamondSize == 'Up to 1 Carat'){
			$byoConfiguration = $this->_getByoConfiguration();
			$this->setUserFilters(
				array(
					'sizeFrom' => $byoConfiguration['defaultFilters']['sizeFrom'],
					'sizeTo' => 1,
				)
			);			
		}
		else if($centerDiamondSize == 'Greater than 1 Carat'){
			$byoConfiguration = $this->_getByoConfiguration();
			$this->setUserFilters(
				array(
					'sizeFrom' => 1.01,
					'sizeTo' => $byoConfiguration['defaultFilters']['sizeTo'],
				)
			);
		}
	}
	
	public function setUserStartWithPreference($preference){
		$byoSession = $this->_getByoSession();
		$byoSession['userStartWithPreference'] = $preference;
		$this->_setByoSession($byoSession);
		return $this;
	}
	
	public function setUserFilters($filters){
		$byoSession = $this->_getByoSession();
		foreach($filters as $filterName => $filterValue){
			$byoSession['appliedFilters'][$filterName] = $filterValue;
		}
		$this->_setByoSession($byoSession);
		return $this;
	}
	
	// According to settings & filters selected with type
	public function getDiamondFeed($page){
		return Mage::getModel('buildyourown/list')->setFilters($this->getUserFilters())->getDiamonds($page);
	}
	
	/*public function getAvailableDiamondPairsFeed(){
		
	}*/
}