<?php
/**
 * Adminhtml newsletter subscribers controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Newsletter'.DS.'SubscriberController.php');
require_once "Mage/Adminhtml/controllers/Newsletter/SubscriberController.php";
class Angara_UtilityBackend_Adminhtml_Newsletter_SubscriberController extends Mage_Adminhtml_Newsletter_SubscriberController {

	/*
		Custom action to import uploaded file and save data in db
	*/
	public function importAction(){ 
		$data	=	$this->getRequest()->getParams();
		if($data){
			if(isset($_FILES['input_file']['name']) && (file_exists($_FILES['input_file']['tmp_name'])) ){
				try {
					$todayDateTime	=	date('d-m-Y-h_i_s');										//	append the time to file name to make the every file having different name
					$temp 			= 	explode(".", $_FILES["input_file"]["name"]);
					//$newfilename 	= 	$temp[0].'-'.round(microtime(true)) . '.' . end($temp);		//	rename the file before uploading
					$newfilename 	= 	$temp[0].'-'.$todayDateTime.'.'.end($temp);					//	rename the file before uploading
					
					$uploader = new Varien_File_Uploader('input_file');
					$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					$fileUploadPath = Mage::getBaseDir('media') . DS . 'importcsv' . DS ;
					//$uploader->save($fileUploadPath, $_FILES['input_file']['name'] );		//	file uploaded
					$uploader->save($fileUploadPath, $newfilename );		//	file uploaded
					//echo '<br>File uploaded successfully.';
					//$file =  $_FILES['input_file']['name'];//.date('d-m-Y_h:i:s');
					$filepath 	= $fileUploadPath.$newfilename;
					$i = 0;
					if(($handle = fopen("$filepath", "r")) !== FALSE) {
						$height	=	$_POST['height'];
						$width	=	$_POST['width'];
						while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){ 
							if($i > 0 && count($data)>1){		//	don't consider the header row
								$this->updateData($data);
							}
							$i++;
						}
					}
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Record(s) imported successfully."));
					$this->_redirect("*/*/");
					return;
				}catch(Exception $e) {
					echo $e->getMessage();
				}
			}
		}else{
			$this->loadLayout();
			$this->renderLayout();
		}
	}
	
	
	public function updateData( $data) {
        try {
			$email		=	$data[0];
			$firstname	=	$data[1];
			$lastname	=	$data[2];
			
			$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
			if ($subscriber->getId()) {
				Mage::log($email.' already subscribed', null, 'newsletter_import.log', true);
				#change status to "subscribed" and save
				$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)->save();
			}else {
                $subscriber = Mage::getModel('newsletter/subscriber')->setImportMode(TRUE);
                if(isset($firstname)){
                    $subscriber->setSubscriberFirstname($firstname);
                }
                if(isset($lastname)){
                    $subscriber->setSubscriberLastname($lastname);
                }
                $subscriber->subscribe($email);
				Mage::log($firstname.' '.$lastname.' '.$email.' subscribed', null, 'newsletter_import.log', true);
            }
        } catch (Exception $e) {
            echo $e->getMessage();die;
        }
	}
	
	
	/**
     * Load newsletter_subscriber by email
     *
     * @param string $email
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function loadByEmail($email)
    {
        return Mage::getModel('newsletter/subscriber')
            ->getCollection()
            ->addFieldToFilter('subscriber_email', $email)
            ->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
            ->getFirstItem();
    }
}
