<?php
class Angara_Customerreview_IndexController extends Mage_Core_Controller_Front_Action {        


	public function reviewAction() {
		$email = $this->getRequest()->getParam('email');
		$orderId = $this->getRequest()->getParam('order');
		try{
			if(isset($email) AND !empty($email)) {
				$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$email)
													->addAttributeToSort('entity_id','DESC');
													
				if(isset($orderId) AND !empty($orderId)){
					$orders =$orders->addFieldToFilter('increment_id',$orderId);
					if(count($orders)<=0){
						Mage::throwException('There are no orders for review.');	
					}
				}
				if(count($orders)==0) {
					Mage::throwException('There are no orders for review.');
				}			
				$reviews = Mage::getModel('customerreview/customerreviewsubmission')->getCollection()->addFieldToFilter('email',$email);
				Mage::register('customer_orders', $orders);
				Mage::register('customer_reviews', $reviews);
		
			}else {				
				Mage::throwException('You have landed here by mistake.');
			}	
		$this->loadLayout();
		$this->renderLayout(); 				
       } catch(Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->loadLayout();
				$this->renderLayout(); 	
				return;
	   }	   
	}
	public function reviewPostAction()
	{
        if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if (($product = $this->_initProduct($data['sku'])) && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            $review     = Mage::getModel('review/review')->setData($data);

			try {
				$myOrder = Mage::getModel('sales/order')->load($data['order_id']);
				if($myOrder->getId()>0) {
				}else {
					Mage::throwException('There was some error in form submission. Kindly try again.');
				}
			
				$review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
					->setEntityPkValue($product->getId())
					->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
					->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
					->setStoreId(Mage::app()->getStore()->getId())
					->setStores(array(Mage::app()->getStore()->getId()))
					->save();

				foreach ($rating as $ratingId => $optionId) {
					Mage::getModel('rating/rating')
					->setRatingId($ratingId)
					->setReviewId($review->getId())
					->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
					->addOptionVote($optionId, $product->getId());
				}

				$review->aggregate();
				$session->addSuccess($this->__('Your review has been accepted for moderation.'));
				
				// Add review details in customerreviewsubmission  table
				$rule = $data['rule'];
				if(strlen($rule)>0) {
					$ruleId = Mage::helper('customerreview/data')->getOrderCompletionRuleId();
				}else {
					$ruleId = Mage::helper('customerreview/data')->getOrderReviewMailRuleId();
				}
				$mod = Mage::getModel('customerreview/customerreviewsubmission');
				$mod->setEmail($data['email'])
					->setItemId($data['item_id'])
					->setReviewId($review->getId())
					->setSequenceNumber($data['sequence_number'])
					->setOrderId($myOrder->getId())
					->setDate(now())
					->setRuleId($ruleId)
					->save();					
				// Add review details in customerreviewsubmission  table
				
				//send thankyou mail				
				$this->sendThankyouMail($data['sequence_number'], $data['email'], $data['nickname'], $myOrder->getId());				
				//send thankyou mail
				
				
				//cancel customreviewlog queue
				
				//load order and get total number of items in that order - match it with total number of rows in customreviewsubmission for an order with distinct itemIds
				//----$myOrder = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id',$data['order_id']);
				
				//cancellation logic for Order Completion Review Mails
				if(strlen($rule)>0) {				
					/*
					$myOrderCustomerEmail =  $myOrder->getCustomerEmail();
					$myItems = $myOrder->getAllItems();
					$countItems = count($myItems); 
					
					$collection = Mage::getModel('customerreview/customerreviewsubmission')->getCollection()
							->addFieldToFilter('order_id', $myOrder->getId())
							->addFieldToFilter('email', $myOrderCustomerEmail);
					$collection->getSelect()->group('item_id');
					$countCollection = count($collection);
					*/
					//if($countItems<=$countCollection) {
					if(true) {
						$reviewLogs = Mage::getModel('customerreview/customerreviewlog')->getCollection()
									->addFieldToFilter('order_id', $myOrder->getId())
									->addFieldToFilter('sequence_number', array("gt"=>$data['sequence_number']));
						$followObjectId  = '';
						foreach($reviewLogs as $reviewLog) {
							$followObjectId = $reviewLog->getFollowupObjectId();
							$reviewLog->setStatus('C')
									  ->save();
						}
						//Mage::log('review log cancelled-'. $followObjectId, null, 'follow.log');
					}
					//cancel customreviewlog queue
					
					//cancel followup queue
					$coll = Mage::getModel('followupemail/queue')->getCollection()
							->addFieldToFilter('object_id',$followObjectId)
							->addFieldToFilter('sent_at',array('null' => true))
							->addFieldToFilter('rule_id',$ruleId);
					foreach($coll as $colItem) {
						$colItem->setStatus('C')
								->save();
					}
					//cancel followup queue
				} else {
					// cancellation logic for old customer reviews
						$reviewLogs = Mage::getModel('customerreview/customerreviewlog')->getCollection()
									->addFieldToFilter('date', array("gt"=>now()))
									->addFieldToFilter('email', $myOrder->getCustomerEmail())
									->addFieldToFilter('rule_id', $ruleId);
						
						foreach($reviewLogs as $reviewLog) {							
							$reviewLog->setStatus('C')
									  ->save();
						}		

					//cancel followup queue
						$coll = Mage::getModel('followupemail/queue')->getCollection()
								->addFieldToFilter('recipient_email',$myOrder->getCustomerEmail())
								->addFieldToFilter('sent_at',array('null' => true))
								->addFieldToFilter('rule_id',$ruleId);
						foreach($coll as $colItem) {
							$colItem->setStatus('C')
									->save();
						}
					//cancel followup queue						
				}				
			}
			catch (Exception $e) {
				$session->setFormData($data);
				$session->addError($this->__('Unable to post the review.'));
			}
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
	}
	
	protected function sendThankyouMail($seqNum, $customerEmail, $customerName, $orderId) {
	
		switch($seqNum) {
			case 1:  $templateId = Mage::helper('customerreview/data')->getThankyouMail1TemplateId(); //'email:Thankyoumail1';	
				break;
			case 2:  $templateId = Mage::helper('customerreview/data')->getThankyouMail2TemplateId(); //'email:Thankyoumail2';
				break;
			case 3:  $templateId = Mage::helper('customerreview/data')->getThankyouMail3TemplateId(); //'email:Thankyoumail3';
				break;
			case 4:  $templateId = Mage::helper('customerreview/data')->getThankyouMail4TemplateId(); //'email:Thankyoumail4';
				break;	
			case 5:  $templateId = Mage::helper('customerreview/data')->getThankyouMail5TemplateId(); //'email:Thankyoumail4';
				break;	
			default: $templateId = Mage::helper('customerreview/data')->getThankyouMail1TemplateId(); //'email:Thankyoumail1';		
		}
		$queueModel = Mage::getModel('followupemail/queue');
		
	$code = AW_Followupemail_Helper_Data::getSecurityCode();
	$senderName =  Mage::helper('customerreview/data')->getSenderName();	//'Angara.com';
	$senderEmail = Mage::helper('customerreview/data')->getSenderEmail();	//'customer.service@angara.com';
	$customerName = $customerName;
	$customerEmail = $customerEmail;
	$ruleId = Mage::helper('customerreview/data')->getThankyouMailRuleId();
	$object['customer_name'] =  $customerName;
	//	S:VA
	if($customerName){
		$arr 		= 	explode(' ',trim($customerName));
		$firstName	=	$arr[0];
		$object['first_name'] =  $firstName;
	}
	$object['customer_email'] 	= $customerEmail;
	$object['expiration_date'] 	= date('d-M-Y', strtotime("+60 days"));
	//	E:VA
	$content = Mage::getModel('followupemail/rule')->_getContent($object,$templateId);
		if(!$content) {
			Mage::log($templateId.'template cant be loaded', null, 'customereview.log');
		}else {
			$queueModel->add($code,	$seqNum,$senderName,$senderEmail,$customerName,$customerEmail,$ruleId,time(),$content['subject'],$content['content'],$orderId,	null);
		}	
	}
	
   protected function _initProduct($sku)
    {
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($product->getId()>0) {
			return $product;
		} else {
			return false;
		}
	}	
	public function validateAction()
	{
		$response = new Varien_Object();
		$data   = $this->getRequest()->getPost();
        $rating = $this->getRequest()->getParam('ratings', array());

        if (($product = $this->_initProduct($data['sku'])) && !empty($data)) {
				$review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */
            $validate = $review->validate();
			if($validate===true){
				$response->setError(false);
				
			} else {
				$response->setError(true);
				if (is_array($validate)) {
					foreach ($validate as $errorMessage) {
						$errorMsg = $errorMsg . ' ' . $errorMessage;
					}
                }
                else {
                    $errorMsg ='Unable to post the review.';
                }
				$response->setMessage($errorMsg);	
			}
		}else {
				$response->setError(true);
				$response->setMessage('Unable to post the review.');	
					
		}
	
		$this->getResponse()->setBody($response->toJson());
	}
}
?>