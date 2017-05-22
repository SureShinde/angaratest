<?php
class Angara_Abandoncartmailchimp_Model_Observer extends Mage_Core_Model_Abstract
{
	function __construct(){}
	
	public function update($observer = null)
	{
	
	
	try{
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$symbol=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$couponcode=$quote->getCouponCode();
		$sessionId      = Mage::getModel('core/session')->getSessionId();
		$quote_id       = Mage::getSingleton('checkout/session')->getQuoteId();	
		$abandonCartDataSessionIdArr = array();
		$abandonCartData = Mage::getModel('abandoncartmailchimp/session')->getCollection()->getData();
			if(!empty($abandonCartData))
			   { 
					foreach($abandonCartData as $abandonCart){					
						$abandonCartDataSessionIdArr[] = $abandonCart['session_id'];	
					}	
				}			
				
				
		        if(in_array($sessionId ,$abandonCartDataSessionIdArr) || Mage::getSingleton('customer/session')->isLoggedIn())	
				{	
					if(Mage::getSingleton('customer/session')->isLoggedIn() && !in_array($sessionId ,$abandonCartDataSessionIdArr) )
					{			
						$customer = Mage::getSingleton('customer/session')->getCustomer();		
						$fname =   $customer->getFirstname();
						$lname  = $customer->getLastname();	
						$email = $customer->getEmail();	
						Mage::getModel('abandoncartmailchimp/session')		
							->setSessionId($sessionId)
							->setVisitorEmail($email)
							->setVisitorFirstname($fname)
							->setVisitorLastname($lname)																
							->setCreatedAt(now())								
							->save(); 
					}						
							
					$items = Mage::getModel('checkout/session')->getQuote()->getAllItems();
					
				
					
						     $quote_id = Mage::getSingleton('checkout/session')->getQuoteId();  
									
							$abandonCart = Mage::getModel('abandoncartmailchimp/visitor')->getCollection()
								->addFieldToFilter('quote_id',$quote_id)
								->load();
							if(!empty($abandonCart)){	
								foreach($abandonCart as $cart){	
							
									$AbonCartId = $cart->getId();
									Mage::getModel('abandoncartmailchimp/visitor')->load($AbonCartId)->delete();
								}
							}
				if(!empty($items))
					{
						foreach($items as $item)
						{
							unset($ringsize);
							unset($bandwidth);
							unset($stonegrade);
							unset($metaltype);
							unset($stonesize);
						
						
						
						    $price=$item->getPrice();							
							//	S:VA	Getting product actual without discount price
							$productOriginalPrice = Mage::helper('core')->currency(($item->getPrice())*($item->getQty()), true, false);	
							
							$discountedprice = ($item->getPrice())*($item->getQty()) - $item->getBaseDiscountAmount();	
							$finalprice=Mage::helper('core')->currency($discountedprice, true, false);								
							$product = Mage::getModel('catalog/product')->load($item->getProductId());
							$price = Mage::helper('core')->currency($item->getPrice(),false,false);	 
							$realprice=Mage::helper('core')->currency($item->getPrice(), true, false);
							$sku=$item->getSku();
							$sku = str_replace('-engraving', '', $sku);
							if(stripos($sku,'-ja0050') !== false){
								$sku = str_replace('-ja0050', '', $sku);
							}
							if($sku[0]=="A" && $sku[1]=="M") {
								$imageurl=$product->getImageUrl(); 
								}
							else{
								$imageurl=Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)->getImageUrl();
								}
							$itemdetails=$item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());						
							$attributeoptions=$itemdetails['attributes_info'];	
							
							
							foreach($attributeoptions as $option)
							{  
								if(	$option['code']=='ring_size')
									{
										$ringsize=$option['value'];
									}	
								if(	$option['code']=='stone1_size')
									{
										$stonesize=$option['value'];
									}	
								if(	$option['code']=='metal1_type')
									{
										$metaltype=$option['value'];
									}
								if(	$option['code']=='band_width')
									{
										$bandwidth=$option['value'];
									}
								if(	$option['code']=='stone1_grade')
									{
										$stonegrade=$option['value'];
									}
							}
							
					if($sku[0] != 'I' && $sku[0] != 'J' && $sku !="EMOP0001SC" && $sku != "OP0001SC")
				       {
						  if($price!= 0 || $sku[0]=='F')
						  {
								Mage::getModel('abandoncartmailchimp/visitor')
									->setQuoteId($quote_id)
									->setShareUrl($this->getShareUrl())
									->setQuoteItemId($item->getItemId())
									->setSessionId($sessionId)
									->setQuantity($item->getQty())																								
									->setCreatedAt(now())	
									->setProductSku($item->getSku())
									->setCouponCode($couponcode)
									->setCurrencySymbol($symbol)
									->setProductId($product->getId())
									->setProductName($product->getShortDescription())
									->setProductOriginalPrice($productOriginalPrice)		//	S:VA	Saving actual price in db
									->setProductPrice($finalprice)
									->setProductImage($imageurl)
									->setProductUrl($product->getProductUrl())
									->setRingSize($ringsize)
									->setStoneSize($stonesize)
									->setMetalType($metaltype)
									->setBandWidth($bandwidth)
									->setStoneGrade($stonegrade)
									->save();
			
								}
							}
								
						}	
					}
				}	
		}
		catch(Exception $e) { 
				Mage::logException($e); 
		}	
	}

	protected $_sharUrl = '';
	public function getShareUrl()
    {        
    	if(strlen($this->_sharUrl) > 0) return $this->_sharUrl;
        try {
            $sharecart = Mage::getModel('sharecart/cart');
            $data['user_id'] = 0;
          
            $data['items'] = serialize(Mage::helper('sharecart')->getItems());
            
            $sharecart->setData($data);
            
            $sharecart->save();
            
            $this->_sharUrl = Mage::helper('sharecart')->generateUrlShare($sharecart);

            return $this->_sharUrl;
        } catch (Exception $exc) {            
           
        }
        return Mage::getUrl('checkout/cart');
    } 
}
			
?>	    
		 
	