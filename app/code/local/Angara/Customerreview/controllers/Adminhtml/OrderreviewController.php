<?php
class Angara_Customerreview_Adminhtml_OrderreviewController extends Mage_Adminhtml_Controller_Action {        

	public function indexAction() {
			$this->_title($this->__('Customerreview'))->_title($this->__('Orders-Review Management '));
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('customerreview/adminhtml_orderreview'));		
			$this->renderLayout(); 
	}

	public function gridAction() {
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('customerreview/adminhtml_orderreview_grid')->toHtml()
			);
	}

	protected function massactionqueueAction() {	
		$emails = $this->getRequest()->getParam('customer_email');
		$cnt = 0;
		if(is_array($emails)) {
			foreach($emails as $email) {
				$res=$this->addQueue($email);
				if($res){$cnt++;}
			}
			if($cnt) {
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('followupemail')->__('Total of %d record(s) were successfully queued', $cnt)
				);
			}
		}
		return $this->_redirect('*/*/index');
	}
    private function addQueue($email) {
		//$order = Mage::getModel('sales/order')->load($orderId);
		$separator = "--";
		$pos = strpos($email, '--');
		if($pos>0) {
			$customerEmail = substr($email, 0, $pos);
			$customerName = substr($email, $pos+strlen($separator), strlen($email)-$pos);
		} else {
			Mage::getSingleton('adminhtml/session')->addError($email.' has some formatting problem. Not queued');
			return false;
		}		
		$queueModel = Mage::getModel('followupemail/queue');
		
		
		$code = AW_Followupemail_Helper_Data::getSecurityCode();
		$senderName =  Mage::helper('customerreview/data')->getSenderName();	//'Angara.com';
		$senderEmail = Mage::helper('customerreview/data')->getSenderEmail();	//'customer.service@angara.com';

		$ruleId = Mage::helper('customerreview/data')->getOrderReviewMailRuleId();
		$followupMailsCount = Mage::helper('customerreview/data')->getFollowupMailsCount();
		$followupMailsSchedule = Mage::helper('customerreview/data')->getOrderReviewEmailsSchedule();		
		$templateId = Mage::helper('customerreview/data')->getOrderReviewMailTemplateId();
		$object['customer_name'] =  $customerName;
		
		//	S:VA
		if($customerName){
			$arr 		= explode(' ',trim($customerName));
			$firstName	=	$arr[0];
			$object['first_name'] =  $firstName;
		}
		//	E:VA
		
		for($i=0;$i<$followupMailsCount; $i++) {
			$object['sequence_number'] =  $i+1;
			$object['customer_email'] =  $customerEmail;
			$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$customerEmail)
																->addAttributeToSort('entity_id','DESC');
			$order=$orders->getFirstItem();
			$object['object_id'] = $order->getId();
			if($object['object_id'] >0 ){} 
			else {
				Mage::getSingleton('adminhtml/session')->addError('No Orders found for email-' . $customerEmail);
				return false;				
			}
			$object['order_id'] = $order->getId();
			$object['order'] = $order;		
            foreach($object['order']->getAllVisibleItems() as $item) {
                $object['order']->getItemById($item->getId())->setProduct(Mage::getModel('catalog/product')->load($item->getProductId()));		
			}				
			$scheduleTime = strtotime(date('Y-m-d H:m:s', strtotime($followupMailsSchedule[$i])));
			
			$content = Mage::getModel('followupemail/rule')->_getContent($object,$templateId);
			if(!$content) {
				Mage::log($templateId.'template cant be loaded', null, 'customereview.log');
				return false;
			}else {
				$queueModel->add($code,	$i+1,$senderName,$senderEmail,$customerName,$customerEmail,$ruleId,$scheduleTime,$content['subject'],$content['content'],$ruleId,null);
				$customerreviewLogModel = Mage::getModel('customerreview/customerreviewlog');
				$customerreviewLogModel->setEmail($customerEmail)
						->setSequenceNumber($i+1)
						//->setOrderId($orderId)
						->setDate(date('Y-m-d H:m:s', strtotime($followupMailsSchedule[$i])))
						->setFollowupObjectId($ruleId)
						->setStatus('R')
						->setRuleId($ruleId)
						->save();	
			}				
		}
	return true;	
    }	
}
?>	