<?php
class Angara_Productbase_Model_Metal extends Mage_Core_Model_Abstract
{
	# @Todo get METAL_WASTAGE_CONSTANT from module config
	const METAL_WASTAGE_CONSTANT = 1.15;
    /*
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('productbase/metal');
    }
	
	public function getMetalCost($avg_metal_weight){
		
		$metal_net_weight = $avg_metal_weight * $this->getConstant();
		$metal_with_wastage = $metal_net_weight * self::METAL_WASTAGE_CONSTANT;
		$total_metal_cost = ($metal_with_wastage * $this->getPrice()) + ($metal_net_weight * $this->getLabour());
		
		return $total_metal_cost;
	}
}
