<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//set_time_limit(0);
ini_set('memory_limit','1024M');                // increasing memory limit
ini_set('max_execution_time', 3000);    		// 300 seconds = 5 minutes
$starttime 	= 	microtime(true);

if(isset($_FILES['input_file']['name']) && (file_exists($_FILES['input_file']['tmp_name'])) ){
	//	Rename File
	$todayDateTime	=	date('d-m-Y-h_i_s');							//	append the time to file name to make the every file having different name
	$temp 			= 	explode(".", $_FILES["input_file"]["name"]);
	$newfilename 	= 	validName($temp[0].'-'.$todayDateTime.'.'.end($temp));		//	File Name Corrections
	
	$uploader = new Varien_File_Uploader('input_file');
	$uploader->setAllowedExtensions(array('csv'));
	$uploader->setAllowRenameFiles(true);
	$uploader->setFilesDispersion(false);
	$fileUploadPath = Mage::getBaseDir('media') . DS . 'importcsv' . DS ;
	$uploader->save($fileUploadPath, $newfilename );		//	file uploaded
	//echo '<br>File uploaded successfully.';
	$filepath = $fileUploadPath.$newfilename;
	//$updates_file		=	"./var/export/change_sku.csv";
	//$updates_file		=	"change_sku.csv";
	$updates_file		=	$filepath;
	$csvData			=	array();
	$updates_handle		=	fopen($updates_file, 'r');
	$i=0;
	if($updates_handle) {
		
		$read  		= Mage::getSingleton('core/resource')->getConnection('core_read');
		$write 		= Mage::getSingleton('core/resource')->getConnection('core_write');
		$storeId    = Mage::app()->getStore()->getId();       
		while($csvData=fgetcsv($updates_handle, 1000, ",")) {
			//	Escape the header
			if($i>0 && count($csvData)>1){
				//echo '<pre>';print_r($csvData);
				$sku				=	$csvData[0];
				$alternate_image_no	=	$csvData[1];			
				
				//	Find the media_gallery images
				$queryMediaGalleryImages 	=	"SELECT * from catalog_product_entity_media_gallery as cpemg 
												left join catalog_product_entity_media_gallery_value as cpemgv 
												on cpemg.value_id = cpemgv.value_id
												where cpemg.entity_id = (select entity_id from catalog_product_entity where sku='".$sku."')
												AND cpemgv.position= '".$alternate_image_no."'";
				//echo $queryMediaGalleryImages;
				$resultsMedia = $read->fetchRow($queryMediaGalleryImages);
				//echo '<pre>';print_r($resultsMedia);//die;
				if( is_array($resultsMedia) && count($resultsMedia)>0 ){
					$alternate_image	=	$resultsMedia['value'];
					//echo $alternate_image;//die;
					
					//	Check if physical image exist
					$file_path			=	Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
					$imagePath			=	$file_path.$alternate_image;
					//echo $imagePath;//die;
					if(!file_exists($imagePath)){
						echo "<br><span style='color:red'>Physical image not exist for sku '".$sku."' at path ".$imagePath."'. The toggle image will be set nut the image will not be visible until you upload the physical image.</span>";;
					}
					
					//	Let update the toggle image path in db
					try {
						//	Get the existing data 
						$query	=	"SELECT * FROM catalog_product_entity_varchar
									WHERE entity_id = (	SELECT entity_id FROM catalog_product_entity WHERE sku='".$sku."') && attribute_id = ( SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'alternate_image')";
						//echo $query;//die;
						$results = $read->fetchRow($query);
						//echo '<pre>';print_r($results);//die;
						if( is_array($results) && count($results)>0 ){
							$query = "UPDATE `catalog_product_entity_varchar` SET `value`='".$alternate_image."' WHERE  `value_id`='".$results['value_id']."'";
							echo "<br><span style='color:green'>Toggle image set successfully for sku '".$sku."'</span>";
						} else{
							$query = "INSERT INTO `catalog_product_entity_varchar` SET entity_type_id='4', attribute_id=( SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'alternate_image'), store_id = '".$storeId."', entity_id= (SELECT entity_id FROM catalog_product_entity WHERE sku='".$sku."'), value='".$alternate_image."'";
							echo "<br><span style='color:green'>A new entry added for Toggle image for sku '".$sku."'.</span>";
						}
						//echo $query;die;
						$write->query($query);
					} catch (Exception $e) {
						echo "Cannot retrieve products : ".$e->getMessage()."<br>";
						return;
					}
				}else{
					echo "<br><span style='color:red'>There is no image available at position '".$alternate_image_no."' for sku '".$sku."'. Please check the images section under admin for this sku.</span>";
				}
			}
			$i++;
		}
	}
	fclose($updates_handle);
	echo "<br>Time: " . (microtime(true) - $starttime) . " seconds\n";
}else{
?>

<!DOCTYPE html>
<html lang="en">
    <div class="row">
        <form role="form" method="post" enctype="multipart/form-data" name="frm_update_data" action="">
          <div class="form-group">
            <label for="input_file">Please upload a csv file to update data :</label>
            <input type="file" id="input_file" name="input_file" accept=".csv" required>
            <button name="submit" type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
    </div>
    <div class="row"><a href="update_attribute.csv">Click here</a> to download a sample import csv file.</div>
</html>
<?php } 

function validName($string){
	$string = str_replace(' ', '-', $string);
	$string = str_replace('&', '', $string);
	return $string;
}

