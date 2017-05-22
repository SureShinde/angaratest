<?php
// This function is used to add product id on product page
class Atwix_Recentlyviewed_Block_Collect extends Mage_Core_Block_Template {
    public function addItem($products_id) {
        $module_status = Mage::getStoreConfig('recentlyviewed/general/enablemodule');
        if ($module_status) {
			$cookieval = Mage::getModel('core/cookie');				
			$rw_items_cookie = $cookieval->get('RecView');
			$rw_items = explode('|',$rw_items_cookie);			
			$countPidCookie	= count($rw_items);
			$session = Mage::getSingleton('core/session');
			$mysession = $session->getData('recentlyViewed');
			if($countPidCookie > 0 && count($mysession) == 0){
				$mysession	=	array_unique($rw_items);
			}
			foreach($mysession as $key => $pid){
				if($pid == $products_id)
					unset($mysession[$key]);
			}
			// Creating session array first time
			$mysession[]	=	$products_id;
			//echo 'After-><pre>'; print_r($mysession);	
			$session->setData( 'recentlyViewed', $mysession );
			$rw_items = implode('|',$mysession);
			$cookieval->set('RecView', $rw_items, time()+2592000);
        }
    }
}
?>