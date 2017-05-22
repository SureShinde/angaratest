<?php
class Angara_Angaracart_Model_Order_Observer extends Mage_Core_Model_Abstract
{
	
	public function addCustomCommentToCustomOrder($observer){
		
		$order = $observer->getEvent()->getOrder();
		$lastOrderId = $order->getId();
		
		$items = Mage::getSingleton('sales/order_item')->getCollection()
				->addFieldToFilter('order_id',$lastOrderId);
				
		foreach($items as $item){
			if(strpos($item->getSku(), 'ANGCP') !== false){
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$order->addStatusToHistory($order->getStatus(), 'Details: '.nl2br($product->getDescription()), false);
				$order->save();
			}
		}
	}
	
	public function salesOrderSaveAfterInsurance($observer)
    {
		$storeId=Mage::app()->getStore()->getStoreId();
		
		
		$order = $observer->getEvent()->getOrder();
		$last_orderid = $order->getId();
		
		$items = Mage::getSingleton('sales/order_item')->getCollection()
				->addFieldToFilter('order_id',$last_orderid);
				
		$skuId='INS001';
		foreach($items as $_it){
			if($_it->getSku()==$skuId){
				$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
				$info = $_it->getBuyRequest();
				$parentItemId = $info->getRelatedItem2();
				$parentItem=Mage::getModel('sales/quote_item')->load($parentItemId);
				Mage::getModel('angaracart/insurance')
				->setIncrementId('INS'.$reservedOrderId)
				->setProductId($parentItem->getProductId())
				->setProductSku($parentItem->getSku())
				->setInsuranceAmount($_it->getPrice())
				->setOrderId($last_orderid)
				->setCreatedAt(now())
				->save();
				
				   $existentOptions = $_it->getProductOptions();
					if (!isset($existentOptions['additional_options'])) {
						$existentOptions['additional_options'] = array(); 
					}
					$existentOptions['additional_options'][] = array(
						'label' => 'Insurance Number',
						'value' => 'INS'.$reservedOrderId,
					);
					$_it->setProductOptions($existentOptions);
					  $_it->save();
					  
					  
					  /*Email Send to Customer*/
					  	$product=Mage::getModel('catalog/product')->load($parentItem->getProductId());
						$customjflag = 0;
						$imgurl = '';
						$customjflag = $product->getCustomj();
						
						//$itemarr = $_it->getData();
						$cartitemid = $parentItemId;
						
						$home_url = Mage::helper('core/url')->getHomeUrl();
						
						if($customjflag == 1){				
							$imgurl = $home_url.Mage::getBlockSingleton('hprcv/hprcv')->getrootpath() . "cartimages/" . $cartitemid . ".png";				
						}else{	
							$imgurl = $product->getImageUrl();
						}
						//Mage::log('Path======== '.$imgurl, null, 'insurance_mail_log.log');
						
					 	$translate  = Mage::getSingleton('core/translate');
						$translate->setTranslateInline(false);
						
						$vars = array();

						$image="<img src='".$imgurl."' height='350' width='350' style='border:solid 1px #dbcdcc;' />";

						$sender = Array('name'  => 'Sales',
						'email' => 'customer.service@angara.com');
						
						$templateId='total_warranty_plan_template';
						$recepientEmail=$order->getCustomerEmail();
						$recepientFirstName=$order->getCustomerFirstname();
						//$recepientLastName=$order->getCustomerLastname();
						
						//$recepientName	=	$recepientFirstName.' '.$recepientLastName;
						$subject		=	$recepientFirstName.', Your Jewelry Protection Warranty Plan From Angara';
						
						$orderDate=date('M d, Y',strtotime($order->getCreatedAt()));
						$orderDatePlusFive=date('M d, Y',strtotime($order->getCreatedAt().'+'. 5 .'year'));
						$orderIncId=$order->getIncrementId();
						
						$vars['image']=$image;
						$vars['prod_name']=$product->getName();
						$vars['cust_name']=$recepientFirstName;
						$vars['order_number']=$orderIncId;
						$vars['order_date']=$orderDate;
						$vars['five_plus_year']=$orderDatePlusFive;
						$vars['recepient_email']=$recepientEmail;
						$vars['free_returen_days']=Mage::helper('function')->freeReturnDays();
						
						$mailTemplate=Mage::getModel('core/email_template');
						$mailTemplate->setTemplateSubject($subject)->sendTransactional($templateId,$sender,$recepientEmail,$recepientFirstName,$vars,$storeId);
						$translate->setTranslateInline(true);
					  
			}	
		}
		
		
	}
}