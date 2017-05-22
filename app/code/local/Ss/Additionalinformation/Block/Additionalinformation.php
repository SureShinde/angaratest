<?php
class Ss_Additionalinformation_Block_Additionalinformation extends Mage_Core_Block_Template
{	
	public function _prepareLayout()	{
		return parent::_prepareLayout();
	}
	public function _getOrderId($getOrderId)	{
		if(isset($allOrderId)){
			unset($allOrderId);
		}
		$getAllAdditionalinformationOrders = Mage::getModel('additionalinformation/additionalinformation')->getCollection();
		foreach($getAllAdditionalinformationOrders as $getAllAdditionalinformationOrders){
			$allOrderId[] .= $getAllAdditionalinformationOrders->getOrderIncrementId();
		}
		$orderId = Mage::helper('core')->urlDecode($getOrderId);
		if(in_array($orderId,$allOrderId)){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	public function _getOrder($getOrderId)	{
		return Mage::getSingleton('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($getOrderId));
	}
}