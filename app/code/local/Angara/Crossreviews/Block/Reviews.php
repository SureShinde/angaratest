<?php

class Angara_Crossreviews_Block_Reviews extends Mage_Core_Block_Template
{
	
	public function getCrossReviews($product_id)
	{
		$variation = Mage::getModel('crossreviews/productvariation')->getVariation($product_id);
		if($variation){
			$mastersku_id = $variation->getMasterskuId();
			if($mastersku_id){
				$reviews = Mage::getModel('crossreviews/reviewdetail')->getReviewsByMasterSkuId($mastersku_id);
				$extraReviews = array();
				foreach($reviews as $review){
					if($review->getStone() != $variation->getStone()){
						$extraReviews[] = $review;
					}
				}
				return $extraReviews;
			}
		}
		return false;
	}
	
	public function getStoneReviews($product_id, $stone, $quality)
	{
		$reviews = Mage::getModel('crossreviews/reviewdetail')->getStoneReviews($product_id, $stone, $quality);
		return $reviews;
	}
}