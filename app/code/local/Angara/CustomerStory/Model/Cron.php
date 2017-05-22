<?php
class Angara_CustomerStory_Model_Cron
{
	/*
		This function runs using cron to send email to those customer who have placed an order on our website before 25 days
	*/
	public function processShareStoryEmail(){
		
		try{
			$mandrillApiKey = Mage::helper('utilitybackend')->getMandrillApiKey();
			$mandrill = new Mandrill($mandrillApiKey);
			$currentServerDateTime = date("Y-m-d H:i:s", strtotime(Mage::helper('function')->getCurrentServerDateTime()));
			
			$to	= $currentServerDateTime;						// todays date -> date('Y-m-d H:i:s', $time);
			$lastTime = strtotime($to) - (86400 * 30);			// time diff in 30 day
			$from = date('Y-m-d H:i:s', $lastTime);			
			
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->getSelect()
				->reset(Zend_Db_Select::COLUMNS)
				->columns('entity_id')
				->columns('customer_firstname')
				->columns('customer_email')
				->columns('increment_id')
				->columns('created_at')
				->columns('status')
				->columns('story_email_shared')
				->join(array('shipment' => 'sales_flat_shipment'), 'main_table.entity_id = shipment.order_id', array('order_id','created_at as shipment_date'))
				->where('main_table.story_email_shared="0" and main_table.status="complete" and main_table.customer_email!="NULL" and main_table.created_at >="'.$from.'" and main_table.created_at <="'.$to.'"');
						
			if(count($orders) > 0){
				foreach ($orders as $order){
					$orderShippedDate = date('Y-m-d H:i:s', strtotime($order->getShipmentDate()));
					$shippedDateApproved = strtotime($orderShippedDate) + (86400 * 25);
									
					if($shippedDateApproved <= strtotime($to)){
						$emailCustomer = $order->getCustomerEmail(); 
						$orderId = $order->getIncrementId();
						$customerFirstName = ucfirst($order->getCustomerFirstname());
						
						if($emailCustomer && $orderId && $customerFirstName){
							$message = array(									
								'to' => array(
									array(
										'email' => $emailCustomer, 
										'name' => $customerFirstName
									)
								),
								'merge_vars' => array(
									array(
										'rcpt' => $emailCustomer,
										'vars' => array(
											array(
												'name' => 'FIRSTNAME',
												'content' => $customerFirstName
											),	
											array(
												'name' => 'ORDERID',
												'content' => $orderId
											)
										)
									)
								)
							);
							
							$template_content = array();					       
							$template_name = 'share-story-test'; 
							
							$response = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
							if($response[0]['status'] == 'sent') {
								$order->setStoryEmailShared(1)->save();
							}
						}
					}	
				}
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
		}
	}
}?>