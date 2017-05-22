<?php
class Angara_Omniture_Model_Sitecatalyst extends Mage_Core_Model_Abstract
{
	
	protected $deployNow;
	
	private $_cartProducts;
	
	protected function _construct()
    {
		$this->deployNow = true;
        $this->_initPageIdentifiers();
		$this->_shortenKeywords();
    }
	
	
	public function toJs(){
		try{
			$js = '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'adobe/s_code.js"></script>';
			$js .= '<script language="javascript">';
			$js .= $this->getSVars();
			$js .= $this->getExtraScript();
			$js .= "
			jQuery(function(){
				jQuery('.customerdiscountlink, #login-link').click(function(){
					loginClicked();
				})
				jQuery('#newsletter-validate-detail-cat, #newsletter-validate-detail-cat-bottom, #popsubform form').submit(function(){
					emailSignupSubmit();
				})
				jQuery('.emailsavemorebtn, .subscribe-button').click(function(){
					emailSignupSubmit();
				})
				jQuery('.live-chat-link').click(function(){liveChatClicked();})
			})
			";
			$js .= '</script>';
			return $js;
		}
		catch(Exception $e){
			# todo replace with log
			echo $e->getMessage();
			return '';
		}
	}
	
	public function getSVars(){
		
			$js = 's.pageName="'.htmlentities($this->getSPageName()).'";';
			$js .= 's.channel="'.htmlentities($this->getSChannel()).'";';
			$js .= 's.prop1="'.$this->getSProp1().'";';
			$js .= 's.prop2="'.$this->getSProp2().'";';
			$js .= 's.prop3="'.$this->getSProp3().'";';
			//$js .= 's.prop20=encodeURI("'.Mage::helper('core/url')->getCurrentUrl().'");';
			$js .= 's.eVar35=encodeURI("'.Mage::helper('core/url')->getCurrentUrl().'");';
			$js .= $this->getSExtraProps();
			$js .= $this->getSLoginVars();
			$js .= $this->getSEvents();
			$js .= $this->getSExtra();
			if($this->deployNow)
				$js .= 's.t();';
			
			return $js;
	}
	
	private function _initPageIdentifiers(){
		$identifiers = array();
		$current_page = '';
		$request = Mage::app()->getFrontController()->getRequest();
		
		
		$this->setSExtraProps('');
		$this->setExtraScript('');

		/*
		* Check to see if its a CMS page
		* if it is then get the page identifier
		*/
		if($request->getRouteName() == 'cms'){
			$current_page = Mage::getSingleton('cms/page')->getIdentifier();
		}
		
		/*
		* If its not CMS page, then just get the route name
		*/
		if(empty($current_page)){
			$current_page = $request->getRouteName();
			$controller = $request->getControllerName();
			$action = $request->getActionName();
			if($current_page == 'catalog'){
				if(strpos($request->getRequestString(), 'jewelry-guide') !== false){
					$this->_setCategoryArticlePage($request->getParam('id'));
				}
				else if($controller == 'category'){
					$this->_setCatalogPage($request->getParam('id'));
				}
				else{
					$this->_setProductPage($request->getParam('id'));
				}
			}
			else if($current_page == 'checkout' || $current_page == 'fancycart' || $current_page == 'onepagecheckout'){
				$this->_setCartPage($controller, $request);
			}
			else if($current_page == 'catalogsearch' && $controller!= 'advanced'){
				$this->_setSearchPage($controller, $request);
			}
			else if($current_page == 'customer'){
				$this->_setCustomerPage($action);
			}
			else if($current_page == 'diamondstud'){
				$this->setSChannel('Earrings');
				$this->setSProp2('Earrings > Diamond Stud');
				$this->setSPageName($this->getSProp2());
			}
			else if($controller == 'cba' && $action=='success'){
				$this->setPageType('Confirmation');
				$this->setSPageName('Purchase Complete');
				$this->_setAmazonPurchaseEvents();
			}
			
		}
		else if($current_page == 'home'){
			$this->setPageType('Home Page');
			$events = 's.events="event15";';
			$this->setSEvents($events);
		}
		else if(strpos($current_page, 'jewelry-guide') !== false){
			$this->_setArticlePage($request->getParam('page_id'));
		}
		else if($current_page == 'no-route.html'){
			$this->setPageType('Error Page');
			$this->setSPageName('404');
			$this->setSExtra('s.prop14 = "404";');
		}
		else{
			$this->_setArticlePage($request->getParam('page_id'));
		}
		
		$this->_setAccountVars();
		
		$prop1 = $this->getSProp1();
		if(empty($prop1)){
			$this->setSProp1($this->getPageType());
		}
		
		$channel = $this->getSChannel();
		if(empty($channel)){
			$prop1 = $this->getSProp1();
			$this->setSChannel($prop1);
		}
		$prop2 =$this->getSProp2();
		if(empty($prop2)){
			$channel = $this->getSChannel();
			$this->setSProp2($channel);
		}
		$prop3 = $this->getSProp3();
		if(empty($prop3)){
			$prop2 = $this->getSProp2();
			$this->setSProp3($prop2);
		}
		
		$pageName = $this->getSPageName();
		if(empty($pageName)){
			$pageType = $this->getPageType();
			$this->setSPageName($pageType);
		}
		else{
			$this->setSPageName($this->getPageType().': '.$this->getSPageName());
		}
		
		$events = $this->getSEvents();
		if(empty($events)){
			# todo what in case of empty events
			$this->setSEvents('');
		}
		
		$extra = $this->getSExtra();
		if(empty($extra)){
			# todo what in case of empty events
			$this->setSExtra('');
		}
		
		# todo remove me
		//$this->_setPurchaseEvents();
		
	}
	
