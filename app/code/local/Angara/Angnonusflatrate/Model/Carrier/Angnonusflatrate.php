<?php 
/**
* Our test shipping method module adapter
*/
class Angara_Angnonusflatrate_Model_Carrier_Angnonusflatrate extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	* unique internal shipping method identifier
	*
	* @var string [a-z0-9_]
	*/
	protected $_code = 'angnonusflatrate';
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
		$subtotalAmount = 0;
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
				
				$subtotalAmount += ($item->getProduct()->getFinalPrice() * $item->getQty());
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

            $method->setCarrier('angnonusflatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('angnonusflatrate');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
			
			$shippingPrice = Mage::getStoreConfig("carriers/angnonusflatrate/price"); // S:VA Dynamic Price			
			$freeShippingMinAmt = Mage::getStoreConfig("carriers/angnonusflatrate/min_cart_value_for_free_shipping");
			/*if($freeShippingMinAmt > 0 && $subtotalAmount > $freeShippingMinAmt){
				$shippingPrice = '0.00';
			}*/
			
			
			
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
			//	S:VA	Free shipping
			//$subTotalWithoutEasyEmi	= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
			//if($freeShippingMinAmt > 0 && $subTotalWithoutEasyEmi > $freeShippingMinAmt){
				//$shippingPrice	=	'0';
			//}
			
			

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('angnonusflatrate'=>$this->getConfigData('name'));
    }  
}?>