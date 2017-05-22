<?php

class Angara_Promotions_Model_Banner extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("promotions/banner");

    }
	
	public function getBanners($activeOnly = false){
		$banners = $this->getCollection();
		
		if($activeOnly)
			$banners->addFieldToFilter('status', 1);
		
		$banners->setOrder('updated_at','DESC');
		if(count($banners)){
			return $banners;
		}
		return false;
	}
	
	public function getHtml(){
		$html = '<img id="bannerImage'.$this->getId().'" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$this->getImagePath().'" usemap="#bannerImageMap'.$this->getId().'" class="center-block"  alt="'.$this->getImageAltText().'" title="'.$this->getImageTitle().'"/>';
		if($this->hasTicker()){
			$html .= '<div class="container relative-placeholder"><div class="row"><div class="col-xs-12"><div class="countdownticker2" id="bannerTicker'.$this->getId().'"></div></div></div></div>';
		}
		$html .= str_replace('{{ID}}',$this->getId(),$this->getHtmlContent());
		return $html;
	}
}
	 