<?php
class Angara_Crossreviews_Model_Reviewdetail extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('crossreviews/reviewdetail');
    }
	public function getReviewsByMasterSkuId($value)
	{
		return $this->getCollection()->getItemsByColumnValue('mastersku_id',$value);
		
	}
	
	public function getStoneReviews($product_id, $stone, $quality){
		return $this->getCollection()
			->addFieldToFilter('stone',$stone)
			->addFieldToFilter('quality',$quality)
			->addFieldToFilter('entity_pk_value', array('neq' => $product_id));
	}
	
}
?>