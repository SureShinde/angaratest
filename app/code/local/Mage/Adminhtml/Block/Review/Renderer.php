<?php
class Mage_Adminhtml_Block_Review_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
		$reviewId				=	$row->getdata('review_id');
		if($reviewId!=''){
			$data	=	Mage::getModel('rating/rating')->getReviewSummary($reviewId);
			//echo '<pre>';print_r($data);die;									
			
			$votesCollection 	= 	Mage::getModel('rating/rating_option_vote')
										->getResourceCollection()
										->setReviewFilter($reviewId)
										//->setStoreFilter(Mage::app()->getStore()->getId())
										->load();
										//->load(1);die;
										
			//echo '<pre>';print_r($votesCollection->getData());die;	
			$ratingData		=	$votesCollection->getData();
			$ratingValue	=	$ratingData[0]['value'];
			//echo '<pre>';print_r($ratingValue);die;
			return $ratingValue;
		}
    }
}
