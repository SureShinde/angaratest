<?php

   class Atwix_Recentlyviewed_Helper_Data extends Mage_Core_Helper_Abstract
   {

      // public function _construct() {
//           parent::_construct();
//           $this->SetSession(Mage::getSingleton('core/session'));
//       }

      public function addViewedItem($products_id) {          
		  $cookieval = Mage::getModel('core/cookie');				
		  $recently_viewed_cookie = $cookieval->get('RecView');		  		  
		  $recently_viewed = explode('|',$recently_viewed_cookie);		  
		  $items_count = 8;
          if (empty($recently_viewed))
              $recently_viewed = array();
          //if (!in_array($products_id, $recently_viewed)) {
              array_push($recently_viewed, $products_id);
              # Process a queue
              if (count($recently_viewed) > $items_count) {
                reset($recently_viewed);
                array_shift($recently_viewed);
              }			
			  $recently_viewed_cookie = implode('|',$recently_viewed);
              $cookieval->set('RecView', $recently_viewed_cookie, time()+2592000);			  
          //}
      }
	public function removeViewedItem($product_id)
	  {
		$arr = array();
	  	$cookieval = Mage::getModel('core/cookie');				
		$rw_items_cookie = $cookieval->get('RecView');
		$rw_items = explode('|',$rw_items_cookie);
		
		for($i=0;$i<count($rw_items);$i++)
		{
			if($rw_items[$i]!=$product_id){
				$arr[count($arr)] = $rw_items[$i];
			}
		}
		//$session->setRecView($arr);
		$arr_cookie = implode('|',$arr);
		$cookieval->set('RecView', $arr_cookie, time()+2592000);
	  }	
      public function savePanelState($state) {
        $cookieval = Mage::getModel('core/cookie');
		$state_cookie = implode('|',$state);
		$cookieval->set('RVIpanelstate', $state_cookie, time()+2592000);		
      }
   }

?>
