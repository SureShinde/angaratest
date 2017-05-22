<?php 
class Angara_Promotions_Block_Offer extends Mage_Core_Block_Template 
{
    public function _prepareLayout(){
        return parent::_prepareLayout();
    }
    
    public function getApplicableCoupons($platform){
		// return all special coupons based on channel
		return Mage::getModel('promotions/offer')->getApplicableCoupons($platform);
	}
	
	public function getCouponHtml($coupon){
		$couponHtml = $this->getChild('promotion_coupon')->setCacheLifetime(NULL)->setCoupon($coupon);
		return $couponHtml->toHtml();
	}	
	
	/*
		S:VA
		Get the offer image according to the channel url params
	*/
	public function getOfferHeaderImage(){
		//$channel			=	Mage::getSingleton('core/session')->getVisitorChannel();
		$channel			=	Mage::getModel('promotions/offer')->getApplicableChannel();
		if(isset($channel)){
			$isMobile	=	Mage::Helper('function')->isMobile();
			$offerBannerDesktop	=	$channel->getData('offer_banner_desktop');
			if($isMobile){
				$offerBannerDesktop	=	$channel->getData('offer_banner_mobile');
			}
			$bannerData			=	Mage::getModel('promotions/banner')->load($offerBannerDesktop);
			//$name				=	$bannerData->getData('name');
			//$description		=	$bannerData->getData('description');
			$image_path			=	$bannerData->getData('image_path');
			$image_title		=	$bannerData->getData('image_title');
			$image_alt_text		=	$bannerData->getData('image_alt_text');
			//$html_content		=	$bannerData->getData('html_content');
			$offerData			=	array('image_path'=>Mage::getBaseUrl('media').$image_path, 'image_title'=>$image_title, 'image_alt_text'=>$image_alt_text);
			if(isset($image_path)){
				return $offerData;	
			}
		}
	}
}