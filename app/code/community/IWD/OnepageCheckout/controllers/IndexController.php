<?php
require_once( 'app/code/local/Angara/UtilityBackend/controllers/Checkout/OnepageController.php');
class IWD_OnepageCheckout_IndexController extends Angara_UtilityBackend_Checkout_OnepageController
{
    private $_current_layout = null;

    protected $_sectionUpdateFunctions = array(
        'preview'          => '_getPreviewHtml',        //  S:VA
        'review'          => '_getReviewHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        //'payment-method'  => '_getPaymentMethodsHtml',
    );
    
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();
        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    protected function _expireAjax()
    {
        if (!$this->getOnepagecheckout()->getQuote()->hasItems()
            || $this->getOnepagecheckout()->getQuote()->getHasError()
            || $this->getOnepagecheckout()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    protected function _getUpdatedLayout()
    {$this->_initLayoutMessages('checkout/session');
        if ($this->_current_layout === null)
        {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();            
            $update->load('onepagecheckout_index_updatecheckout');
            
            $layout->generateXml();
            $layout->generateBlocks();
            $this->_current_layout = $layout;
        }
        
        return $this->_current_layout;
    }
        
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->_getUpdatedLayout();
        $html = $layout->getBlock('checkout.shipping.method')->toHtml();
        if(Mage::getSingleton('checkout/session')->getData('shipment') == 'angnonusflatrate_angnonusflatrate'){
            $rates = $layout->getBlock('checkout.shipping.method')->getShippingRates();
            if(count($rates) > 1){
                $html = "<script>jQuery(function(){jQuery('#shipping-method').show();})</script>".$html;
            }
            else{
                $html = "<script>jQuery(function(){jQuery('#shipping-method').hide();})</script>".$html;
            }
        }
        return $html;
    }

    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.payment.method')->toHtml();
    }

    protected function _getCouponDiscountHtml()
    {
        $layout = $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.cart.coupon')->toHtml();
    }
    
    protected function _getAddressCandidatesHtml()
    {
        $layout = $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.addresscandidates')->toHtml();
    }
    
    protected function _getReviewHtml()
    {
        $layout = $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.review')->toHtml();
    }
    
    //  S:VA
    protected function _getPreviewHtml()
    {
        $layout = $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.preview')->toHtml();
    }

    public function getOnepagecheckout()
    {
        return Mage::getSingleton('onepagecheckout/type_geo');
    }

