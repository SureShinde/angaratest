<?php
 
/**
* Our test shipping method module adapter
*/
class Angara_Ang2dayflatrate_Model_Carrier_Ang2dayflatrate extends Mage_Shipping_Model_Carrier_Abstract
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */
  protected $_code = 'ang2dayflatrate';
  protected $_isFixed = true;
  
    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {

                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        $result = Mage::getModel('shipping/rate_result');
		
        if ($this->getConfigData('type') == 'O') { // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') { // per item
            $shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('ang2dayflatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('ang2dayflatrate');
            $method->setMethodTitle($this->getConfigData('name'));
			
            if (($this->getConfigData('free_shipping_enable') && $this->getConfigData('free_shipping_subtotal') <= $request->getBaseSubtotalInclTax()) || $request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
			
			//	S: Added By VA Modified by Pankaj
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			$shippingPriceAdmin	=	Mage::getStoreConfig("carriers/ang2dayflatrate/price");			//	S:VA	Dynamic Price
			if (stripos($_SERVER['HTTP_REFERER'], 'admin/sales_order_create') !== false) { // admin orders
				$gtotals = $request->getPackageValueWithDiscount();	
				if(!empty($customerId) && Mage::getSingleton('customer/session')->isLoggedIn()){
					$shippingPrice = $shippingPriceAdmin;
				}
				else{
					if($gtotals <= 2000){
						$shippingPrice = $shippingPriceAdmin;
					}else{
						$shippingPrice = $shippingPriceAdmin;
					}
				}
			}else{
				$grandtotalwithout_easy = Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments(); // front end order
				if(!empty($customerId) && Mage::getSingleton('customer/session')->isLoggedIn()){
					$shippingPrice = $shippingPriceAdmin;
				}
				else{
					if($grandtotalwithout_easy <= 2000){
						$shippingPrice = $shippingPriceAdmin;
					}else{
						$shippingPrice = $shippingPriceAdmin;
					}
				}
			}
			//	E:VA
             //VSK
            $flag=0;
            //Mage::log($this->getEmailFreeShipping(),null,"white1.log",true);
            
            $flag=$this->getEmailFreeShipping();
            
            //Mage::log($flag,null,"flagcheck.log",true);

            if($flag)
            {
				$shippingPrice = '0.00';
            }
			
			$couponCode = Mage::getSingleton('core/session')->getPromotionCode();
			$couponShipping = Mage::helper('function')->getCouponShipping($couponCode);
			if($couponShipping && $couponShipping == 'ang2dayflatrate_ang2dayflatrate'){
				$shippingPrice = '0.00';
			}
			
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('ang2dayflatrate'=>$this->getConfigData('name'));
    }

    public function getEmailFreeShipping()
    {
        if(Mage::getSingleton('checkout/session')->getEmailFreeShipping())
            {

              $email=Mage::getSingleton('checkout/session')->getEmailFreeShipping();
             
              
              $subs=Mage::getModel('newsletter/subscriber')->loadByEmail($email);
              if ($subs->getId()) 
              {
                 if($subs->getFreeShipping())
                 {
                    return 1;
                 }
              }
              else
              {
                
                Mage::getSingleton('checkout/session')->unsEmailFreeShipping();
              }
     
            }
        return 0;
       
    }
  
}
?>