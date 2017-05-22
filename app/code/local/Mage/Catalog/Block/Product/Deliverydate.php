<?php
class Mage_Catalog_Block_Product_Deliverydate extends Mage_Catalog_Block_Product_Abstract
{
    /*
		get product delivery date based on the shipping method chosen in admin
	*/
	public function getArrivesByDate($_product){
		$extraLeadTime = 0;
		$isFreeProduct = Mage::helper('function')->checkFreeProductBySku($_product);
		if(!$_product->isSaleable() || $isFreeProduct){
			return false;
		}		
		$leadtimeProduct = $_product->getVendorLeadTime();
		$appliedShippingMethod = Mage::getSingleton('checkout/session')->getData('shipment');		
		$shippingMethodCode = (($this->getRequest()->getParam('estimate_method'))? Mage::helper('function')->shippingShortForm($this->getRequest()->getParam('estimate_method')) : (($appliedShippingMethod) ? Mage::helper('function')->shippingShortForm($appliedShippingMethod) : 'freeshipping'));
		$leadTimeShippingMethod	= Mage::helper('function')->getShippingDays($shippingMethodCode);
		$leadtime =	$leadtimeProduct + $leadTimeShippingMethod + $extraLeadTime;
		
		$todayDate = Mage::helper('function')->getCurrentServerDateTime();
		$estimatedArrivalDate = Mage::helper('function')->shipDateAdmin($todayDate,$leadtime);
		$deliveredByDate = date('m/d/y', strtotime($estimatedArrivalDate));
		return $this->convertDeliveryDateToHtml($deliveredByDate);
    }
	
	/*
		$_product	is the product object of configurable product
		$sku		is the sku having custom option (engraving) added in the sku
	*/
	public function getArrivesByDateAtCart($item){
		$isFreeProduct = Mage::helper('function')->checkFreeProductBySku($item);
		if($isFreeProduct){
			return false;
		}
		
		$productLeadTime = $item->getVendorLeadTime();
		$leadTimeAddons = Mage::helper('function')->checkProductLeadTimeAddons($item);				// leadtime addons
		
		$appliedShippingMethod = Mage::getSingleton('checkout/session')->getData('shipment');
		$estimateShippingMethod	= $this->getRequest()->getParam('estimate_method');
		
		$shippingMethodCode = (($estimateShippingMethod)? Mage::helper('function')->shippingShortForm($estimateShippingMethod) : (($appliedShippingMethod) ? Mage::helper('function')->shippingShortForm($appliedShippingMethod) : 'freeshipping'));
		$leadTimeShippingMethod	= Mage::helper('function')->getShippingDays($shippingMethodCode);	// leadtime shipping
		
		$todayDate = Mage::helper('function')->getCurrentServerDateTime();
		//$todayDate = Mage::helper('function')->getCurrentServerDateTimeWithMerridian();
		//$scheduleLeadTime = Mage::helper('function')->scheduleLeadTime($todayDate);					// leadtime order scheduling
		
		$finalShippingMethod = (($estimateShippingMethod) ? $estimateShippingMethod : (($appliedShippingMethod)? $appliedShippingMethod : 'freeshipping_freeshipping'));	
		$leadTimeDateRules = Mage::helper('function')->getLeadTimeDateRules($finalShippingMethod);	// leadtime date rules
		
		$leadTime = $productLeadTime + $leadTimeAddons + $leadTimeShippingMethod /*+ $scheduleLeadTime*/ + $leadTimeDateRules;
		
		$estimatedArrivalDate = Mage::helper('function')->shipDateAdmin($todayDate,$leadTime);
		$deliveredByDate = date('m/d/y', strtotime($estimatedArrivalDate));
		return $this->convertDeliveryDateToHtml($deliveredByDate, $shippingMethodCode);
    }

	/*
		this function accept the delivery date and convert it to delivery html
	*/
	public function convertDeliveryDateToHtml($deliveredByDate, $shippingMethodCode){
		if($deliveredByDate){
			return '<div class="delivered-by dyn_delivered_by"> <span class="apricot-text">'.$this->getDeliveredByText().$deliveredByDate.'</span>'.(($shippingMethodCode)?' <span>(with '.Mage::getStoreConfig("carriers/$shippingMethodCode/title").')</span>':'').'</div>';
		}
		return false;
	}
	
	/*
		static text
	*/
	public function getDeliveredByText(){
		return 	'Estimated Delivery by ';
	}
}?>