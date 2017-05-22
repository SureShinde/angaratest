<?php

class Atwix_Recentlyviewed_Block_Expand extends Mage_Core_Block_Template {

    public function checkRVItems() {
        $expand_status = Mage::getStoreConfig('recentlyviewed/general/enableextraspace');

        if ($expand_status) {
            $cookieval = Mage::getModel('core/cookie');				
			$rw_items_cookie = $cookieval->get('RecView');
			$rw_items = explode('|',$rw_items_cookie);			
			if (count($rw_items) > 0)
                return true;
        }

        return false;
    }

}

?>