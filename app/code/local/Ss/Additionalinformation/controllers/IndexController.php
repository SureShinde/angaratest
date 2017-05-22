<?php
class Ss_Additionalinformation_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		//	Check if user already uploaded files or verified email address
		$key		 = $this->getRequest()->getParam('key');
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($key);
		//echo '<pre>';print_r($resonseData);
		if($resonseData[0]['flag']==1 || $resonseData[0]['otp_flag']==1){	
			$this->_redirect('*/*/thankyou', array( 'key'=>$key ));
		}else{
			$this->loadLayout();     
			$this->renderLayout();
		}
    }
	
	public function uploadimagesAction(){
		$data =array();
		if(isset($saveToDb)){
			unset($saveToDb);
		}
		if(isset($existingImages)){
			unset($existingImages);
		}
		//	Check file size
		//echo '<pre>';print_r($_FILES['additionalinformationimg']);
		foreach ($_FILES['additionalinformationimg']['size'] as $key => $fileSize) {
			if($fileSize>2097152){
				$message = $this->__('Uploaded file is too large. Please upload a valid file.');
				Mage::getSingleton('core/session')->addError($message);
				echo Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('*/*/index', array( 'param'=>$this->getRequest()->getParam('param'),'key'=>$this->getRequest()->getParam('order'))));die;
			}
		}
		
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($this->getRequest()->getParam('order'));
		if($resonseData[0]['flag']!=1){						
			if(count($_FILES['additionalinformationimg']['name'])>0){
				foreach ($_FILES['additionalinformationimg']['name'] as $key => $image) {
					//	Check if file is valid
					/*echo '<pre>';print_r($_FILES['additionalinformationimg']);
					echo '<pre>';print_r($key);
					echo '<pre>';print_r($image);
					die;*/
					$image	=	strtolower($image);
					if(!strpos($image,'.jpg') && !strpos($image,'.jpeg') && !strpos($image,'.png') && !strpos($image,'.gif') && !strpos($image,'.doc') && !strpos($image,'.pdf') && $image!=''){
						//echo 'not a valid extension';
						$message = $this->__('Uploaded file doesnt match gif, jpg, png, pdf or doc. Please upload a valid file.');
						Mage::getSingleton('core/session')->addError($message);
						echo Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('*/*/index', array( 'param'=>$this->getRequest()->getParam('param'),'key'=>$this->getRequest()->getParam('order'))));die;
					}else{
						//echo 'allowed extension';
						if($_FILES['additionalinformationimg']['name'])
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
							$orderIncrementId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('order'));
							$uploader->setAllowedExtensions(array('jpg', 'JPG', 'jpeg', 'gif', 'GIF', 'png', 'PNG', 'bmp' , 'pdf', 'PDF', 'doc' , 'docx'));
							$uploader->setAllowRenameFiles(false);
							$uploader->setFilesDispersion(false);
							// We set media as the upload dir
							$path = Mage::getBaseDir('media') . DS . 'additionalinformation' . DS . $orderIncrementId . DS;
							//preg_replace('/[^a-zA-Z0-9 s]/', '-', $image);
							$image = strtolower(str_replace(array('    ','   ','  ', ' '), '', preg_replace('/[^a-zA-Z0-9. s]/', '-', trim($image))));
							if(strstr($image,'-')){
								$image	=	str_replace(array('---','--','-'), '_', $image);
							}
							//$newName    = time() .$image;
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
			//$resonseCustomerOfMail->load($resonseCustomerOfMail->getId());
			//foreach($resonseCustomerOfMail as $resonseCustomer){
				
				$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
				$resonseCustomer->setData('customer_images',$save_img_data_db)
								->setData('flag',1)
								->setData('updated_at',now())
								->save();
			//}
				$_order = Mage::getModel('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($this->getRequest()->getParam('order')));
				$comment = 'Customer Uploaded Images For Fraud Check.';
				$_order->addStatusHistoryComment($comment);
				$_order->save();
				//echo "uploaded";
				$this->_redirect('*/*/thankyou', array( 'key'=>$this->getRequest()->getParam('order')));
			}
			else{
				//$this->_redirect('*/*/index', array( 'key'=>$this->getRequest()->getParam('order'),'key'=>$this->getRequest()->getParam('order')));
				echo "already uploaded";
			}
       // var_dump($_fraudCheck->getCustomerImages());exit;
	}
	
	
	public function uploadimagesIEAction(){
		//echo '<pre>'; print_r($_REQUEST);die;
		
		//$output_dir = "uploads/";
		
		$data =array();
		if(isset($saveToDb)){
			unset($saveToDb);
		}
		if(isset($existingImages)){
			unset($existingImages);
		}
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($this->getRequest()->getParam('order'));
		
		if($resonseData[0]['flag']!=1){
			$orderIncrementId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('order'));
			$output_dir = Mage::getBaseDir('media') . DS . 'additionalinformation' . DS . $orderIncrementId . DS;
			//echo $output_dir;die;
			//echo '<pre>'; print_r($_FILES["additionalinformationimg"]);
			//echo '<pre>'; print_r($_FILES["additionalinformationimg"]["name"]);
			//die;
			if(isset($_FILES["additionalinformationimg"]))
			{
				//$ret = array();
				
				$error =$_FILES["additionalinformationimg"]["error"];
				//You need to handle  both cases
				//If Any browser does not support serializing of multiple files using FormData() 
				if(!is_array($_FILES["additionalinformationimg"]["name"])) //single file
				{ 
				}else{
					$_FILES['additionalinformationimg']['name'] = $_FILES['additionalinformationimg']['name'][0];
					$_FILES['additionalinformationimg']['type'] = $_FILES['additionalinformationimg']['type'][0];
					$_FILES['additionalinformationimg']['tmp_name'] = $_FILES['additionalinformationimg']['tmp_name'][0];
					$_FILES['additionalinformationimg']['error'] = $_FILES['additionalinformationimg']['error'][0];
					$_FILES['additionalinformationimg']['size'] = $_FILES['additionalinformationimg']['size'][0];

				}
					//$fileName = $_FILES["additionalinformationimg"]["name"];
					//@move_uploaded_file($_FILES["additionalinformationimg"]["tmp_name"],$output_dir.$fileName);
					//$ret[]= $fileName;
					
		
					
					//foreach ($_FILES['additionalinformationimg']['name'] as $key => $image) {
						$image	=	$_FILES['additionalinformationimg']['name'];
						try {
							$uploader = new Varien_File_Uploader(
								array(
								'name' => str_replace(' ','',$_FILES['additionalinformationimg']['name']),
								'type' => $_FILES['additionalinformationimg']['type'],
								'tmp_name' => $_FILES['additionalinformationimg']['tmp_name'],
								'error' => $_FILES['additionalinformationimg']['error'],
								'size' => $_FILES['additionalinformationimg']['size']
									)
							);
							// Any extention would work
							$orderIncrementId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('order'));
							$uploader->setAllowedExtensions(array('jpg', 'JPG', 'jpeg', 'gif', 'GIF', 'png', 'PNG', 'bmp' , 'pdf', 'PDF', 'doc' , 'docx'));
							$uploader->setAllowRenameFiles(false);
							$uploader->setFilesDispersion(false);
							// We set media as the upload dir
							$path = Mage::getBaseDir('media') . DS . 'additionalinformation' . DS . $orderIncrementId . DS;
							//preg_replace('/[^a-zA-Z0-9 s]/', '-', $image);
							$image = strtolower(str_replace(array('    ','   ','  ', ' '), '', preg_replace('/[^a-zA-Z0-9. s]/', '-', trim($image))));
							if(strstr($image,'-')){
								$image	=	strtolower(str_replace(array('---','--','-'), '_', $image));
							}
							//$newName    = time() .$image;
							//echo $image;die;
							$uploader->save($path, $image);
							$imageUrl = $path . $image;
							$imageResized = $path . $image;
							//echo $imageResized;die;
						if(strpos($image,'.jpg')!='' || strpos($image,'.jpeg')!='' || strpos($image,'.png')!='' || strpos($image,'.gif')!='' || strpos($image,'.bmp')!=''){
							//echo 'test1';die;
							if (file_exists($imageResized) && file_exists($imageUrl)) {
								//echo 'test2';die;
								$imageObj = new Varien_Image($imageUrl);
								$imageObj->constrainOnly(TRUE);
								$imageObj->keepAspectRatio(TRUE);
								$imageObj->keepFrame(FALSE);
								//$imageObj->resize(150, 150);
								$imageObj->save($imageResized);
							}else{
								//echo 'test3';die;
							}
						}else{
							//echo 'test4';die;	
						}
							//echo $image;die;
							$saveToDb[] .= $image;
							$ret= $image;
							//$img = $uploader->save($path, $_FILES['additionalinformationimg']['name']);
						} catch (Exception $e) {
							Mage::log($e->getMessage());
						}
					//}
					
					
					if($resonseData[0]['customer_images']!=''){
						$existingImages = explode(',',$resonseData[0]['customer_images']);
						$save_img_data_db = implode(',',array_merge($saveToDb,$existingImages));
					}
					else{
						$save_img_data_db = implode(',',$saveToDb);
					}
					
					$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
					$resonseCustomer->setData('customer_images',$save_img_data_db)
									//->setData('flag',1)
									->setData('updated_at',now())
									->save();
				//}
					$_order = Mage::getModel('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($this->getRequest()->getParam('order')));
					$comment = 'Customer Uploaded Images For Fraud Check.';
					$_order->addStatusHistoryComment($comment);
					$_order->save();
					//echo "uploaded";die;
				
				
				
				//}
				/*else  //Multiple files, file[]
				{	echo 'multiple';die;
				  $fileCount = count($_FILES["additionalinformationimg"]["name"]);
				  for($i=0; $i < $fileCount; $i++)
				  {
					$fileName = $_FILES["additionalinformationimg"]["name"][$i];
					@move_uploaded_file($_FILES["additionalinformationimg"]["tmp_name"][$i],$output_dir.$fileName);
					$ret[]= $fileName;
					//echo '<pre>';print_r($ret);
				  }*/
				
				
				//var_dump(json_encode('already_uploaded'));
				//var_dump(json_encode($ret));
				echo json_encode($ret);
		 	}
		}else{
			//echo "already uploaded";
			echo json_encode('already_uploaded');
		}
		 
	}
	
	
	public function getgovtemailAction(){
		$govtEmail = strtolower($this->getRequest()->getParam('govtemail'));
		//echo 'test'; echo $govtEmail;die;
		$validateGovtEmail = explode(',',Mage::getStoreConfig('additionalinformation/settings/blacklist_emails'));
		foreach($validateGovtEmail as $validate){
			if (strpos($govtEmail,$validate) !== false) {
				echo "please enter valid government email id";
				return;
			}
		}
		$key = $this->getRequest()->getParam('ord');
		
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($key);
		if($resonseData[0]['flag']!=1 && $resonseData[0]['otp_flag']==0){						
			$IsGovtMailSent = Mage::helper('additionalinformation')->sendOtpAdditionalinformationEmail($govtEmail,$key);
			if($IsGovtMailSent==1){
				//$url = Mage::getUrl().'additionalinformation/index/otpcheck?govtemail='.$govtEmail.'&key='.$key;
				//Mage::app()->getResponse()->setRedirect($url);
				//$this->getResponse()->setBody($govtEmail);
				echo "Email sent to govt id!!!";
			}
		}
		else if($resonseData[0]['flag']==1 && $resonseData[0]['otp_password']!=''){
			echo "We have already sent verification code";
		}
		else{
			echo "You have already fill your data";
		}
	}
	public function otpcheckAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	public function otpsubmitAction(){
		$otpvalue 		= trim($this->getRequest()->getParam('otpval'));
		$resonseData 	= Mage::helper('additionalinformation')->_loadByOrderId($this->getRequest()->getParam('ord'));
		$otpSave 		= Mage::getSingleton('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
		if($otpSave->getOtpFlag()!=1){
			if($resonseData[0]['otp_password']==$otpvalue){
				$otpSave->setData('otp_flag',1)
						->setData('updated_at',now())
						->save();
				//$url = Mage::getUrl().'additionalinformation/index/thankyou';
				$_order = Mage::getModel('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($this->getRequest()->getParam('ord')));
				$comment = 'Customer confirmed order via e-mail '.$resonseData[0]['customer_govt_email'];
				$_order->addStatusHistoryComment($comment);
				$_order->save();
				echo "OTP Verified!!!";
				
				$otpSave->setData('flag',1)
						->setData('updated_at',now())
						->save();
				//Mage::app()->getResponse()->setRedirect($url);
			}
			else{
				echo "OTP not verified!!!";
			}
		}
		else{
			echo 'You have already verified your email account';
		}
	}
	
	public function savebankdetailAction(){
		$bankName = $this->getRequest()->getParam('bankname');
		$transactionId = $this->getRequest()->getParam('transactionid');
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($this->getRequest()->getParam('ord'));
		if($resonseData[0]['flag']!=1){	
			$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
			$resonseCustomer->setData('customer_bank_name',$bankName)
							->setData('customer_bank_auth_code',$transactionId)
							->setData('flag',1)
							->setData('updated_at',now())
							->save();
			$_order = Mage::getModel('sales/order')->loadByIncrementId(Mage::helper('core')->urlDecode($this->getRequest()->getParam('ord')));
			$comment = 'Customer confirmed his/her bank details';
			$_order->addStatusHistoryComment($comment);
			$_order->save();	
			echo "bank detail saved";				
			//$url = Mage::getUrl().'additionalinformation/index/thankyou';
			//Mage::app()->getResponse()->setRedirect($url);			
		}
		else{
			echo "You cannot upload more than one data";
		}
	}
	public function thankyouAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function thanksyouAction(){
		$resonseData = Mage::helper('additionalinformation')->_loadByOrderId($this->getRequest()->getParam('key'));
		//echo '<pre>';print_r($resonseData);
		if($resonseData[0]['flag']!=1){						
			$resonseCustomer = Mage::getModel('additionalinformation/additionalinformation')->load($resonseData[0]['additionalinformation_id']);
			$resonseCustomer->setData('flag',1)
							->setData('updated_at',now())
							->save();
		}
		$this->loadLayout();
		$this->renderLayout();
	}
	
}