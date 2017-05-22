<?php
class Angara_Feeds_Block_Feeds extends Mage_Core_Block_Template
{
	private $_display = '0';
	
	public function _prepareLayout()	{
		return parent::_prepareLayout();
	}
    
	public function getFeeds() { 
		if (!$this->hasData('feeds')) {
			$this->setData('feeds', Mage::registry('feeds'));
		}
		return $this->getData('feeds');			
	}
	
	public function setDisplay($display){
		$this->_display = $display;
	}
	
	public function getFeedsCollection() {
		$collection = Mage::getModel('feeds/feeds')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',0);
			//->addFieldToFilter('is_home',$this->_display);
		if ($this->_display == Angara_Feeds_Helper_Data::DISP_CATEGORY){
			$current_category = Mage::registry('current_category')->getId();
			$collection->addFieldToFilter('categories',array('finset' => $current_category));
		}
		
		$current_store = Mage::app()->getStore()->getId();
		$feedss = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
			//if ($banner->getStatus())
				$feedss[] = $banner;
		}
		return $feedss;
	}	
	
	public function getDelayTime() {
		$delay = (int) Mage::getStoreConfig('feeds/settings/time_delay');
		$delay = $delay * 1000;
		return $delay;
	}
	
	public function isShowDescription(){
		return (int)Mage::getStoreConfig('feeds/settings/show_description');
	}
	
	public function getListStyle(){
		return (int)Mage::getStoreConfig('feeds/settings/list_style');
	}
	
	public function getImageWidth() {
		return (int)Mage::getStoreConfig('feeds/settings/image_width');
	}
	
	public function getImageHeight() {
		return (int)Mage::getStoreConfig('feeds/settings/image_height');
	}
}