	private function _shortenKeywords(){
		
		// change "Lab Created" to "Lab"
		$this->setSProp1(str_replace('Lab Created','Lab',$this->getSProp1()));
		$this->setSProp2(str_replace('Lab Created','Lab',$this->getSProp2()));
		$this->setSProp3(str_replace('Lab Created','Lab',$this->getSProp3()));
		$this->setSChannel(str_replace('Lab Created','Lab',$this->getSChannel()));
		$this->setSPageName(str_replace('Lab Created','Lab',$this->getSPageName()));
	}
	
	private function _setCatalogPage($cid){
		$this->setPageType('Browse');
		$category = Mage::getModel('catalog/category')->load($cid);
		
		if($category->getLevel() == 4){
			$parent1 = Mage::getModel('catalog/category')->load($cid)->getParentCategory();
			$parent2 = $parent1->getParentCategory();
			$this->setSChannel($parent2->getname());
			$this->setSProp2($parent2->getname().' > '.$parent1->getName());
			$this->setSProp3($parent2->getname().' > '.$parent1->getName().' > '.$category->getName());
			$this->setSPageName($this->getSProp3());
		}
		if($category->getLevel() == 3){
			$parent = Mage::getModel('catalog/category')->load($cid)->getParentCategory();
			$this->setSChannel($parent->getname());
			$this->setSProp2($parent->getname().' > '.$category->getName());
			$this->setSPageName($this->getSProp2());
		}
		else if($category->getLevel() == 2){
			$this->setSChannel($category->getname());
			$this->setSProp2($category->getName());
			$this->setSPageName($this->getSProp2());
		}
		
		$evars = 's.eVar4="D=ch";';
		$evars .= 's.eVar5="D=c2";';
		$evars .= 's.eVar6="D=c3";';
		
		$this->setSExtra($evars);
		
		$events = 's.events="event16";';
		$this->setSEvents($events);
		
		$js = "";
		
		$this->setExtraScript($this->getExtraScript().$js);
		//$parent = Mage::getModel('catalog/category')->load($cid)->getParentCategory();
		//var_dump($parent);
		
	}
	private function _setCategoryArticlePage($aid){
		$this->setPageType('Article');
		$category = Mage::getModel('catalog/category')->load($aid);
		$this->setSPageName($category->getName());
	}
	private function _setArticlePage($aid){
		$this->setPageType('Article');
		$article = Mage::getModel('cms/page')->load($aid);
		$this->setSPageName($article->getTitle());
	}
	private function _setProductPage($pid){
		$this->deployNow = false;
		$product = Mage::getModel('catalog/product')->load($pid);
		$this->setPageType('Product detail');
		$this->setSPageName($product->getShortDescription());		//	S:VA
		$events = 's.events="prodView';
		if(Mage::getSingleton('core/session')->getWishlistItemAdded() === 1){
			$events .=',event20';
			Mage::getSingleton('core/session')->unsWishlistItemAdded();
		}
		
		if(Mage::getSingleton('core/session')->getJustLoggedIn() === 1){
			$events .= ',event11';
			Mage::getSingleton('core/session')->unsJustLoggedIn();
		}
		
		$events .='";s.products=";'.$product->getSku().'";';
		
		$this->setSEvents($events);
		$this->setSExtraProps($this->getSExtraProps().'s.prop10="Product Details";s.eVar22="1";');
		
		$js = "jQuery(function(){
	jQuery('.tweetShare').click(function(){
		socialNetworkShare('Twitter');
	})
	jQuery('.googleShare').click(function(){
		socialNetworkShare('GooglePlus');
	})
	jQuery('.emailtofriend').click(function(){
		socialNetworkShare('Email To Friend');
	})
	jQuery('.pinShare').click(function(){
		socialNetworkShare('Pinterest');
	})
	jQuery('.fbShare').click(function(){
		socialNetworkShare('Facebook');
	})
	jQuery('.whatsappShare').click(function(){
		socialNetworkShare('WhatsApp');
	})
	
	jQuery('#easyopt, #easy-pay-box').change(function(){
		var emi = jQuery(this).val();
		emi = parseInt(emi.slice(0,emi.indexOf('_'))) + 1;
		emiSelect(emi);
	})
	
	jQuery('#ring-size-guide').click(function(){
		customLinkTracking('Ring size guide link');
	})
	
	jQuery('#sizechartupperlink').click(function(){
		customLinkTracking('View size chart link');
	})
	
	jQuery('.qualitychartlink.qualitypopupbtn').click(function(){
		customLinkTracking('View quality chart');
	})
	
	jQuery('.retailpricepopup').click(function(){
		customLinkTracking('Retail Price Popup');
	})
	
	jQuery('.whatiseasypay, #know-more-easypay, #easy-pay-info').mouseenter(function(){
		customLinkTracking('Easy Pay Popup');
	})
	
	jQuery('.certpopup').click(function(){
		customLinkTracking('Jewelry Appraisal Popup');
	})
	
	jQuery('#know-more-appraisal').mouseenter(function(){
		customLinkTracking('Jewelry Appraisal Popup');
	})
	
	jQuery('#authenticity-cert').click(function(){
		customLinkTracking('Authenticity Certificate');
	})
	
	jQuery('.approx-dim-help').click(function(){
		customLinkTracking('Approximate Dimensions Popup');
	})
	
	jQuery('.app-ctw-help').click(function(){
		customLinkTracking('Approximate Carat Total Weight Popup');
	})
	
	jQuery('.quality-popup-help').click(function(){
		customLinkTracking('Quality Grade Popup');
	})
	
	jQuery('input[name=\"insurance\"]').change(function(){
		customLinkTracking('Insurance Clicked')
	})
	
	jQuery('.insruanceicon, #know-more-warranty').mouseenter(function(){
		customLinkTracking('Insurance Popup')
	})
	
	jQuery('#moreviews img').click(function(){
		moreViewClicked();
	})
	
	jQuery('.ppwishlist, #wishlist-link, .link-wishlist').click(function(){
		wishListClicked();
	})
	
	jQuery('#product-video-btn, #playvedio').click(function(){
		productVideoClicked();
	})
	
	jQuery('.playvideothumb').parent().click(function(e){
		e.stopPropagation();
		productVideoClicked();
	})
	
	jQuery('#pckImg').click(function(){
		customLinkTracking('Jewelry Box Viewed');
	})
	
	jQuery('.product-custom-option').change(function(){
		switch(jQuery(this).attr('title')){
			case 'Ring Size':
				ringSizeSelect(jQuery.trim(jQuery(this).find('option:selected').text()));
				break;
			/*case 'Stone Quality':
				var quality = jQuery.trim(jQuery(this).find('option:selected').text());
				if(quality.indexOf('|') != -1){
					quality = quality.slice(0,quality.indexOf('|'))
				}
				gemstoneQualitySelect(quality);
				
				break;
			case 'Metal Type':
				var metal = jQuery.trim(jQuery(this).find('option:selected').text());
				if(metal.indexOf('|') != -1){
					metal = metal.slice(0,metal.indexOf('|'))
				}
				metalTypeSelect(metal);
				break;*/
		}
	})
	jQuery('#matchingband-link').click(function(){
		matchingBandClicked();
	})
});";
		$this->setExtraScript($this->getExtraScript().$js);
	}
	private function _setCartPage($controller, $request){
		$action = $request->getActionName();
		if($controller == 'cart' || $controller == 'ajax'){
			$this->setPageType('Shopping Cart');
			if(Mage::getSingleton('core/session')->getSCOpened()){
				$this->setSEvents('s.events="scAdd,scOpen";');
				$this->setSExtra('s.products=";'.Mage::getSingleton('core/session')->getItemAdded().'";');
				Mage::getSingleton('core/session')->unsItemAdded();
				Mage::getSingleton('core/session')->unsSCOpened();
			}
			else if(Mage::getSingleton('core/session')->getItemAdded()){
				$this->setSEvents('s.events="scAdd";');
				$this->setSExtra('s.products=";'.Mage::getSingleton('core/session')->getItemAdded().'";');
				Mage::getSingleton('core/session')->unsItemAdded();
			}
			else if(Mage::getSingleton('core/session')->getItemRemoved()){
				$this->setSEvents('s.events="scRemove";');
				$this->setSExtra('s.products=";'.Mage::getSingleton('core/session')->getItemRemoved().'";');
				Mage::getSingleton('core/session')->unsItemRemoved();
			}
			else{
				$this->setSEvents('s.events="scView";');
			}

			$js = $this->getCartScript();
			$this->setExtraScript($this->getExtraScript().$js);
		}
		else{
			if($action == 'index'){
				$this->setPageType('Checkout');
				$this->setSPageName('Purchase Start');
			}
			else if($action == 'success'){
				$this->setPageType('Confirmation');
				$this->setSPageName('Purchase Complete');
				$this->_setPurchaseEvents();
			}
			/*else if($action == 'successamazon'){
				$this->setPageType('Confirmation');
				$this->setSPageName('Purchase Complete');
				$this->_setPurchaseEvents();
			}*/
			
			$js = "
				jQuery(function(){
					var s = s_gi(s_account);
					s.linkTrackVars = 'products,events';
					s.linkTrackEvents = s.events = 'scCheckout';
					s.products = '".$this->_getCartProducts()."';
					s.tl(this,'o','Checkout Initiated');
				})
				jQuery(function(){
					/*jQuery('#billing\\\:firstname').focusout(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event8';
						s.tl(this,'o','Billing Information Initiated');
					})
					
					jQuery('#verisign_cc_number').focusout(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event9';
						s.tl(this,'o','Payment Information Initiated');
					})*/
					
					jQuery('.shipping-continue').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event8';
						s.tl(this,'o','Shipping Continue Button Clicked');
					})
					
					jQuery('#shipping\\\:telephone').focusout(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event8';
						s.tl(this,'o','Phone Number Entered');
					})
					
					jQuery('#payment-submit-button').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event9';
						s.tl(this,'o','Payment Continue Button Clicked');
					})
					
					jQuery('#verisign_cc_cid').focusout(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event9';
						s.tl(this,'o','CVV Number Entered');
					})
					
					jQuery('#btn-order-button, .btn-place-order, #order-button, #btn-order-button-bottom').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'events';
						s.linkTrackEvents = s.events = 'event10';
						s.tl(this,'o','Complete Order Clicked');
					})
					
				})";
			$this->setExtraScript($this->getExtraScript().$js);
		}
		
	}
	
	public function getCartScript(){
		return "jQuery(function(){
					/*jQuery('.btn-proceed-checkout').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'products,events';
						s.linkTrackEvents = s.events = 'scCheckout';
						s.products = '".$this->_getCartProducts()."';
						s.tl(this,'o','Checkout Initiated');
					})*/
					
					jQuery('.checkout-types li:eq(1)').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'products,events';
						s.linkTrackEvents = s.events = 'scCheckout';
						s.products = '".$this->_getCartProducts()."';
						s.tl(this,'o','Paypal Checkout Initiated');
					})
					
					jQuery('.checkout-types li:eq(2)').click(function(){
						var s = s_gi(s_account);
						s.linkTrackVars = 'products,events';
						s.linkTrackEvents = s.events = 'scCheckout';
						s.products = '".$this->_getCartProducts()."';
						s.tl(this,'o','Amazon Checkout Initiated');
					})
					
					jQuery('.btn-continue').click(function(){
						continueBtnClick();
					})
					
					
				})";
	}
	
	private function _getCartProducts(){
		if(!$this->_cartProducts){
			$items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
			$skus = array();
			foreach($items as $item){
				// if it is simple product than don't track it just track the parent item
				if(!$item->getParentItemId())
					$skus[] = ';'.$item->getProduct()->getData('sku');
			}
			$this->_cartProducts = (implode(',',$skus));
		}
		return $this->_cartProducts;
	}

	private function _setSearchPage($controller, $request){
		
		$this->setPageType('Search Results');
		$this->setSExtraProps($this->getSExtraProps().'s.prop4="'.Mage::helper('catalogsearch')->getQueryText().'";');
		$count = Mage::getBlockSingleton('catalogSearch/result')->getResultCount();
		if($count == 0){
			$this->setSExtraProps($this->getSExtraProps().'s.prop5="zero";');
		}
		else{
			$this->setSExtraProps($this->getSExtraProps().'s.prop5="'.$count.'";');
		}
		
		// # todo set prop5 with the no. of search results
	}
	
	private function _setCustomerPage($action){
		$this->setPageType('Customer');
		$this->setSPageName($action);
		if($action == 'login'){
			$js = 's.events="event7";';
			$this->setSEvents($js);
		}
		# todo check all possible types
	}
	
	private function _setPurchaseEvents(){
		
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		
		//var_dump($order->getShippingMethod());
		//var_dump(get_class_methods(get_class($order)));
		
		//var_dump(Mage::getSingleton('checkout/session')->getQuote()); exit;
		$coupon = ($order->getCouponCode()!='')?$order->getCouponCode():'No Discount';
		if(Mage::getSingleton('core/session')->getJustLoggedIn() === 1){
			$js = 's.events="purchase,event11,event12,event13,event14";';
			Mage::getSingleton('core/session')->unsJustLoggedIn();
		}
		else{
			$js = 's.events="purchase,event12,event13,event14";';
		}
		$js .= 's.products="'.$this->_getPurchasedProducts($order).'";';
		$js .= 's.purchaseID="'.$orderId.'";';
		$js .= 's.eVar12="'.$order->getPayment()->getMethodInstance()->getTitle().'";';
		$js .= 's.eVar13="'.$order->getShippingMethod().'";';
		$js .= 's.eVar21="'.$coupon.'";';
		$js .= 's.zip="'.$order->getBillingAddress()->getPostcode().'";';
		$js .= 's.state="'.$order->getBillingAddress()->getRegionCode().'";';
		
		//echo $js;
		//exit;
		
		$this->setSEvents($js);
	}
	
	
	private function _getPurchasedProducts($order){
		
		
		$items = $order->getAllItems();
		$pStr = array();
		$pStr[] = ';;;;event12='.((-1) * $order->getBaseDiscountAmount())
				.'|event13='.$order->getBaseTaxAmount()
				.'|event14='.$order->getBaseShippingAmount();
		foreach($items as $item){
			//var_dump($item->getData()); exit;
			if(!$item->getParentItemId()){
				
				$pStr[] = ';'.$item->getProduct()->getSku()	// pid
						.';'.$item->getQtyOrdered()		// qty
						.';'.$item->getBaseOriginalPrice() * $item->getQtyOrdered()
						;
			}
		}
		
		return(implode(',', $pStr));
	}
	
	private function _setAccountVars(){
		$session = Mage::getSingleton('customer/session');
		if($session->isLoggedIn()) {
			$customer = $session->getCustomer();
			$loginVars = 's.prop8="'.$customer->getId().'";';	// user id
			$loginVars .= 's.prop9="logged in";';	// login status\
			
			// put below code at appropriate place so that the spontaneous events can be merged
			if(Mage::getSingleton('core/session')->getJustLoggedIn() === 1){
				$this->setSEvents('s.events="event11";');
				Mage::getSingleton('core/session')->unsJustLoggedIn();
			}
		}
		else{
			$loginVars = 's.prop9="logged out";';	// login status
		}
		
		$this->setSLoginVars($loginVars);
	}
	
	private function _setAmazonPurchaseEvents(){
		if(isset($_REQUEST['amznPmtsOrderIds']) && !empty($_REQUEST['amznPmtsOrderIds']))
		{
			$orderId = $_REQUEST['amznPmtsOrderIds'];
		}else{
			$orderId = 'Amazon-CBA-Order';
		}
		
		$cartHelper = Mage::helper('checkout/cart');

		
		$coupon = ($cartHelper->getCart()->getQuote()->getCouponCode()!='')?$cartHelper->getCart()->getQuote()->getCouponCode():'No Discount';
		if(Mage::getSingleton('core/session')->getJustLoggedIn() === 1){
			$js = 's.events="purchase,event11,event12,event13,event14";';
			Mage::getSingleton('core/session')->unsJustLoggedIn();
		}
		else{
			$js = 's.events="purchase,event12,event13,event14";';
		}
		$js .= 's.products="'.$this->_getAmazonPurchasedProducts($cartHelper).'";';
		$js .= 's.purchaseID="'.$orderId.'";';
		$js .= 's.eVar12="Amazon Checkout";';
		//$js .= 's.eVar13="'.$order->getShippingMethod().'";';
		$js .= 's.eVar21="'.$coupon.'";';
		//$js .= 's.zip="'.$order->getBillingAddress()->getPostcode().'";';
		//$js .= 's.state="'.$order->getBillingAddress()->getRegionCode().'";';
		
		//echo $js;
		//exit;
		
		$this->setSEvents($js);
	}
	
	private function _getAmazonPurchasedProducts($cartHelper){
		$items = $cartHelper->getCart()->getItems();
		$pStr = array();
		
		$tax_amt = Mage::getBlockSingleton('checkout/cart')->getTaxWithInstallments();	
		
		$pStr[] = ';;;;event12='.((-1) * Mage::getBlockSingleton('checkout/cart')->getQuote()->getBaseDiscountAmount())
				.'|event13='.$tax_amt;
		foreach($items as $item){
			//var_dump($item->getData()); exit;
			$productOptions = $item->getProductOptions();
			$options = $productOptions['options']; 
			
			$pStr[] = ';'.$item->getProduct()->getSku()	// pid
					.';'.$item->getQtyOrdered()		// qty
					.';'.$item->getBaseOriginalPrice() * $item->getQtyOrdered()
					//.';;'.$this->_getCheckoutEvars($options)
					;
		}
		
		return(implode(',', $pStr));
	}
}