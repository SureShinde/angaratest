<?php
class Angara_CustomerStory_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function uploadStoryImageAction() 
	{
		if(isset($_POST['form_submit-story']) && $_POST['form_submit-story'] == 1)
		{			
			$commentText = $_POST['comments'];
			if(trim($commentText) != ''){
				if(isset($_FILES['imagesToUpload']))
				{
					$orderId = $this->getRequest()->getParam('orderId');
					if($orderId){
						$order = Mage::getModel('sales/order')->load($orderId, 'increment_id');						
						if($order && $order->getId()){
							$customerStory = Mage::getModel('customerstory/story');
							/*$customerData = $customerStory->getCollection()
												->addFieldToFilter('order_id', $orderId);
							if(count($customerData) > 0){
								$isSuccess = 0;
								header('Content-Type: application/json');
								echo json_encode(array('html' => 'Sorry! You have already shared your story.','status' => 'false'));  
							}
							else{*/							
								/* Allowed file extension */
								$allowedExtensions = array("gif", "jpeg", "jpg", "png");
								
								/* Allowed Image types */
								$types = array('image/gif', 'image/png', 'image/x-png', 'image/pjpeg', 'image/jpg', 'image/jpeg');
								
								/* Uploaded Directory */
								$target_dir = Mage::getBaseDir('media').DS.'shareStory'.DS.$orderId.DS;
							
								foreach($_FILES['imagesToUpload']['name'] as $key => $file)
								{
									if(!empty($_FILES['imagesToUpload']['name'][$key])){
										$fileExtension = explode(".", $_FILES['imagesToUpload']['name'][$key]);
										
										/* Contains file extension */
										$extension = end($fileExtension);				
										
										if(in_array(strtolower($_FILES['imagesToUpload']['type'][$key]), $types) && in_array(strtolower($extension), $allowedExtensions) && !$_FILES['imagesToUpload']['error'][$key] > 0)
										{				
											if($_FILES['imagesToUpload']['size'][$key] <= 5242880)
											{							
												$isSuccess = 1;
												
												$finalImages[$key]['name'] = $_FILES['imagesToUpload']["name"][$key];
												$finalImages[$key]['tmp_name'] = $_FILES['imagesToUpload']["tmp_name"][$key];
												$finalImages[$key]['size'] = $_FILES['imagesToUpload']["size"][$key];
												$finalImages[$key]['type'] = $_FILES['imagesToUpload']["type"][$key];
											}
											else
											{
												$isSuccess = 0;
												header('Content-Type: application/json');
												echo json_encode(array('html' => 'Image "'.$_FILES['imagesToUpload']["name"][$key].'" size exceeded. Supported image size upto 5 MB.','status' => 'false'));
											}	
										}
										else
										{
											$isSuccess = 0;
											header('Content-Type: application/json');
											echo json_encode(array('html' => 'Please upload only jpeg, jpg, png, gif images.','status' => 'false'));
										}
									}	
								}
						
								if($isSuccess){
									if(!is_dir($target_dir)) 
									{
										mkdir($target_dir);
									}
									
									for($finImg=0; $finImg < count($finalImages); $finImg++){
										$target_file = $target_dir.$finalImages[$finImg]['name'];
										move_uploaded_file($finalImages[$finImg]['tmp_name'], $target_file);
									}
									
									$customerStory->setData('description', trim($commentText))
										->setData('image_details', urlencode(serialize($finalImages)))
										->setData('order_id', $orderId)
										->setData('is_approved', 0)
										->setData('created_at',now())
										->setData('updated_at',now())
										->save();
									header('Content-Type: application/json');
									echo json_encode(array('html' => 'Thank You! Your story has been shared successfully.','status' => 'true'));  	
								}
							//}
						}
						else{
							$isSuccess = 0;
							header('Content-Type: application/json');
							echo json_encode(array('html' => 'Sorry! There was no order placed with this id: '.$orderId,'status' => 'false')); 
						}	
					}
					else{
						$isSuccess = 0;
						header('Content-Type: application/json');
						echo json_encode(array('html' => 'Sorry! We did not find any order id placed by you.','status' => 'false')); 
					}
				}
				else
				{
					$isSuccess = 0;
					header('Content-Type: application/json');
					echo json_encode(array('html' => 'Please choose the image(s).','status' => 'false'));
				}
			}
			else
			{
				$isSuccess = 0;
				header('Content-Type: application/json');
				echo json_encode(array('html' => 'Please enter the text.','status' => 'false'));
			}	
		}	
    }
}?>