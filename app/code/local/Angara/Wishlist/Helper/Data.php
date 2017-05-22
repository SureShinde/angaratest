<?php
class Angara_Wishlist_Helper_Data extends Mage_Core_Helper_Abstract
{

	/*
		Enabled Settings
	*/
	public function getWishlistStatus()
    {
        return Mage::getStoreConfig('ajax_wishlist/settings/status');
    }
	
	/*
		check product in customer wishlist
	*/
	public function getProductInWishlist($productId){
		if(empty($productId)){return false;}
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		if(!empty($customerId) && Mage::helper('customer')->isLoggedIn()){
			$wishlist = Mage::getModel('wishlist/item')->getCollection();
			$wishlist->getSelect()
					  ->join(array('t2' => 'wishlist'),
							 'main_table.wishlist_id = t2.wishlist_id',
							 array('wishlist_id','customer_id'))
							 ->where("main_table.product_id='".$productId."' AND t2.customer_id='".$customerId."'");
							 //->limit(1);
			//echo $wishlist->load(1);die;
			$count = $wishlist->count();
			//echo $count;die;
			if($count > 0){ 
				foreach ($wishlist as $wishlist) {
					//echo '<pre>';print_r($wishlist->getData());die;
					$wishlistItemId	=	$wishlist['wishlist_item_id'];
					return $wishlistItemId;
				}
				return true;
			}
		}
		return false;		
	}
}
?>