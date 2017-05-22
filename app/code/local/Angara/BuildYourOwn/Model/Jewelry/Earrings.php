<?php
class Angara_BuildYourOwn_Model_Jewelry_Earrings extends Angara_BuildYourOwn_Model_Jewelry
{	
	public function getJewelryType(){
		return 'earrings';
	}
	
	protected function _getByoSession(){
		$byoSession = Mage::getSingleton('core/session')->getByoEarringSession();
		if(!$byoSession){
			$byoConfiguration = $this->_getByoConfiguration();
			$byoSession = array(
				'userStartWithPreference' => 'diamond',
				'appliedFilters' => $byoConfiguration['defaultFilters'],
				'selections' => array(
					'diamond' => false,
					'diamondPair' => false,
					'setting' => false
				)
			);
			$this->_setByoSession($byoSession);
		}
		return $byoSession;
	}
	
	protected function _setByoSession($byoSession){
		Mage::getSingleton('core/session')->setByoEarringSession($byoSession);
	}
	
	protected function _getByoConfiguration(){
		return array(
			'isDiamondRequired' => true,
			'isDiamondPairRequired' => false,
			'defaultFilters' => array(
				'shapes' => '"Round", "Pear", "Princess", "Marquise", "Oval", "Radiant", "Emerald", "Heart", "Cushion", "Asscher"',
				'sizeFrom' => 0.2,
				'sizeTo' => 15.30,
				'priceFrom' => 100,
				'priceTo' => 10000000,
				'cutFrom' => 'Good',
				'cutTo' => 'Excellent',
				'depthPercentFrom' => 0,
				'depthPercentTo' => 100,
				'tablePercentFrom' => 0,
				'tablePercentTo' => 100,
				'polishFrom' => 'Good', 
				'polishTo' => 'Excellent',
				'symmetryFrom' => 'Good',
				'symmetryTo' => 'Excellent',
				'colorFrom' => 'D',
				'colorTo' => 'J',
				'clarityFrom' => 'IF',
				'clarityTo' => 'SI2',
				'sortBy' => 'Shape',
				'sortDir' => 'Asc'
			)
		);
	}
}