    public function indexAction()
    {
        
        /* Angara Customization Start */
		
        Mage::getSingleton('checkout/session')->setData('shipment',$this->getRequest()->getParam('shipment'));
        Mage::getSingleton('checkout/session')->setData('shipment2',$this->getRequest()->getParam('shipment'));
        $checkout = Mage::getSingleton('checkout/type_onepage');
        if (!$checkout->getQuote() || !$checkout->getQuote()->getId()) {
			$this->_redirectUrl(Mage::getBaseUrl());
			return;
		}
		
		$alredyincart = array();
		Mage::getModel('promotions/offer')->process($checkout->getQuote()->getAppliedRuleIds());
		$freeItemsInCart = Mage::getModel('promotions/cart')->getFreeItems();
		foreach($freeItemsInCart as $freeItemInCart){
			if(isset($alredyincart[$freeItemInCart->getSku()]) && isset($alredyincart[$freeItemInCart->getSku()][0])){
				Mage::getModel('promotions/cart')->removeFreeItem($alredyincart[$freeItemInCart->getSku()][0]);
				$alredyincart[$freeItemInCart->getSku()][0] = $freeItemInCart->getId();
				continue;
			}
			$alredyincart[$freeItemInCart->getSku()][] = $freeItemInCart->getId();
		}
		
		$userCountryCode = $checkout->getQuote()->getShippingAddress()
            ->getCountryId();
        if(strlen($userCountryCode) == 0) {
            $cntrycode = 'US';
            if($this->getRequest()->getParam('shipment') == 'angmexcanflatrate_angmexcanflatrate')
            {
                $cntrycode = 'CA';
            }
            else if($this->getRequest()->getParam('shipment') == 'angnonusflatrate_angnonusflatrate')
            {
                $cntrycode = 'AU';
            }
            else
            {
                $cntrycode = 'US';
            }
            

            $checkout->getQuote()->getShippingAddress()
                ->setCountryId($cntrycode)
                ->setCollectShippingRates(true)
                ->collectTotals();
            $checkout->getQuote()->save();
        } else {
            $checkout->getQuote()->getShippingAddress()
                ->setCountryId($userCountryCode)
                ->setCollectShippingRates(true)
                ->collectTotals();
            $checkout->getQuote()->save();
            
        }
        $checkout->saveShippingMethod($this->getRequest()->getParam('shipment'));
        /* Angara Customization End */
        
        // clear verification results from prevous checkout
        $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
        $this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
        $this->getOnepagecheckout()->getCheckout()->setBillingValidationResults(false);
        $this->getOnepagecheckout()->getCheckout()->setShippingValidationResults(false);
        
        if (!Mage::helper('onepagecheckout')->isOnepageCheckoutEnabled())
        {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('onepagecheckout')->__('The one page checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }

        $quote = $this->getOnepagecheckout()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
			if($quote && $quote->getId()){
				Mage::log('Empty Reason(QuoteId:'.$quote->getId().'): '.$quote->getHasError(),null,'empty_cart.log',true);
			}
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));

        $this->getOnepagecheckout()->initDefaultData()->initCheckout();
        
        $enb = (bool)Mage::getStoreConfig('onepagecheckout/general/enabled');
        if (!$enb)
        {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('onepagecheckout')->__('The one page checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $title  = Mage::getStoreConfig('onepagecheckout/general/title');
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->renderLayout();
    }
        
    public function successAction()
    {
        $session = $this->getOnepagecheckout()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        // mark that order will be saved by OPC module        
        $session->setProcessedOPC('opc');
        
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepagecheckout()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepagecheckout()->getCheckout()->getLastOrderId();
		//	S:VA
		if( $lastQuoteId && $lastOrderId ){
			Mage::log('Order Failure (Last Quote Id:'.$lastQuoteId.' and Last Order Id:'.$lastOrderId.')',null,'cart_debug.log',true);
		}

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function getAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepagecheckout()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }
    
    public function updateCheckoutAction()
    {
        if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
            return;
        }
        
        $validation_enabled = Mage::helper('onepagecheckout')->isAddressVerificationEnabled();

/// special fix (see above). Sometimes there are issues with shipping methods. It may solve issue for some system
//      $rates_before = Mage::getModel('sales/quote_address_rate')->getCollection()->setAddressFilter($this->getOnepagecheckout()->getQuote()->getShippingAddress()->getId())->toArray();
/// end special fix

        /*********** DISCOUNT CODES **********/
 
        $quote              = $this->getOnepagecheckout()->getQuote();
        $couponData         = $this->getRequest()->getPost('coupon', array());
        $processCoupon      = $this->getRequest()->getPost('process_coupon', false);
       
        //  S:VA
        $isMobile   =   Mage::helper('function')->isMobile();
        $pmnt_data = $this->getRequest()->getPost('payment', array());
        if(count($pmnt_data)>0 && !$isMobile){
            $this->_saveOrderPurchase();    //  saving payment details in db
        }
       
       
        $couponChanged      = false;
        if ($couponData && $processCoupon) {
                if (!empty($couponData['remove'])) {
                    $couponData['code'] = '';
                     
                }
                $oldCouponCode = $quote->getCouponCode();
                if ($oldCouponCode != $couponData['code']) {
                    try {
                        $quote->setCouponCode(
                            strlen($couponData['code']) ? $couponData['code'] : ''
                        );
                        $this->getRequest()->setPost('payment-method', true);
                        $this->getRequest()->setPost('shipping-method', true);
                        if ($couponData['code']) {
                            $couponChanged = true;
                        } else {
                            $couponChanged = true;
                            Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('onepagecheckout')->__('Coupon code was canceled.'));
                        }
                    } catch (Mage_Core_Exception $e) {
                        $couponChanged = true;
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    } catch (Exception $e) {
                        $couponChanged = true;
                        Mage::getSingleton('checkout/session')->addError(Mage::helper('onepagecheckout')->__('Cannot apply the coupon code.'));
                    }
                    
                }
            }
        
        /***********************************/ 

        if(!$this->getOnepagecheckout()->reinit_data())
            return;
            
        $new_billingData = $this->getRequest()->getPost('billing', array());
        $new_use_for_shipping = $new_billingData['use_for_shipping'];
        
        if($new_use_for_shipping) {
            $bill_data = $this->getRequest()->getPost('billing', array());
            /* $bill_data = $this->getRequest()->getPost('shipping', array()); */
             $bill_addr_id = $this->getRequest()->getPost('shipping_address_id', false);
        } else {
            $bill_data = $this->getRequest()->getPost('shipping', array());
            if(isset($new_billingData['email'])) $bill_data['email'] = $new_billingData['email'];
            $bill_data['use_for_shipping'] = $new_billingData['use_for_shipping'];
            $bill_addr_id = $this->getRequest()->getPost('billing_address_id', false);
        }
        
        $bill_data = $this->_filterPostData($bill_data);
       
        $result = array();
        $ship_updated = false;
        
// 27.10.13 special rules to update totals when payment method is checked
        $pm_applied = false;
//
        // need for verification
        $billing_address_changed    = false;
        if($this->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled))
            $billing_address_changed    = true;
            
        $shipping_address_changed   = false;
        
        if ($billing_address_changed || $this->getRequest()->getPost('payment-method', false))
        {
            if (isset($bill_data['email']))
            {
                $bill_data['email'] = trim($bill_data['email']);
            }

            $bill_result = $this->getOnepagecheckout()->saveBilling($bill_data, $bill_addr_id, false);

            if (!isset($bill_result['error']))
            {
                $pmnt_data = $this->getRequest()->getPost('payment', array());
                $this->getOnepagecheckout()->usePayment(isset($pmnt_data['method']) ? $pmnt_data['method'] : null);

// 27.10.13 special rules to update totals when payment method is checked
                $pm_applied = true;
//
                //$result['update_section']['payment-method'] = $this->_getPaymentMethodsHtml();

                if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1 && !$this->getOnepagecheckout()->getQuote()->isVirtual())
                {
                    $result['update_section']['shipping-method'] = $this->_getShippingMethodsHtml();
                    /* $result['duplicateBillingInfo'] = 'true'; */
                    
                    $ship_updated = true;
                    
                    if($billing_address_changed)
                        $shipping_address_changed = true;
                }
            }
            else
            {
                $result['error_messages'] = $bill_result['message'];
            }
        }

