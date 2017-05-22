<?php
class Angara_Shipment_Helper_Data extends Mage_Core_Helper_Abstract {
	
	const shipping_method_comment = 'The shipping method has been changed';
	
	function getShippingMethods($isMultiSelect = false)
	{
		$methods = Mage::getModel("ship/ship")->getCollection()
											->addFieldToSelect('*')
											->setOrder('name',ASC)
											->setOrder('sort_order',ASC)
											->addFieldToFilter('enabled', array('eq' => '1'))
											->load();
										   //->load(1);die;
		//echo '<pre>';print_r($methods->getdata());die;
		if( $methods->count() ){
			foreach($methods as $_method) {
				$options[] 	= 	$_method->getData('name');
			}
		}
		//echo '<pre>';print_r($options);die;
		return $options;
	}
	
	/*function getMagentoShippingMethods($isMultiSelect = false)
	{
		$excludeTitle	=	array('fedex', 'googlecheckout');
		$methods 		= 	Mage::getSingleton('shipping/config')->getActiveCarriers();
		$options 		= 	array();
		foreach($methods as $_code => $_method) {
			if(!$_title = Mage::getStoreConfig("carriers/$_code/title")){
				$_title = $_code;
			}
			//echo '<br>_title->'.$_title.'<br>';
			//echo 'carrier->'.Mage::getStoreConfig("carriers/$_code/title").'<br>';
			if( !in_array($_code, $excludeTitle) ){
				$options[] = array( 'value' => $_code, 'label' => $_title );
			}
		}
		//echo '<pre>';print_r($options);die;
		if($isMultiSelect) {
			array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
		}
		//	S:VA
		foreach($options as $_code => $_method) {
			$_code				=	$_method['value'];
			$modifiedTitle		=	explode('(',$_method['label']);
			$_title				=	$modifiedTitle[0];
			$newOptions[$_code] = 	$_title;
		}
		return $newOptions;
	}*/
	
	function getOrderHistoryComments($order) {
		$_history = $order->getAllStatusHistory();
		foreach ($_history as $_historyItem){
			$comments[] = $_historyItem->getData('comment');
		}
		return $comments;
	}
}
	 