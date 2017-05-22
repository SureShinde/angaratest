<?php
class Customy_Tracking_Model_Tracking extends Mage_Core_Model_Abstract {

    /**
     * Loads tracking data
     *
     * @param string $email customer email
     * @param string $orderIncrementId order's increment_id
     * @return array tracks info list
     */
    public function getTrackingData($email, $orderIncrementId) 
	{
		// Get order by "order"
        $order = Mage::getModel('sales/order');
        $order->loadByAttribute('increment_id', $orderIncrementId);
		
        // Return empty array if order# not belongs to customer        
		if (strtolower($email) != strtolower($order['customer_email'])) {	//	S:VA Capital letters in email issue fixed
            return array();
        }
		
        // Get shipment of this order
        $shipments = $order->getShipmentsCollection();
		
		if (empty($shipments)) {
            return array();
        }

        // Collects trackings data from
        $result = array();
        foreach ($shipments as $shipment) { // each shipment
            $tracks = $shipment->getTracksCollection();
            if (empty($tracks)) {
                continue;
            }

            foreach ($tracks as $track) { // each track
                $track_data = $track->getData();
                if (empty($track_data)) {
                    continue;
                }
                $result[] = $track_data;
            }
        }

        return $result;
    }
	
	public function getOrderDetails($email, $orderIncrementId) 
	{
		// Get order by "order"
        $order = Mage::getModel('sales/order');
        $order->loadByAttribute('increment_id', $orderIncrementId);
				
		// For Track Your Order Added by Pankaj on 18052015 
		$order_item_collection = $order->getAllVisibleItems();	
		
		$shippingMethodCode = $order['shipping_method'];
		$shippingMethodShortForm = Mage::helper('function')->shippingShortForm($shippingMethodCode);
		$leadTimeShippingMethod	= Mage::helper('function')->getShippingDays($shippingMethodShortForm);
		$orderCompleteDate  = Mage::helper('sales')->formatDate($order['created_at'], 'medium', true);
		//$scheduleLeadTime = Mage::helper('function')->scheduleLeadTime($orderCompleteDate);
		$leadTimeDateRules = Mage::helper('function')->getLeadTimeDateRules($shippingMethodCode);
		
		foreach($order_item_collection as $item){
			if($item->getProductId()){
				$_product = Mage::getModel('catalog/product')->load($item->getProductId());
				if($_product){
					$categoryIds = $_product->getCategoryIds();
					if(in_array('99',$categoryIds)){
						$_productImage[] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true)."media/catalog/product/images/mothers/cartproducts/".$item->getQuoteItemId()."_thumb.png";
					}
					else{
						if($_product->getSku() == 'ANGCBYO007'){
							$options = $item->getProductOptions();			
							if(!empty($options["info_buyRequest"]["options"])){
								$optionDetail = $options["info_buyRequest"]["options"];
								if($optionDetail){
									$shape = $optionDetail["diamond"]["shape"];
								}
							}							
							$pImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/ress/default/images/buildyourown/diamond/'.$shape.'_top_diamond.jpg';
							$_productImage[] = $pImage;
						}
						else{
							$pImage = Mage::helper('catalog/image')->init($_product, 'image')->resize(80,80)->__toString();
							$_productImage[] = $pImage;
						}
					}
					
					$checkFreeProduct	=	Mage::helper('function')->checkFreeProductBySku($_product);
					if(!in_array('346',$categoryIds) && !$checkFreeProduct ){						
						$productLeadTime = $item->getVendorLeadTime();
						$leadTimeAddons = Mage::helper('function')->checkProductLeadTimeAddons($item);
						$leadTime =	$productLeadTime + $leadTimeAddons + $leadTimeShippingMethod /*+ $scheduleLeadTime*/ + $leadTimeDateRules;
						$estimatedArrivalDate[] = Mage::helper('function')->shipDateAdmin($orderCompleteDate, $leadTime);
					}	
				}
			}
		}
		
		$laterArrivalTime = 0;
		foreach($estimatedArrivalDate as $arriveDate){
			$curArrivalTime = strtotime($arriveDate);
			if ($curArrivalTime > $laterArrivalTime) {
				$laterArrivalTime = $curArrivalTime;
				$estimatedArrivalDate = $arriveDate;
			}		  
		} 
		
		$shippingAddress = $order->getShippingAddress();
		$billingAddress = $order->getBillingAddress();
		
		$countryNameShipping = Mage::getModel('directory/country')->load($shippingAddress['country_id'])->getName();
		$custShippingAddress = $shippingAddress['street'].'<br />'.$shippingAddress['city'].', '.$shippingAddress['region'].', '.$shippingAddress['postcode'].' <br />'.$countryNameShipping.(($shippingAddress['telephone']) ? ' <br />T: '.$shippingAddress['telephone'] : '');
		
		$countryNameBilling = Mage::getModel('directory/country')->load($billingAddress['country_id'])->getName();
		$custBillingAddress = $billingAddress['street'].'<br />'.$billingAddress['city'].', '.$billingAddress['region'].', '.$billingAddress['postcode'].' <br />'.$countryNameBilling.(($billingAddress['telephone']) ? ' <br />T: '.$billingAddress['telephone'] : '');
		// For Track Your Order Added by Pankaj on 18052015 
		
        // Return empty array if order# not belongs to customer        
		if (strtolower($email) != strtolower($order['customer_email'])) {	//	S:VA Capital letters in email issue fixed
            return array();
        }
		
		$estimated_shipping_date = 'NA';
		
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result = $db->query("SELECT * FROM `sales_flat_order_estimated_shippingdate` where orderid='" . $order->getId() . "'");
		$rows = $result->fetch(PDO::FETCH_ASSOC);
		if($rows) {
			$estimated_shipping_date = $rows['shippingdate'];
		}
		
		$result2 = $db->query("SELECT * FROM `sales_flat_order_oktoship` where orderid='" . $order->getId() . "'");
		$rows2 = $result2->fetch(PDO::FETCH_ASSOC);
		if($rows2 && !empty($rows2)) {
			$ok_to_ship = $rows2['oktoship'];
		}
		else{
			$ok_to_ship = '0';
		}
		
        return array(
			'id' => $order['increment_id'],
			'state' => $order['state'],
			'estimated_shippingdate' => $estimated_shipping_date,
			'estimated_arrivaldate' => $estimatedArrivalDate,
			'ok_to_ship' => $ok_to_ship,
			'customer_firstname' => $order['customer_firstname'],
			'customer_lastname' => $order['customer_lastname'],
			'customer_billingaddress' => $custBillingAddress,
			'customer_shippingaddress' => $custShippingAddress,
			'total_paid' => $order['base_grand_total'],
			'product_image_path' => $_productImage,
			'created_at' => $order['created_at'],
			'updated_at' => $order['updated_at']
		);
    }
}