// 27.10.13 special rules to update totals when payment method is checked
        if ($this->getRequest()->getPost('payment-changed', false))
        {
            if(!$pm_applied)
            {
                $pmnt_data = $this->getRequest()->getPost('payment', array());
                $this->getOnepagecheckout()->usePayment(isset($pmnt_data['method']) ? $pmnt_data['method'] : null);
            }
        }
///////////
        
        $new_billingData = $this->getRequest()->getPost('billing', array());
        $new_use_for_shipping = $new_billingData['use_for_shipping'];
        if($new_use_for_shipping){
            $ship_data = $this->getRequest()->getPost('shipping', array());
        } else {
            $ship_data = $this->getRequest()->getPost('billing', array());
        }
        
        $ship_addr_id = $this->getRequest()->getPost('shipping_address_id', false);
        $ship_method    = $this->getRequest()->getPost('shipping_method', false);

        $url_shipment=$this->getRequest()->getParam('shipment');

        $button1=$this->getRequest()->getParam('button',false);

        
        if($button1){
        

        if(Mage::getSingleton('customer/session')->isLoggedIn())        //if customer logged in 
        {
            if(strlen($ship_addr_id)>0)                                    //if saved address selected
            {
               
               $cust_addr= Mage::getModel('customer/address')->load($ship_addr_id);//vsk
               $cust_addr_country= $cust_addr->getCountry();                                    //vsk
               $url_shipment=Mage::getSingleton('checkout/session')->getData('shipment2');
               
        
               if(strlen($cust_addr_country) > 0 && $cust_addr_country!='US')
                  $ship_method ='angnonusflatrate_angnonusflatrate';
               else
                  $ship_method ='freeshipping_freeshipping';
                if($cust_addr_country=='US' && $url_shipment=='angnonusflatrate_angnonusflatrate')
                    {
                       $session_shipment=Mage::getSingleton('checkout/session')->getData('shipment');
                    if(strlen($session_shipment)>0 && $session_shipment != 'angnonusflatrate_angnonusflatrate')
                        $ship_method =$session_shipment;
                    else
                        $ship_method ='freeshipping_freeshipping';
                   
                   
                    } 
                else if($cust_addr_country=='US' && (Mage::getSingleton('checkout/session')->getData('shipment')=='angnonusflatrate_angnonusflatrate'))
                {
                    $ship_method=$url_shipment;
                    
                }
                else if($cust_addr_country=='US')
                {
                    $ship_method=Mage::getSingleton('checkout/session')->getData('shipment');
                }
                else
                {
                    $ship_method ='angnonusflatrate_angnonusflatrate';
                        $result['reload_totals'] = 'true';
                    }
            }
            else
            {
                
                $non_us_default = $this->getRequest()->getPost('billing', array());
            
                if(isset($non_us_default['country_id']))
                {
                
                    $country=$non_us_default['country_id'];
                
                    $url_shipment=Mage::getSingleton('checkout/session')->getData('shipment2');
                    

                
                    if($country=='US' && $url_shipment=='angnonusflatrate_angnonusflatrate')
                    {
                       $session_shipment=Mage::getSingleton('checkout/session')->getData('shipment');
                    if(strlen($session_shipment)>0 && $session_shipment != 'angnonusflatrate_angnonusflatrate')
                        $ship_method =$session_shipment;
                    else
                        $ship_method ='freeshipping_freeshipping';
                   
                   
                    }
                    else if($country=='US' && (Mage::getSingleton('checkout/session')->getData('shipment')=='angnonusflatrate_angnonusflatrate'))
                      {
                         $ship_method=$url_shipment;
                    
                      }
                     else if($country=='US')
                     {
                         $ship_method=Mage::getSingleton('checkout/session')->getData('shipment');
                      }
                     else
                     {
                          $ship_method ='angnonusflatrate_angnonusflatrate';
                    
                     }

                }
            }
        }  //end of if logged in

        else{
            
            $non_us_default = $this->getRequest()->getPost('billing', array());
            
            if(isset($non_us_default['country_id'])){
                
                $country=$non_us_default['country_id'];
                
                 $url_shipment=Mage::getSingleton('checkout/session')->getData('shipment2');
                

                
                if($country=='US' && $url_shipment=='angnonusflatrate_angnonusflatrate')
                {
                    $session_shipment=Mage::getSingleton('checkout/session')->getData('shipment');
                    if(strlen($session_shipment)>0 && $session_shipment != 'angnonusflatrate_angnonusflatrate')
                        $ship_method =$session_shipment;
                    else
                        $ship_method ='freeshipping_freeshipping';
                   
                   
                   
                }
                else if($country=='US' && (Mage::getSingleton('checkout/session')->getData('shipment')=='angnonusflatrate_angnonusflatrate'))
                {
                    $ship_method=$url_shipment;
                    
                }
                else if($country=='US')
                {
                    $ship_method=Mage::getSingleton('checkout/session')->getData('shipment');
                }
                else
                {
                    $ship_method ='angnonusflatrate_angnonusflatrate';

                    /*switch($country)
                    {
                //case 'CA':  $ship_method ='angmexcanflatrate_angmexcanflatrate'; break;
                       case 'US':  $ship_method ='freeshipping_freeshipping'; break;
                       default: $ship_method ='angnonusflatrate_angnonusflatrate';
                    }*/

                }

        }
        }
      //} //end of check before after
    }  // end of button1
    
        
         //$result['update_section']['vaibhav'] =$ship_method;
        
        if($ship_method){
            Mage::getSingleton('checkout/session')->setData('shipment',$ship_method);
        }

        if (!$ship_updated && !$this->getOnepagecheckout()->getQuote()->isVirtual())
        {
            $real_shipping_address_changed  = false;

// fix (26.10.13) (when user click 'same as billing' need to save this)
            // check if 'same as billing' was checked
            if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1)
            {
                $this->getOnepagecheckout()->saveBilling($bill_data, $bill_addr_id, false);
                $ship_updated   = true; 
                $shipping_address_changed   = true;
            }
            else
            {
                if ($this->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
                {
                    $shipping_address_changed = true;
                    $real_shipping_address_changed  = true;
                }
                
                if (($real_shipping_address_changed || $ship_method) && !$ship_updated) 
                {
                    $ship_result = $this->getOnepagecheckout()->saveShipping($ship_data, $ship_addr_id, false);
    
                    if (!isset($ship_result['error']))
                    {
                        $result['update_section']['shipping-method'] = $this->_getShippingMethodsHtml();
                    }
                }
            }

// fix
            if(!isset($result['update_section']['shipping-method']) && $this->getRequest()->getPost('shipping-method', false))
            {
                $result['update_section']['shipping-method'] = $this->_getShippingMethodsHtml();
            }
        }

        $check_shipping_diff    = false;

        // check how many shipping methods exist
        $rates = Mage::getModel('sales/quote_address_rate')->getCollection()->setAddressFilter($this->getOnepagecheckout()->getQuote()->getShippingAddress()->getId())->toArray();
        if(count($rates['items'])==1)
        {
            if($rates['items'][0]['code']!=$ship_method)
            {
                $check_shipping_diff    = true;

                $result['reload_totals'] = 'true';
            }
        }
        else        
            $check_shipping_diff    = true;

