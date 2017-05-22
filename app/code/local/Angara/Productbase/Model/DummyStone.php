<?php
class Angara_Productbase_Model_DummyStone extends Mage_Core_Model_Abstract
{
	
	//getCount
	
	
	public function getAlias(){
		# todo check if alias is fine
		return $this->getShape().$this->getName().'-'.$this->getGrade().'-'.$this->getSize();
	}
	public function getSettingAlias(){
		# todo check if alias is fine
		return $this->getSettingType().'-'.$this->getSize();
	}
	
}
