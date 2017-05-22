<?php
set_time_limit(0);
ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes

class AngaraCustom_Function_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/*
		return the cart total amount
	*/
	public function getCartTotalAmount(){
		$pageType =	Mage::helper('function')->getPageType();
		if($pageType == 'cart' && !Mage::getSingleton('checkout/session')->getData('shipment')){
			$baseCurrencyCode 		= 	Mage::app()->getStore()->getBaseCurrencyCode();				// Base Currency
			$currentCurrencyCode 	= 	Mage::app()->getStore()->getCurrentCurrencyCode();			// Current Currency
			if($currentCurrencyCode == $baseCurrencyCode){
				$availableShippingMethods	=	Mage::helper('function')->getDefaultShippingMethods();
				$i = 0;
				foreach($availableShippingMethods as $_method){
					if($i == 0){
						if($_method['price'] == '' || $_method['price'] == 0 || $_method['price'] == '0.00' || $_method['code'] == 'freeshipping_freeshipping'){
							$priceForTotal = 0; 
						}else{
							$priceForTotal = $_method['price'];
						}
					}
				$i++;
				}
			}else{
				$subTotalWithoutEasyEmi				= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
				$freeInternationalShippingAmount	=	Mage::getStoreConfig("carriers/angnonusflatrate/min_cart_value_for_free_shipping");
				$price								=	Mage::getStoreConfig("carriers/angnonusflatrate/price");
				if($subTotalWithoutEasyEmi >= $freeInternationalShippingAmount){
					$_title 	= 	Mage::getStoreConfig("carriers/angnonusflatrate/title");
					$priceForTotal = 0;
				} 
				else {
					$priceForTotal = $price;
				}
			}
			$priceForTotal 	= 	Mage::helper('directory')->currencyConvert($priceForTotal, $baseCurrencyCode, $currentCurrencyCode);
			$cartTotal		=	Mage::helper('checkout')->formatPrice($priceForTotal+= Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal());
		}else{
			$cartTotal		=	Mage::helper('checkout')->formatPrice(Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal());
		}
		return $cartTotal;
	}
	
	/*
		@returns the minimum price to show Jewelry Appraisal and Engraving Add ons
	*/
	public function minPriceToShowAddOns(){
		return '0';	
	}
	
	public function canShowJAEngraving(){
		return true;	
	}
	
	/*
	
	*/
	public function getJewelryAppraisalPrice($sku = 'JA0050'){
		$price	=	Mage::helper('core')->currency(85,true,false);
		return $price;		
	}
	
	/*
		Get the statically defined coupon codes for a date
	*/
	public function getTodaysCouponCode(){
		//echo $this->getCurrentServerDateTime();	
		$todayDateTime	=	$this->getCurrentServerDate();	
		//echo $todayDateTime;die;
		$dateCouponArray	=	array('2016-12-07'=>'DEAL1','2016-12-08'=>'DEAL2','2016-12-09'=>'DEAL3','2016-12-10'=>'DEAL4','2016-12-11'=>'DEAL5','2016-12-12'=>'DEAL6','2016-12-13'=>'DEAL7');
		return $dateCouponArray[$todayDateTime];
	}
	
	/*
		Get the sku for which the coupon code will work
	*/
	public function getSkuFromCoupon(){
		$todaysCouponCode	=	$this->getTodaysCouponCode();
		//echo $todaysCouponCode;die;
		if(!empty($todaysCouponCode)){
			$oCoupon = Mage::getModel('salesrule/coupon')->load($todaysCouponCode, 'code');
			$oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
			
			if ($oRule->getId()) {
				$conditions = unserialize($oRule->getActionsSerialized());
				//echo '<pre>';print_r($conditions);die;
				$attribute = $conditions['conditions'][0]['attribute'];
				if($attribute == 'sku'){
					$sku = $conditions['conditions'][0]['value'];
					//echo '<pre>';print_r($sku);die;
					$arraySku	=	explode(',',$sku);
					$arraySku	=	array_map('trim',$arraySku);
					return $arraySku;
				}
			}
		}
		return false;
	}
	
	/*
		Get static email addresses that we use to send order process decline emails
	*/
	public function getDeclineEmailAddresses(){
		return 'vaseem.ansari@angara.com, vinod.guneja@angara.com, customer.support@angara.com, receivables@angara.com, hitesh.baid@angara.com, ankit.maheshwari@angara.com';
	}
	
	/*
		Get the channel type using cid params
	*/
	public function getChannelType(){
		$cid			=	Mage::app()->getRequest()->getParam('cid');				//	getting the cid parameter from url
		/*if(isset($cid)){
			$cidChannel	=	$this->setChannelBasedOnCid($cid);	//	if user change the cid then set a new session
		}*/
		
		$channelSession	=	Mage::getSingleton('core/session')->getChannel();
		if(isset($channelSession)){
			return $channelSession;	
		}else{
			$cidChannel	=	$this->setChannelBasedOnCid($cid);
		}
		return $cidChannel;
	}
	
	/*
		Set visitor channel based on cid params
	*/
	public function setChannelBasedOnCid($cid){
		if(empty($cid)){return false;}
		if(strstr($cid,'ps')) { 
			$cidChannel	=	'paid-search';
			Mage::getSingleton('core/session')->setChannel($cidChannel);
		}else{
			$cidChannel	=	'other';
			Mage::getSingleton('core/session')->setChannel($cidChannel);
		}	
		return $cidChannel;
	}

	/*
		return google recaptcha keys
	*/
	public function getGoogleRecaptchaKeys(){
		$pubKey 	= '6LdOkscSAAAAAGDz7XT4vcw-WpLFwqAfXOjcQc-C';
		$privKey 	= '6LdOkscSAAAAALpss4hPLseK0XiB9pRV399fApIs';
		
		if(strstr(Mage::getBaseUrl(), 'angara.dev') || strstr(Mage::getBaseUrl(), 'angara.git')){
			$pubKey 	= '6LdEbf4SAAAAAM8ls58eJ5Gb-yXg-Mk0uQeDzyrW';
			$privKey 	= '6LdEbf4SAAAAAK356-eb6_o4n3cB5_iersibJfMt';
		}
		return array('public_key' => $pubKey, 'private_key' => $privKey);
	}
	
	/*
		Allowed category array for Jewelry Type filter to show on catalog pages
	*/
	public function getAllowedJewelryTypeCategory($categoryId){
		if(empty($categoryId)) return false;
		$allowedCategories	=	array('4', '59', '101', '313', '60', '353', '3', '414', '413', '58', '56', '61', '310', '311', '312', '314', '457', '458', '485');
		if(in_array($categoryId, $allowedCategories)){
			return true;	
		}
		return false;
	}
	
	/*
		return the total items in cart - free products count
	*/
	public function getCartItemsCount(){
		$totalCartItems = Mage::helper('checkout/cart')->getSummaryCount();
		/*if(empty($totalCartItems)){
			$totalCartItems = 0;
		}else{
			$quote = Mage::getSingleton('checkout/session')->getQuote();  
			$cartItems = $quote->getAllVisibleItems();
			foreach ($cartItems as $item) {
				$skuPrefix	=	substr(strtolower($item->getSku()),0,2);
				if($skuPrefix == 'fr' || $skuPrefix == 'fp' || $skuPrefix == 'fe' || $skuPrefix == 'fb'){	
					$totalCartItems--;
				}  
			} 	
		}*/
		return $totalCartItems;
	}
	
	/*
		return hard coded static values
	*/
	public function getStaticValues($value, $superAttributeCode, $childProductSku){
		if($value=='A'){
			return 'Good (A)';
		}elseif($value=='AA'){
			return 'Better (AA)';
		}elseif($value=='AAA'){
			return 'Best (AAA)';
		}elseif($value=='AAAA'){
			return 'Heirloom (AAAA)';
		}elseif($value=='Gemstone Size'){
			return 'Carat Weight';
		}elseif($superAttributeCode=='stone1_size' && $childProductSku!=''){
			$childSku		=	$this->getRealChildSku($childProductSku);
			$childProductId = 	Mage::getModel('catalog/product')->getIdBySku( $childSku );
			$_product		=	Mage::getModel('catalog/product')->load($childProductId);
			$stones			=	Mage::getBlockSingleton('catalog/product_view_type_configurable')->getStones($_product);
			return $stones['total_weight'];
		}
		return $value;
	}
	
	/*
		check if product exist in wishlist
	*/
	public function checkProductInWishlist($productId){
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		if($customerId && $productId){
			$wishlist 	= Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			$collection = Mage::getModel('wishlist/item')->getCollection()
								->addFieldToFilter('wishlist_id', $wishlist->getId())
								->addFieldToFilter('product_id', $productId);
			//$collection->load(1);die;
			$item 		= $collection->getFirstItem();
			//print_r($item->getData());die;
			if($item->getProductId()==$productId){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/*
		check for mothers theme for mobile devices
	*/
	public function checkMothersMobileTheme(){
		$pageType	=	$this->getPageType();
		//echo '<br>pageType '.$pageType;
		if($pageType=='product'){
			$productId 	= Mage::app()->getRequest()->getParam('id');
			
			// Get product.
			$product 	= Mage::getModel('catalog/product')->load($productId);
	
			// Get attribute set model.
			$model 		= Mage::getModel('eav/entity_attribute_set');
	
			// Get attribute set id.
			$attributeSetId = $product->getAttributeSetId();
			$attributeSet 	= $model->load($attributeSetId);
	
			// This is attribute set name.
			$attributeSetName 	= 	$attributeSet->getAttributeSetName();
			$isMobile			=	$this->isMobile();
			if($attributeSetName == 'mothers' && $isMobile){
				return 'imobile-mothers';
			}
		}	
	}
	
	/*
		resize and save product image to resized folder
	*/
	public function getResizeImageUrl($imageUrl, $x, $y=NULL){
		$widht		=	$x;
		$y?$height	=	$y:$height=$x;
		
		//	getting the folder path of image to maintain the directory structure 'm/a/'
		$imgArray	=	explode("/",$imageUrl);
		$dirArray	=	array_slice($imgArray, count($imgArray)-3, 2);		//	m	a	
		
		//	getting image name
		$imageName	=	$imgArray[count($imgArray)-1];
		$dirSkuPath	=	implode("/",array_diff($dirArray,array($imageName)));		//	m/a
					
		//	hardcoded directory path where we will save the resized images
		$fixPath		=	Mage::getBaseDir("media").DS.'catalog'.DS.'product'.DS.'resized';
		$resizedDirPath	=	$fixPath.DS.$dirSkuPath;		//	F:\wamp\www\angara.git\media\catalog/product/resized\m/a
		
		// create 'resized' folder if not exist
		if(!file_exists($fixPath))     mkdir($fixPath,0777);
		
		//	creating sub folders
		if(count($dirArray)>0){
			foreach($dirArray as $dir){
				$path	=	$fixPath.DS.$dir;
				if(!file_exists($path)){
					mkdir($path,0777);
				}
				$fixPath	=	$path;		//	appending the folder name
			}
		}
		
		//	creating image full path as which the new image will be saved
		$imageResizedPath 	= 	$resizedDirPath.DS.$imageName;	//	F:\wamp\www\angara.git\media\catalog/product/resized\m/a\manual.jpg
		
		// changing image url into direct path
		$dirImg 		= 	Mage::getBaseDir().str_replace("/",DS,strstr($imageUrl,'/media'));
		
		// if resized image doesn't exist, save the resized image to the resized directory
		if (!file_exists($imageResizedPath) && file_exists($dirImg)){
			$imageObj = new Varien_Image($dirImg);
			$imageObj->constrainOnly(TRUE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->keepFrame(FALSE);
			$imageObj->resize($widht,$height);
			$imageObj->save($imageResizedPath);
			//echo '<br>New Image created at '.$imageResizedPath;
		}else{
			//echo '<br>Image already exist. ';
			//echo $imageResizedPath;
		}
		$resizedImageUrlTemp	=	Mage::getBaseUrl('media').'catalog'.DS.'product'.DS.'resized'. DS . $dirSkuPath . DS . $imageName;
		$resizedImageUrl		=	str_replace(DS,"/",$resizedImageUrlTemp);
		$resizedImageUrl		=	str_replace('commercemanager.angara.com',"cdn.angara.com",$resizedImageUrlTemp);
		return $resizedImageUrl;
	}
	
	/*
		Handy function to remove engraving and other custom option text so that we can get real sku and can easily load product using sku
	*/
	public function getRealChildSku($sku){
		//echo '<br>sku '.$sku;
		$simpleProductSku	=	$sku;
		if( strtolower(substr($sku,0,2)) == 'am' ){							//	Handle Mothers Sku
			$tempSku			=	explode('-', $sku);
			$simpleProductSku	=	$tempSku[0];
		}elseif(stristr($sku, 'engraving')){								//	Engraving selected for product
			$tempSku			=	explode('-engraving', $sku);
			$simpleProductSku	=	$tempSku[0];
		}elseif(stristr($sku, 'ja0050')){									//	Free Jewelry Appraisal selected for product
			$tempSku			=	explode('-ja0050', $sku);
			$simpleProductSku	=	$tempSku[0];
		}	
		return $simpleProductSku;
	}
	/*
		returns the text for each item in cart
	*/
	
	public function getAdditionalCartText($_item, $qty){
		$realChildSku	=	$this->getRealChildSku($_item->getSku());
		if(stristr(substr($realChildSku,0,1), 'f') || stristr(substr($realChildSku,0,4), 'agif')){
			return '';
		}
		$childProduct	=	Mage::getModel('catalog/product')->loadByAttribute('sku',$realChildSku);
		$priceDiff		=	($childProduct->getMsrp() - $childProduct->getPrice()) * $qty;
		if($priceDiff > 0 && $realChildSku!='OP0001SC' && $realChildSku!='EMOP0001SC' && $_item->getPrice()!=''){
			$couponCode 	= 	Mage::getSingleton('checkout/session')->getQuote()->getData('coupon_code');
			$td				=	Mage::helper('core')->currency($priceDiff);
			$additionalText	=	'<div class="additional"><div class="addtional-text-holder"><span class="additional-text"><i class="fa fa-check-circle"></i> Congratulations! You saved <span class="apricot-text">'.$td.'</span> of the Traditional Retail Price</span>';
			if($couponCode && $_item->getBaseDiscountAmount()>0){
				$additionalText.=	'<span class="additional-text"><i class="fa fa-check-circle"></i>You saved an additional <span class="apricot-text">'.Mage::helper('core')->currency($_item->getBaseDiscountAmount()).'</span> with code '.strtoupper($couponCode).'</span>';
			}
			$additionalText.=	'<span class="additional-text"><i class="fa fa-check-circle"></i>Free Shipping and Returns</span></div></div>';
			return $additionalText;
		}
	}
		
	public function packagingImage($sku,$categoryIds,$embStoneName,$embStoneName2,$embStoneName3,$attributeId){
		$diamondCategoryArray	=	array('4','70','71','72','73','76','82','84','92','219','279','320');
		$mergedArray			=	array_intersect($diamondCategoryArray,$categoryIds);
		//echo '<pre>'; print_r($mergedArray); die;
		$itemType		=	$this->getProductAttribute($attributeId);
		
		// Condition for Shah Diamond Products
		if($sku!='' && (substr($sku,0,3)=='sd_' || substr($sku,0,3)=='SD_') ){
			$productType	=	'Shah';
		}
		// Condition for Diamond Products
		elseif(count($mergedArray)>0){
			$productType	=	'Diamond';
			if($itemType!=''){
					$productType.=	$itemType;
			}
		}
		// Condition for Jewelry
		elseif($embStoneName=='' && $embStoneName2=='' && $embStoneName3=='' && !in_array('99',$categoryIds)){
			$productType	=	'Jewelry';
		}
		// Condition for Color Jewelry
		else{
			$productType	=	'ColorJewelry';
			if($itemType!=''){
					$productType.=	$itemType;
			}
		}
		return $productType;
	}
	
	//	Function created by Vaseem for Packaging Box Images for single template products BT -	789
	public function packagingImageST($stone1Type,$sku,$jewelryType){
		//	Check product type using the product attribute
		if($jewelryType=='Ring'){
			$itemType	=	'Ring';	
		}elseif($jewelryType=='Earrings'){
			$itemType	=	'Earrings';	
		}elseif($jewelryType=='Pendant'){
			$itemType	=	'Pendant';	
		}
		// Condition for Shah Diamond Products
		if($sku!='' && (substr($sku,0,3)=='sd_' || substr($sku,0,3)=='SD_') ){
			$productType	=	'Shah';
		}
		// Condition for Diamond Products
		elseif($stone1Type=='Diamond'){
			$productType	=	'Diamond';
			if($itemType!=''){
					$productType.=	$itemType;
			}
		}
		// Condition for Jewelry
		/*elseif($embStoneName=='' && $embStoneName2=='' && $embStoneName3=='' && !in_array('99',$categoryIds)){
			$productType	=	'Jewelry';
		}*/
		// Condition for Color Jewelry
		else{
			$productType	=	'ColorJewelry';
			if($itemType!=''){
					$productType.=	$itemType;
			}
		}
		return $productType;
	}
	
	public function getProductAttribute($attributeId){
		//$attributeSetIdArray	=	array('11'=>'Earrings','14'=>'Gemstones','16'=>'mothers','12'=>'Pendants','10'=>'Rings','15'=>'Sets');
		$attributeSetIdArray	=	array('11'=>'Earrings','12'=>'Pendants','10'=>'Rings');
		//echo $attributeSetIdArray[$attributeId]; die;
		if($attributeSetIdArray[$attributeId]=='Rings'){
			$type	=	'Rings';
		}elseif($attributeSetIdArray[$attributeId]=='Earrings'){
			$type	=	'Earrings';
		}elseif($attributeSetIdArray[$attributeId]=='Pendants'){
			$type	=	'Pendants';
		}
		return $type;
	}
	
	// 	Function to get the estimated shipping time based on weekends and national holidays
	//	Create by Vaseem on 5 Feb 2013
	//	$leadTime	= digit (day when we deliver the product to customers)
	public function skipUsaHolidays($leadTime=0){
		//echo '<br>leadTime->'.$leadTime;
		//$SaturdayDateArr 	= 	$this->getDateForSpecificDayBetweenDates(date('Y-m-d'), date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")+1)), 6);// saturday
		//$SundayDateArr 		= 	$this->getDateForSpecificDayBetweenDates(date('Y-m-d'), date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")+1)), 0);// sunday
		$SaturdayDateArr 	= 	$this->getSaturdayDate();// saturday
		$SundayDateArr 		= 	$this->getSundayDate();// sunday
		//echo "<pre>SaturdayDateArr->";print_r($SaturdayDateArr).'<br>';
		
		$holiday_arr		=	$this->getYearlyHolidays();
		$AllHolidayArr 		= 	array_merge($SaturdayDateArr, $SundayDateArr,$holiday_arr);
		$AllHolidayUniqueArr= 	array_unique($AllHolidayArr);
		//echo "<pre>";print_r($AllHolidayUniqueArr).'<br>';
		$numberofholiday 	= 	0;	
		$aa 				= 	0;
		
	//	Code modified by Vaseem for BT 878	
		$leadTime	=	$this->leadTimePlusOne($leadTime);
		//echo '<br>leadTime->'.$leadTime;
		//	Fixed when lead time is 0 or customer open the page before 1 pm
		if($leadTime==0){
			$delivery_date		=	date('Y-m-d');
			//echo "<pre>";print_r($AllHolidayUniqueArr).'<br>';	
			if(in_array($delivery_date,$AllHolidayUniqueArr)){
				$leadTime	=	1;
			}
		}
		//	Fixed when lead time is 0 or customer open the page before 1 pm
		//echo '<br>leadTime->'.$leadTime;
	//	Code modified by Vaseem for BT 878
		
		for($i=1;$i<=300;$i++){
			//echo 'aa->'.$aa;	echo 'leadTime->'.$leadTime;
			if($aa==$leadTime){
				break;	
			}
			$seldate  = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y")));
			//echo '<br>seldate->'.$seldate;
			if (in_array($seldate, $AllHolidayUniqueArr)) {
				//echo '<br>'.$seldate;
				$numberofholiday++;
			}else{	
				$delivery_date = $seldate;
				$aa++;
			}
		}
		//echo '<br>delivery_date->'.$delivery_date; 
		$delivery_date = $this->formatDate($delivery_date);
		return $delivery_date;
	}
	
	/*
		returns delivery date based on the lead time of product
	*/	
	public function getDeliveryDate($leadTime=0){
		$SaturdayDateArr 	= 	$this->getSaturdayDate();
		$SundayDateArr 		= 	$this->getSundayDate();
		
		$holiday_arr		=	$this->getYearlyHolidays();
		$AllHolidayArr 		= 	array_merge($SaturdayDateArr, $SundayDateArr,$holiday_arr);
		$AllHolidayUniqueArr= 	array_unique($AllHolidayArr);
		$numberofholiday 	= 	0;	
		$aa 				= 	0;
		
		$leadTime	=	$this->leadTimePlusOne($leadTime);
		if($leadTime==0){
			$delivery_date		=	date('Y-m-d');
			if(in_array($delivery_date,$AllHolidayUniqueArr)){
				$leadTime	=	1;
			}
		}
		for($i=1;$i<=300;$i++){
			if($aa==$leadTime){
				break;	
			}
			$seldate  = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y")));
			if (in_array($seldate, $AllHolidayUniqueArr)) {
				$numberofholiday++;
			}else{	
				$delivery_date = $seldate;
				$aa++;
			}
		}
		return $delivery_date;
	}
	
	//	Function Added by Vaseem for BT 878
	public function leadTimePlusOne($leadTime){
		//echo '<br>leadTime before->'.$leadTime;
		$currentTime 	= 	date('H', time());		
		//$currentTime	=	'12';
		//echo '<br>currentTime->'.$currentTime;
		$scheduleTime	=	'16';			//	lead time 4pm
		if($currentTime<$scheduleTime){
			$leadTime	=	$leadTime-1;
		}
		//echo '<br>leadTime after->'.$leadTime;
		return $leadTime;
	}
	
	public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
	{
		$startDate = strtotime($startDate);
		$endDate = strtotime($endDate);
		$dateArr = array();
		do
		{
			if(date("w", $startDate) != $weekdayNumber)
			{
				$startDate += (24 * 3600); // add 1 day
			}
		} while(date("w", $startDate) != $weekdayNumber);
	
		while($startDate <= $endDate)
		{
			$dateArr[] = date('Y-m-d', $startDate);
			$startDate += (7 * 24 * 3600); // add 7 days
		}
		return($dateArr);
	}
	
	public function formatDate($date){
		return date('l\, F j', strtotime($date));
	}
	
	public function formatDateAdmin($date){
		return date('l\, F j, Y', strtotime($date));
	}
    
	/*
		Check if sku is a virtual product that can not be delivered ie Gift Cards
	*/
	public function checkVirtualProductBySku($_product){
		$sku	=	strtolower(substr($_product->getSku(),0,2));
		if($sku == 'ag'){
			return true;
		}
		return false;
	}
	
	/*
		Check if sku if of free product
		The products lies in below critria are those with either price 0 or for which we don't want to show text (Order today for delivery by 12/23/16) at cart
	*/
	public function checkFreeProductBySku($_product){
		$sku	=	strtolower(substr($_product->getSku(),0,2));
		//	Jewelry Appraisal 	 Warranty	Silver Chain	Gold Chain		Gift Cards		Free Products												
		if($sku == 'ja' || $sku == 'in' || $sku == 'op' || $sku == 'em' || $sku == 'ag' || $sku == 'fr' || $sku == 'fp' || $sku == 'fe' || $sku == 'fb'){
			return true;
		}
		return false;
	}
	
	/*
		return true if the product in cart can be purchased in multiple quantity
	*/
	public function canEditQtyForProducts($_product){
		$sku	=	strtolower(substr($_product->getSku(),0,2));
		//	Jewelry Appraisal 	 Warranty		Free Products												
		if($sku == 'ja' || $sku == 'in' || $sku == 'fr' || $sku == 'fp' || $sku == 'fe' || $sku == 'fb'){
			return false;
		}
		return true;
	}
	
	
	/*public function getCurrentServerDateTimeWithMerridian() {
		return Mage::getModel('core/date')->gmtDate('Y-m-d h:i:s a');
	}*/
	
	/*public function scheduleLeadTime($currentTime){
		$currentHour = date('H', strtotime($currentTime));
		$scheduleHour = '13';
		
		$currentMerridian = date('A', strtotime($currentTime));
		if($currentHour >= $scheduleHour && $currentMerridian == 'PM'){
			$scheduleLeadTime = 1;
		}
		else{
			$scheduleLeadTime = 0;
		}
		return $scheduleLeadTime;
	}*/
	
	/* get date rules */
	public function getDateRulesAdmin(){
		$dateCollection = Mage::getModel('arrivaldate/daterules')->getCollection();
		if(!empty($dateCollection)){
			$arriveByDays = $dateCollection->getFirstItem()->getDate();
			$wantItDays = $dateCollection->getLastItem()->getDate();
			$dateRulesCollection['arriveByDays'] = $arriveByDays;
			$dateRulesCollection['wantItDays'] = $wantItDays;
		}
		return $dateRulesCollection;
	}
	
	/* get lead time of date rules */
	public function getLeadTimeDateRules($finalShippingMethod){
		$dateRulesCollection = $this->getDateRulesAdmin();
		$leadTimeDateRules = 0;
		if($dateRulesCollection){		
			if($finalShippingMethod && $finalShippingMethod == 'angovernightflatrate_angovernightflatrate'){
				if($dateRulesCollection['wantItDays']){
					$leadTimeDateRules = $dateRulesCollection['wantItDays'];
				}
			}
			else{
				if($dateRulesCollection['arriveByDays']){
					$leadTimeDateRules = $dateRulesCollection['arriveByDays'];
				}
			}
		}	
		return $leadTimeDateRules;
	}
		
	/* get lead time for addons cart items */	
	public function checkProductLeadTimeAddons($item){
		$leadTimeExtra = 0;	
		$hasAppraisal = false;
		$hasEngraving = false;	
		if(!empty($item)){			
			if($info = $item->getBuyRequest()){
				if($info->getData('appraisal') && $info->getData('appraisal') == 'on'){
					$hasAppraisal = true;
				}
				if($info->getData('engraving') && $info->getData('engraving') == 'on'){
					$hasEngraving = true;
				}	
			}
						
			if($hasAppraisal){											//	Paid Jewelry Appraisal selected for product
				$leadTimeExtra = $leadTimeExtra + 1;
			}		
			if($hasEngraving){											//	Paid Engraving selected for product
				$leadTimeExtra = $leadTimeExtra + 1;
			}
			
			$itemSku = $item->getSku();
			
			if(!$hasAppraisal){
				if($itemSku && stristr($itemSku, '-ja0050')){			//	Free Jewelry Appraisal selected for product
					$leadTimeExtra = $leadTimeExtra + 1;
				}
			}
		}
		return $leadTimeExtra;
	}
	
	/*
		shipping method short form as used in helper
	*/	
	public function shippingShortForm($ShippingMethod){
		if($ShippingMethod){
			if($ShippingMethod == 'freeshipping_freeshipping'){
				$ShippingMethod = 'freeshipping';
			}
			else if($ShippingMethod == 'ang2dayflatrate_ang2dayflatrate'){
				$ShippingMethod = 'ang2dayflatrate';
			}
			else if($ShippingMethod == 'angnonusflatrate_angnonusflatrate'){
				$ShippingMethod = 'angnonusflatrate';
			}
			else if($ShippingMethod == 'angovernightflatrate_angovernightflatrate'){
				$ShippingMethod = 'angovernightflatrate';
			}
			else if($ShippingMethod == 'flatrate_flatrate'){
				$ShippingMethod = 'flatrate';
			}
		}	
		return $ShippingMethod;
	}
	
	/*
		check product for leadtime increment for order admin item ordersheet
	*/
	public function checkProductForLeadTimeAdminSheet($item){
		$leadTimeAddons = 0;
		$hasEngraving = false;
		if(!empty($item)){
			$productOptions = $item->getProductOptions();
			if(!empty($productOptions)){
				if($info = $productOptions['info_buyRequest']){
					if($info['engraving'] && $info['engraving'] == 'on'){
						$hasEngraving = true;
					}	
				}
				
				if($hasEngraving){											//	Paid Engraving selected for product
					$leadTimeAddons = 1;
				}
			}	
		}
		return $leadTimeAddons;
	}
	
	/*
		check product for leadtime increment for order admin item view
	*/
	public function checkProductForLeadTimeAdmin($item){
		$leadTimeAddons = 0;
		$hasAppraisal = false;
		$hasEngraving = false;
		if(!empty($item)){
			$productOptions = $item->getProductOptions();
			if(!empty($productOptions)){
				if($info = $productOptions['info_buyRequest']){
					if($info['appraisal'] && $info['appraisal'] == 'on'){
						$hasAppraisal = true;
					}
					if($info['engraving'] && $info['engraving'] == 'on'){
						$hasEngraving = true;
					}	
				}
				
				if($hasAppraisal){											//	Paid Jewelry Appraisal selected for product
					$leadTimeAddons = $leadTimeAddons + 1;
				}		
				if($hasEngraving){											//	Paid Engraving selected for product
					$leadTimeAddons = $leadTimeAddons + 1;
				}
				
				$itemSku = $item->getSku();
				
				if(!$hasAppraisal){
					if($itemSku && stristr($itemSku, '-ja0050')){			//	Free Jewelry Appraisal selected for product
						$leadTimeAddons = $leadTimeAddons + 1;
					}
				}
			}	
		}
		return $leadTimeAddons;
	}
	
	/*
		S:VA	To show Estimated Ship Date in Sales Order View in Admin
	*/
	public function estimatedShipDateAdmin($orderDate,$leadTime){
		if($leadTime<1){	$leadTime	=	1;}	// fixed the leadtime zero for old orders
		$seldate 			= 	date('Y-m-d', strtotime($orderDate));
		$SaturdayDateArr 	= 	$this->getSaturdayDate();		// saturday
		$SundayDateArr 		= 	$this->getSundayDate();			// sunday
		$holiday_arr		=	$this->getYearlyHolidays();
		$AllHolidayArr 		= 	array_merge($SaturdayDateArr, $SundayDateArr,$holiday_arr);
		$AllHolidayUniqueArr= 	array_unique($AllHolidayArr);
		$numberofholiday 	= 	0;	
		$aa 				= 	0;
		// Code modified by Vaseem for BT 878	
		$orderTime			=	date('H', strtotime($orderDate));
		$scheduleTime		=	'13';
		if( $orderTime < $scheduleTime && $leadTime > 1 ){ // condition modified as leadtime getting zero before this	by pankaj
			$leadTime		=	$leadTime-1;
		}
		// Code modified by Vaseem for BT 878
	
		for($i=1;$i<=100;$i++){
			if($aa==$leadTime){
				break;
			}
			$seldate 	 = 	date("Y-m-d", strtotime($seldate . "+1 day"));
			if (in_array($seldate, $AllHolidayUniqueArr)) {
				$numberofholiday++;
			}else{	
				$delivery_date = $seldate;
				$aa++;
			}
		}
		
		$deliveredByDate = date('m/j/y', strtotime($delivery_date));
		$delivery_date = $this->formatDateAdmin($deliveredByDate);
		return $delivery_date;
	}
	
	public function shipDateAdmin($orderDate,$leadTime=0){
		$seldate = date('Y-m-d', strtotime($orderDate));
		$SaturdayDateArr = $this->getSaturdayDate();		// saturdays
		$SundayDateArr = $this->getSundayDate();			// sundays
		$holiday_arr = $this->getYearlyHolidays();			// yearly holidays
		
		$AllHolidayArr = array_merge($SaturdayDateArr,$SundayDateArr,$holiday_arr);
		$AllHolidayUniqueArr = array_unique($AllHolidayArr);
		
		$orderTime			=	date('H', strtotime($orderDate));
		$scheduleTime		=	'13';
		if( $orderTime < $scheduleTime && $leadTime > 1 ){ // condition modified as leadtime getting zero before this	by pankaj
			$leadTime		=	$leadTime-1;
		}
		
		$numberofholiday = 0;	
		$aa = 0;
			
		for($i=1;$i<=300;$i++){
			if($aa == $leadTime){
				break;
			}
			$seldate = date("Y-m-d", strtotime($seldate . "+1 day"));
			if (in_array($seldate, $AllHolidayUniqueArr)) {
				$numberofholiday++;
			}else{	
				$delivery_date = $seldate;
				$aa++;
			}
		}
		$deliveredByDate = date('m/d/y', strtotime($delivery_date));
		$delivery_date = $this->formatDateAdmin($deliveredByDate);
		return $delivery_date;
	}
	
	// Function created by Vaseem for getting sku for image zoom
	public function imageZoomSku($sku){
		$skuArray	=	array('SR0601CT','SE0914CT','SP0576CT','SR0603CR','SP0577CR','SR0604AM','SP0574AM','SR0638CT','SE0917CT','SR0605BT','SP0575RQ','SR0637P','SE0918P','SR0639BT','SP0579TQ','SR0607AM','SP0578LL','SR0606AM','SR0641CT','SR0634CT','SE0915CH','SR0602RQ','SR0636CT');
		if(in_array($sku,$skuArray)){
			$enable	=	'1';
		}else{
			$enable	=	'0';
		}
		return $enable;
	}
	
	// Function created by Vaseem to check if single template is live or not
	public function singleTemplate(){
		$singleTemplateLiveDate	= '2013-05-12';			//	This is the date when single template goes live
		$curr_time = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		if($curr_time>$singleTemplateLiveDate){
			$otext	= '1';
		}else{
			$otext	= '0';
		}
		return $otext;
	}
	// Function created by Vaseem to generate a url link for backup sales sheet html files
	public function singleTemplateBackupURL($productID,$orderID,$itemID){
		$resource 	= 	Mage::getSingleton('core/resource');
		$read 		= 	$resource->getConnection('core_read');		// reading the database resource
		$orderItemQuery	=	"SELECT `sales_flat_order_item`.* FROM `sales_flat_order_item` WHERE (`order_id`='".$orderID."')";
		//$orderItemQuery	=	"SELECT `sales_flat_order_item`.* FROM `sales_flat_order_item` WHERE (`order_id`='4552')";
		$orderItemResults		= $read->fetchAll($orderItemQuery);
		//echo '<pre>'; print_r($orderItemResults); die;
		$orderQuery		=	"SELECT increment_id FROM `sales_flat_order` where (`entity_id`='".$orderID."')";
		//echo $orderQuery; 
		$orderResults	= 	$read->fetchAll($orderQuery);
		$orderno		=	$orderResults[0]['increment_id'];
		if(count($orderItemResults) > 1){
			foreach($orderItemResults as $orderItem){
				//echo '<pre>'; print_r($orderItem); 
				$itemidArray[] 	= $orderItem['item_id'];
			}
			//echo $itemid;
			//echo '<pre>'; print_r($itemidArray); die;
			$key = array_search($itemID, $itemidArray)+1;
			//echo $key;
			$ordernoFile	=	$orderno.'_'.$key;
		}else{
			$ordernoFile	=	$orderno;	
		}
		//echo $ordernoFile; die;
		$backupfile = "/media/sales/orders/".$ordernoFile.".html";
		return $backupfile;
	}
	
	function getResizedUrl($imgUrl,$x,$y=NULL){
		$imgPath	=	$this->splitImageValue($imgUrl,"path");
		$imgName	=	$this->splitImageValue($imgUrl,"name");
	
		/**
		 * Path with Directory Seperator
		 */
		$imgPath	=	str_replace("/",DS,$imgPath);
		/**
		 * Absolute full path of Image
		 */
		$imgPathFull=	Mage::getBaseDir("media").DS.$imgPath.DS.$imgName;
		//echo 'imgPathFull->'.$imgPathFull.'<br>'; 
		/**
		 * If Y is not set set it to as X
		 */
		$widht		=	$x;
		$y?$height	=	$y:$height=$x;
	
		/**
		 * Resize folder is widthXheight
		 */
		$resizeFolder=	$widht."X".$height;
	
		/**
		 * Image resized path will then be
		 */
		$imageResizedPath	=	Mage::getBaseDir("media").DS.$imgPath.DS.$resizeFolder.DS.$imgName;
	
		/**
		 * First check in cache i.e image resized path
		 * If not in cache then create image of the width=X and height = Y
		 */
		if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
			$imageObj = new Varien_Image($imgPathFull);
			$imageObj->constrainOnly(TRUE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->resize($widht,$height);
			$imageObj->save($imageResizedPath);
		endif;
	
		/**
		 * Else image is in cache replace the Image Path with / for http path.
		 */
		$imgUrl	=	str_replace(DS,"/",$imgPath);
		//echo 'imgUrl->'.$imgUrl.'<br>';
		/**
		 * Return full http path of the image
		 */
		//echo Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;die;
		return Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;
	}
    
	function splitImageValue($imageValue,$attr="name"){
		//echo 'imageValue->'.$imageValue.'<br>';
		$imArray	=	explode("/",$imageValue);
		//echo '<pre>'; print_r($imArray).'<br>';
		
		$name	=	$imArray[count($imArray)-1];					//	Name of the image with extension
		$path	=	implode("/",array_diff($imArray,array($name)));	//	Folder path without image name
		//echo 'name->'.$name.'<br>'; 
		//echo 'path->'.$path.'<br>'; 
		if($attr=="path"){
			return $path;
		}
		else{
			return $name;
		}
	}
	
	// Function created by Vaseem to update the min price of configurable products based on its simple products min price
	public function updateMinPrice(){
		/*//	Getting list of all configurable products
		$collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
									->addAttributeToFilter('status', 1)
									->addAttributeToFilter('type_id', array('eq' => 'configurable'))
									//->addAttributeToFilter('entity_id', array('eq' => '20188'))
									->addAttributeToSort('entity_id', 'DESC')
									//->load(1);die;
									;
		$totalConfigurableProductsCount	=	count($collectionConfigurable);
		if($totalConfigurableProductsCount>0){
			$childIdsArray	=	array();
			foreach ($collectionConfigurable as $_configurableproduct) {
				$configurableProductId	=	$_configurableproduct->getId(); 
				$childIds 				= 	Mage::getModel('catalog/product_type_configurable')
											->getChildrenIds($configurableProductId);	//	Get child products id (only ids)
				$childIdsArray			=	$childIds[0];
	
				$noOfAssociateProducts	=	count($childIdsArray); 
				if($noOfAssociateProducts>1){
					$firstElement	=	array_shift($childIdsArray);
					array_unshift($childIdsArray,$firstElement);				// Push the element on top of array
					$childPrice		=	array();
					foreach($childIdsArray as $child){
						$_child = Mage::getModel('catalog/product')->load($child);
						$childPrice[] =  $_child->getPrice();
					}
					//echo '<pre>'; print_r($childPrice); die;
					$_minimalPriceValue	=	min($childPrice);
					$_maxPriceValue		=	max($childPrice);
					//	Lets update the minimum price in DB
					$resource 	= 	Mage::getSingleton('core/resource');
					$write 		= 	$resource->getConnection('core_write');		// reading the database resource
					
					$updatefieldsData 		= 	array('min_price' => $_minimalPriceValue,'max_price' => $_maxPriceValue);
					$updateWhereCondition	=	" entity_id = '".$configurableProductId."' ";
					if(	$write->update('catalog_product_index_price', $updatefieldsData, $updateWhereCondition)	){
						//echo 'Record updated successfully for configurable product ID - '.$configurableProductId.' with minimum price '.$_minimalPriceValue.'<br>';
					}else{
						//echo 'Record not updated for configurable product ID - '.$configurableProductId.'<br>';
					}
				}	//	end if
			}	//	end foreach
		}	//	end if*/
	}
	
	//	Function created by Vaseem to get the payment mode, this function is used in admin sales order grid
	//public function getPaymentMode($orderID,$productId)
	public function getPaymentMode($no_of_installment)
    {
		if($no_of_installment=='1'){
			$paymentMode='Full';
		}elseif($no_of_installment=='2'){
			$paymentMode='2 EMI';
		}elseif($no_of_installment=='3'){
			$paymentMode='3 EMI';	
		}else{
			$paymentMode='Not Available';	
		}
    	return $paymentMode;
		
		//echo 'orderID->'.$orderID;		echo 'productId->'.$productId;die;
		/*$resource 	= 	Mage::getSingleton('core/resource');
		$read 		= 	$resource->getConnection('core_read');		// reading the database resource
		$orderItemQuery	=	"SELECT `main_table`.quote_id, `sales_flat_order_item`.`sku` FROM `sales_flat_order` AS `main_table` INNER JOIN `sales_flat_order_item` ON main_table.entity_id = sales_flat_order_item.order_id WHERE  (main_table.entity_id='".$orderID."')";
		//echo $orderItemQuery;die;
		//$orderItemResults		= $read->fetchAll($orderItemQuery);
		$orderItemResults		= $read->fetchRow($orderItemQuery);
		//echo '<pre>'; print_r($orderItemResults); 
		if(count($orderItemResults) > 1){
			$quoteId 	= $orderItemResults['quote_id'];
		}
		//echo $quoteId;die;
		$paymentQuery	=	"SELECT no_of_installment,product_id from sales_flat_quote_item where (quote_id='".$quoteId."' AND product_id='".$productId."') ";	
		//$paymentQuery	=	"SELECT no_of_installment from sales_flat_quote_item where (quote_id='".$quoteId."')";	
		//echo $paymentQuery; die;
		$paymentResults		= $read->fetchRow($paymentQuery);
		//echo '<pre>'; print_r($paymentResults); die;
		if(count($paymentResults) > 0){
			$no_of_installment 	= $paymentResults['no_of_installment'];
			//echo $no_of_installment;die;
			if($no_of_installment=='1'){
				$paymentMode='Full';
			}elseif($no_of_installment=='2'){
				$paymentMode='2 EMI';
			}elseif($no_of_installment=='3'){
				$paymentMode='3 EMI';	
			}else{
				$paymentMode='Not Available';	
			}
		}
    	return $paymentMode;*/
    }
	
	
	//	Function created by Vaseem to get installment details, this function is used in invoice emails sends to customer
	public function getInstallmentsForEmailInvoice($orderID,$productId)
    {
		$resource 	= 	Mage::getSingleton('core/resource');
		$read 		= 	$resource->getConnection('core_read');		// reading the database resource
		$orderItemQuery	=	"SELECT easy_pay_installments from `sales_flat_order_item` WHERE (order_id = '".$orderID."' && product_id='".$productId."');";
		//$orderItemResults		= $read->fetchAll($orderItemQuery);
		$orderItemResults		= $read->fetchRow($orderItemQuery);
		
		if(count($orderItemResults) > 0){
			return $no_of_installment 	= $orderItemResults['easy_pay_installments'];
		}
    }
	
	//	Function created by Vaseem to get values of Angara Configuration Module saved in backend
	public function getDefaultCoupon()
    {
        return Mage::getStoreConfig('function/general/global_coupon_code');
    }
	
	public function getPromotionCoupon()
    {
        return Mage::getStoreConfig('function/general/promotion_coupon_code');
    }
	
	// Angara Modification - Anil Jain - 01-07-2013
	public function getAmazonCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/amazon_coupon_code');
    }
	
	public function getPricegrabberCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/pricegrabber_coupon_code');
    }
	
	public function getShoppingCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/shopping_coupon_code');
    }
	
	public function getMyshoppingCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/myshopping_coupon_code');
    }
	public function getBecomeCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/become_coupon_code');
    }
	public function getShopbotCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/shopbot_coupon_code');
    }
	public function getPinterestCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/pinterest_coupon_code');
    }
	public function getPrantoCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/pranto_coupon_code');
    }
	
	public function getGoogleCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/google_coupon_code');
    }
	public function getBingCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/bing_coupon_code');
    }
	public function getShopzillaCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/shopzilla_coupon_code');
    }
	public function getGetpriceCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/getprice_coupon_code');
    }
	public function getBizrateCustomerCoupon()
    {
        return Mage::getStoreConfig('function/general/bizrate_coupon_code');
    }
	// Angara Modification - Anil Jain - 01-07-2013
	
	//	Code Added by Vaseem for Email Marketing 
	public function getEmailCoupon()
    {
        return Mage::getStoreConfig('function/general/email_coupon_code');
    }
	public function getBlueDiamondRingsCoupon()
    {
        return Mage::getStoreConfig('function/general/blue_diamond_rings_coupon_code');
    }
	public function getBlueDiamondStudsCoupon()
    {
        return Mage::getStoreConfig('function/general/blue_diamond_studs_coupon_code');
    }
	public function getBlackDiamondRingsCoupon()
    {
        return Mage::getStoreConfig('function/general/black_diamond_rings_coupon_code');
    }
	public function getBlackDiamondStudsCoupon()
    {
        return Mage::getStoreConfig('function/general/black_diamond_studs_coupon_code');
    }
	public function getMoissaniteCoupon()
    {
        return Mage::getStoreConfig('function/general/moissanite_coupon_code');
    }
	public function getOnyxCoupon()
    {
        return Mage::getStoreConfig('function/general/onyx_coupon_code');
    }
	public function getGooglePaidCoupon()
    {
        return Mage::getStoreConfig('function/general/google_paid_coupon_code');
    }
	public function getMobileCseCoupon()
    {
        return Mage::getStoreConfig('function/general/mobile_cse_coupon_code');
    }
	public function getMobileGoogleCoupon()
    {
        return Mage::getStoreConfig('function/general/mobile_google_coupon_code');
    }
	public function getMobileBingCoupon()
    {
        return Mage::getStoreConfig('function/general/mobile_bing_coupon_code');
    }
	/*public function getReturningVisitorCoupon()
    {
        return Mage::getStoreConfig('function/general/returning_visitor_coupon_code');
    }*/
	/*public function getMobilePlaCoupon()
    {
        return Mage::getStoreConfig('function/general/mobile_pla_coupon_code');
    }*/
	//	Code Added by Vaseem for Email Marketing 
	
	public function getNotFoundURL()
    {
        return Mage::getStoreConfig('function/seo_redirect/textarea_not_found_url');
    }
	public function get410URL()
    {
        return Mage::getStoreConfig('function/seo_redirect/textarea_410_url');
    }
	public function get301URL()
    {
        //return Mage::getStoreConfig('function/seo_redirect/textarea_301_url');
		$string301	=	Mage::getStoreConfig('function/seo_redirect/textarea_301_url');
		$string301_1	=	Mage::getStoreConfig('function/seo_redirect/textarea_301_url_1');
		$string301_2	=	Mage::getStoreConfig('function/seo_redirect/textarea_301_url_2');
		//echo '<pre>'; print_r($string301);die;
		$final301String	=	$string301."\n".$string301_1."\n".$string301_2;
		//echo '<pre>'; print_r($final301String);
		return $final301String;
    }
	 
	//	Function to remove given html tags from html string
	function RssProperFormat($string,$arrayRemoveTags) 
	{		
		if( is_array($arrayRemoveTags) ){
			$totalTagsToRemove	=	count($arrayRemoveTags);
			foreach($arrayRemoveTags as $tag){
				$string = str_replace($tag, '', $string);		//	Removing given tag		
			}
			$string = str_replace('<>', '', $string);			//	Removing blank nested tags symbols	
			$string = str_replace('</>', '', $string);	
			$string = str_replace('&#8217;', "'", $string);
		}
		return $string;	
	}
	
	//	Function that will show last full word and trim the paragraph to given length
	//	$str	-	paragrapgh or string
	//	$maxlen	-	max length after which you want to trim the paragraph
	function truncateOnWord($str, $maxlen) 
	{
		if ( strlen($str) <= $maxlen ) return $str;
		$newstr = substr($str, 0, $maxlen);
		if ( substr($newstr,-1,1) != ' ' ) $newstr = substr($newstr, 0, strrpos($newstr, " ")).'...'; return $newstr;
	}
	
	//	Function to detect mobile users
	function isMobile() {
		$isMobile = (bool)preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'. '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'. '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
		return $isMobile;
	}
	
	/*
		return true if visitor is browsing using mac OS
	*/
	function isMac() {
		$userAgent = getenv("HTTP_USER_AGENT");
		if(strpos($userAgent, "Mac") !== FALSE){
			return true;
		}
	}
	
	/*
		return true if visitor is browsing using Safari browser
	*/
	function isMacSafari() {
		//Mage::log('user agent '.$_SERVER['HTTP_USER_AGENT'],null, 'agent.log', true);
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
			return true;
		}
	}
	
	//	Function to get mobile coupon code
	public function getMobileCoupon()
    {
        return Mage::getStoreConfig('function/general/mobile_coupon_code');
    }
	
	public function getDiscountType1Text()
    {
        return Mage::getStoreConfig('function/general/textarea_discount_type1');
    }
	
	//	Function to return modified category name('s) on catalog page
	public function getModifiedCategoryName($_category)
    {
		$categoryID	=	$_category->getId();
		$categoryNameArray	=	array( '297'=>'Mother\'s Earrings' , '271'=>'Mother\'s Rings' , '296'=>'Mother\'s Necklace Pendants' , '95'=>'Valentine\'s Day Gifts' , '105'=>'St Patrick\'s Gifts');
		$categoryName	=	$categoryNameArray[$categoryID];
		if($categoryName==''){
			$categoryName	=	$_category->getName();
		}
		return $categoryName;
    }
	
	public function getTimerBasedPromotionStatus()
    {
        return Mage::getStoreConfig('function/angara_promotion/timer_based_promotion_status');
    }
	
	public function getPromotionTime()
    {
        return Mage::getStoreConfig('function/angara_promotion/promotion_time');
    }
	
	public function getNoCouponRuleId()
    {
        return Mage::getStoreConfig('function/angara_promotion/no_coupon_rule_id');
    }
	
	public function getHappyHoursTimerText()
    {
        return Mage::getStoreConfig('function/angara_promotion/happyhours_timer_text');
    }
	
	public function showTimer($timerExpireMinutes = 20)
    {
		//	Get time when user first open the page
		$currentTimestamp 	= 	Mage::getModel('core/date')->timestamp(time()); 
		$currentTime 		= 	date('Y-m-d h:i:s', $currentTimestamp);
		if(!isset($_SESSION['serverTime'])){	
			$_SESSION['serverTime']	=	$currentTime;
			$startTime = $currentTime;
		}else{
			$startTime = $_SESSION['serverTime'];	
		}
		$differenceInSeconds 		= 	abs(strtotime($currentTime) - strtotime($startTime));
		//$differenceInMins 			= 	(strtotime($currentTime) - strtotime($startTime))/60;
		$millisecondsToShowTimer	=	$timerExpireMinutes * 60;		//	20 mins * 60 seconds
		$totalRemainingMiliSeconds	=	($millisecondsToShowTimer - $differenceInSeconds)*1000;
		
		/*echo '<br>startTime->'.$startTime;
		echo '<br>currentTime->'.$currentTime;
		echo '<br>differenceInSeconds->'.$differenceInSeconds;
		//echo '<br>differenceInMins->'.$differenceInMins;
		echo '<br>totalRemainingMiliSeconds->'.$totalRemainingMiliSeconds;*/

		
		return $totalRemainingMiliSeconds;
    }
	
	//	Show happy hours timer at product page
	public function showTimerPP()
    {
		$currentServerTime 		= 	Mage::getModel('core/date')->date('H:i:s');			//	17:24:51
		//echo '<br>currentServerTime->'.$currentServerTime;
		$happyHoursEndTime		=	$this->happyHoursEndTime();
		//echo '<br>happyHoursEndTime->'.$happyHoursEndTime;
		
		/*$currentTimestamp 	= 	Mage::getModel('core/date')->timestamp(time()); 
		$currentTime 		= 	date('h:i:s', $currentTimestamp);
		if(!isset($_SESSION['serverTimePP'])){	
			$_SESSION['serverTimePP']	=	$currentTime;
			$startTime = $currentTime;
		}else{
			$startTime = $_SESSION['serverTimePP'];	
		}*/
		
		$startTime 		= 	$currentServerTime;
		$currentTime 	= 	$happyHoursEndTime;
		//echo '<br>startTime->'.$startTime;
		//echo '<br>currentTime->'.$currentTime;//die;
		
		$differenceInSeconds 		= 	abs(strtotime($currentTime) - strtotime($startTime));
		//$differenceInMins 			= 	(strtotime($currentTime) - strtotime($startTime))/60;
		//$millisecondsToShowTimer	=	$timerExpireMinutes * 60;		//	20 mins * 60 seconds
		//$totalRemainingMiliSeconds	=	($millisecondsToShowTimer - $differenceInSeconds)*1000;
		$totalRemainingMiliSeconds	=	$differenceInSeconds*1000;
		
		//echo '<br>differenceInSeconds->'.$differenceInSeconds;
		//echo '<br>differenceInMins->'.$differenceInMins;*/
		//echo '<br>totalRemainingMiliSeconds->'.$totalRemainingMiliSeconds;die;
		
		return $totalRemainingMiliSeconds;
    }
	
	//	Function to check if 20 mins promotion will apply in cart or not
	public function getExtraDiscount()
    {
		/*$currentDateTime 		= 	Mage::getModel('core/date')->date('Y-m-d H:i:s');
		//echo '<br>currentDateTime->'.$currentDateTime;
		$currentDateTimeArray	=	explode(' ',$currentDateTime);
		$currentTime			=	strtotime($currentDateTimeArray[1]);
		$happyHoursStartTime	=	strtotime($this->happyHoursStartTime());
		$happyHoursEndTime		=	strtotime($this->happyHoursEndTime());
		/*echo '<br>currentTime->'.$currentTime;
		echo '<br>happyHoursStartTime->'.$happyHoursStartTime;
		echo '<br>happyHoursEndTime->'.$happyHoursEndTime;die;*/
		/*if( $happyHoursStartTime < $currentTime && $happyHoursEndTime > $currentTime ){
			
			$currentUrl 	= 	Mage::helper('core/url')->getCurrentUrl();
			if(!strstr($currentUrl,'country/switch')){
				//	Check cart amount total
				$cartGrandTotalWithoutEasyPay	=	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
				//echo '<br>cartGrandTotalWithoutEasyPay->'.$cartGrandTotalWithoutEasyPay;
				
				// retrieve quote items collection
				$itemsCollection 				= 	Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
				// get array of all items what can be display directly
				$itemsVisible 					= 	Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
				// retrieve quote items array
				$items 							= 	Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
				//echo '<pre>';print_r($items->getData());die;
				$jewelryAppraisalSku	=	1;
				$emiProduct				=	0;
				foreach($items as $item) {
					//	Check if there is any product with Easy Pay Installment(s)
					$cartItemDetail = $item->getBuyRequest()->getData();					
					//echo '<pre>';print_r($cartItemDetail);echo '</pre>';
					if(isset($cartItemDetail['easyopt'])){
						list($no_of_installment,$installment_amount) = explode('_',$cartItemDetail['easyopt']);
						if($no_of_installment > 0){
							$easypay_flag 	= 	1;
							$emiProduct		=	1;
							//return false;
						}else{
							$easypay_flag = 0;	
						}
					}else{
						$easypay_flag = 0;	
					}
					//	Check if Jewelry Appraisal Sku is in the cart
					if($item->getSku() == 'JA0050'){
						$jewelryAppraisalSku	=	1;
						$cartGrandTotalWithoutEasyPay	=	$cartGrandTotalWithoutEasyPay - $item->getPrice();
					}
				}
				/*if($easypay_flag==0){
					return true;
				}*/
				//echo '<br>cartGrandTotalWithoutEasyPay->'.$cartGrandTotalWithoutEasyPay;
		
				/*if( $this->getTimerBasedPromotionStatus() && $cartGrandTotalWithoutEasyPay > 500 && $emiProduct==0){
					//	Check time condition to apply special discount in cart
					$timeContition	=	$this->showTimer($this->getPromotionTime());
					if($timeContition > 0 ){
						return 1;
					}else{
						return 0;		
					}
				}else{
					return 0;	
				}
			}
		}else{
			return 0;	
		}*/
	}

	public function getCustomerCoupon(){
		$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		$hasCustomProduct = false;
		foreach ($items as $item) {
			if (stripos($item->getProduct()->getSku(), 'ANGCP') !== false) {
				$hasCustomProduct = true;
				break;
			}
		}
		$isMobile = $this->isMobile();
		
		if($hasCustomProduct){
			$autoapply_coupon = "CUSTOMORDER";
		}
		else if(Mage::getSingleton("checkout/session")->getData("offer_code")){
			$autoapply_coupon = Mage::getSingleton("checkout/session")->getData("offer_code");
		}
		else if(Mage::getModel('core/cookie')->get('bannertitlecookie')){
			$autoapply_coupon = $this->getGoogleCustomerCoupon();		
		}
		else if($isMobile){	//	Mobile browser
			$autoapply_coupon = $this->getMobileCoupon();	
		}
		else if(Mage::getSingleton('core/session')->getBannertitleSession()){
			switch (Mage::getSingleton('core/session')->getBannertitleSession()) {
				case 'pricegrabber':
					$autoapply_coupon = $this->getPricegrabberCustomerCoupon();
					break;
				case 'shopping':
					$autoapply_coupon = $this->getShoppingCustomerCoupon();
					break;
				case 'myshopping':
					$autoapply_coupon = $this->getMyshoppingCustomerCoupon();
					break;
				case 'pronto':
					$autoapply_coupon = $this->getPrantoCustomerCoupon();
					break;
				case 'amazon':
					$autoapply_coupon = $this->getAmazonCustomerCoupon();
					break;
				case 'becomeshopper':
					$autoapply_coupon = $this->getBecomeCustomerCoupon();
					break;
				case 'shopshopper':
					$autoapply_coupon = $this->getShopbotCustomerCoupon();
					break;
				case 'pinterest':
					$autoapply_coupon = $this->getPinterestCustomerCoupon();
					break;	
				case 'blue_diamond_rings':
					$autoapply_coupon = $this->getBlueDiamondRingsCoupon();
					break;								
				case 'blue_diamond_studs':
					$autoapply_coupon = $this->getBlueDiamondStudsCoupon();
					break;								
				case 'black_diamond_rings':
					$autoapply_coupon = $this->getBlackDiamondRingsCoupon();
					break;								
				case 'black_diamond_studs':
					$autoapply_coupon = $this->getBlackDiamondStudsCoupon();
					break;	
				case 'moissanite':
					$autoapply_coupon = $this->getMoissaniteCoupon();
					break;
				case 'onyx':
					$autoapply_coupon = $this->getOnyxCoupon();
					break;
				case 'google_paid':
					$autoapply_coupon = $this->getGooglePaidCoupon();
					break;
				case 'bing':
					$autoapply_coupon = $this->getBingCustomerCoupon();
					break;	
				case 'getprice':
					$autoapply_coupon = $this->getGetpriceCustomerCoupon();
					break;	
				case 'shopzilla':
					$autoapply_coupon = $this->getShopzillaCustomerCoupon();
					break;	
				case 'bizrate':
					$autoapply_coupon = $this->getBizrateCustomerCoupon();
					break;
				/*case 'returning_visitor':
					$autoapply_coupon = $this->getReturningVisitorCoupon();
					break;	*/	
				case 'mobile_cse':
					$autoapply_coupon = $this->getMobileCseCoupon();
					break;
				/*case 'mobile-pla':
					$autoapply_coupon = $this->getMobilePlaCoupon();
					break;*/
				case 'mobile_google':
					$autoapply_coupon = $this->getMobileGoogleCoupon();
					break;
				case 'mobile_bing':
					$autoapply_coupon = $this->getMobileBingCoupon();
					break;						
			}
		}else{
			$autoapply_coupon = $this->getDefaultCoupon();	
		}
		/*else{
			if($autoapply_coupon == ''){
				//	Check if user is a mobile theme or mobile user
				$isMobile = $this->isMobile();
				if($isMobile){	//	Mobile browser
					$autoapply_coupon = $this->getMobileCoupon();	
				}else{
					$autoapply_coupon = $this->getDefaultCoupon();	
				}
			}
		}*/
	return $autoapply_coupon;	
	}
	
	public function iPadPromotionStatus(){
		$ipadSku	=	'FP1003SD_VB';
		//$ipadSku	=	'FR00023S';
		$_product	=	Mage::getModel('catalog/product')->loadByAttribute('sku',$ipadSku); 
		//echo $_product->isSaleable();
		//echo '<pre>'; print_r($_product); 
		
		if ($_product && $_product->isSaleable()) {
			return true;
		}else{
			return false;
		}
	}
	
	public function checkAdmin($userId){
		$role_data = Mage::getModel('admin/user')->load($userId)->getRole()->getData();
		//var_dump($role_data);
		if($role_data['role_name']=='Administrators'){
			return true;	
		}
	}
	
	public function writeTextOnImage($imageType, $image_path, $text, $newPath, $font)
	{
		switch ($imageType) {
			case "jpg":
			case "jpeg":
				header("Content Type: image/jpeg");
				$img   = imagecreatefromjpeg($image_path);
				$grey  = imagecolorallocate($img, 108, 108, 108);
				
				imagettftext($img, 24, 45, 75, 250, $grey, $font, $text);
				imagejpeg($img, $newPath);
				break;
			
			case "png";
				header("Content Type: image/png");
				$img = imagecreatefrompng($image_path);
				$grey  = imagecolorallocate($img, 128, 128, 128);
				
				imagettftext($img, 12, 10, 61, 111, $grey, $font, $text);
				imagepng($img, $newPath);
				break;
		}
	}
	
	public function getHappyHoursStatus()
    {
		return Mage::getStoreConfig('function/angara_happyhours/happyhours_status');
	}
	
	public function happyHoursStartTime()
    {
		return str_replace(',',':',Mage::getStoreConfig('function/angara_happyhours/happyhours_start_time'));
	}
	
	public function happyHoursEndTime()
    {
		return str_replace(',',':',Mage::getStoreConfig('function/angara_happyhours/happyhours_end_time'));
	}
	
	public function happyHoursSku()
    {
		return Mage::getStoreConfig('function/angara_happyhours/happyhours_free_sku');
	}
	
	public function happyHoursSkuText()
    {
		return Mage::getStoreConfig('function/angara_happyhours/happyhours_free_sku_text');
	}
	
	//	Function to check if Happy Hours will work or not
	public function getHappyHours()
    {
		//	Get time when user first open the page
		$currentDateTime 		= 	Mage::getModel('core/date')->date('Y-m-d H:i:s');
		//echo '<br>currentDateTime->'.$currentDateTime;
		$currentDateTimeArray	=	explode(' ',$currentDateTime);
		$currentTime			=	strtotime($currentDateTimeArray[1]);
		$happyHoursStartTime	=	strtotime($this->happyHoursStartTime());
		$happyHoursEndTime		=	strtotime($this->happyHoursEndTime());
		//echo '<br>currentTime->'.$currentTime;
		//echo '<br>happyHoursStartTime->'.$happyHoursStartTime;
		//echo '<br>happyHoursEndTime->'.$happyHoursEndTime;
		if( $happyHoursStartTime < $currentTime && $happyHoursEndTime > $currentTime ){
			if( $this->getHappyHoursStatus() ){
				return 1;
			}elseif( $this->getTimerBasedPromotionStatus() ){
				return 2;
			}
		}
	}
	
	//	Function to get the minimum price of products in particular category
	public function getCategoryMinPrice($categoryID)
    {
		$categoryModel 	= Mage::getModel('catalog/category')->load($categoryID); 
		$productColl 	= Mage::getModel('catalog/product')->getCollection()
							->addCategoryFilter($categoryModel)
							->addAttributeToSort('price', 'asc')
							->setPageSize(1)
							->load();
		return $lowestProductPrice = $productColl->getFirstItem()->getPrice();
	}
	
	//	Function to get the maximum price of products in particular category
	public function getCategoryMaxPrice($categoryID)
    {
		$categoryModel 	= Mage::getModel('catalog/category')->load($categoryID); 
		$productColl 	= Mage::getModel('catalog/product')->getCollection()
							->addCategoryFilter($categoryModel)
							->addAttributeToSort('price', 'desc')
							->setPageSize(1)
							->load();
		return $highestProductPrice = $productColl->getFirstItem()->getPrice();
	}
	
	//	Function to format the product price
	public function formatPrice($price)
    {
		return Mage::helper('core')->currency($price);
	}
	
	//	Function to get the latest post from blog.angara.com
	public function getDataUsingCurl($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	//	Function to get the related links in footer
	public function getRelatedLinks($categoryID) {
		$getDefaultRelatedLinksData		=	Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('related_links')->toHtml();
		$getRelatedLinksData			=	Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('related_links_'.$categoryID)->toHtml();
		if($getRelatedLinksData){
			return $getRelatedLinksData;	
		}else{
			return $getDefaultRelatedLinksData;	
		}
	}
	
	//	simple function to send email
	public function sendEmail( $emailSubject, $emailHtmlContent , $to = 'vaseem.ansari@angara.com', $name = 'Vaseem Angara') {
		$mail = Mage::getModel('core/email');
		$mail->setToName($name);
		$mail->setToEmail($to);
		//$mail->setToEmail('customer.service@angara.com;allam.ramesh@angara.com;nancy.farinas@angara.com');
		$mail->setBody($emailHtmlContent);
		$mail->setSubject($emailSubject);
		$mail->setFromEmail('noreply@angara.com');
		$mail->setFromName("No Reply Angara");
		$mail->setType('html');// can use Html or text as Mail format
		//echo $html;
		try {
			$mail->send();
			if($emailSubject != 'Shipping Method'){
				echo '<span style="color:#070;font:12px/18px Arial, Helvetica, sans-serif;">Thank you. An email will be send to vaseem.ansari@angara.com with the content.</span>';
			}
		}
		catch (Exception $e) {
			echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">Error in sending email.</span>';
		}
	}
	
	//	S:VA	Create by Vaseem to show banners at catalog pages
	public function calculateBannerPositioning ( $banners ) {
		$categoryID = Mage::registry('current_category')->getId();
		$bannerPositioning	=	array(
								//		Sapphire Rings (ID: 33)				-	last row before load more option
								'33' => array('banner1' => array('p' => 3, 'position' => '2'),	
											  'banner2' => array('p' => 3, 'position' => '10')
										),
								//		Ruby Rings (ID: 34)					-	second page, first row.
								'34' => array('banner1' => array('p' => 4, 'position' => '2'),	
											  'banner2' => array('p' => 4, 'position' => '10')
										),		//		Ruby Rings (ID: 34)					-	second page, first row.
								//		Tanzanite Rings (ID: 36)			- 9th row, position 1 and 2.
								'36' => array('banner1' => array('p' => 3, 'position' => '6'),	
											  'banner2' => array('p' => 3, 'position' => '10')
										),		
								//		Sapphire Necklace Pendants (ID: 40)	- Row 5 , position 2 and 3.
								'40' => array('banner1' => array('p' => 2, 'position' => '6'),	
											  'banner2' => array('p' => 2, 'position' => '10')
										),		
								//		Ruby Necklace Pendants (ID: 41)		- 9th row, position 3 and 4.
								'41' => array('banner1' => array('p' => 3, 'position' => '2'),	
											  'banner2' => array('p' => 3, 'position' => '10')
										),	
								//		Tanzanite Necklace Pendants (ID: 43)- 6th row, position 2 and 3.
								'43' => array('banner1' => array('p' => 2, 'position' => '2'),	
											  'banner2' => array('p' => 2, 'position' => '10')
										),		
								//		Engagement Rings (ID: 5)			- 9th row, position 3 and 4.
								'5'  => array('banner1' => array('p' => 3, 'position' => '2'),	
											  'banner2' => array('p' => 3, 'position' => '10')
										),
								//		Sapphire Engagement Rings (ID: 78)	- Row 8, position 3 and 4
								'78'  => array('banner1' => array('p' => 3, 'position' => '6'),	
											   'banner2' => array('p' => 3, 'position' => '10')
										),
								//		Ruby Engagement Rings (ID: 79)		-	Row 10, position 2 and 3
								'79'  => array('banner1' => array('p' => 4, 'position' => '2'),	
										  	   'banner2' => array('p' => 4, 'position' => '10')
										),
								//		Tanzanite Engagement Rings (ID: 81)	-	Row 7 , position 3 and 4
								'81'  => array('banner1' => array('p' => 3, 'position' => '2'),	
									  		   'banner2' => array('p' => 3, 'position' => '10')
										),
								//		Natural Gemstone Jewelry (ID: 3)	-	Row 7 , position 3 and 4
								'3'  => array('banner1' => array('p' => '', 'position' => '6'),	
									  		  'banner2' => array('p' => '', 'position' => '10'),
											  'banner3' => array('p' => 2, 'position' => '6'),
											  'banner4' => array('p' => 2, 'position' => '10')
										)
								);
		return $bannerPositioning[$categoryID];
	}
		
	public function formatBannerHtml ( $banner , $i) {		//	$i = $whichBanner
		//$bannerId			=	$banner[$j]['id'];
		$bannerTitle		=	$banner[$i]['title'];
		$bannerImage		=	$banner[$i]['filename'];
		$bannerImageType	=	$banner[$i]['image_type'];
		$bannerImagePosition=	$banner[$i]['image_position'];
		$bannerImageAlt		=	$banner[$i]['image_alt'];
		$bannerContent		=	$banner[$i]['content'];
		//$bannerWeblink		=	Mage::getBaseUrl().$banner[$i]['weblink'];
		$bannerWeblink		=	$banner[$i]['weblink'];
		$bannerHtml		=	'<div class="col-md-6 hidden-sm col-xs-12 max-margin-bottom catalog-banner"><a href="'.$bannerWeblink.'"><img class="img-responsive" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$bannerImage.'" title="'.$bannerTitle.'" alt="'.$bannerImageAlt.'"/></a></div>';
		return $bannerHtml;
	}
	
	//	Function to return 1 if user agent is iPad
	public function isIPad(){
		return (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad'); 			
	}
	
	
	public function soldOutSku(){
		return $soldOutSkuList		=	explode(',',Mage::getStoreConfig('function/angara_sold_out/sold_out_skus'));
	}
	
	/*
	<!--more-->
	Showing the read more link
	*/
	public function readMore($content){
		if(!strpos($content, '<!--more-->')){
			return $content;	
		}else{
			$readMore		=	' <a href="javascript:void(0)" class="read_more fontcolor-type1">Read More</a><span class="hidden" id="category_read_more">';
			$suffixContent	=	'</span><a href="#" class="hidden read_less fontcolor-type1">Read Less</a>';
			$myContent		=	str_replace('<!--more-->',$readMore,$content).$suffixContent;
			return $myContent;
		}
	}
	
	/*
		//	S:VA	
		//	Function to show the sorted products from category
		//	Calling	-	$_products = $this->getProductsFromCategory('71');
	*/
	public function getProductsFromCategory($categoryId, $noOfProducts=10){
		$_category 			= 	Mage::getModel('catalog/category')->load($categoryId); 		//	Where $category_id is the id of the category
		$collection 		= 	Mage::getResourceModel('catalog/product_collection');
		$collection->addCategoryFilter($_category); 										// 	Category filter
		$collection->addAttributeToFilter('status',1); 										// 	Include only enabled product
		$collection->addAttributeToFilter('visibility', 4);									// 	catalog, search
		$collection->addAttributeToSelect(array('name','short_description','url','small_image','price')); 		// 	Add product attribute to be fetched
		$collection->addAttributeToSort('cat_index_position', 'DESC');						//	Order by entity_id
		$limit 			= $noOfProducts;	
		$starting_from 	= '0';
		$collection->getSelect()->limit($limit,$starting_from);
		//echo '<pre>';print_r($collection->getData());die;
		$totalProducts		=	count($collection);
		if($totalProducts > 1){
			return $collection;
		}
	}
	
	/*
		@return string as a no of days for free return policy
	*/
	public function freeReturnDays(){
		return Mage::helper('flashsale')->getFreeReturnDays();
	}
	
	/*
		@return page type 
	*/
	public function getPageType(){
		$request        = 	Mage::app()->getRequest();
		$moduleName     = 	$request->getModuleName();
		$controllerName = 	$request->getControllerName();
		$actionName     = 	$request->getActionName();
		$currentUrl		=	Mage::app()->getRequest()->getServer('HTTP_REFERER');
		//echo 'moduleName-> '.$moduleName.' controllerName-> '.$controllerName.' actionName-> '.$actionName.' currentUrl-> '.$currentUrl;
		if( $moduleName=='cms' && $controllerName=='index' && $actionName=='index' ){
			return 'home';
		}elseif ($moduleName == 'checkout' || $controllerName == 'cart') {
			return 'cart';
		}elseif( ($moduleName == 'onepagecheckout' && $controllerName == 'index' && $actionName =='success') || ($moduleName == 'checkout' && $controllerName == 'onepage' && $actionName =='success') ){
			return 'success';
		}elseif ($moduleName == 'checkout' || $moduleName == 'onepagecheckout' || stristr($currentUrl, 'onepagecheckout')) {
			return 'checkout';
		}elseif( $moduleName=='cms' && $controllerName=='page' && $actionName=='view' ){
			return 'cms';
		}elseif($product = Mage::registry('current_product')){
			return 'product';
		}elseif($product = Mage::registry('current_category')){
			return 'category';
		}elseif($moduleName == 'catalogsearch'){
			return 'catalogsearch';
		}elseif($moduleName == 'amazon_payments'){
			return 'amazon_payments';
		}
	}
	
	/*
		returns the current date time of server (this time will be same as it is showing in admin footer/the time we receive orders)
	*/
	public function getCurrentServerDateTime() {
		return $date 	= 	Mage::getModel('core/date')->gmtDate();
	}
	
	/*
		returns the current date of server
	*/
	public function getCurrentServerDate() {
		$temp	= 	Mage::getModel('core/date')->gmtDate();
		$date	=	explode(' ',$temp);
		return $date[0];
	}
	
	/*
		returns the current date time of server for cron functions (this time will be same as it is showing in admin footer/the time we receive orders)
	*/
	public function getCurrentServerDateTimeForCron() {
		$date	=	Mage::getModel('core/date')->date('Y-m-d');
		$time	=	Mage::app()->getLocale()->storeDate(Mage::app()->getStore(), null, true)->toString('HH:mm:ss');
		return $date.' '.$time;	
	}
	
	/*
		Check if we can run the cron function
	*/
	public function canRunCron($cronName = 'cron'){
		$todayDateDB	= 	$this->getCurrentServerDateTime();			//	this will o/p date time same as db created_at
		$todayDate 		= 	$this->getCurrentServerDateTimeForCron();	//	'2015-12-10 16:11:35';
		$date 			= 	strtotime($todayDate);
		$currentHour	=	date('H', $date);
		
		if($currentHour=='00'){
			$logMsg		=	$cronName." cron running between 12 to 1 AM. The admin orders time is ".$todayDate." and DB created_at time is ".$todayDateDB;
			Mage::log($logMsg, null, 'cron.log', true);	
			return true;
		}/*elseif($currentHour=='04'){
			$logMsg		=	$cronName." cron running between 4 to 5 AM. The admin orders time is ".$todayDate." and DB created_at time is ".$todayDateDB;
			Mage::log($logMsg, null, 'cron.log', true);	
			return true;
		}*/else{
			$logMsg		=	$cronName." cron not running at ".$todayDate;
			Mage::log($logMsg, null, 'cron.log', true);	
			return false;
		}
	}
	
	/*
		returns the ticker html for home page
	*/
	public function getHomePageTickerHtml($bannerTime){
		$startTime 			= 	$this->getCurrentServerDateTime();		//	'2015-12-10 16:11:35';
		$endTimeTemp		= 	date("Y-m-d h:i:s", strtotime($bannerTime));
		$endTime 			= 	date("Y-m-d h:i:s A", strtotime($bannerTime));			
				
		/*echo '<br>startTime->'.$startTime;
		echo '<br>endTime->'.$endTimeTemp;die;*/
		if(strtotime($endTimeTemp) < strtotime($startTime)){
			return false;
		}
		$start_date 		= 	new DateTime($startTime);
		$since_start 		= 	$start_date->diff(new DateTime($endTime));
		
		$remainingDays		=	$since_start->d;
		$remainingHours		=	$since_start->h;
		$remainingMins		=	$since_start->i;
		$remainingSeconds	=	$since_start->s;
		$randomId			=	rand(0,9999);
		$remainingMS		=	($remainingDays * 24 * 60 * 60 * 1000) + ($remainingHours * 60 * 60 * 1000) + ($remainingMins * 60 * 1000) + ($remainingSeconds * 1000);	//	MiliSeconds
		return "<script>showtimer($remainingMS, 'flashdealtimer_$randomId', 'hidediv_$randomId')</script>
		<div id='hidediv_$randomId' class='tickr-holder'><div class='tickr-width'><div  class='tickr-style'><span class='ofrend'>Offer Ends In</span><span class='text-format min-padding-left' id='flashdealtimer_$randomId'></span><span><span class='hrs'>HRS</span><span class='min'>MINS</span><span class='sec'>SECS</span></span></div></div></div>";
	}
	
	/*
		returns the timer html for deal of the day- it shows the timer for a single day only
	*/
	public function getOfferCatalogTickerHtml(){
		$startTime 			= 	$this->getCurrentServerDateTime();				//	'2015-12-10 16:11:35';
		$endTime 			= 	$this->getCurrentServerDate . ' '. '23:59:59';	// date("Y-m-d h:i:s A", strtotime($bannerTime));			
				
		/*echo '<br>startTime->'.$startTime;
		echo '<br>endTime->'.$endTime;*/
		if(strtotime($endTime) < strtotime($startTime)){
			return false;
		}
		$start_date 		= 	new DateTime($startTime);
		$since_start 		= 	$start_date->diff(new DateTime($endTime));
		
		$remainingDays		=	$since_start->d;
		$remainingHours		=	$since_start->h;
		$remainingMins		=	$since_start->i;
		$remainingSeconds	=	$since_start->s;
		$randomId			=	rand(0,9999);
		$remainingMS		=	($remainingDays * 24 * 60 * 60 * 1000) + ($remainingHours * 60 * 60 * 1000) + ($remainingMins * 60 * 1000) + ($remainingSeconds * 1000);	//	MiliSeconds
		return "<script>showtimer($remainingMS, 'flashdealtimer_$randomId', 'hidediv_$randomId')</script>
		<div id='hidediv_$randomId' class='tickr-holder'><div class='tickr-width'><div  class='tickr-style'><span class='ofrend'>Offer Ends In</span><span class='text-format min-padding-left' id='flashdealtimer_$randomId'></span><span><span class='hrs'>HRS</span><span class='min'>MINS</span><span class='sec'>SECS</span></span></div></div></div>";
	}
	
	/*
		returns the array of available shipping methods
	*/
	public function getAvailableShippingMethods() {
		//		Check max want it date
		//$wantIt	=	$this->getMaxWantItDate();
		//echo $wantIt;die;
		
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
		foreach($methods as $_ccode => $_carrier){
			//$_methodOptions = array();
			if($_methods = $_carrier->getAllowedMethods()){
				/*foreach($_methods as $_mcode => $_method){
					$_code = $_ccode . '_' . $_mcode;
					$_methodOptions[] = array('value' => $_code, 'label' => $_method);
				}*/
	
				//if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
					//$_title = $_ccode;

				$active		=	Mage::getStoreConfig("carriers/$_ccode/active");
				if($active){
					$subTotalWithoutEasyEmi	= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
					$freeShippingPrice		=	Mage::getStoreConfig("carriers/$_ccode/min_cart_value_for_free_shipping");
					if( $freeShippingPrice>0 && $subTotalWithoutEasyEmi > $freeShippingPrice ){
						$_title 	= 	'Free '.Mage::getStoreConfig("carriers/$_ccode/title");
						$price		=	0;
					}else{
						$_title 	= 	Mage::getStoreConfig("carriers/$_ccode/title");
						$price		=	Mage::getStoreConfig("carriers/$_ccode/price");
					}
					$sortOrder	=	Mage::getStoreConfig("carriers/$_ccode/sort_order");
					$_titleToDisplay	=	$_title;
					if($price > 0){
						$ang2dayObj=Mage::getModel('ang2dayflatrate/carrier_ang2dayflatrate');
						
						if($ang2dayObj->getEmailFreeShipping() && $_ccode=='ang2dayflatrate'){
							$price=0;
							$_titleToDisplay	=	'Free '.$_titleToDisplay;
						}
						else{
							/* s: code for free shipping bound with coupon. */
							$couponCode = Mage::getSingleton('core/session')->getPromotionCode();
							$couponShipping = $this->getCouponShipping($couponCode);
							/* e: code for free shipping bound with coupon. */
							if($couponShipping && $this->shippingShortForm($couponShipping) == $_ccode){
								$price = 0;
								$_titleToDisplay = 'Free '.$_titleToDisplay;
							}
							else{
								$_titleToDisplay	=	$_titleToDisplay .' $'.$price;
							}	
						}	
					}
					//	S:VA	Add Shipping Date
					//$_titleToDisplay	=	'<span class="title">'.$_titleToDisplay.'</span><br><span>Estimated Delivery '.$this->shippingDate($wantIt, $_ccode).'</span>';
					$_titleToDisplay	=	'<span class="title">'.$_titleToDisplay.'</span>';
					//	Showing Priority Shipping Method at top based on cart total
					/* if($_ccode=='angovernightflatrate' && $subTotalWithoutEasyEmi > $freeShippingPrice){
						$shippingMethods[0]	=	array('code' => $_ccode.'_'.$_ccode, 'title' => $_titleToDisplay);
					}else{ */
						$shippingMethods[$sortOrder]	=	array('code' => $_ccode.'_'.$_ccode, 'title' => $_titleToDisplay);
					//}
				}
				//$options[] = array('value' => $_methodOptions, 'label' => $_title);
			}
		}
		ksort($shippingMethods);
		return $shippingMethods;
	}
	
	/*
		returns the array of available shipping methods
	*/
	public function getDefaultShippingMethods() {
		//		Check max want it date
		$wantIt	=	$this->getMaxWantItDate();
		//echo $wantIt;die;
		
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
		foreach($methods as $_ccode => $_carrier){
			//$_methodOptions = array();
			if($_methods = $_carrier->getAllowedMethods()){
				$active		=	Mage::getStoreConfig("carriers/$_ccode/active");
				if($active){
					$subTotalWithoutEasyEmi	= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
					$freeShippingPrice		=	Mage::getStoreConfig("carriers/$_ccode/min_cart_value_for_free_shipping");
					if( $freeShippingPrice>0 && $subTotalWithoutEasyEmi > $freeShippingPrice ){
						$_title 	= 	'Free '.Mage::getStoreConfig("carriers/$_ccode/title");
						$price		=	0;
					}else{
						$_title 	= 	Mage::getStoreConfig("carriers/$_ccode/title");
						$price		=	Mage::getStoreConfig("carriers/$_ccode/price");
					}
					$sortOrder	=	Mage::getStoreConfig("carriers/$_ccode/sort_order");
					$_titleToDisplay	=	$_title;
					if($price > 0){
						$_titleToDisplay	=	$_titleToDisplay;
					}
					//	S:VA	Add Shipping Date
					$_titleToDisplay	=	$_titleToDisplay;
					//	Showing Priority Shipping Method at top based on cart total
					/* if($_ccode=='angovernightflatrate' && $subTotalWithoutEasyEmi > $freeShippingPrice){
						$shippingMethods[0]	=	array('code' => $_ccode.'_'.$_ccode, 'title' => $_titleToDisplay);
					}else{ */
						$shippingMethods[$sortOrder]	=	array('code' => $_ccode.'_'.$_ccode, 'title' => $_titleToDisplay);
					//}
					$_priceToDisplay = $price;
					$shippingMethods[$sortOrder]	=	array('code' => $_ccode.'_'.$_ccode, 'title' => $_titleToDisplay, 'price' => $_priceToDisplay);
				}
				//$options[] = array('value' => $_methodOptions, 'label' => $_title);
			}
		}
		ksort($shippingMethods);
		return $shippingMethods;
	}
	
	/*
		get max vendor lead time based on the products in cart
	*/
	public function getMaxWantItDate(){
		$quote = Mage::helper('checkout')->getQuote();
		//$extraDays = 0;		$arriveDays = 0;	$jaProduct=0;
		foreach ($quote->getAllVisibleItems() as $_item) {
			$extraDays = 0;		$arriveDays = 0;
			//	don't consider products with price 0 and skin loop
			if($_item->getPrice() <= 0){
				continue;
			}
			$realChildSku		=	$this->getRealChildSku($_item->getSku());
			
			$productId			=	$_item->getProductId();
			//	get child product id if configurable product is in the cart
			if($_item->getProductType()=='configurable'){
				$simpleProduct	=	Mage::getModel('catalog/product')->loadByAttribute('sku',$realChildSku);
				$productId		=	$simpleProduct->getId();
			}
			
			$childSku		=	$_item->getSku();
			if(strstr($childSku,'-engraving')){
				$childSkuArray	=	explode("-engraving",$childSku);
				$realChildSku	= 	$childSkuArray[0];
				$extraDays++;	$arriveDays++;
			}		
			/*echo '<br>extraDays '. $extraDays;
			echo '<br>arriveDays '. $arriveDays;
			echo '<br><br>'. $_item->getId();		//	quote item id
			echo '<br>'. $_item->getSku();			//	sku		child or configurable both
			echo '<br>'. $productId;	//	product id
			echo '<br>'. $_item->getProductType();*/
			
			$getDatePP		=	Mage::getModel("estimateShipping/date")->getDate($productId, $extraDays, $arriveDays);
			$wantIt[]		=	$getDatePP['arriveDays'];
			$newArray[]		=	array('product_id'=>$_item->getProductId(), 'arrive_date'=>$getDatePP['arriveDays'], 'sku'=>$childSku, 'extraDays'=>$extraDays, 'arriveDays'=>$arriveDays);
		}
		
		$closestDate	=	$this->getMaxDate($wantIt);
		//echo '<pre>';print_r($wantIt);		echo '<br>closestDate '. $closestDate;		echo '<pre>';print_r($newArray);
				
		if(count($newArray)>0){
			$incrementDays=0;
			foreach($newArray as $arr){
				$productId	=	$arr['product_id'];
				$arriveDate	=	$arr['arrive_date'];
				$childSku	=	$arr['sku'];
				$extraDays	=	$arr['extraDays'];
				$arriveDays	=	$arr['arriveDays'];
				
				if( substr($childSku,0,2) == 'JA'){			//	appraisal
					$incrementDays++;	
				}
			}				
		}

		if($incrementDays>0){
			//	retrun array element
			$key		=	array_search($closestDate,array_keys($newArray));
			$myArray	=	$newArray[$key];
			$productId	=	$myArray['product_id'];
			$extraDays	=	$myArray['extraDays']+$incrementDays;
			$arriveDays	=	$myArray['arriveDays']+$incrementDays;
			
			$getDatePP	=	Mage::getModel("estimateShipping/date")->getDate($productId, $extraDays, $arriveDays);
			$newWantIt[]	=	$getDatePP['arriveDays'];
			
			//echo '<pre>';print_r($wantIt);	
			$newclosestDate	=	$this->getMaxDate($newWantIt);
		}else{
			$newclosestDate	=	$closestDate;
		}
		
		//	compare both array
		if($closestDate>$newclosestDate){
			$newclosestDate	=	$closestDate;
		}
		//echo '<br>newclosestDate '. $newclosestDate;
		return $newclosestDate;
	}
	
	
	public function getMaxDate($dateArray){
		$i=0;
		foreach ($dateArray as $date){
			$myDate	=	date('Y-m-d', strtotime($date));		
			if($i==0 || $myDate > $tempDate){
				$tempDate = $myDate;				
			}
			$i++;
		}
		return $tempDate;
	}
	
	/*
		calculate the order shipping date based on product vender_lead_time
		$wantIt		integer		-	
		$shippingMethodCode		- var_char		-	shipping method code
	*/
	public function shippingDate($wantIt, $shippingMethodCode){
		$incrementDays	=	$this->getShippingDays($shippingMethodCode);
		if(empty($incrementDays)){
			$incrementDays	=	0;	
		}
		$wantIt		=	date('Y-m-d', strtotime($wantIt));		
		$wantItDate = 	$this->skipUsaHolidaysNew($incrementDays, $wantIt);
		return $wantItDate;
	}
	
	/*
		get static days for shipping methods
	*/
	public function getShippingDays($shippingMethod){
		if($shippingMethod==''){$shippingMethod	=	'freeshipping';}
							//	Ground Shipping,  	2 Day Shipping $12, 	 International Shipping, 	Priority Overnight $20, 	Saturday Delivery $32.95
		$mappingArray	=	array('freeshipping'=>'4', 'ang2dayflatrate'=>'2', 'angnonusflatrate'=>'4', 'angovernightflatrate'=>'1', 'flatrate'=>'1');
		return $mappingArray[$shippingMethod];
	}
	
	public function skipUsaHolidaysNew($leadTime=0, $delivery_date){
		$SaturdayDateArr 	= 	$this->getSaturdayDate();	// 	saturday
		$SundayDateArr 		= 	$this->getSundayDate();		// 	sunday
		$holiday_arr		=	$this->getYearlyHolidays();	//	yearly
		
		$AllHolidayArr 		= 	array_merge($SaturdayDateArr, $SundayDateArr,$holiday_arr);
		$AllHolidayUniqueArr= 	array_unique($AllHolidayArr);

		$numberofholiday 	= 	0;	
		$aa 				= 	0;
		
		$final_delivery_date	=	$delivery_date;
		/*if($leadTime==0){
			//$delivery_date		=	date('Y-m-d');
			//echo "<pre>";print_r($AllHolidayUniqueArr).'<br>';	
			if(in_array($delivery_date,$AllHolidayUniqueArr)){
				$leadTime	=	1;
			}
		}*/
		
		for($i=1;$i<=300;$i++){
			//echo '<br>aa->'.$aa;	echo 'leadTime->'.$leadTime;
			if($aa==$leadTime){
				break;	
			}
			//$seldate  = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y")));
			$seldate  = date("Y-m-d", strtotime("+".$i." days", strtotime($delivery_date)));
			//echo '<br>delivery_date->'.$delivery_date;
			//echo '<br>seldate->'.$seldate;
			
			if (in_array($seldate, $AllHolidayUniqueArr)) {
				//echo '<br>'.$seldate;
				$numberofholiday++;
			}else{	
				$final_delivery_date = $seldate;
				$aa++;
			}
		}
		//echo '<br>final_delivery_date->'.$final_delivery_date; 
		return $this->formatDate($final_delivery_date);
	}
	
	/*
		returns the array of available shipping methods
	*/
	public function getFreeInternationalShippingPrice() {
		return '$'.Mage::getStoreConfig("carriers/angnonusflatrate/min_cart_value_for_free_shipping");
	}
	
	/* get international shipping price added by Pankaj */
	public function getInternationalShippingPrice() {
		return '$'.Mage::getStoreConfig("carriers/angnonusflatrate/price");
	}
	
	/*
		returns the Shipment Title for the order from sales_flat_shipment_track table
	*/
	public function getShipmentTitle($order) {
		$orderId	=	$order->getId();
		$tableName	=	Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_track');
		$sql		=	"SELECT title from sales_flat_shipment_track where order_id='".$orderId."'";
		//echo $sql;
		$read 		= 	Mage::getSingleton('core/resource')->getConnection('core_read');		// reading the database resource
		$data 		= 	$read->fetchOne($sql);
		return $data;
	}
	
	/*
		returns the offer coupon code as per the offer
	*/
	public function getCurrentOfferCouponCode(){
		$pageType		=	$this->getPageType();
		if($pageType!='checkout' && $pageType!='amazon_payments'){
			$platform 			= 	Mage::helper('promotions')->getPlatform();
			$applicableCoupons	=	Mage::getModel('promotions/offer')->getApplicableCoupons($platform);
			//prd($applicableCoupons);
			if(count($applicableCoupons) > 0){
				foreach ($applicableCoupons as $coupon){
					$ruleData	=	Mage::getModel('salesrule/coupon')->load($coupon->getRuleId(), 'rule_id');
					$oRule 		= 	Mage::getModel('salesrule/rule')->load($ruleData->getRuleId());
					if($oRule->getSimpleAction() == 'by_percent'){
						$couponPercent	=	number_format($oRule->getDiscountAmount(),0).'%';
					}
					return $couponData	=	array('coupon_code' => strtoupper($ruleData->getCode()), 'coupon_perc' => $couponPercent, 'coupon_object' => $coupon);
				}
			}
		}
	}
		
	/*
		returns html for the offer text
	*/
	public function getOfferText(){
		$couponDataArray=	$this->getCurrentOfferCouponCode();
		//prd($couponDataArray);
		$couponCode		=	$couponDataArray['coupon_code'];
		//echo $couponCode;die;
		if($couponCode && $this->canShowHeaderStrip()){
			$coupon		=	$couponDataArray['coupon_object'];
			$couponData = 	Mage::getModel('promotions/coupon')->loadByRuleId($coupon->getRuleId()); 
			$shortDesc	=	$couponData['short_description'];
			return $shortDesc.' <strong class="top-head-cc">'.$couponCode.'</strong>';
		}
	}
	
	/* get coupon shipping method selected */
	public function getCouponShipping($coupon){
		if($coupon){
			$couponObj = Mage::getModel('promotions/coupon')->loadByCouponCode($coupon);
			if($couponObj && $couponObj->getValidShipping()){
				$couponShipping = $couponObj->getValidShipping();
			}
		}
		return $couponShipping;
	}
	
	/*
		return yes if we can show header offer strip
	*/
	public function canShowHeaderStrip(){
		$pageType	=	$this->getPageType();
		if($pageType=='cms'){
			$currentUrl		=	Mage::helper('core/url')->getCurrentUrl();
			if(stristr($currentUrl, 'deal-of-the-day')){
				return false;	
			}
		}else if($pageType=='product'){
			$product 	= 	Mage::registry('current_product');
			$skuArray	=	Mage::helper('function')->getSkuFromCoupon();
			if(is_array($skuArray) && in_array($product->getSku(), $skuArray)){
				return false;	
			}	
		}
		return true;
	}
	
	/*
		returns html for the offer text to be shown on catalog pages
	*/
	public function getOfferTextCatalog(){
		$currentOfferCouponData = $this->getCurrentOfferCouponCode();
		$couponCodeCatalog		=	$currentOfferCouponData['coupon_code'];
		
		if($couponCodeCatalog){
			//$couponCatalogObj =	$currentOfferCouponData['coupon_object'];
			//$couponCatalog = Mage::getModel('promotions/coupon')->loadByRuleId($couponCatalogObj->getRuleId()); 
			if(isset($currentOfferCouponData['coupon_perc']) && $currentOfferCouponData['coupon_perc'] != '0%') {
				$shortDesc = 'Extra '.$currentOfferCouponData['coupon_perc'].' off with code '.$couponCodeCatalog;
			} else {
				$shortDesc = 'Free Jewelry Gift with code '.$couponCodeCatalog;
			}
			return $shortDesc;
		}
	}
	
	/*
		returns html for the offer SKU text to be shown on catalog pages
	*/
	public function getOfferTextForOfferSku(){
		$couponCodeCatalog		=	$this->getTodaysCouponCode();
		if($couponCodeCatalog){
			$shortDesc = 'Extra 20% off with code '.$couponCodeCatalog;
			return $shortDesc;
		}
	}
	
	/*
		return toggle image path
	*/
	public function getToggleImage($_product){
		$toggleImage	=	$_product->getData('alternate_image');
		if($toggleImage && $toggleImage!='no_selection'){
			return $toggleImage;
		}		
		/*if(!$productId){return false;}
		$product 		= 	Mage::getModel('catalog/product')->load($productId);
		$gallery 		= 	$product->getMediaGalleryImages();
		$imagesCount	=	count($gallery); 
		//echo $imagesCount;die;
		if($imagesCount>1){
			foreach ($gallery as $image){
				//echo '<pre>';print_r($image);
				if($image->getLabel() =='toggle'){
					//echo $productId. ' '.$image->getFile();die;
					return $backImage	=	$image->getFile();
				}
			}
		}	*/
	}
	
	/*
		$debug	=	Mage::helper('function')->isDeveloper();
		if($debug){	echo 'show me only';}
	*/
	public function isDeveloper(){
		$params	=	Mage::app()->getFrontController()->getRequest()->getParams();
		$ip 	= 	$_SERVER['REMOTE_ADDR'];
		//$allowed = array('14.141.251.118');
		//if( in_array($ip, $allowed) || in_array('va', $params) ){
		if( in_array('va', $params) ){
			return true;	
		}	
	}
	
	
	/*
		get Saturday's Date
	*/
	public function getSaturdayDate(){
		return array('2016-12-03','2016-12-10','2016-12-17','2016-12-24','2016-12-31','2017-01-07','2017-01-14','2017-01-21','2017-01-28','2017-02-04','2017-02-11','2017-02-18','2017-02-25','2017-03-04','2017-03-11','2017-03-18','2017-03-25','2017-04-01','2017-04-08','2017-04-15','2017-04-22','2017-04-29','2017-05-06','2017-05-13','2017-05-20','2017-05-27','2017-06-03','2017-06-10','2017-06-17','2017-06-24','2017-07-01','2017-07-08','2017-07-15','2017-07-22','2017-07-29','2017-08-05','2017-08-12','2017-08-19','2017-08-26','2017-09-02','2017-09-09','2017-09-16','2017-09-23','2017-09-30','2017-10-07','2017-10-14','2017-10-21','2017-10-28','2017-11-04','2017-11-11','2017-11-18','2017-11-25','2017-12-02','2017-12-09','2017-12-16','2017-12-23','2017-12-30','2018-01-06','2018-01-13','2018-01-20','2018-01-27','2018-02-03','2018-02-10','2018-02-17','2018-02-24','2018-03-03','2018-03-10','2018-03-17','2018-03-24','2018-03-31','2018-04-07','2018-04-14','2018-04-21','2018-04-28','2018-05-05','2018-05-12','2018-05-19','2018-05-26','2018-06-02','2018-06-09','2018-06-16','2018-06-23','2018-06-30','2018-07-07','2018-07-14','2018-07-21','2018-07-28','2018-08-04','2018-08-11','2018-08-18','2018-08-25','2018-09-01','2018-09-08','2018-09-15','2018-09-22','2018-09-29','2018-10-06','2018-10-13','2018-10-20','2018-10-27','2018-11-03','2018-11-10','2018-11-17','2018-11-24','2018-12-01','2018-12-08','2018-12-15','2018-12-22','2018-12-29','2019-01-05','2019-01-12','2019-01-19','2019-01-26','2019-02-02','2019-02-09','2019-02-16','2019-02-23','2019-03-02','2019-03-09','2019-03-16','2019-03-23','2019-03-30','2019-04-06','2019-04-13','2019-04-20','2019-04-27','2019-05-04','2019-05-11','2019-05-18','2019-05-25','2019-06-01','2019-06-08','2019-06-15','2019-06-22','2019-06-29','2019-07-06','2019-07-13','2019-07-20','2019-07-27','2019-08-03','2019-08-10','2019-08-17','2019-08-24','2019-08-31','2019-09-07','2019-09-14','2019-09-21','2019-09-28','2019-10-05','2019-10-12','2019-10-19','2019-10-26','2019-11-02','2019-11-09','2019-11-16','2019-11-23','2019-11-30','2019-12-07','2019-12-14','2019-12-21','2019-12-28','2020-01-04','2020-01-11','2020-01-18','2020-01-25','2020-02-01','2020-02-08','2020-02-15','2020-02-22','2020-02-29','2020-03-07','2020-03-14','2020-03-21','2020-03-28','2020-04-04','2020-04-11','2020-04-18','2020-04-25','2020-05-02','2020-05-09','2020-05-16','2020-05-23','2020-05-30','2020-06-06','2020-06-13','2020-06-20','2020-06-27','2020-07-04','2020-07-11','2020-07-18','2020-07-25','2020-08-01','2020-08-08','2020-08-15','2020-08-22','2020-08-29','2020-09-05','2020-09-12','2020-09-19','2020-09-26','2020-10-03','2020-10-10','2020-10-17','2020-10-24','2020-10-31','2020-11-07','2020-11-14','2020-11-21','2020-11-28','2020-12-05','2020-12-12','2020-12-19','2020-12-26');
	}
	
	/*
		get Sunday's Date
	*/
	public function getSundayDate(){
		return array('2016-12-04','2016-12-11','2016-12-18','2016-12-25','2017-01-01','2017-01-08','2017-01-15','2017-01-22','2017-01-29','2017-02-05','2017-02-12','2017-02-19','2017-02-26','2017-03-05','2017-03-12','2017-03-19','2017-03-26','2017-04-02','2017-04-09','2017-04-16','2017-04-23','2017-04-30','2017-05-07','2017-05-14','2017-05-21','2017-05-28','2017-06-04','2017-06-11','2017-06-18','2017-06-25','2017-07-02','2017-07-09','2017-07-16','2017-07-23','2017-07-30','2017-08-06','2017-08-13','2017-08-20','2017-08-27','2017-09-03','2017-09-10','2017-09-17','2017-09-24','2017-10-01','2017-10-08','2017-10-15','2017-10-22','2017-10-29','2017-11-05','2017-11-12','2017-11-19','2017-11-26','2017-12-03','2017-12-10','2017-12-17','2017-12-24','2017-12-31','2018-01-07','2018-01-14','2018-01-21','2018-01-28','2018-02-04','2018-02-11','2018-02-18','2018-02-25','2018-03-04','2018-03-11','2018-03-18','2018-03-25','2018-04-01','2018-04-08','2018-04-15','2018-04-22','2018-04-29','2018-05-06','2018-05-13','2018-05-20','2018-05-27','2018-06-03','2018-06-10','2018-06-17','2018-06-24','2018-07-01','2018-07-08','2018-07-15','2018-07-22','2018-07-29','2018-08-05','2018-08-12','2018-08-19','2018-08-26','2018-09-02','2018-09-09','2018-09-16','2018-09-23','2018-09-30','2018-10-07','2018-10-14','2018-10-21','2018-10-28','2018-11-04','2018-11-11','2018-11-18','2018-11-25','2018-12-02','2018-12-09','2018-12-16','2018-12-23','2018-12-30','2019-01-06','2019-01-13','2019-01-20','2019-01-27','2019-02-03','2019-02-10','2019-02-17','2019-02-24','2019-03-03','2019-03-10','2019-03-17','2019-03-24','2019-03-31','2019-04-07','2019-04-14','2019-04-21','2019-04-28','2019-05-05','2019-05-12','2019-05-19','2019-05-26','2019-06-02','2019-06-09','2019-06-16','2019-06-23','2019-06-30','2019-07-07','2019-07-14','2019-07-21','2019-07-28','2019-08-04','2019-08-11','2019-08-18','2019-08-25','2019-09-01','2019-09-08','2019-09-15','2019-09-22','2019-09-29','2019-10-06','2019-10-13','2019-10-20','2019-10-27','2019-11-03','2019-11-10','2019-11-17','2019-11-24','2019-12-01','2019-12-08','2019-12-15','2019-12-22','2019-12-29','2020-01-05','2020-01-12','2020-01-19','2020-01-26','2020-02-02','2020-02-09','2020-02-16','2020-02-23','2020-03-01','2020-03-08','2020-03-15','2020-03-22','2020-03-29','2020-04-05','2020-04-12','2020-04-19','2020-04-26','2020-05-03','2020-05-10','2020-05-17','2020-05-24','2020-05-31','2020-06-07','2020-06-14','2020-06-21','2020-06-28','2020-07-05','2020-07-12','2020-07-19','2020-07-26','2020-08-02','2020-08-09','2020-08-16','2020-08-23','2020-08-30','2020-09-06','2020-09-13','2020-09-20','2020-09-27','2020-10-04','2020-10-11','2020-10-18','2020-10-25','2020-11-01','2020-11-08','2020-11-15','2020-11-22','2020-11-29','2020-12-06','2020-12-13','2020-12-20','2020-12-27');
	}

	/*
		get Yearly Holiday's Date
	*/
	public function getYearlyHolidays(){
		$yearArray = array(
		'2016-12-24','2016-12-25','2016-12-26',
			'2017-01-01','2017-01-02','2017-01-16',
			'2017-02-20',
			'2017-05-29',
			'2017-07-04',
			'2017-09-04',
			'2017-10-09',
			'2017-11-11','2017-11-23',
			'2017-12-25',
		'2018-01-01','2018-01-15',
		'2018-02-19',
		'2018-05-28',
		'2018-07-04',
		'2018-09-03',
		'2018-10-08',
		'2018-11-11','2018-11-22',
		'2018-12-25'
		);
		return $yearArray;
	}
	public function isEmiCollection(){
		$easypay_flag = 0;	
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$cartItems = $quote->getAllVisibleItems();
		foreach ($cartItems as $item) {
			if($item->getNoOfInstallment() > 1){
				$easypay_flag = 1;
				break;
			}
		}
		return $easypay_flag;
	}

}
?>