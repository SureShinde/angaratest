<?php
class Angara_Offers_CouponController extends Mage_Core_Controller_Front_Action{
	
	protected function _construct(){
        
    }
	
	public function offerMinimizedAction(){
		$minimized = $this->getRequest()->getParam('minimized');
		if($minimized)
			Mage::getSingleton("checkout/session")->setData("offer_minimized", true);
		else
			Mage::getSingleton("checkout/session")->setData("offer_minimized", false);
	}
	
	public function ApplyAction(){
		$params 	= $this->getRequest()->getParams();
		$code 		= $params['code'];
		$noChoose 	= $params['nochoose'];
		$refer 		= $params['r'];			//	S:VA	New param added so that the fname and email can be prefilled on refer a friend form
		$state 		= $params['state'];		//	S:VA	To identify visitor state and accordingly show the subscribe popup
		
		/* S: Mailchimp Tracking */
		$email=$params['email'];
		$fname =$params['fname'];
		$lname= $params['lname'];
		$sessionId = Mage::getModel('core/session')->getSessionId(); 
		if($refer != 1){
			unset($params['email']);
			unset($params['lname']);
			unset($params['fname']);  
		}
   
   
		if(isset($email)) {  
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				Mage::getModel('abandoncartmailchimp/session')		
							->setSessionId($sessionId)
							->setVisitorEmail($email)
							->setVisitorFirstname($fname)
							->setVisitorLastname($lname)																
							->setCreatedAt(now())								
							->save(); 
			}
		}	
		/* E: Mailchimp Tracking */
		
		if($code){
			Mage::getSingleton("checkout/session")->setData("offer_code", $code);
			Mage::getSingleton("checkout/session")->setData("visitor_state", $state);		//	S:VA	Creating variable to check visitor state
			if(!$noChoose)
				Mage::getSingleton("checkout/session")->setData("has_offer_to_process", true);
		}
		$url = $params['url'];
		$ajax = $params['ajax'];
		unset($params['code']);
		unset($params['url']);
		unset($params['ajax']);
		if($ajax){
			// do nothing
		}
		else if($url){
			if($params){
				$this->_redirectUrl($url.'?'.http_build_query($params));
			}
			else{
				$this->_redirectUrl($url);
			}	
		}
		else{
			if($params){
				$this->_redirectUrl('http://www.angara.com/?'.http_build_query($params));
			}
			else{
				$this->_redirectUrl('http://www.angara.com/');
			}
		}
		# @todo track visitors
		
