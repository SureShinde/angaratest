<?php
class Ss_Additionalinformation_Block_Adminhtml_Additionalinformation extends Mage_Core_Block_Template
{
	
 	public function _prepareLayout(){
	  
		return parent::_prepareLayout();
	}
	
	public function _getImages($orderId){
		$incrementId = $this->_getIncrementId($orderId);
		$customerData = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
							->addFieldToFilter('order_increment_id',$incrementId)
							->getData();
		if($customerData[0]['customer_images']!=''){
			return explode(',',$customerData[0]['customer_images']);
		}
		else{
			return '';
		}
	}
	
	public function _getIncrementId($orderId){
		$_order= Mage::getModel('sales/order')->load($orderId);
		return $_order->getIncrementId();
	}
	
	public function _getFraudCheckId($orderId){
		$incrementId = $this->_getIncrementId($orderId);
		$customerData = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
							->addFieldToFilter('order_increment_id',$incrementId)
							->getData();
		return $customerData[0]['additionalinformation_id'];
	}
}