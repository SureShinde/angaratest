<?php
class Atwix_Recentlyviewed_Block_View extends Mage_Catalog_Block_Product_Abstract {
	// This function calls on home & product page only 
    public function CollectRecentlyViewed() {
        $module_status = Mage::getStoreConfig('recentlyviewed/general/enablemodule');
        $rw_items = array();
        if($module_status) {
            $cookieval = Mage::getModel('core/cookie');				
			$rw_items_cookie = $cookieval->get('RecView');
			$rw_items = explode('|',$rw_items_cookie);			
        }
		$rw_items = array_reverse(array_filter($rw_items));
		$ProductId = $this->getRequest()->getParam('productId');
		
		if(in_array($ProductId, $rw_items)){
			unset($rw_items[array_search('$ProductId',$rw_items)]);
		}
		//	S:VA	Check byo products and hide them from recently viewed products
		foreach($rw_items as $pId){
			$sku 	= 	Mage::getModel('catalog/product')->load($pId)->getSku();
			if( strtolower(substr($sku, 0, 2)) == 'by' ){
				unset($rw_items[array_search($pId,$rw_items)]);
			}
		}	
		return $rw_items;
    }

    public function checkPanelState() {
        $cookieval = Mage::getModel('core/cookie');				
		$state_cookie = $cookieval->get('RVIpanelstate');
		$state = explode('|',$state_cookie);
		if(empty($state))
            $state = 'show';
        return $state;
    }
}?>