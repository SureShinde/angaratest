<?php

$mageFilename = '../app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);

ini_set('display_errors', 'Off');
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

set_time_limit(0);
ini_set('memory_limit','1024M');
$starttime 	= 	microtime(true);


$resource 			= 	Mage::getSingleton('core/resource');
$writeConnection 	= 	$resource->getConnection('core_write');


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
	$sku_entry			=	array();
	$updates_handle		=	fopen($updates_file, 'r');
	$i=0;
	if($updates_handle) {
		while($sku_entry=fgetcsv($updates_handle, 1000, ",")) {
			//echo '<pre>';print_r($sku_entry);
			//	Escape the header
			if($i>0 && count($sku_entry)>1){
				$old_sku	=	$sku_entry[0];
				$new_sku	=	str_replace(' ','-',$sku_entry[1]);			
				//	Check if there is a change in sku
				if($old_sku	== $new_sku){
					echo "<br>No change in sku for '".$old_sku."'";
				}else{
					try {
						$get_item = Mage::getModel('catalog/product')->loadByAttribute('sku', $old_sku);
						if ($get_item) {
							//$get_item->setSku($new_sku)->save();
							$product_id = $get_item->getId();
							$writeConnection->update(
									"catalog_product_entity",
									array("sku" => $new_sku),
									"entity_id='".$product_id."'"
							);
							
							//	Change sku in sales_flat_order_item to avoid ordersheet not opening issue
							$writeConnection->update(
									"sales_flat_order_item",
									array("sku" => $new_sku),
									"sku = '".$old_sku."'"
							);
							echo "<br><span style='color:green'>Successfully changed the old sku '".$old_sku."' to new sku '".$new_sku."'</span>";
						} else {
							echo "<br><span style='color:red'>The product with sku '".$old_sku."' does not found.</span>";
						}
					} catch (Exception $e) {
						echo "Cannot retrieve products : ".$e->getMessage()."<br>";
						return;
					}
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
    <div class="row"><a href="change_sku.csv">Click here</a> to download a sample import csv file.</div>
</html>
<?php } 

function validName($string){
	$string = str_replace(' ', '-', $string);
	$string = str_replace('&', '', $string);
	return $string;
}
?>