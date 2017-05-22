<?php 

/**
 * rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Block_Rewrite_Review_Product_View_List extends Mage_Review_Block_Product_View_List
{
	public function getCrossReviewIds()
    {  
      $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$sql        = 'select similar_style_id, stone from angara_crossreviews where product_id ='.$this->getProductId();
					$rows       = $connection->fetchAll($sql);
					 $crossIds = array();
					 $Ids = array();
					
					foreach($rows as $row)
					{						
						$Ids[] = $row['similar_style_id'];
						$crossIds[$row['similar_style_id']] = $row['stone'];						
					}				
				$crossIds['id'] = 	$Ids;
        return $crossIds;
    }
	
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
