<?php
class Mage_Adminhtml_Block_Estdd extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
		//$productLeadTime = $row->getdata('vendor_lead_time');
		
		$shippingMethodCode = $row->getdata('shipping_method');
		$shippingMethodShortForm = Mage::helper('function')->shippingShortForm($shippingMethodCode);
		$leadTimeShippingMethod	= Mage::helper('function')->getShippingDays($shippingMethodShortForm);
		
		$orderCompleteDate  = Mage::helper('sales')->formatDate($row->getdata('created_at'), 'medium', true);
		//$scheduleLeadTime = Mage::helper('function')->scheduleLeadTime($orderCompleteDate);
		$leadTimeDateRules = Mage::helper('function')->getLeadTimeDateRules($shippingMethodCode);
		
		$leadTimeAddons = 0;
		$orderIncrementId = $row->getdata('increment_id');
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$items = $order->getAllVisibleItems();
		foreach($items as $item):
			$productLeadTime = $item->getdata('vendor_lead_time');
			$leadTimeAddons = Mage::helper('function')->checkProductForLeadTimeAdmin($item);
			$leadTime =	$productLeadTime + $leadTimeAddons + $leadTimeShippingMethod /*+ $scheduleLeadTime*/ + $leadTimeDateRules;	
			$estimatedArrivalDate[] = Mage::helper('function')->shipDateAdmin($orderCompleteDate, $leadTime);
		endforeach;
		
		$laterArrivalTime = 0;
		foreach($estimatedArrivalDate as $arriveDate){
			$curArrivalTime = strtotime($arriveDate);
			if ($curArrivalTime > $laterArrivalTime) {
				$laterArrivalTime 		= 	$curArrivalTime;
				$estimatedArrivalDate 	= 	$arriveDate;
			}		  
		}		
		
		return $estimatedArrivalDate;
    }
}
