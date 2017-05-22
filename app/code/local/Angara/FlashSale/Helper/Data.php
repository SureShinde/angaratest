<?php
class Angara_FlashSale_Helper_Data extends Mage_Core_Helper_Abstract{
	
	/*
		Check if flash sale is enabled
	*/
	public function getFlashSaleStatus(){
		$flashSaleProductData	=	$this->getFlashSaleProducts();
		if(count($flashSaleProductData)>0){
			return Mage::getStoreConfig('angarainfo/flashsale_settings/enabled');		//	sections name 	group name	field name
		}else{
			return false;
		}
	}
	
	
	/*
		Static defined flash sale products
	*/
	public function getFlashSaleProducts() {
		$date 	=	$this->getCurrentServerDateTime();	
		//if ( $this->getFlashSaleStatus() ) {
			$collection 	= Mage::getModel('flashsale/flashsale')->getCollection() 
								->addFieldToSelect('*')
								->addFieldToFilter('is_active', array('eq' => '0'))
								->addFieldToFilter('from_date', array('lt' => $date))
								->addFieldToFilter('to_date', 	array('gt' => $date))
								->setOrder('flashsale_id',DESC)
								->setPageSize(1)
								->load();
								//->load(1); 
			if( $collection->count() ){
				$firstdata	=	$collection->getFirstItem()->getData(); 
				//$sku		=	$firstdata['product_id']; 
			}
		//}
		return $firstdata;
	}
	
	public function getCurrentServerDateTime() {
		return Mage::app()->getLocale()->storeDate(Mage::app()->getStore(), null, true)->toString('Y-MM-dd HH:mm:ss');	
	}
	
	/*
		@ returns string css version that will be used in head.php
	*/
	public function getCssVersion() {
        return Mage::getStoreConfig('angarainfo/utility_settings/css_cache_version');		/*	module name / group name / field name	*/
    }
	
	/*
		@ returns string js version that will be used in head.php
	*/
	public function getJsVersion() {
        return Mage::getStoreConfig('angarainfo/utility_settings/js_cache_version');
    }
	
	/*
		@ returns string no of days for free shipping and returns
	*/
	public function getFreeReturnDays() {
        return Mage::getStoreConfig('angarainfo/utility_settings/free_return_days');
    }
}
	 