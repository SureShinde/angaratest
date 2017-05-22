<?php 

/**
 * rewrite by Asheesh
 */
 
class Angara_UtilityBackend_Block_Rewrite_Review_Product_View extends Mage_Review_Block_Product_View
{	
	public function getCrossReviewsCollection($crossIds)
    {
		$collection = Mage::getModel('review/review')->getCollection()
					->addStoreFilter(Mage::app()->getStore()->getId())
					->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
					->addFieldToFilter('entity_id', Mage_Review_Model_Review::ENTITY_PRODUCT)
					->addFieldToFilter('entity_pk_value', array('in' => $crossIds))
					->setDateOrder()
					->addRateVotes();        
        return $collection;
    }
}
