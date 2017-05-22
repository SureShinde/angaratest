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
ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes


if(isset($_FILES['input_file']['name']) && (file_exists($_FILES['input_file']['tmp_name'])) ){
	try {
		$startTime = date('d-m-Y h:i:s');
		echo '<br>The script started at '. $startTime.'<br>';	
		
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
				updateData($data, $height, $width);
				$i++;
			}
			echo "<br><br>Total ".($i)." records updated.";
			//echo '<br>Voila! You have done a great job.';
		}
	}catch(Exception $e) {
		echo $e->getMessage();
	}
	
	$endTime = date('d-m-Y h:i:s');
	echo '<br><br>The script finished at '. $endTime;
	
	$diff = strtotime($endTime) - strtotime($startTime);
	echo '<br> The script took ';
	if($diff > 300){
		$diff_in_mins = $diff/60;
			echo $diff_in_mins.' minutes.';
			$diff_in_hrs = $diff/3600;		
			echo $diff_in_hrs.' hours.';
			$diff_in_days = $diff_in_hrs/24;
			echo $diff_in_days.' days.';
	}else{
		echo $diff.' seconds.';	
	}
	echo '<br><a href="">Do you wanna do it once again</a>';
}else{ ?>
	
<!DOCTYPE html>
<html lang="en">
    <div class="row">
        <form role="form" method="post" enctype="multipart/form-data" name="frm" action="">
          <div class="form-group">
            <label for="input_file">Please upload a <a href="resize_images.csv" title="Download sample file">csv file</a> with sample urls to resize images:</label>
            <input type="file" id="input_file" name="input_file" accept=".csv" required>
            
            Height (px)	:	<input name="height" type="text" value="600">
            Width (px)	:	<input name="width" type="text" value="600">
            
            <button name="submit" type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
    </div>
</html>
<?php }

function updateData( $data, $height, $width ) {
	$urlArray	=	explode('media/', $data[0]);
	$imageUrl	=	$urlArray[1];
	if($imageUrl){
		try {
			$resizedImageUrl	=	Mage::helper('function')->getResizedUrl( $imageUrl, $height, $width );
			echo '<br>Image Resized at <a href="'.$resizedImageUrl.'">'. $resizedImageUrl.'</a>';
		} catch (Exception $e) {
			echo '<br>'.$e->getMessage();
		}
	}else{
		echo '<br>Sorry! The Image can not resized.';
	}
}
?>