		# @todo make custom offers page
		/*$this->loadLayout();
		$this->renderLayout();*/
    }
	
	public function viewSpecialOfferAction(){
		$_coreHelper = Mage::helper('core');
		$coupon = Mage::helper('function')->getCustomerCoupon();
		if(!empty($coupon)){
			$oCoupon = Mage::getModel('salesrule/coupon')->load($coupon, 'code');
			if($oCoupon->getId()){
				#todo pankaj i need to check coupon expire or not
				//$expirationDate = $oCoupon->getExpirationDate();
				//$currDate = strtotime(date('Y-m-d H:i:s'));
				//if($currDate < strtotime($expirationDate)){
					$oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
					if($oRule->getId()){
						$discountPercent = (int) $oRule->getDiscountAmount();
						$productId = (int) $this->getRequest()->getParam('productId');
						$addEngrave = (int) $this->getRequest()->getParam('hasEngraving');
						$hasRange = (int) $this->getRequest()->getParam('hasRange');
						$productMainId = (int) $this->getRequest()->getParam('productMainId');
						$productFinalPrice = $this->getRequest()->getParam('finalPrice');
						
						$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productId);
						if(isset($parentIds[0])){
							$mainProduct = Mage::getModel('catalog/product')->load($parentIds[0]);
						}
						else{
							$_product = $mainProduct = Mage::getModel('catalog/product')->load($productId);
						}
						
						if(!isset($_product)){
							$_product = Mage::getModel('catalog/product')->load($productId);
						}
						
						if($productMainId){
							$productMain = Mage::getModel('catalog/product')->load($productMainId);
							
							if($productMain->getTypeId() == "configurable"):
								$configurable = Mage::getModel('catalog/product_type_configurable')->setProduct($productMain);
								$simpleCollection = $configurable->getUsedProductCollection()
																->addAttributeToSelect('*')
																->addFilterByRequiredOptions();
								$minPrice = 99999;
								$maxPrice = 0;
																
								foreach($simpleCollection as $simpleProduct){
									if($simpleProduct->getPrice() < $minPrice)
										$minPrice = $simpleProduct->getPrice();
									
									if($simpleProduct->getPrice() > $maxPrice)
										$maxPrice = $simpleProduct->getPrice();										
								}							
							endif;
						}
						
						$productOptions = $mainProduct->getOptions();	
						if(!empty($productOptions)){	
							foreach($productOptions as $prodOpt){
								if($prodOpt->getSku() == 'engraving'){
									$priceEngraving = $prodOpt->getPrice();
								}
							}
						}
						
						if(!empty($productId)){
							$product = Mage::getModel('catalog/product')
											->setStoreId(Mage::app()->getStore()->getId())
											->load($productId);
							$productSku = $product->getSku();
							$excludeSku = array('AGIF00100', 'AGIF01000', 'AGIF10000', 'AGIF00150', 'AGIF01500', 'AGIF00200', 'AGIF02000', 'AGIF00250', 'AGIF02500', 'AGIF00300', 'AGIF03000', 'AGIF00350', 'AGIF00400', 'AGIF00500', 'AGIF05000', 'AGIF00750', 'OP0001SC', 'JA0050', 'INS001', 'EMOP0001SC');
							if($productSku && !in_array($productSku,$excludeSku)){
								if($productFinalPrice){										
									$productPrice = $productFinalPrice;
								}
								else{
									$productPrice = $product->getPrice();
								}
								
								$productRetailPrice = $product->getMsrp();
								if($addEngrave == 1 && $priceEngraving > 0)	{
									if($productFinalPrice){
										$productPrice = $productFinalPrice;
									}
									else{
										$productPrice = $productPrice + $priceEngraving;
									}
									$productRetailPrice = $productRetailPrice + $priceEngraving;
								}
								
								$totalDiscountAmount = 	$productPrice * ($discountPercent / 100);
								$netTotalAmount = $productPrice - $totalDiscountAmount;
								
								$discountedMinPrice = $minPrice - ($minPrice * ($discountPercent / 100));
								$discountedMaxPrice = $maxPrice - ($maxPrice * ($discountPercent / 100));
								
								$priceRange = $_coreHelper->currency($discountedMinPrice,true,false).' - '.$_coreHelper->currency($discountedMaxPrice,true,false);
								
								if($hasRange == 1){
									$priceValue = $priceRange;
								}
								else{
									if($productFinalPrice){
										$priceValue = Mage::helper('core')->formatPrice($netTotalAmount);
									}
									else{
										$priceValue = $_coreHelper->currency($netTotalAmount,true,false);
									}
								}
								//	S:VA
								
								/*$promotionCoupon	=	Mage::helper('function')->getPromotionCoupon();
								if(Mage::helper('function')->isMobile() == '1'){
									echo '<strong style="font-size:15px; color:green;">'.$discountPercent.'%</strong> Off Today: Get this Item for <span style="font-size:22px; color:#fa505a;"><br class="visible-xs"><strong>'.$priceValue.'</strong></span> <span class="hidden-md"></span> <b class="fontsize-type3">+</b> Free Gift(s) at Cart on all Orders.<br /><span style="line-height:10px; display:inline-block; padding-top:11px;">Use Discount Code</span> <span class="padding-type-2 text-green" style="background-color:#f0f8ef; border:dotted 1px #030b02"><strong>'.$coupon.'</strong></span>';
									// hardcoding mob15 coupon
									if(strtolower($coupon) == 'mob15'){
										echo '<br /><small class="fontcolor-type1">*This coupon code is valid only on mobile devices.</small></div></div>';
									}
								}else if( $discountPercent > 10 ) { 
									echo '<strong style="font-size:15px; color:green;">'.$discountPercent.'%</strong> Off Today: Get this Item for <span style="font-size:22px; color:#fa505a;"><br class="visible-xs"><strong>'.$priceValue.'</strong></span> <span class="hidden-md"></span> <b class="fontsize-type3">+</b> Free Gift(s) at Cart on all Orders.<br /><span style="line-height:10px; display:inline-block; padding-top:11px;">Use Discount Code</span> <span class="padding-type-2 text-green" style="background-color:#f0f8ef; border:dotted 1px #030b02"><strong>'.$coupon.'</strong></span></div></div>';
								}else{ 
									echo '<strong style="font-size:15px; color:green;">'.$discountPercent.'%</strong> Off <b class="fontsize-type3">+</b> Free Gift(s) at Cart on all Orders. Use Code <span class="padding-type-2 text-green" style="background-color:#f0f8ef; border:dotted 1px #030b02"><strong>'.$coupon.'</strong></span>
								<br /><div class="low-padding-top"><strong style="font-size:15px; color:green;">15%</strong> Off <b class="fontsize-type3">+</b> Free Gift(s) at Cart on all Orders Over $1000. Use Code <span class="padding-type-2 text-green" style="background-color:#f0f8ef; border:dotted 1px #030b02"><strong>'.$promotionCoupon.'</strong></span></div></div></div>';
								}*/
								echo '<strong style="font-size:15px; color:green;">'.$discountPercent.'%</strong> Off Today: Get this Item for <span style="font-size:22px; color:#fa505a;"><br class="visible-xs"><strong>'.$priceValue.'</strong></span> <span class="hidden-md"></span> <b class="fontsize-type3">+</b>';
								/*if( Mage::helper('function')->isMobile() ){
									echo 'Free gift(s) at cart on orders over $750.';
								}else{
									echo 'Free gift(s) at cart on all orders.';	
								}*/
								echo 'Free gift(s) at cart on all orders.';
								/*if(strtolower($coupon) == 'event'){
									echo '<span style="line-height:10px; display:inline-block; padding-top:11px;"> Additional <span style="font-size:22px; color:#fa505a;"><strong>$50</strong></span> off at cart on orders over $750.</span>';
								} 
								else{
									echo 'Free gift(s) at cart on orders over $750.'; 
								} */
								echo '<br /><span style="line-height:10px; display:inline-block; padding-top:11px;">Use Discount Code</span> <span class="padding-type-2 text-green" style="background-color:#f0f8ef; border:dotted 1px #030b02"><strong>'.$coupon.'</strong></span>';
								if(strtolower($coupon) == 'mob15'){
									echo '<br /><br /><small class="fontcolor-type1">*This coupon code is valid only on mobile devices.</small>';
								}
								echo '</div></div>';
								
								//	E:VA
							}
							else{
								echo 'The item is already at its best price.';
							}
						}
						else{
							echo 'The item is already at its best price.';
						}
					}
					else{
						echo 'The item is already at its best price.';
					}
				//}
			}
			else{
				echo 'The item is already at its best price.';
			}
		}
		else{
			echo 'The item is already at its best price.';
		}
		
	}
}