// get prev shipping method
        if($check_shipping_diff){
            $shipping = $this->getOnepagecheckout()->getQuote()->getShippingAddress();
            $shippingMethod_before = $shipping->getShippingMethod();
        }

/// special fix (see above). Sometimes there are issues with shipping methods. It may solve issue for some system
/*
        if(count($rates_before['items'])<=1 && count($rates['items'])>1)
        {
            if(empty($shippingMethod_before) || $shippingMethod_before == 'storepickupmodule_pickup')
            {
                foreach ($rates['items'] as $_rate)
                {
                    if($_rate['code'] != 'storepickupmodule_pickup')
                    {
                        $ship_method    = $_rate['code'];
                        break;
                    }
                }
            }
        }
*/
/// end special fix
        
        $this->getOnepagecheckout()->useShipping($ship_method);

        $this->getOnepagecheckout()->getQuote()->collectTotals()->save();

        if($check_shipping_diff){        
            $shipping = $this->getOnepagecheckout()->getQuote()->getShippingAddress();
            $shippingMethod_after = $shipping->getShippingMethod();
        
            if($shippingMethod_before != $shippingMethod_after)
            {
                $result['update_section']['shipping-method'] = $this->_getShippingMethodsHtml();
                $result['reload_totals'] = 'true';
            }
            else
                unset($result['reload_totals']);
        }
