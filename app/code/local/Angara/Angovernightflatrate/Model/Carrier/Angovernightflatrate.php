<?php
 
/**
* Our test shipping method module adapter
*/
class Angara_Angovernightflatrate_Model_Carrier_Angovernightflatrate extends Mage_Shipping_Model_Carrier_Abstract
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */
  protected $_code = 'angovernightflatrate';
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
		$subtotalAmount = 0;		//	S:VA
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
				$subtotalAmount += ($item->getProduct()->getFinalPrice() * $item->getQty());		//	S:VA
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

            $method->setCarrier('angovernightflatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('angovernightflatrate');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
			
			//	S:VA	Free shipping based on price condition set from admin
			$shippingPrice 		= 	Mage::getStoreConfig("carriers/angovernightflatrate/price"); // S:VA Dynamic Price			
			$freeShippingMinAmt = 	Mage::getStoreConfig("carriers/angovernightflatrate/min_cart_value_for_free_shipping");
			
			if (stripos($_SERVER['HTTP_REFERER'], 'admin/sales_order_create') !== false) { // admin orders
				$gtotals = $request->getPackageValueWithDiscount();				
				if($gtotals >= $freeShippingMinAmt){
					$shippingPrice = '0.00';
				}
			}
			else{
				if($freeShippingMinAmt > 0 && $subtotalAmount > $freeShippingMinAmt){
					$shippingPrice = '0.00';
				}
			}
			//	E:VA
			
			$couponCode = Mage::getSingleton('core/session')->getPromotionCode();
			$couponShipping = Mage::helper('function')->getCouponShipping($couponCode);
			if($couponShipping && $couponShipping == 'angovernightflatrate_angovernightflatrate'){
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
        return array('angovernightflatrate'=>$this->getConfigData('name'));
    }
  
}
?>