<?php

class Angara_Shipment_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function changeShippingAction()
	{
		$params	=	$this->getRequest()->getParams();
		//echo '<pre>';print_r($params);die;
		$orderId     		= 	$this->getRequest()->getParam('order_id');
		$currentShippingMethod	=	$this->getRequest()->getParam('current_shipping_method');
		$shippingMethod		=	$this->getRequest()->getParam('shipping_method');
		$order 				= 	Mage::getModel('sales/order')->load($orderId);//print_r($order);
		$setShipment 		= 	$order->setShippingDescription($shippingMethod)->save();
		
		//	Add comment to order history
		if($currentShippingMethod != $shippingMethod){
			// Add the comment and save the order (last parameter will determine if comment will be sent to customer)
			$orderComment	=	Angara_Shipment_Helper_Data::shipping_method_comment .' from "'.$currentShippingMethod .'" to "'.$shippingMethod .'".'; 
			$order->addStatusHistoryComment($orderComment);
			$order->save();
			$message = $this->__('The Shipping & Handling Information has been updated successfully.');
			Mage::getSingleton('adminhtml/session')->addSuccess($message); 
		}else{
			$message = $this->__('Please try selecting different Shipping & Handling Information.');
			Mage::getSingleton('adminhtml/session')->addError($message);
		}		
		$this->_redirectReferer('');
	}
}
