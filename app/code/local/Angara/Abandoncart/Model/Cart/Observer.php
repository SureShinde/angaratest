<?php
class Angara_Abandoncart_Model_Cart_Observer extends Mage_Core_Model_Abstract
{
	function __construct(){}
	// Added by Pankaj for Bug Id : 118	
	public function checkoutCartProductAddAfterAbandoncart($observer)
    {
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			
			$items = Mage::getSingleton('checkout/cart')->getItems();
						
			$abandonCartDataQuoteIdArr = array();
			$abandonCartData = Mage::getModel('abandoncart/abandoncart')->getCollection()->getData();
			
			if(!empty($abandonCartData)){ 
				foreach($abandonCartData as $abandonCart){					
					$abandonCartDataQuoteIdArr[] = $abandonCart['quote_item_id'];	
				}				
			}
			
			if(!empty($items)){
				
				foreach($items as $item){
					
					$getItemSku = $item->getSku();
					
					$productSkuArr = array('INS001','JA0050','FR0006AMB','FR0006G','FR0006BT','FR0006AM','FR0005GC','FR0004G','FR0004AM','FR0003G','FR0002S','FR0002R','FR0004OP','FR0002RQ','FR0002P','FR0002CT','FR0002BT','FR0003AM','FR0002G','FR0002AM','FR0001BG','OP0001SC');
				                             
					if(!in_array($getItemSku,$productSkuArr)){ 
					
						$customer = Mage::getSingleton('customer/session')->getCustomer();
						$product = Mage::getModel('catalog/product')->load($item->getProductId());
						
						$customjflag = 0;
						$imgurls = '';
						$customjflag = $product->getCustomj();
						$cartitemid = $item->getItemId();
						
						$home_url = Mage::helper('core/url')->getHomeUrl();
						
						if($customjflag == 1){				
							$imgurls = $home_url.Mage::getBlockSingleton('hprcv/hprcv')->getrootpath() . "cartimages/" . $cartitemid . ".png";				
						}
						else{	
							$imgurls = $product->getImageUrl();
						}
						
						//$imgurl = Mage::helper('abandoncart')->getResizedUrl($imgurls,250,250);
						
						$price = Mage::helper('core')->currency($item->getPrice(),false,false);
												
						$cart = new Mage_Checkout_Model_Cart();
						$cart->init();
						$cartItems = $cart->getItems()->getData();
						$cartItemsIndex = (int) (count($cartItems) - 1);
						
						//$quoteItemId = $cartItems[$cartItemsIndex]['item_id']; 
						if(!in_array($cartitemid,$abandonCartDataQuoteIdArr)){
							
							Mage::getModel('abandoncart/abandoncart')
								->setQuoteItemId($item->getItemId())
								->setCustomerId($customer->getId())
								->setCustomerFirstname($customer->getFirstname())
								->setCustomerLastname($customer->getLastname())
								->setCustomerEmail($customer->getEmail())
								->setProductId($product->getId())
								->setProductSku($item->getSku())
								->setProductName($product->getName())
								->setProductUrl($product->getProductUrl())
								->setProductImage($imgurls)
								->setProductPrice($price)
								->setCreatedAt(now())
								->setFlag(0)
								->save();	
						}
					}
				}
			}
		}	
	}
	
	public function checkoutCartRemoveItemBeforeAbandoncart($observer){
		$itemId = $observer->getItemDeleting();
		
		$item = Mage::getModel('sales/quote_item')->load($itemId);
		
		$getItemSku = $item->getSku();
		$productSkuArr = array('INS001','JA0050','FR0006AMB','FR0006G','FR0006BT','FR0006AM','FR0005GC','FR0004G','FR0004AM','FR0003G','FR0002S','FR0002R','FR0004OP','FR0002RQ','FR0002P','FR0002CT','FR0002BT','FR0003AM','FR0002G','FR0002AM','FR0001BG','OP0001SC');
				
		if(!in_array($getItemSku,$productSkuArr)){ 
		
			$abandonCart = Mage::getModel('abandoncart/abandoncart')->getCollection()
				->addFieldToFilter('quote_item_id',$itemId)
				->load();
			if(!empty($abandonCart)){			
				$AbonCartId = $abandonCart->getData();
				Mage::getModel('abandoncart/abandoncart')->load($AbonCartId[0]['abandoncart_id'])->delete();
			}
		}		
	}
	
	public function salesOrderPlaceAfterAbandoncart($observer){
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
				
		$abandonCart = Mage::getModel('abandoncart/abandoncart')->getCollection()
			->addFieldToFilter('customer_id',$customer->getId())
			->load();
		if(!empty($abandonCart)){	
			foreach($abandonCart as $cart){			
				$AbonCartId = $cart->getId();
				Mage::getModel('abandoncart/abandoncart')->load($AbonCartId)->delete();
			}
		}
	}
	// Ended by Pankaj for Bug Id : 118
	
	// Added by Saurabh for Bug Id : 118
	public function sendDataToPardot(){
		$apiKey = Mage::helper('abandoncart')->getApiKeyPardot();
		
		$abandonCart = Mage::getModel('abandoncart/abandoncart')->getCollection();
		$abandonCart->addExpressionFieldToSelect('cust_email', 'COUNT({{customer_email}})', 'customer_email');
		$abandonCart->getSelect()->group('customer_email');
		
		foreach($abandonCart as $_cust){	
		
			//$customerData=Mage::getModel('abandoncart/abandoncart')->load($AbonCartId[0]['abandoncart_id']);
			$customerEmail = $_cust->getCustomerEmail();	
			$customerProduct =  Mage::getModel('abandoncart/abandoncart')->getCollection()
								->addFieldToFilter('flag',0)
								->addFieldToFilter('customer_email',$customerEmail);
			$customerProduct->getSelect()->order('created_at DESC');	
			//$customerProduct->getSelect()->limit(3);
			$custProd=$customerProduct->getData();				
				
			if($custProd!=NULL){

				// Read Customer
				$readUrls="https://pi.pardot.com//api/prospect/version/3/do/read/email/".$customerEmail."?api_key=".$apiKey."&user_key=".$pardot_user_key;
				$readUrl=simplexml_load_file($readUrls);
				//echo $readUrl->email;
				$node = $readUrl->children();
				$email=$node[0]->email;
				if(!empty($email)){
					for($i=0;$i<3;$i++){
							$prodName[$i]=$custProd[$i]['product_name'] ? $custProd[$i]['product_name']:NULL;
							$prodImage[$i]=$custProd[$i]['product_image'] ? $custProd[$i]['product_image']:NULL;	
							$prodUrl[$i]=$custProd[$i]['product_url'] ? $custProd[$i]['product_url']:NULL;
							$abandoncartId[$i]=$custProd[$i]['abandoncart_id']?$custProd[$i]['abandoncart_id']:NULL;
					}
					//Update Data				
					$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$customerEmail."?pr_nm_cart_1=".$prodName[0]."&pr_nm_cart_2=".$prodName[1]."&pr_nm_cart_3=".$prodName[2]."&pr_img_cart_1=".$prodImage[0]."&pr_img_cart_2=".$prodImage[1]."&pr_img_cart_3=".$prodImage[2]."&pr_link_cart_1=".$prodUrl[0]."&pr_link_cart_2=".$prodUrl[1]."&pr_link_cart_3=".$prodUrl[2]."&api_key=".$apiKey."&user_key=".$pardot_user_key;
					$updateUrl=simplexml_load_file($updateUrls);	
					for($i=0;$i<3;$i++){
						$abandonId[$i]=$abandoncartId[$i]?$abandoncartId[$i]:NULL;
						if($abandonId[$i]!=NULL){
							$abdFlag=Mage::getModel('abandoncart/abandoncart')->load($abandonId[$i]);
					
							if($abdFlag->getFlag()==0){
								$abdFlag->setFlag(1)->save();
							}
						}
					}
				}	
				else{
					$createUrls=	"https://pi.pardot.com//api/prospect/version/3/do/create/email/".$customerEmail."&api_key=".$apiKey."&user_key=".$pardot_user_key;
					$createUrl=simplexml_load_file($createUrls);
							
					for($i=0;$i<3;$i++){
						$prodName[$i]=$custProd[$i]['product_name'] ? $custProd[$i]['product_name']:NULL;
						$prodImage[$i]=$custProd[$i]['product_image'] ? $custProd[$i]['product_image']:NULL;	
						$prodUrl[$i]=$custProd[$i]['product_url'] ? $custProd[$i]['product_url']:NULL;
						$abandoncartId[$i]=$custProd[$i]['abandoncart_id']?$custProd[$i]['abandoncart_id']:NULL;

					}
					
					//Update Data
					$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$customerEmail."?pr_nm_cart_1=".$prodName[0]."&pr_nm_cart_2=".$prodName[1]."&pr_nm_cart_3=".$prodName[2]."&pr_img_cart_1=".$prodImage[0]."&pr_img_cart_2=".$prodImage[1]."&pr_img_cart_3=".$prodImage[2]."&pr_link_cart_1=".$prodUrl[0]."&pr_link_cart_2=".$prodUrl[1]."&pr_link_cart_3=".$prodUrl[2]."&api_key=".$apiKey."&user_key=".$pardot_user_key;
					
					$updateUrl=simplexml_load_file($updateUrls);
					for($i=0;$i<3;$i++){
						$abandonId[$i]=$abandoncartId[$i]?$abandoncartId[$i]:NULL;
						if($abandonId[$i]!=NULL){
							$abdFlag=Mage::getModel('abandoncart/abandoncart')->load($abandonId[$i]);
							if($abdFlag->getFlag()==0){
								$abdFlag->setFlag(1)->save();
							}
						}
					}
				}
			}
		}
	}
	// Ended by Saurabh for Bug Id : 118
		
	// Added by Pankaj for Bug Id : 428 regarding Wishlist Product on Pardot
	public function sendWishlistDataToPardot(){
		
		$apiKey = Mage::helper('abandoncart')->getApiKeyPardot();
				
		$wishlist = Mage::getModel('wishlist/wishlist')->getCollection();
		
		$wishlist->getSelect()
					->join(array('customer' => 'customer_entity'),
						'main_table.customer_id = customer.entity_id',
						array('customer.email'));
		
		foreach($wishlist as $wish){
			
			$wishlistId = $wish->getWishlistId();
			
			$itemCollection = Mage::getModel('wishlist/item')->getCollection()
				->addFieldToFilter('wishlist_id', $wishlistId)
				->addFieldToFilter('flag', 0);
				
			$itemCollection->getSelect()
				->order('added_at DESC')->limit(6);
			
			if(count($itemCollection)>0){
				
				$customerEmail = $wish->getEmail();
				$readUrls = "https://pi.pardot.com//api/prospect/version/3/do/read/email/".$customerEmail."?api_key=".$apiKey."&user_key=".$pardot_user_key;	
				$readUrl = simplexml_load_file($readUrls);
				$node = $readUrl->children();
				$email = $node[0]->email;
				$custProd = $itemCollection->getData();
				
				if(!empty($email)){
					for($i=0;$i<6;$i++){
						$productId = $custProd[$i]['product_id'];
						if($productId) {
							$productDetails = Mage::getModel('catalog/product')->load($productId);
							$prodName[$i] = $productDetails['name'] ? $productDetails->getName():NULL;								
							$prodImage[$i] = $productDetails['image'] ? $productDetails->getImageUrl():NULL;								
							$prodUrl[$i] = $productDetails['url_path'] ? $productDetails->getProductUrl():NULL;								
							$wishlistItemId[$i] = $custProd[$i]['wishlist_item_id'] ? $custProd[$i]['wishlist_item_id']:NULL;								
						}
					}
					
					$updateUrls = "https://pi.pardot.com//api/prospect/version/3/do/update/email/".$customerEmail."?wl_pname1=".$prodName[0]."&wl_pname2=".$prodName[1]."&wl_pname3=".$prodName[2]."&wl_pname4=".$prodName[3]."&wl_pname5=".$prodName[4]."&wl_pname6=".$prodName[5]."&wl_pimage1=".$prodImage[0]."&wl_pimage2=".$prodImage[1]."&wl_pimage3=".$prodImage[2]."&wl_pimage4=".$prodImage[3]."&wl_pimage5=".$prodImage[4]."&wl_pimage6=".$prodImage[5]."&wl_plink1=".$prodUrl[0]."&wl_plink2=".$prodUrl[1]."&wl_plink3=".$prodUrl[2]."&wl_plink4=".$prodUrl[3]."&wl_plink5=".$prodUrl[4]."&wl_plink6=".$prodUrl[5]."&api_key=".$apiKey."&user_key=".$pardot_user_key;							
					
					$updateUrl = simplexml_load_file($updateUrls);
					
					for($i=0;$i<6;$i++){
						if($wishlistItemId[$i]!=NULL){
							$abdFlag = Mage::getModel('wishlist/item')->load($wishlistItemId[$i]);
							if($abdFlag->getFlag()==0){
								$abdFlag->setFlag(1)->save();
							}
						}
					}
				}
				else{ 
										
					$createUrls = "https://pi.pardot.com//api/prospect/version/3/do/create/email/".$customerEmail."&api_key=".$apiKey."&user_key=".$pardot_user_key;
					$createUrl = simplexml_load_file($createUrls);
					
					for($i=0;$i<6;$i++){
						$productId = $custProd[$i]['product_id'];
						if($productId) {
							$productDetails = Mage::getModel('catalog/product')->load($productId);
							$prodName[$i] = $productDetails['name'] ? $productDetails->getName():NULL;								
							$prodImage[$i] = $productDetails['image'] ? $productDetails->getImageUrl():NULL;								
							$prodUrl[$i] = $productDetails['url_path'] ? $productDetails->getProductUrl():NULL;								
							$wishlistItemId[$i] = $custProd[$i]['wishlist_item_id'] ? $custProd[$i]['wishlist_item_id']:NULL;								
						}						
					}
					
					$updateUrls = "https://pi.pardot.com//api/prospect/version/3/do/update/email/".$customerEmail."?wl_pname1=".$prodName[0]."&wl_pname2=".$prodName[1]."&wl_pname3=".$prodName[2]."&wl_pname4=".$prodName[3]."&wl_pname5=".$prodName[4]."&wl_pname6=".$prodName[5]."&wl_pimage1=".$prodImage[0]."&wl_pimage2=".$prodImage[1]."&wl_pimage3=".$prodImage[2]."&wl_pimage4=".$prodImage[3]."&wl_pimage5=".$prodImage[4]."&wl_pimage6=".$prodImage[5]."&wl_plink1=".$prodUrl[0]."&wl_plink2=".$prodUrl[1]."&wl_plink3=".$prodUrl[2]."&wl_plink4=".$prodUrl[3]."&wl_plink5=".$prodUrl[4]."&wl_plink6=".$prodUrl[5]."&api_key=".$apiKey."&user_key=".$pardot_user_key;	
					
					$updateUrl = simplexml_load_file($updateUrls);
					
					for($i=0;$i<6;$i++){ 
						if($wishlistItemId[$i]!=NULL){
							$abdFlag = Mage::getModel('wishlist/item')->load($wishlistItemId[$i]);
							if($abdFlag->getFlag()==0){
								$abdFlag->setFlag(1)->save();
							}
						}
					}
				}				
			}
		}	
	}
	// Ended by Pankaj for Bug Id : 428 regarding Wishlist Product on Pardot
	
	// Added by Pankaj for Bug Id : 328 regarding sending recently viewed Product Details on Pardot
	/*public function sendProductDataToPardot(){
		
		$apiKey = Mage::helper('abandoncart')->getApiKeyPardot();	
		
	}*/
	// Ended by Pankaj for Bug Id : 328 regarding sending recently viewed Product Details on Pardot
}