///////////////

        $result['update_section']['preview'] = $this->_getPreviewHtml();        //  S:VA
        $result['update_section']['review'] = $this->_getReviewHtml();

        
        /*********** DISCOUNT CODES **********/
        if ($couponChanged) {
            if ($couponData['code'] == $quote->getCouponCode()) {
                Mage::getSingleton('checkout/session')->addSuccess(
                    Mage::helper('onepagecheckout')->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponData['code']))
                );
            } else {
                Mage::getSingleton('checkout/session')->addError(
                    Mage::helper('onepagecheckout')->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponData['code']))
                );
            }
            $method = str_replace(' ', '', ucwords(str_replace('-', ' ', 'coupon-discount')));          
            $result['update_section']['coupon-discount'] = $this->{'_get' . $method . 'Html'}();
           
        }
        /************************************/
        
        
        
        
        /*************** Address Verification ***********/
        
        // check if need to validate address 
        if(Mage::helper('onepagecheckout')->isAddressVerificationEnabled())
        {
            $bill_was_validated = $this->getOnepagecheckout()->getCheckout()->getBillingWasValidated();
            if($billing_address_changed)
                $this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
        
            if($shipping_address_changed)
            {
                // check if shipping is the same as billing
                if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1 && !$this->getOnepagecheckout()->getQuote()->isVirtual())
                    $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(true);
                else
                    $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
            }
            
            // clear validation results
            $this->getOnepagecheckout()->getCheckout()->setBillingValidationResults(false);
            $this->getOnepagecheckout()->getCheckout()->setShippingValidationResults(false);
        }

        /*************** End Address Verification ***********/
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function forgotpasswordAction()
    {
        $session = Mage::getSingleton('customer/session');

        if ($this->_expireAjax() || $session->isLoggedIn()) {
            return;
        }

        $email = $this->getRequest()->getPost('email');
        $result = array('success' => false);
        
        if (!$email)
        {
            $result['error'] = Mage::helper('onepagecheckout')->__('Please enter your email.');
        }
        else
        {
            if (!Zend_Validate::is($email, 'EmailAddress'))
            {
                $session->setForgottenEmail($email);
                $result['error'] = Mage::helper('onepagecheckout')->__('Invalid email address.');
            }
            else
            {
                $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                if(!$customer->getId())
                {
                    $session->setForgottenEmail($email);
                    $result['error'] = Mage::helper('onepagecheckout')->__('This email address was not found in our records.');
                }
                else
                {
                    try
                    {
                        $new_pass = $customer->generatePassword();
                        $customer->changePassword($new_pass, false);
                        $customer->sendPasswordReminderEmail();
                        $result['success'] = true;
                        //$result['message'] = Mage::helper('onepagecheckout')->__('A new password has been sent.');
                        $result['message'] = Mage::helper('onepagecheckout')->__('Password reset link has been sent to your mailbox.');
                    }
                    catch (Exception $e)
                    {
                        $result['error'] = $e->getMessage();
                    }
                }
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function loginAction()
    {
        $session = Mage::getSingleton('customer/session');
        if ($this->_expireAjax() || $session->isLoggedIn()) {
            return;
        }

        $result = array('success' => false);

        if ($this->getRequest()->isPost())
        {
            $login_data = $this->getRequest()->getPost('login');
            if (empty($login_data['username']) || empty($login_data['password'])) {
                $result['error'] = Mage::helper('onepagecheckout')->__('Login and password are required.');
            }
            else
            {
                try
                {
                    $session->login($login_data['username'], $login_data['password']);
                    $result['success'] = true;
                    $result['redirect'] = Mage::getUrl('*/*/index', array('_secure'=>true));
                }
                catch (Mage_Core_Exception $e)
                {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('onepagecheckout')->__('Email is not confirmed. <a href="%s">Resend confirmation email.</a>', Mage::helper('customer')->getEmailConfirmationUrl($login_data['username']));
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $result['error'] = $message;
                    $session->setUsername($login_data['username']);
                }
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        $validation_enabled = Mage::helper('onepagecheckout')->isAddressVerificationEnabled();
            
        $result = array();
        try {
            
            $new_billingData = $this->getRequest()->getPost('billing', array());
            $new_use_for_shipping = $new_billingData['use_for_shipping'];
            
            if($new_use_for_shipping) {
                $bill_data = $this->getRequest()->getPost('billing', array());
                $bill_addr_id = $this->getRequest()->getPost('shipping_address_id', false);
            } else {
                $bill_data = $this->getRequest()->getPost('shipping', array());
                if(isset($new_billingData['email'])) $bill_data['email'] = $new_billingData['email'];
                $bill_data['use_for_shipping'] = $new_billingData['use_for_shipping'];
                $bill_addr_id = $this->getRequest()->getPost('billing_address_id', false);
            }
            
            
            
            // need for verification
            $ship_updated = false;
            $shipping_address_changed   = false;

            // get prev shipping data.
            $prev_ship = $this->getOnepagecheckout()->getQuote()->getShippingAddress();
            $prev_same_as_bill = $prev_ship->getSameAsBilling();

            $billing_address_changed    = false;
            if($this->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled))
            {
                $billing_address_changed    = true;
                $this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
            }
            
            $result = $this->getOnepagecheckout()->saveBilling($bill_data,$bill_addr_id,true,true);
            if ($result)
            {
                $result['error_messages'] = $result['message'];
                $result['error'] = true;
                $result['success'] = false;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }

            // need for address validation
            if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1 && !$this->getOnepagecheckout()->getQuote()->isVirtual())
            {
                $ship_updated = true;
                if($billing_address_changed)
                    $shipping_address_changed = true;
                $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(true);
            }
                            
            if ((!$bill_data['use_for_shipping'] || !isset($bill_data['use_for_shipping'])) && !$this->getOnepagecheckout()->getQuote()->isVirtual())
            {
                $ship_data      = $this->_filterPostData($this->getRequest()->getPost('billing', array()));
                $ship_addr_id   = $this->getRequest()->getPost('shipping_address_id', false);

                if (!$ship_updated)
                {
                    if ($this->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
                    {
                        $shipping_address_changed = true;
                        $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
                    }
                    else
                    {
                        // check if 'use for shipping' has been changed
                        if($prev_same_as_bill == 1)
                        {
                            $shipping_address_changed = true;
                            $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
                        }
                    }
                }
                
                $result = $this->getOnepagecheckout()->saveShipping($ship_data,$ship_addr_id, true, true);
                if ($result)
                {
                    $result['error_messages'] = $result['message'];
                    $result['error'] = true;
                    $result['success'] = false;
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

    /*************** Address Verification ***********/
        
    // check if need to validate address 
    if($validation_enabled)
    {
        // setup library for validation
        $this->getOnepagecheckout()->setVerificationLib($validation_enabled);
        
        if(Mage::helper('onepagecheckout')->allowNotValidAddress($validation_enabled)) // if not valid addresses allowed for checkout
            $bill_was_validated = $this->getOnepagecheckout()->getCheckout()->getBillingWasValidated();
        else
            $bill_was_validated = false;

        if(!$bill_was_validated)
        {
            $bill_validate  = $this->getOnepagecheckout()->validate_address('Billing');
            if($bill_validate)
                $this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(true);
            else
                $this->getOnepagecheckout()->getCheckout()->setBillingWasValidated(false);
        }
        
        if(Mage::helper('onepagecheckout')->allowNotValidAddress($validation_enabled)) // if not valid addresses allowed for checkout
            $ship_was_validated = $this->getOnepagecheckout()->getCheckout()->getShippingWasValidated();
        else
            $ship_was_validated = false;

        if(!$this->getOnepagecheckout()->getQuote()->isVirtual())
        {
            if(!$ship_was_validated)
            {
                // check if shipping is the same as billing
                if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1)
                    $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(true);
                else
                {
                    $ship_validate  = $this->getOnepagecheckout()->validate_address('Shipping');
                    if($ship_validate)
                        $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(true);
                    else
                        $this->getOnepagecheckout()->getCheckout()->setShippingWasValidated(false);
                }
            }
        }

        // check if exist validation results for any address
        if((isset($bill_validate) && is_array($bill_validate)) || (isset($ship_validate) && is_array($ship_validate)))
        {
            if((isset($bill_validate) && isset($bill_validate['error']) && !empty($bill_validate['error'])) ||
               (isset($ship_validate) && isset($ship_validate['error']) && !empty($ship_validate['error'])) 
            )
            {
                $result['update_section']['address-candidates'] = $this->_getAddressCandidatesHtml();
                if(isset($bill_validate) && isset($bill_validate['error']))
                {
                    if(!empty($bill_validate['error']))
                        $result['not_valid_address'] = true;
                    else
                        $result['billing_valid'] = true;
                }
            
                if(isset($ship_validate) && isset($ship_validate['error']))
                {
                    if(!empty($ship_validate['error']))
                        $result['not_valid_address'] = true;
                    else
                        $result['shipping_valid'] = true;
                }

                // clear validation results
                $this->getOnepagecheckout()->getCheckout()->setBillingValidationResults(false);
                $this->getOnepagecheckout()->getCheckout()->setShippingValidationResults(false);
                
                $result['error'] = true;
                $result['success'] = false;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
        }
        
        // clear validation results
        $this->getOnepagecheckout()->getCheckout()->setBillingValidationResults(false);
        $this->getOnepagecheckout()->getCheckout()->setShippingValidationResults(false);
    }
    /*************** End Address Verification ***********/
            
            
            $agreements = Mage::helper('onepagecheckout')->getAgreeIds();
            if($agreements)
            {
                $post_agree = array_keys($this->getRequest()->getPost('agreement', array()));
                $is_different = array_diff($agreements, $post_agree);
                if ($is_different)
                {
                    $result['error_messages'] = Mage::helper('onepagecheckout')->__('Please agree to all the terms and conditions.');
                    $result['error'] = true;
                    $result['success'] = false;
                    
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            
            $result = $this->_saveOrderPurchase();
            
            // Start easy pay - Anil Jain
            $this->easyPayProcessAction($this->getOnepagecheckout()->getQuote());       
            // End easy pay - Anil Jain

            if($result && !isset($result['redirect']))
            {
                $result['error_messages'] = $result['error'];
            }

            if(!isset($result['error']))
            {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepagecheckout()->getQuote()));
                $this->_subscribeNews();
            }

            Mage::getSingleton('customer/session')->setOrderCustomerComment($this->getRequest()->getPost('order-comment'));

            $this->getOnepagecheckout()->getQuote()->setCustomerNote($this->getRequest()->getPost('order-comment'));

            if (!isset($result['redirect']) && !isset($result['error']))
            {
                $pmnt_data = $this->getRequest()->getPost('payment', false);
                if ($pmnt_data)
                    $this->getOnepagecheckout()->getQuote()->getPayment()->importData($pmnt_data);

                $this->getOnepagecheckout()->saveOrder();
                $redirectUrl = $this->getOnepagecheckout()->getCheckout()->getRedirectUrl();

                $result['success'] = true;
                $result['error']   = false;
                $result['order_created'] = true;
            }
        }
        catch (Mage_Core_Exception $e)
        {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepagecheckout()->getQuote(), $e->getMessage());

            $result['error_messages'] = $e->getMessage();
            $result['error'] = true;
            $result['success'] = false;
            
            $custom_msg = $e->getMessage();
            $system_error_msg = $e->getMessage();
            
            if($custom_msg == "Declined") {
                $custom_msg = "Sometimes banks block certain credit and debit card numbers for security purposes. We would request you to call the bank to authorize the purchase on Angara and try purchasing again. You can also call us on 1-888-926-4272 or chat with us for further assistance.";
                $this->emailAlertOrderProblem($this->getOnepagecheckout()->getQuote(),'Decline',$system_error_msg);             
            }
            elseif($custom_msg == "Referral") {
                $custom_msg = "Your transaction is not approved by your card issuing bank. \n\rPlease contact your card issuing bank to provide a verbal authorization. \n\rYou may also try using another card. Once approved, click \"Complete Order\" again. \n\rIf this does not resolve the problem, please contact us.";
                $this->emailAlertOrderProblem($this->getOnepagecheckout()->getQuote(),'Referral',$system_error_msg);
            }
            else{
                $this->emailAlertOrderProblem($this->getOnepagecheckout()->getQuote(),'Bounce',$system_error_msg);
            }
            
            $result['error_messages'] = $custom_msg;
            
            $goto_section = $this->getOnepagecheckout()->getCheckout()->getGotoSection();
            if ($goto_section)
            {
                $this->getOnepagecheckout()->getCheckout()->setGotoSection(null);
                $result['goto_section'] = $goto_section;
            }

            $update_section = $this->getOnepagecheckout()->getCheckout()->getUpdateSection();
            if ($update_section)
            {
                if (isset($this->_sectionUpdateFunctions[$update_section]))
                {
                    $layout = $this->_getUpdatedLayout();

                    $updateSectionFunction = $this->_sectionUpdateFunctions[$update_section];
                    $result['update_section'] = array(
                        'name' => $update_section,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepagecheckout()->getCheckout()->setUpdateSection(null);
            }

            $this->getOnepagecheckout()->getQuote()->save();
        } 
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepagecheckout()->getQuote(), $e->getMessage());
            $result['error_messages'] = Mage::helper('onepagecheckout')->__('There was an error processing your order. Please contact support or try again later.');
            $result['error']    = true;
            $result['success']  = false;
            
            $this->getOnepagecheckout()->getQuote()->save();
        }

        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    

    protected function _saveOrderPurchase()
    {
        $result = array();
        
        try 
        {
            $pmnt_data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepagecheckout()->savePayment($pmnt_data);

            $redirectUrl = $this->getOnepagecheckout()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl)
            {
                $result['redirect'] = $redirectUrl;
            }
        }
        catch (Mage_Payment_Exception $e)
        {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        }
        catch (Mage_Core_Exception $e)
        {
            $result['error'] = $e->getMessage();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            $result['error'] = Mage::helper('onepagecheckout')->__('Unable to set Payment Method.');
        }
        return $result;
    }

    protected function _subscribeNews()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('newsletter'))
        {
            $customerSession = Mage::getSingleton('customer/session');

            if($customerSession->isLoggedIn())
                $email = $customerSession->getCustomer()->getEmail();
            else
            {
                $bill_data = $this->getRequest()->getPost('billing');
                $email = $bill_data['email'];
            }

            try {
                if (!$customerSession->isLoggedIn() && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1)
                    Mage::throwException(Mage::helper('onepagecheckout')->__('Sorry, subscription for guests is not allowed. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));

                $ownerId = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email)->getId();
                
                if ($ownerId !== null && $ownerId != $customerSession->getId())
                    Mage::throwException(Mage::helper('onepagecheckout')->__('Sorry, you are trying to subscribe email assigned to another user.'));

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
            }
            catch (Mage_Core_Exception $e) {
            }
            catch (Exception $e) {
            }
        }
    }

    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
    
    public function emailAlertOrderProblem($getQuoteArr=NULL,$email_type='Approved',$system_error_msg=NULL)
    {   
		// Billing Information
        $billingArr = $getQuoteArr->getBillingAddress()->getData();
        
        // Card/Payment Information
        $cardPaymentArr = $getQuoteArr->getPayment()->getData();
        
        // Cart Data
        $cartArr = $getQuoteArr->getAllItems();         
        
        $firstName = trim($billingArr['firstname']);
        $lastName = trim($billingArr['lastname']);
        $email = trim($billingArr['email']);
        $address1 = trim($billingArr['street']);
        $address2 = trim($billingArr['street']);
        $city = trim($billingArr['city']);
        $state =trim($billingArr['region']);
        $zip = trim($billingArr['postcode']);
        $country = trim($billingArr['country_id']);
        $phone = trim($billingArr['telephone']);
                
		//	S:VA 
		$domainName	=	Mage::getBaseUrl();
		if(strstr($domainName, 'www.angara.com')){
			 $to  = Mage::helper('function')->getDeclineEmailAddresses();
		}else{
			 $to  = 'qaangara@gmail.com';	
		}
		//	E:VA
                
        $subject = 'Order process - '.$email_type.' at '.$domainName.' - '.date('Y-m-d H:i');
        $emi_profile_list = 'NA';
        if(isset($_SESSION['emi_profile_list'])){
            $emi_profile_list = $_SESSION['emi_profile_list'];
            // start profile cancel by api if order decline by system
            include_once 'app/code/core/Mage/Easypay/constants.php';
            include_once 'app/code/core/Mage/Easypay/CallerService.php';
            $profile_cancel_param = "TRXTYPE=R&TENDER=C&PARTNER=".PAYPAL_PARTNER."&VENDOR=".PAYPAL_VENDOR."&USER=".PAYPAL_USER."&PWD=".PAYPAL_PWD."&ACTION=C&ORIGPROFILEID";
            if(trim($emi_profile_list) != ''){
                $profile_arr = explode(',',$emi_profile_list);
                foreach($profile_arr as $profileparam){
                    $acturl = $profile_cancel_param.'='.$profileparam;
                    $res_arr = hash_call(PAYPAL_RECURRING_URL,trim($acturl));   
                    Mage::log('Easy Pay Request Gateway Decline Profile Response: '.$acturl, null, 'easy_pay.log');
                }
            }
            // end profile cancel by api if order decline by system
        }else{
            $emi_profile_list = 'NA';   
        }
        
        // message
        $message = '<p><h3>'.$subject.'</h3></p>
          <table width="600">
            <tr>
                <td align="left" valign="top" colspan="2"><strong>Customer Billing Information</strong><hr width="100%"></td>               
            </tr>           
            <tr>
                <td align="left" valign="top">Customer Name: </td>
                <td align="left" valign="top">'.$firstName.' '.$lastName.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Email: </td>
                <td align="left" valign="top">'.$email.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Address1: </td>
                <td align="left" valign="top">'.$address1.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Address2: </td>
                <td align="left" valign="top">'.$address2.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">City: </td>
                <td align="left" valign="top">'.$city.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">State: </td>
                <td align="left" valign="top">'.$state.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Country: </td>
                <td align="left" valign="top">'.$country.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Zip: </td>
                <td align="left" valign="top">'.$zip.'</td>
            </tr>
            <tr>
                <td align="left" valign="top">Phone: </td>
                <td align="left" valign="top">'.$phone.'</td>
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2">&nbsp;</td>               
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2"><strong>Shopping Cart Information</strong><hr width="100%"></td>              
            </tr>           
            <tr>
                <td align="left" valign="top" colspan="2">
                    <table>';
                        $aj = 1;
                        foreach($cartArr as $item){
                            $product_info = $item->getProduct()->getSku().' : '.$item->getProduct()->getName();                                 
                            $message.='<tr>
                                    <td align="left" valign="top">Product '.$aj.': </td>
                                    <td align="left" valign="top">'.$product_info.'</td>                
                                </tr>';
                            $aj++;  
                        }
                    $message.='</table>
                </td>               
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2"><strong>EMI Profile Information</strong><hr width="100%"></td>                
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2">'.$emi_profile_list.'</td>                
            </tr>
            <tr>
                <td align="left" valign="top"><strong>Customer Error Message: </strong></td>
                <td align="left" valign="top">'.$system_error_msg.'</td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>IP Address: </strong></td>
                <td align="left" valign="top">'.@$_SERVER["REMOTE_ADDR"].'</td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>User Browser Info: </strong></td>
                <td align="left" valign="top">'.@$_SERVER['HTTP_USER_AGENT'].'</td>
            </tr>           
          </table>';
        
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
        // Additional headers       
        $headers .= 'From: Angara Order Technical Department <techsupport@angara.com>' . "\r\n";        
        
        // Mail it
        mail($to, $subject, $message, $headers);
    }
    
    protected function _checkChangedAddress($data, $addr_type = 'Billing', $addr_id = false, $check_city_street = false)
    {
        $method = "get{$addr_type}Address";
        $address = $this->getOnepagecheckout()->getQuote()->{$method}();

        if(!$addr_id)
        {
        	//if($addr_type == 'Billing'){
				return true;
			/*}
			else{
				if(($address->getRegionId()	!= $data['region_id']) || ($address->getPostcode() != $data['postcode']) || ($address->getCountryId() != $data['country_id']))
					return true;

				// if need to compare street and city
				if($check_city_street)
				{
					// check street address
					$street1	= $address->getStreet();
					$street2	= $data['street'];

					if(is_array($street1))
					{
						if(is_array($street2))
						{
							if(trim(strtolower($street1[0])) != trim(strtolower($street2[0])))
							{
								return true;
							}
							if(isset($street1[1]))
							{
								if(isset($street2[1]))
								{
									if(trim(strtolower($street1[1])) != trim(strtolower($street2[1])))
										return true;        						
								}
								else
								{
									if(!empty($street1[1]))
										return true;
								}
							}
							else
							{
								$s21	= trim($street2[1]);
								if(isset($street2[1]) && !empty($s21))
									return true;
							}
						}
						else
						{
							if(trim(strtolower($street1[0])) != trim(strtolower($street2)))
								return true;
						}
					}
					else
					{
						if(is_array($street2))
						{
							if(trim(strtolower($street1)) != trim(strtolower($street2[0])))
								return true;
						}
				else
						{
							if(trim(strtolower($street1)) != trim(strtolower($street2)))
								return true;
						}
					}
					
					// check city
					$add_city	= $address->getCity();
					$add_city	= trim(strtolower($add_city));
					if( $add_city	!= trim(strtolower($data['city'])))
						return true;
				}
				
				return false;
			}*/
        }
        else{
            if($addr_id != $address->getCustomerAddressId())
                return true;
            else
                return false;
        }
    }
}
