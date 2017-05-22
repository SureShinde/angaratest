<?php

class Ss_Additionalinformation_Adminhtml_AdditionalinformationController extends Mage_Adminhtml_Controller_Action
{
	public function sendrequestAction(){
			$params			=	$this->getRequest()->getParams();
			$requestType	=	$params['request_type'];
			$orderId 		= 	$this->getRequest()->getParam('order');
			$_order			= 	Mage::getModel('sales/order')->loadByIncrementId($orderId);
			$customerFirstName = $_order->getCustomerFirstname();
			$customerLastName = $_order->getCustomerLastname();
			$customerOrderEmail = $_order->getCustomerEmail();
			
			$_fraudCheckMailSent = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
						->addFieldToFilter('order_increment_id',$orderId);
			//$_fraudCheckMailSent = $_fraudCheckMailSent->getData();
			//$_fraudCheckMailSentVerify = $_fraudCheckMailSent[0]['is_mail_sent'];
			if($_fraudCheckMailSent->getSize()==0){			
				$_fraudCheck= Mage::getModel('additionalinformation/additionalinformation')
							->setOrderIncrementId($orderId)
							->setCustomerFirstname($customerFirstName)
							->setCustomerLastname($customerLastName)
							->setCustomerOrderEmail($customerOrderEmail)
							->setAdminUserId($adminUserId)
							->setAdminUserFirstname($adminUserFirstName)
							->setAdminUserLastname($adminUserLastName)
							->setAdminUserName($adminUserName)
							->setIsMailSent(1)
							->setCreatedAt(now())
							->setUpdatedAt(now())
							->save();
				//if($this->getRequest()->getParam('fraudchecksendmail')=='fraudchecksendmail'){
					$IsMailSent = Mage::helper('additionalinformation')->sendToVerifyAdditionalinformationEmail($orderId, $requestType);
						if($IsMailSent==1){
							$this->_getSession()->addSuccess($this->__('Fraud check request email has been sent.'));
							$comment = 'Fraud Check Request E-mail has been sent to the Customer';
							$_order->addStatusHistoryComment($comment);
							$_order->save();
						}
						else{
							$this->_getSession()->addError($this->__('Failed to send the email.'));
						}
				/*}
				else{
					$this->_getSession()->addSuccess($this->__('Fraud check entry generated but email not been sent.'));
				}*/
			}
			else{
				$IsMailSent = Mage::helper('additionalinformation')->sendToVerifyAdditionalinformationEmail($orderId, $requestType);
				if($IsMailSent==1){
					$this->_getSession()->addSuccess($this->__('Fraud check request email has been sent.'));
					$comment = 'Fraud Check Request E-mail has been sent to the Customer';
					$_order->addStatusHistoryComment($comment);
					$_order->save();
				}
				else{
					$this->_getSession()->addError($this->__('Failed to send the email.'));
				}
				//$this->_getSession()->addError($this->__('Admin has already sent the email.'));
			}
		//$this->_redirectUrl($url);
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$_order->getId(),'_secure'=>true)));
	}
	
	public function deleteimagesAction(){
		$imageId =  $this->getRequest()->getParam('image');
		$additionalinformationId = $this->getRequest()->getParam('id');
		
		$_order= Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
		
		$additionalinformationLoadData = Mage::getModel('additionalinformation/additionalinformation')->load($additionalinformationId);
		$images = explode(',',$additionalinformationLoadData->getCustomerImages());
		if(count($images)>1){
			$imageName = $images[$imageId];
			unset($images[$imageId]);
			$images = implode(',',$images);
			$additionalinformationLoadData->setData('customer_images',$images)->save();
			$comment = $imageName.' Deleted Successfully.';
				$_order->addStatusHistoryComment($comment);
				$_order->save();
			$this->_getSession()->addSuccess($imageName.' Deleted Successfully.');	
		}
		else if(count($images)==1){
			$imageName = $images[$imageId];
			$images = '';
			$additionalinformationLoadData->setData('customer_images',$images)
								->setData('flag',0)
								->setData('updated_at',now())
								->save();
			$comment = $imageName.' is deleted';
				$_order->addStatusHistoryComment($comment);
				$_order->save();
			$this->_getSession()->addSuccess($imageName.' Deleted Successfully.');	
		}
		else{
			$this->_getSession()->addError($this->__('An error has occured to delete image.'));
		}
	}
	
	public function imagesuploadAction(){
		$data =array();
		if(isset($saveToDb)){
			unset($saveToDb);
		}
		if(isset($existingImages)){
			unset($existingImages);
		}
		//$adminSession = Mage::getSingleton('admin/session')->getUser();
		$orderIncrementId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('order'));
		$_order= Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$customerFirstName = $_order->getCustomerFirstname();
		$customerLastName = $_order->getCustomerLastname();
		$customerOrderEmail = $_order->getCustomerEmail();
		/*$adminUserId = $adminSession->getUserId();
		$adminUserFirstName = $adminSession->getFirstname();
		$adminUserLastName = $adminSession->getLastname();
		$adminUserName = $adminSession->getUsername();//Mage::helper('core')->urlDecode($this->getRequest()->getParam('order'));
		*/
		$adminUploadImages = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
								->addFieldToFilter('order_increment_id',$orderIncrementId);
		$resonseData = $adminUploadImages->getData();		
		if(count($resonseData)=='' || $resonseData[0]['flag']!=1){
			if(count($_FILES['additionalinformationimg']['name'])>0){
				foreach ($_FILES['additionalinformationimg']['name'] as $key => $image) {
					try {
						$uploader = new Varien_File_Uploader(
							array(
							'name' => str_replace(' ','',$_FILES['additionalinformationimg']['name'][$key]),
							'type' => $_FILES['additionalinformationimg']['type'][$key],
							'tmp_name' => $_FILES['additionalinformationimg']['tmp_name'][$key],
							'error' => $_FILES['additionalinformationimg']['error'][$key],
							'size' => $_FILES['additionalinformationimg']['size'][$key]
								)
						);
						// Any extention would work
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png' , 'bmp' , 'pdf' , 'doc' , 'docx'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						// We set media as the upload dir
						$path = Mage::getBaseDir('media') . DS . 'additionalinformation' . DS . $orderIncrementId . DS;
						//$newName    = time() .$image;
						$image = strtolower(str_replace(array('    ','   ','  ', ' '), '', preg_replace('/[^a-zA-Z0-9. s]/', '-', trim($image))));
						if(strstr($image,'-')){
							$image	=	str_replace(array('---','--','-'), '_', $image);
						}
                    	$uploader->save($path, $image);
						
						$imageUrl = $path . $image;
						$imageResized = $path . $image;
						if(strpos($images,'.jpg')!='' || strpos($images,'.jpeg')!='' || strpos($images,'.png')!='' || strpos($images,'.gif')!='' || strpos($images,'.bmp')!=''):
							if (file_exists($imageResized)&&file_exists($imageUrl)) :
								$imageObj = new Varien_Image($imageUrl);
								$imageObj->constrainOnly(TRUE);
								$imageObj->keepAspectRatio(TRUE);
								$imageObj->keepFrame(FALSE);
								//$imageObj->resize(150, 150);
								$imageObj->save($imageResized);
							endif;
						endif;
						$saveToDb[] .= $image;
						//$img = $uploader->save($path, $_FILES['additionalinformationimg']['name'][$key]);
					} catch (Exception $e) {
						Mage::log($e->getMessage());
					}
				}
			}
			else{
				$img='';
			}
			
			if($resonseData[0]['customer_images']!=''){
				$existingImages = explode(',',$resonseData[0]['customer_images']);
				$save_img_data_db = implode(',',array_merge($saveToDb,$existingImages));
			}
			else{
				$save_img_data_db = implode(',',$saveToDb);
			}
			//foreach($resonseCustomerOfMail as $resonseCustomer){
			if(count($resonseData)==''){
				$_fraudCheck= Mage::getModel('additionalinformation/additionalinformation')
							->setOrderIncrementId($orderIncrementId)
							->setCustomerFirstname($customerFirstName)
							->setCustomerLastname($customerLastName)
							->setCustomerOrderEmail($customerOrderEmail)
							->setCustomerImages($save_img_data_db)
							->setFlag(1)
							->setAdminUserId($adminUserId)
							->setAdminUserFirstname($adminUserFirstName)
							->setAdminUserLastname($adminUserLastName)
							->setAdminUserName($adminUserName)
							->setIsMailSent(1)
							->setCreatedAt(now())
							->setUpdatedAt(now())
							->save();
			}
			else{
			$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
			$resonseCustomer->setData('customer_images',$save_img_data_db)
						->setData('flag',1)
						->setData('updated_at',now())
						->save();
			}
			//}
				$comment = 'Images Uploaded For Fraud Check.';
				$_order->addStatusHistoryComment($comment);
				$_order->save();
				$this->_getSession()->addSuccess($this->__('Images Uploaded For Fraud Check.'));
			}
			else{
				$this->_getSession()->addError($this->__('Admin has already uploaded images.'));
			}
	}
	
	public function resetrequestAction(){
		$orderId = $this->getRequest()->getParam('order');

		$_order= Mage::getModel('sales/order')->loadByIncrementId($orderId);
		
		$_fraudCheckMailSent = Mage::getModel('additionalinformation/additionalinformation')->getCollection()
					->addFieldToFilter('order_increment_id',$orderId)->getData();
		if($_fraudCheckMailSent){			
			$_fraudCheck= Mage::getSingleton('additionalinformation/additionalinformation')->load($_fraudCheckMailSent[0]['additionalinformation_id']);
			$_fraudCheck->setData('flag',0)
						->setData('otp_flag',0)
						->setData('updated_at',now())
						->save();
			if($this->getRequest()->getParam('resetreqmail')=='resetreqmail'){
				$IsMailSent = Mage::helper('additionalinformation')->sendToVerifyAdditionalinformationEmail($orderId);
				//$IsMailSent = Mage::helper('additionalinformation')->_resetFraudCheck($orderId);
					if($IsMailSent==1){
						$this->_getSession()->addSuccess($this->__('Fraud check request reset and mail sent to customer.'));
						$comment = 'Fraud check request reset and mail sent to customer.';
						$_order->addStatusHistoryComment($comment);
						$_order->save();
					}
			}
			else{
				$this->_getSession()->addSuccess($this->__('Fraud check request reset and mail not sent to customer.'));
				$comment = 'Fraud check request reset and mail not sent to customer.';
				$_order->addStatusHistoryComment($comment);
				$_order->save();
			}
		}
		else{
			$this->_getSession()->addError($this->__('An error occured while sending fraud check request reset.'));
		}
		
		//$this->_redirectUrl($url);
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$_order->getId(),'_secure'=>true)));
	}
	
	public function sendconfirmationAction(){
		$orderId = $this->getRequest()->getParam('order');
		$_order= Mage::getModel('sales/order')->load($orderId);
		if($this->getRequest()->getParam('fraudcheckconfirmation')=='fraudcheckconfirmation'){
			$IsMailSent = Mage::helper('additionalinformation')->_fraudCheckConfirmation($orderId);
				if($IsMailSent==1){
					$this->_getSession()->addSuccess($this->__('Fraud Check Confirmation E-mail has been sent to the customer.'));
					$comment = 'Fraud check confirmation mail sent to customer';
					$_order->addStatusHistoryComment($comment);
					$_order->save();
				}
				else{
					$this->_getSession()->addError($this->__('An error occured while sending fraud check confirmation mail.'));
				}
		}
		//$this->_redirectUrl($url);
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$_order->getId(),'_secure'=>true)));
	}
}