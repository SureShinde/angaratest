<?php
//error_reporting(1);
class Angara_Crossreviews_Model_Productvariation extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('crossreviews/productvariation');
    }
	public function getVariation($id)
	{
		return $this->getCollection()->getItemByColumnValue('product_id',$id);
		
	}
	public function getProductStone($id){
		$item = $this->getCollection()->getItemByColumnValue('product_id',$id);
		if($item){
			return $item->getStone();
		}
		else{
			return false;
		}
	}
}
?>