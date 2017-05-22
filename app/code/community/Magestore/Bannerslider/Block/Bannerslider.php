<?php
class Magestore_BannerSlider_Block_BannerSlider extends Mage_Core_Block_Template
{
	private $_display = '0';
	
	public function _prepareLayout()	{
		return parent::_prepareLayout();
	}
    
	public function getBannerSlider() { 
		if (!$this->hasData('bannerslider')) {
			$this->setData('bannerslider', Mage::registry('bannerslider'));
		}
		return $this->getData('bannerslider');			
	}
	
	public function setDisplay($display){
		$this->_display = $display;
	}
	
	public function getBannerCollection() {
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',0)
			->addFieldToFilter('title','home');
			//->addFieldToFilter('is_home',$this->_display);
		if ($this->_display == Magestore_Bannerslider_Helper_Data::DISP_CATEGORY){
			$current_category = Mage::registry('current_category')->getId();
			$collection->addFieldToFilter('categories',array('finset' => $current_category));
		}
		
		$current_store = Mage::app()->getStore()->getId();
		$banners = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
				$banners[] = $banner;
		}
		return $banners;
	}
	
	//	S:VA
	public function getNewBannerCatalogCollection() {
		$current_category = Mage::registry('current_category')->getId();
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',1)												//	Get catalog banners only
			->addFieldToFilter('categories',array('finset' => $current_category))		//	Get banners for current categories only
			->setOrder('image_position', 'ASC')											//	Order by entity_id
			//->load(1);die;
			;
		$banners = array();
		if(count($collection) > 0){
			$i=1;
			foreach ($collection as $banner) {
				$banners[$i] = $banner->getData();
				$i++;
			}
		}
		return $banners;
	}
	//	E:VA
	
	public function getBannerCatalogCollection() {
		$sku	=	'';
		if($product = Mage::registry('current_product')){
			$cats 	= 	$product->getCategoryIds();
			$sku	=	$product->getSku();
		}else if($category = Mage::registry('current_category')){
			$cats = array($category->getId());
		}
				
		//$googleCategory = array(33,78,323,324);			
		//$googleCommonCategory = array_intersect($cats, $googleCategory);
		
		$existingSession	=	Mage::getSingleton('core/session')->getBannertitleSession();
		$existingCookie		= 	Mage::getModel('core/cookie')->get('bannertitlecookie');	
		//$returningVisitorCookie		= 	Mage::getModel('core/cookie')->get('cookiepopups');		//	Code Added by Vaseem
		$cid				=	Mage::app()->getRequest()->getParam('cid');				//	getting the cid parameter from url
		
		//	Code added by Vaseem to check returning customer starts
			/*$returningVisitorCookie		= 	Mage::getModel('core/cookie')->get('firsttime');		//	Code Added by Vaseem
			$timeCookieArray			=	explode('--',$returningVisitorCookie);
			$timeCookie					=	str_replace('-',':',$timeCookieArray[1]);
			$cookieSetTime				=	$timeCookieArray[0].' '.$timeCookie;
			
			$todayDate					=	Mage::getModel('core/date')->date('Y-m-d h:i:s');
			$to_time 					= 	strtotime($todayDate);
			$from_time 					= 	strtotime($cookieSetTime);
			$returningVisitorTimeDiff	=	round(abs($to_time - $from_time) / 3600,2);	*/	//	getting time diff in hours
			
			/*echo '<br>cookieSetTime->'.$cookieSetTime;
			echo '<br>todayDate->'.$todayDate;
			echo '<br>returningVisitorTimeDiff->'.$returningVisitorTimeDiff;*/
		//	Code added by Vaseem to check returning customer ends
		
		
		/*echo '<pre>'; print_r($cats); echo '</pre>';
		echo '<br>existingSession->'.$existingSession;
		echo '<br>existingCookie->'.$existingCookie;
		echo '<br>returningVisitorCookie->'.$returningVisitorCookie;*/
		
		//	cid parameters explaination
		//	ps-grsus	=	google		will work on cookie for 72 hours so banner will show till 72 hours
		//	cse-pg-pf	=	pricegrabber
		//	cse-shp-pf	=	shopping
		//	cse-msau-pf	=	myshopping
		//	cse-pnto-pf	=	pronto
		//	cse-az-pf	=	amazon
		
		//	em-ne-		=	newsletter
		
		//	Moissanite Jewelry (ID: 371)	Moissanite Rings (ID: 368)	Moissanite Necklace Pendants (ID: 370)	Moissanite Earrings (ID: 369)
		$moissaniteCategoryPage	=	0;
		$moissaniteCategory		=	array('368','369','370','371');		//	Live Ids
		//$moissaniteCategory		=	array('363','368','370','367');			// Sandbox Ids
		$moissaniteCommonCategory = array_intersect($cats, $moissaniteCategory);
		if( count($moissaniteCommonCategory)>0 ){
			$moissaniteCategoryPage	=	1;
		}
		//echo '<br>moissaniteCategoryPage->'.$moissaniteCategoryPage; 
		
		//	Black Onyx Jewelry (ID: 374)	Black Onyx Rings (ID: 373)	Black Onyx Necklace Pendants (ID: 375)	Black Onyx Earrings (ID: 376)
		$onyxCategoryPage	=	0;
		//remove introductory offer
		//$onyxCategory		=	array('351','362'); // new intro category  //array('373','374','375','376');		//	Live Ids  323 397 398 //black blue diamond
		//$onyxCategory		=	array('372','373','374','375');			// Sandbox Ids
		$onyxCommonCategory = array_intersect($cats, $onyxCategory);
		if( count($onyxCommonCategory)>0 ){
			$onyxCategoryPage	=	1;
		}
		//echo '<br>onyxCategoryPage->'.$onyxCategoryPage; 
		
		
		//	Code added by Vaseem to track cse customer BT 30
		if(strstr($cid,'cse')) { 
			//echo 'found'; 
			Mage::getSingleton('core/session')->setCseCustomerSession('yes');
		} else{
			//echo 'track domain now';
			$referringURL	=	Mage::app()->getFrontController()->getRequest()->getServer('HTTP_REFERER');
			
			/*www.amazon.com
			www.myinternationalshopping.com
			www.myshopping.com.au
			www.nextag.com
			www.pricegrabber.com
			www.pronto.com
			www.shopzilla.com
			comcastfs.pricegrabber.com
			about.pricegrabber.com
			smile.amazon.com
			productads-manualpolicing.amazon.com
			www.local.smartshopping.com
			www.onewayshopping.com
			www.shopmania.com
			www.shopbot.com.au
			www.local.smartshopping.com */
			
			if( strstr($referringURL,'www.amazon.com') || strstr($referringURL,'www.myinternationalshopping.com') || strstr($referringURL,'www.myshopping.com.au')  || strstr($referringURL,'www.nextag.com')  || strstr($referringURL,'www.pricegrabber.com')  || strstr($referringURL,'www.pronto.com')  || strstr($referringURL,'www.shopzilla.com')  || strstr($referringURL,'comcastfs.pricegrabber.com')  || strstr($referringURL,'about.pricegrabber.com')  || strstr($referringURL,'smile.amazon.com')   || strstr($referringURL,'productads-manualpolicing.amazon.com')   || strstr($referringURL,'www.local.smartshopping.com')   || strstr($referringURL,'www.onewayshopping.com')   || strstr($referringURL,'www.shopmania.com')   || strstr($referringURL,'www.shopbot.com.au')   || strstr($referringURL,'www.local.smartshopping.com')  ){
				Mage::getSingleton('core/session')->setCseCustomerSession('yes');
			}
		} 
			
			
		if(isset($cid)){
			//echo '<br>cid case';
			//$pos1 = stripos(strtolower($cid), 'ps-grsus');
			$pos2 = stripos(strtolower($cid), 'em-ne-');
			//$pos3 = stripos(strtolower($cid), 'ps-g');
			
			
			if(substr($cid,0,6)=='cse-pg'){	
				Mage::getSingleton('core/session')->setBannertitleSession('pricegrabber');
				$bannertitle = 'pricegrabber';
			}else if(substr($cid,0,7) == 'cse-shp'){	
				Mage::getSingleton('core/session')->setBannertitleSession('shopping');
				$bannertitle = 'shopping';
			}else if(substr($cid,0,8) == 'cse-msau'){	
				Mage::getSingleton('core/session')->setBannertitleSession('myshopping');
				$bannertitle = 'myshopping';
			}else if(substr($cid,0,8) == 'cse-pnto'){	
				Mage::getSingleton('core/session')->setBannertitleSession('pronto');
				$bannertitle = 'pronto';
			}else if(substr($cid,0,6) == 'cse-az'){	
				Mage::getSingleton('core/session')->setBannertitleSession('amazon');
				$bannertitle = 'amazon';
			}else if(substr($cid,0,4)=='ps-g'){	
				Mage::getSingleton('core/session')->setBannertitleSession('google_paid');
				$bannertitle = 'google_paid';
			}else if(substr($cid,0,4)=='ps-b'){	
				Mage::getSingleton('core/session')->setBannertitleSession('bing');
				$bannertitle = 'bing';
			}else if(substr($cid,0,8)=='cse-gpau'){	
				Mage::getSingleton('core/session')->setBannertitleSession('getprice');
				$bannertitle = 'getprice';
			}else if(substr($cid,0,9)=='cse-bizca'){	
				Mage::getSingleton('core/session')->setBannertitleSession('bizrate');
				$bannertitle = 'bizrate';
			}else if(substr($cid,0,10) == 'cse-become'){	
				Mage::getSingleton('core/session')->setBannertitleSession('becomeshopper');
				$bannertitle = 'becomeshopper';
			}else if(substr($cid,0,6)=='cse-sz'){	
				Mage::getSingleton('core/session')->setBannertitleSession('shopzilla');
				$bannertitle = 'shopzilla';
			}
			/*else if(strtolower($cid) == 'cse-sbau-pf'){	
				Mage::getSingleton('core/session')->setBannertitleSession('shopshopper');
				$bannertitle = 'shopshopper';
			}*/
			else if(stripos($cid, 'sm-pin') !== false){
				Mage::getSingleton('core/session')->setBannertitleSession('pinterest');
				$bannertitle = 'pinterest';
			}//	Code added by Vaseem for bt 156 starts
			else if($pos2 !== false){	
			//	Find date in string
				$string	=	$cid;
				preg_match( '/([0-9]{8,9})/', $string, $matches );
				//echo '<pre>'; print_r($matches);
				$emailerDate	=	$matches[0];
				//echo 'cid->'.$cid;	
				//echo '<br>emailerDate->'.$emailerDate;
				$bannertCollection	=	Mage::getModel('bannerslider/bannerslider')
										->getCollection()
										->addFieldToFilter('title',$emailerDate)
										;
										//->load(1);die;
				$bannerData = $bannertCollection->getData();
				if(count($bannerData)>0){
					if($bannerData[0]['title']!='') { 
						Mage::getSingleton('core/session')->setBannertitleSession($emailerDate);
						$bannertitle = $emailerDate;
					}
				}else{	
					//Mage::getSingleton('core/session')->setBannertitleSession('email');
					//$bannertitle = 'email';
					//	Settings default banner if emailer date is not found
					Mage::getSingleton('core/session')->setBannertitleSession('catalog');
					$bannertitle = 'catalog';
				}
				//	Code added by Vaseem for bt 156 ends
			}/*else if($pos1 !== false){
				Mage::getModel('core/cookie')->set('bannertitlecookie', 'google', 3600*72); // expire in 72 hour
				$bannertitle = 'google';
			}*/
		/*}else if(in_array('361',$cats) && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email')){			//	Black Diamond Rings (ID: 361)
			Mage::getSingleton('core/session')->setBannertitleSession('black_diamond_rings');
			$bannertitle = 'black_diamond_rings';
		}else if(in_array('362',$cats) && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email' )){			//	Blue Diamond Rings (ID: 362)
			Mage::getSingleton('core/session')->setBannertitleSession('blue_diamond_rings');
			$bannertitle = 'blue_diamond_rings';
		}else if($moissaniteCategoryPage==1 && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email' )){			
			Mage::getSingleton('core/session')->setBannertitleSession('moissanite');
			$bannertitle = 'moissanite';*/
		}
		//	Onyx Category
		else if($onyxCategoryPage==1 && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email' )){			
			Mage::getSingleton('core/session')->setBannertitleSession('onyx');
			$bannertitle = 'onyx';
		/*}else if($sku!='' && $sku=='SE0952BLD' && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email' )){
			Mage::getSingleton('core/session')->setBannertitleSession('blue_diamond_studs');
			$bannertitle = 'blue_diamond_studs';
		}else if($sku!='' && $sku=='SE0952BKD' && ($existingSession!='pricegrabber' && $existingSession!='shopping' && $existingSession!='myshopping' && $existingSession!='pronto' && $existingSession!='amazon'  && $existingSession!='email' )){
			Mage::getSingleton('core/session')->setBannertitleSession('black_diamond_studs');
			$bannertitle = 'black_diamond_studs';*/
		}else if($existingSession){
			//echo '<br>existingSession case';
			if($existingSession=='black_diamond_rings' || $existingSession=='blue_diamond_rings' || $existingSession=='blue_diamond_studs' || $existingSession=='black_diamond_studs' || $existingSession=='moissanite' || $existingSession=='onyx'){
				Mage::getSingleton('core/session')->setBannertitleSession('');
				$bannertitle = 'catalog';		
			}else{
				$bannertitle = Mage::getSingleton('core/session')->getBannertitleSession();
			}
		}else if($existingCookie){
			//echo '<br>cookie case';
			$bannertitle = $existingCookie;				
		}
		//	Code added by Vaseem to check returning customer starts
		/*else if($returningVisitorTimeDiff>24 && $returningVisitorCookie!=''){			//	show first time banner for users who are browsing website after 24 hours
			Mage::getSingleton('core/session')->setBannertitleSession('returning_visitor');
			$bannertitle = 'returning_visitor';			
		}*/
		//	Code added by Vaseem to check returning customer ends
		else{
			//echo '<br>default case';
			$bannertitle = 'catalog';	
		}
		
		/*if ((count($googleCommonCategory) == 0) && ($bannertitle == 'google')){
			//echo '<br>google category case';
			$bannertitle = 'catalog';
		}*/
		//	To show default banner 
		if($bannertitle==''){
			$bannertitle = 'catalog';
		}
		
		//echo '<br>bannertitle->'.$bannertitle;
		
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('title',$bannertitle)
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',array('neq'=>0));		
		//echo $collection->getSelect()->__toString(); exit;
		$current_store = Mage::app()->getStore()->getId();
		$banners = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
				$banners[] = $banner;
		}
		//echo '<pre>';print_r($banners);exit;
		return $banners;
	}
	
	public function getMobileHomeBannerCollection() {
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('title','mobile_home')
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',0);
			
		if ($this->_display == Magestore_Bannerslider_Helper_Data::DISP_CATEGORY){
			$current_category = Mage::registry('current_category')->getId();
			$collection->addFieldToFilter('categories',array('finset' => $current_category));
		}
		
		$current_store = Mage::app()->getStore()->getId();
		$banners = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
				$banners[] = $banner;
		}
		return $banners;
	}
	
	public function getMobileCatalogBannerCollection(){
		//	getting the cid parameter from url
		$cid = Mage::app()->getRequest()->getParam('cid');
		$existingBannerTitle = Mage::getSingleton('core/session')->getBannertitleSession();
							
		if(isset($cid)){
			$emailPos = stripos(strtolower($cid), 'em-ne-');
			
			if(strtolower($cid) != '' && strpos($cid,'cse') !== false){	
				Mage::getSingleton('core/session')->setBannertitleSession('mobile_cse');
				$bannertitle = 'mobile_cse';
			}
			else if(strtolower($cid) != '' && substr($cid,0,4)=='ps-g'){	
				Mage::getSingleton('core/session')->setBannertitleSession('mobile_google');
				$bannertitle = 'mobile_google';
			}
			else if(strtolower($cid) != '' && substr($cid,0,4)=='ps-b'){		
				Mage::getSingleton('core/session')->setBannertitleSession('mobile_bing');
				$bannertitle = 'mobile_bing';
			}
			else if(strtolower($cid) != '' && $emailPos !== false){	
				// Find date in string
				$string	=	$cid;
				preg_match( '/([0-9]{8,9})/', $string, $matches );
				$emailerDate = 'mobile_'.$matches[0];
				$bannertCollection = Mage::getModel('bannerslider/bannerslider')
										->getCollection()
										->addFieldToFilter('title',$emailerDate);
				$bannerData = $bannertCollection->getData();
				if(count($bannerData)>0){
					if($bannerData[0]['title']!='') { 
						Mage::getSingleton('core/session')->setBannertitleSession($emailerDate);
						$bannertitle = $emailerDate;
					}
				}
				else{	
					// Settings default banner if emailer date is not found
					Mage::getSingleton('core/session')->setBannertitleSession('mobile_catalog');
					$bannertitle = 'mobile_catalog';
				}
			}			
			/*if(strtolower($cid)!= '' && strpos($cid,'ps-gpla') !== false){
				Mage::getSingleton('core/session')->setBannertitleSession('mobile-pla');
				$bannertitle = 'mobile_pla';
			}*/
		}
		else{
			if($existingBannerTitle != ''){
				$bannertitle = Mage::getSingleton('core/session')->getBannertitleSession();
			}
			else{
				Mage::getSingleton('core/session')->setBannertitleSession('mobile_catalog');
				$bannertitle = 'mobile_catalog';
			}
		}
		
		if($bannertitle == ''){
			Mage::getSingleton('core/session')->setBannertitleSession('mobile_catalog');
			$bannertitle = 'mobile_catalog';
		}
		
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('title',$bannertitle)
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',array('neq'=>0));
			
		$current_store = Mage::app()->getStore()->getId();
		$banners = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
				$banners[] = $banner;
		}
		return $banners;
	}
	
	public function getDelayTime() {
		$delay = (int) Mage::getStoreConfig('bannerslider/settings/time_delay');
		$delay = $delay * 1000;
		return $delay;
	}
	
	public function isShowDescription(){
		return (int)Mage::getStoreConfig('bannerslider/settings/show_description');
	}
	
	public function getListStyle(){
		return (int)Mage::getStoreConfig('bannerslider/settings/list_style');
	}
	
	public function getImageWidth() {
		return (int)Mage::getStoreConfig('bannerslider/settings/image_width');
	}
	
	public function getImageHeight() {
		return (int)Mage::getStoreConfig('bannerslider/settings/image_height');
	}
}