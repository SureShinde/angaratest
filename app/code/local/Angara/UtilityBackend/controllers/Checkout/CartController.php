<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'CartController.php');
class Angara_UtilityBackend_Checkout_CartController extends Mage_Checkout_CartController
{
	
	/**
     * Shopping cart display action
     */
    public function indexAction()
    {
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

                $warning = Mage::getStoreConfig('sales/minimum_order/description')
                    ? Mage::getStoreConfig('sales/minimum_order/description')
                    : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $cart->getCheckoutSession()->addNotice($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);
		
		if($cart->getQuote()->getCouponCode() != '' && !Mage::app()->getRequest()->isAjax())
			$this->_getSession()->addSuccess($this->__('<span style="color:#008000;">Your discount code "'.Mage::helper('core')->htmlEscape($cart->getQuote()->getCouponCode()).'" has been applied. You save '.Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal() - $cart->getQuote()->getSubtotalWithDiscount()).'.</span>'));
		/* s: shipping method to bind with coupon code. */
		try{
			$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();				// Base Currency
			$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		// Current Currency	
			
			$currentShippingMethod = Mage::getSingleton('checkout/session')->getData('shipment');			
			if($currentCurrencyCode == $baseCurrencyCode){
				if(strlen($currentShippingMethod) > 0 && $currentShippingMethod == 'angnonusflatrate_angnonusflatrate'){
					$couponCode = Mage::helper('core')->htmlEscape($cart->getQuote()->getCouponCode());
					if($couponCode){
						$rule = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
						if($rule){
							$applicableShippingMethod = Mage::getModel('promotions/offer')->_getApplicableShippingMethod($rule->getRuleId());
							if($applicableShippingMethod){
								Mage::getSingleton('checkout/session')->setData('shipment', $applicableShippingMethod);
							}
						}
					}
				}
			}
			else{
				if(strlen($currentShippingMethod) > 0 && $currentShippingMethod != 'angnonusflatrate_angnonusflatrate'){
					Mage::getSingleton('checkout/session')->setData('shipment', 'angnonusflatrate_angnonusflatrate');
				}
			}
		}
		catch(Exception $e){
			Mage::logException($e);
		}
		/* e: shipping method to bind with coupon code. */
        if(!Mage::app()->getRequest()->isAjax()) {
			Varien_Profiler::start(__METHOD__ . 'cart_display');
			$this
				->loadLayout()
				->_initLayoutMessages('checkout/session')
				->_initLayoutMessages('catalog/session')
				->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
			$this->renderLayout();
			Varien_Profiler::stop(__METHOD__ . 'cart_display');
		} else {
			$alredyincart = array();
			$freeItemsInCart = Mage::getModel('promotions/cart')->getFreeItems();
			foreach($freeItemsInCart as $freeItemInCart){
				if(isset($alredyincart[$freeItemInCart->getSku()]) && isset($alredyincart[$freeItemInCart->getSku()][0])){
					Mage::getModel('promotions/cart')->removeFreeItem($alredyincart[$freeItemInCart->getSku()][0]);
					$alredyincart[$freeItemInCart->getSku()][0] = $freeItemInCart->getId();
					continue;
				}
				$alredyincart[$freeItemInCart->getSku()][] = $freeItemInCart->getId();
			}
			
			$html = array();
			$sidecart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
			$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default.phtml');

			foreach($sidecart->getItems() as $_item):
				$html['items'][]= array('id'=>'item_container_'.$_item->getId(),'content'=>$sidecart->getItemHtml($_item),'sku'=>$_item->getSku(),'category'=>addslashes($_item->getBuyRequest()->getData('category')));
			endforeach;
			$html['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals')->setTemplate("checkout/cart/totals.phtml")->toHtml();
			//	S:VA		
			$cartTotal			=	Mage::helper('function')->getCartTotalAmount();
			$html['cartTotal'] 	= 	$cartTotal;
			//	E:VA			
			Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($html));
			Mage::app()->getFrontController()->getResponse()->sendResponse();
			die;
		}

	}
	/*
	*	Code to get country id using shipping method
	*/
	private function _getCountryId($shippingMethod){
		if($shippingMethod == 'angmexcanflatrate_angmexcanflatrate'){
			$country_id = 'CA';
		}
		else if($shippingMethod == 'angnonusflatrate_angnonusflatrate'){
			$country_id = 'AU';
		}
		else{
			$country_id = 'US';
		}
		return $country_id;
	}

    /**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');
		
		$shippingMethod = $this->getRequest()->getParam('estimate_method');
		Mage::getSingleton('checkout/session')->setData('shipment',$shippingMethod);
		
		$quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
		if(!$quote->getShippingAddress()->getCountryId()){
            $customerSession = Mage::getSingleton("customer/session");
            if($customerSession && $customerSession->isLoggedIn()){
                $customerAddress = $customerSession->getCustomer()->getDefaultShippingAddress();
                if($customerAddress && $customerAddress->getId()){
					$country_id = $customerAddress->getCountryId();
					$region_id = $customerAddress->getRegionId();
					$region = $customerAddress->getRegion();
				}
            }			
        }
		
		if(empty($country_id)){
			$country_id = $this->_getCountryId($shippingMethod);
		}
		
		$quote->getShippingAddress()
				->setCountryId($country_id)->save();
				
		if($region_id){
			$quote->getShippingAddress()->setRegionId($region_id)->save();
		}
		if($region){
			$quote->getShippingAddress()->setRegion($region)->save();
		}
		
		$quote->getShippingAddress()		
				->setCollectShippingRates(true)
				->collectTotals()
				->save();
				
		Mage::getSingleton('checkout/type_onepage')->saveShippingMethod($shippingMethod);
		
        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        if(!Mage::app()->getRequest()->isAjax()) {
			$this->_goBack();
		} else {
			$cart = $this->_getCart();
			if ($cart->getQuote()->getItemsCount()) {
				$cart->init();
				$cart->save();
			}
			
			$alredyincart = array();
			$freeItemsInCart = Mage::getModel('promotions/cart')->getFreeItems();
			foreach($freeItemsInCart as $freeItemInCart){
				if(isset($alredyincart[$freeItemInCart->getSku()]) && isset($alredyincart[$freeItemInCart->getSku()][0])){
					Mage::getModel('promotions/cart')->removeFreeItem($alredyincart[$freeItemInCart->getSku()][0]);
					$alredyincart[$freeItemInCart->getSku()][0] = $freeItemInCart->getId();
					continue;
				}
				$alredyincart[$freeItemInCart->getSku()][] = $freeItemInCart->getId();
			}
			
			$html = array();
			$sidecart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
			$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default.phtml');
			
			foreach($sidecart->getItems() as $_item):
				$html['deliveryDate'][] = array('id'=>'delivery_date_'.$_item->getId(),'content'=>Mage::getBlockSingleton('catalog/product_deliverydate')->getArrivesByDateAtCart($_item));
			endforeach;
			
			$html['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals')->setTemplate("checkout/cart/totals.phtml")->toHtml();	
			//	S:VA		
			$cartTotal			=	Mage::helper('function')->getCartTotalAmount();
			$html['cartTotal'] 	= 	$cartTotal;
			//	E:VA
			Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($html));
			Mage::app()->getFrontController()->getResponse()->sendResponse();
			die;
		}
    }

	
	/*
		S:VA
		Calculate tax based on US zipcode
	*/
	public function estimateTaxAction()
    {
        $country    = (string) $this->getRequest()->getParam('country_id');
		$country    =	'US';
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');

		$this->_getQuote()->getBillingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
			
		$this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
			
        //$this->_getQuote()->save();		
		$this->_getQuote()->collectTotals()->save();
		//echo $this->_getQuote()->getId();die;		
        if(!Mage::app()->getRequest()->isAjax()) {
			$this->_goBack();
		} else {
			$html = array();
			/* $sidecart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
			$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default.phtml');

			foreach($sidecart->getItems() as $_item):
				$html['items'][]= array('id'=>'item_container_'.$_item->getId(),'content'=>$sidecart->getItemHtml($_item),'sku'=>$_item->getSku(),'category'=>addslashes($_item->getBuyRequest()->getData('category')));
			endforeach; */
			
			$html['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals"')->setTemplate("checkout/cart/totals.phtml")->toHtml();
			//	S:VA		
			$cartTotal			=	Mage::helper('function')->getCartTotalAmount();
			$html['cartTotal'] 	= 	$cartTotal;
			//	E:VA
			Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($html));
			Mage::app()->getFrontController()->getResponse()->sendResponse();
			die;
		}
    }

    
    /**
     * Initialize coupon
     */
    public function couponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            if(!Mage::app()->getRequest()->isAjax()) {
				$this->_goBack();
				return;
		    } else {
				Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode(array()));
				Mage::app()->getFrontController()->getResponse()->sendResponse();
				die;
		    } 
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        
		if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            if(!Mage::app()->getRequest()->isAjax()) {
				$this->_goBack();
				return;
		    } else {
				Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode(array()));
				Mage::app()->getFrontController()->getResponse()->sendResponse();
				die;
		    }
        }
		
		/* S: Device check for coupon */
		if($couponCode){
			$todayDate = Mage::getModel('core/date')->date('Y-m-d');
			$platform = Mage::helper('promotions')->getPlatform();
			$deviceApplicable = Mage::helper('promotions')->deviceApplicable($platform);
			$couponsObj = Mage::getModel('promotions/coupon')->validateCode($couponCode, $deviceApplicable, $todayDate);
			if(count($couponsObj) == 0){
				$couponAppliedObj = Mage::getModel('salesrule/rule')->getCollection();
				$couponAppliedObj->addFieldToFilter('rule_valid_on', array('in' => $deviceApplicable))
								->addFieldToFilter('expiration_date', array('gteq' => $todayDate))
								->addFieldToFilter('code', $couponCode)
								->addFieldToFilter('is_primary', 1);
				
				if(count($couponAppliedObj) == 0){	
					//$this->_getSession()->addError($this->__('Coupon "'.$couponCode.'" not applicable for this platform.'));
					$couponCode = '';
				}	
			}
		}
		/* E: Device check for coupon */

        try {
			// added by custom angara promotion
			Mage::getSingleton('core/session')->setPromotionCode($couponCode);
			
            /*$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();*/
				
			$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();				// Base Currency
			$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		// Current Currency						
						
            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                   /* $this->_getSession()->addSuccess(
                        //$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    ); */
                }
                else {
					/* $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    ); */
					
					$customCoupon = Mage::getModel('promotions/coupon')->loadByCouponCode($couponCode);                    
					if($customCoupon && $customCoupon != ''){
						/* $this->_getSession()->addError(
							$customCoupon->getCriteriaErrorMessage()
						); */
					}
                }
				
				/* s: shipping method to bind with coupon code. */
				try {	
					$rule = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
					if($rule){
						$applicableShippingMethod = Mage::getModel('promotions/offer')->_getApplicableShippingMethod($rule->getRuleId());
						if($applicableShippingMethod){
							if($currentCurrencyCode == $baseCurrencyCode){
								Mage::getSingleton('checkout/session')->setData('shipment', $applicableShippingMethod);
							}
							else{
								Mage::getSingleton('checkout/session')->setData('shipment', 'angnonusflatrate_angnonusflatrate');
							}
						}
					}
				}
				catch (Exception $e) {
					Mage::logException($e);
				}	
				/* e: shipping method to bind with coupon code. */
            } else {
                //$this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
				/* s: shipping method to bind with coupon code. */
				if($currentCurrencyCode == $baseCurrencyCode){
					Mage::getSingleton('checkout/session')->setData('shipment', 'freeshipping_freeshipping');
				}
				else{
					Mage::getSingleton('checkout/session')->setData('shipment', 'angnonusflatrate_angnonusflatrate');
				}
				/* e: shipping method to bind with coupon code. */
            }
			
			$this->_getQuote()->getShippingAddress()
				->setShippingMethod(Mage::getSingleton('checkout/session')->getData('shipment'))
				->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

        } catch (Mage_Core_Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            //$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

       if(!Mage::app()->getRequest()->isAjax()) {
			$this->_goBack();
	   } else {
			/* $cart = $this->_getCart();
			if ($cart->getQuote()->getItemsCount()) {
				$cart->init();
				$cart->save();
			} */
			$html = array();
			/*$sidecart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
			$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default.phtml');

			foreach($sidecart->getItems() as $_item):
				$html['items'][]= array('id'=>'item_container_'.$_item->getId(),'content'=>$sidecart->getItemHtml($_item),'sku'=>$_item->getSku(),'category'=>addslashes($_item->getBuyRequest()->getData('category')));
			endforeach;*/
			$html['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals')->setTemplate("checkout/cart/totals.phtml")->toHtml();
			$html['invalidCoupon']=$couponCode?0:1;
			//	S:VA		
			$cartTotal			=	Mage::helper('function')->getCartTotalAmount();
			$html['cartTotal'] 	= 	$cartTotal;
			//	E:VA
			Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($html));
			Mage::app()->getFrontController()->getResponse()->sendResponse();
			die;
		}
    }
	
	
	/*
	*	Angara Modifications Starts
	*/
	public function addchainAction(){	
		$sku = $this->getRequest()->getParam('sku');
		//echo $sku;die;
		try{
			# @TODO move logic to custom model
			# @TODO check if chain is not already present in cart
			//if(!Mage::getSingleton('checkout/session')->getData('ChainAdded')){
				$chain = Mage::getModel('catalog/product');
				$chain->load($chain->getIdBySku($sku));
				
				if($chain->getId()){
					$additionalOptions[] = array(
					 	'label' => 'ItemType',
					 	'value' => 'Add-on'
					);    
					$chain->addCustomOption('additional_options', serialize($additionalOptions));
					$info = new Varien_Object();
					$info->setQty(1);
					Mage::getSingleton("checkout/cart")->addProduct($chain, $info)->save();
					
					if($sku=='op0001sc'){
						$silverChain = '1';
						Mage::getSingleton('checkout/session')->setData('silverchain',$silverChain);
					}
					if($sku=='emop0001sc'){
						$goldChain = '1';
						Mage::getSingleton('checkout/session')->setData('goldchain',$goldChain);
					}
					
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($chain->getName()));
					$this->_getSession()->addSuccess($message);
				}
			//}
		}
		catch(Exception $e){
			$this->_getSession()->addError($this->__('Unable to add Chain. Please try again later'));
		}
		$this->_goBack();
	}
	
	
	/*
	*	Angara Modifications Starts
	*/
	public function removechainAction(){	
		$sku = strtolower($this->getRequest()->getParam('sku'));
		try{
			# @TODO move logic to custom model
			# @TODO check if chain is not already present in cart
			//if(Mage::getSingleton('checkout/session')->getData('ChainAdded')){
				$cartHelper = Mage::helper('checkout/cart');
				$items = $cartHelper->getCart()->getItems();
				
				foreach ($items as $item) {
					if (strtolower($item->getProduct()->getSku()) == $sku) {		//	Code modified by Vaseem
						$itemId = $item->getItemId();
						if($cartHelper->getCart()->removeItem($itemId)->save()){
							if($sku=='op0001sc'){
								Mage::getSingleton('checkout/session')->setData('silverchain','');
							}
							if($sku=='emop0001sc'){
								Mage::getSingleton('checkout/session')->setData('goldchain','');
							}							
							
							$message = $this->__('%s was removed from your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
							$this->_getSession()->addSuccess($message);
						}else{
							$this->_getSession()->addError($this->__('Unable to remove Chain.'));
						}
					}
				}
			//}
		}
		catch(Exception $e){
			$this->_getSession()->addError($this->__('Unable to remove Chain.'));
		}
		$this->_goBack();
	}	
	
	
	/*
	*	Angara Modifications Starts
	*/
	public function personalizedAction(){
		/*$cartHelper = Mage::helper('checkout/cart');
		$items = $cartHelper->getCart()->getItems();
		foreach ($items as $item) {
			$cartHelper->getCart()->removeItem($item->getItemId());
		}*/
		Mage::getSingleton('checkout/cart')->truncate();	//	Clear Shopping Cart		remove all active items in cart page
		//Mage::getSingleton('checkout/session')->clear();	//	Clear Checkout Session	remove all items from cart
		$this->addAction();
	}
	
	public function abandoncartmailchimpAction(){
		Mage::getModel('abandoncartmailchimp/observer')->update();
	}
	
	/* Cart Optimization */
	/* Asheesh:Start */
	public function loadTotalsAction(){
		$totalsHtml['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals"')->setTemplate("checkout/cart/totals.phtml")->toHtml();
		Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($totalsHtml));
        Mage::app()->getFrontController()->getResponse()->sendResponse();
        die;
	}
	
	protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

	public function loadItemsAction(){
		if (Mage::getSingleton('checkout/session')->getItemsWasUpdated(true)) {
			$this->_ajaxRedirectResponse();
		} else {		
			$this->_getSession()->getItemsWasUpdated(true);
			$alredyincart = array();
			$freeItemsInCart = Mage::getModel('promotions/cart')->getFreeItems();
			foreach($freeItemsInCart as $freeItemInCart){
				if(isset($alredyincart[$freeItemInCart->getSku()]) && isset($alredyincart[$freeItemInCart->getSku()][0])){
					Mage::getModel('promotions/cart')->removeFreeItem($alredyincart[$freeItemInCart->getSku()][0]);
					$alredyincart[$freeItemInCart->getSku()][0] = $freeItemInCart->getId();
					continue;
				}
				$alredyincart[$freeItemInCart->getSku()][] = $freeItemInCart->getId();
			}
			$html = array();
			$sidecart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
			$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default.phtml');
			$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default.phtml');
	
			foreach($sidecart->getItems() as $_item):
				$html['items'][]= array('id'=>'item_container_'.$_item->getId(),'content'=>$sidecart->getItemHtml($_item),'sku'=>$_item->getSku(),'category'=>addslashes($_item->getBuyRequest()->getData('category')));
			endforeach;
			$html['content'] = Mage::app()->getLayout()->createBlock("checkout/cart_totals",'checkout.cart.totals"')->setTemplate("checkout/cart/totals.phtml")->toHtml();
			//	S:VA		
			$cartTotal			=	Mage::helper('function')->getCartTotalAmount();
			$html['cartTotal'] 	= 	$cartTotal;
			//	E:VA
			Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($html));
	        Mage::app()->getFrontController()->getResponse()->sendResponse();
			$this->_getSession()->getItemsWasUpdated(false);
	        die;
		}
	}
	
	public function loadCartHelpCalloutAction(){
		if (Mage::getSingleton('checkout/session')->getItemsWasUpdated(true)) {
			$this->_ajaxRedirectResponse();
		}
		$this->_getSession()->getItemsWasUpdated(true);
		if(!strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
			$html = '<div class="col-sm-12 col-md-6 fullwidth1024">
					<div class="cart-extra-detail sec1">
						<div class="img-box"><span></span></div>
						<div class="detail-bx text-center">
							<span class="title-bx">Free Return Shipping</span>
							<div class="clearfix"></div>
							<div class="link-bx">
								<a href="/return-policy" target="_blank">Return & Exchange Policy</a>
								<br>
								<a href="/shipping" target="_blank">Shipping Policy</a>
							</div>
						</div>
					</div>
				</div>';
				
			$html .= '<div class="col-sm-12 col-md-6 fullwidth1024">
						<div class="cart-extra-detail sec2">
							<div class="img-box"><span></span></div>
							<div class="detail-bx text-center">';
			if(strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
				$class = 'col-sm-6';
			}else{
				$class = 'col-md-4';
			}
			$html = $html . '<div class="'.$class.'">
									<div class="showcase-bg-dark" style="padding:10px 0px">
										<i class="fa fa-2x fa-phone text-on-light"></i>
										<span class="center-block">1-888-926-4272</span>
									</div>
								</div>';
			if(!strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
				$html .= '<div class="'.$class.'">
										<a href="mailto:customer.service@angara.com" class="no-underline" onclick="s_objectID=&quot;mailto:customer.service@angara.com_1&quot;;return this.s_oc?this.s_oc(e):true">
											<div class="showcase-bg-dark" style="padding:10px 0px">
												<i class="fa fa-2x fa-envelope text-on-light"></i>
												<span class="center-block">Email</span>
											</div>
										</a>
									</div>';
			}

			$html .= '<div class="'.$class.'">
									<a class="top-nav-live-chat no-underline live-chat-link" href="javascript:void(0);" onClick="javascript:window.open(\'https://server.iad.liveperson.net/hc/609151/?cmd=file&amp;file=visitorWantsToChat&amp;site=609151&amp;imageUrl=https://server.iad.liveperson.nethttp://www.angara.comhttp://www.angara.com/store/images/lp/&amp;referrer=\'+escape(document.location),\'chat609151\',\'width=475,height=400,resizable=yes\');return false;" class="low-padding-left max-padding-right fontsize-type4 color555 live-chat-link">
										<div class="showcase-bg-dark" style="padding:10px 0px">
											<i class="fa fa-2x fa-comments text-on-light"></i>
											<span class="center-block">Live Chat</span>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>';
		}else{
			$day = Mage::helper('function')->freeReturnDays();
			$intOrderLimit = Mage::helper('function')->getFreeInternationalShippingPrice();
			$intShippingPrice = Mage::helper('function')->getInternationalShippingPrice();
			$html = '<p>Free Shipping Worldwide: Free Shipping within the USA. We ship Free to 64 countries on all International Orders over '.$intOrderLimit.'. International orders below '.$intOrderLimit.' are charged '.$intShippingPrice.' each.</p>
					<p>'.$day.'-Day Money Back Guarantee: If you don\'t love it. simply send it back in its original condition for a 100% refund. We will even send you a Free return shipping label if you don\'t love your purchase (applies only to domestic orders).</p>
					<p>Exceptional Value and Quality, Guaranteed by a 110% Refund Policy: If any certified appraiser appraises our jewelry for less than 125% of your purchase price, we will issue you a 110% refund upon return (Refund Amount up to $250 above your Purchase Price).</p>
					<p style="font-size:12px; color:#999999">*Sales tax applies only to orders shipped to NY, NJ & CA.</p>
					<p style="font-size:12px; color:#999999">Customs, Duties, Taxes and other charges to your international order are the responsibility of the recipient.</p>';
		}
		
		$callout['content'] = $html;
		Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($callout));
        Mage::app()->getFrontController()->getResponse()->sendResponse();
		$this->_getSession()->getItemsWasUpdated(false);
	    die;			
	}
	/* Asheesh:End */
}