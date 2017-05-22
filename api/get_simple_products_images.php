<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes


/*
	http://angara2.git/api/get_products_images.php?limit=10
	return the csv file with image dimentions
*/

try {
	
	$startTime = date('d-m-Y h:i:s');
	$message.='The script started at '. $startTime;	
		
	$limit	=	'1000';	
	$from	=	Mage::app()->getRequest()->getParam('from');
	if($from==''){
		$offset	=	'0';	
	}else{
		$offset	=	$limit * $from;	
	}
	
	$read 	= 	Mage::getSingleton('core/resource')->getConnection('core_read');		// reading the database resource
	$query 	= 	'SELECT * FROM ' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity') .' where entity_id NOT IN (SELECT product_id FROM ' . Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link') .') and type_id="simple" order by entity_id desc LIMIT '.$limit.' OFFSET '.$offset.'';
	//echo $query; die;
	$productData = 	$read->fetchAll($query);
	//echo '<pre>'; print_r($results); die;
						
	if (count($productData) > 0) {
		$mediaURL		=	'http://cdn.angara.com/media/catalog/product';//Mage::getBaseUrl();
		
		//	Create a file
		$io 	= 	new Varien_Io_File();
		$path 	= 	Mage::getBaseDir('var') . DS . 'export' . DS;
		//$name = 	md5(microtime());
		$name 	= 	'Image_Dimensions_'.$from;//_' . date('d-m-Y') .'_' . date('H-i-s');
		$file 	= 	$path . DS . $name . '.csv';
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $path));
		
		if ($io->fileExists($file) && !$io->isWriteable($file)) {
			echo $message.='The file is not writable '. $file; die;
		}
		$io->streamOpen($file, 'w+');
		$io->streamLock(true);
		
		//	Write data in file
		$csvHeader 	= 	array('Sku', 'Image URL', 'Width (Pixel)', 'Height (Pixel)');
		$io->streamWriteCsv($csvHeader);
				
		$csvValues			=	array();
		$i=1;
		foreach ($productData as $product){
			//echo '<pre>';print_r($product);
			$sku			=	$product['sku'];
			$_product 		= 	Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
			//$imageUrl		=	$mediaURL.$product['image'];
			$imageUrl		= 	Mage::getModel('catalog/product_media_config')->getMediaUrl($_product->getImage());
			if($_product->getImage()){
				list($width, $height) 	= 	@getimagesize($imageUrl);					//	Check image dimensions
				$csvValues				=	array($sku, $imageUrl, $width, $height);	//	Creating data to write in csv	
				$io->streamWriteCsv($csvValues);										//	Write data in file
				$message.='<br>Data writed for sku '. $sku;	
			}else{
				$message.='<br>There is no image for sku '. $sku;	
			}
		} 
		$io->streamClose();
	} else{
		$message.='<br>There are no records';
	}	
} catch (Exception $e) {
    Mage::printException($e);
}

	$endTime = date('d-m-Y h:i:s');
	$message.='<br>The script finished at '. $endTime;
	
	$diff = strtotime($endTime) - strtotime($startTime);
	$message.='<br>The script took ';
	if($diff > 300){
		$diff_in_mins 	= 	$diff/60;
		$diff_in_hrs 	= 	$diff/3600;		
		$diff_in_days 	= 	$diff_in_hrs/24;
		if($diff_in_mins < 61 ){
			$message.=$diff_in_mins.' minutes.';
		}elseif($diff_in_mins > 61 && $diff_in_hrs < 2 ){
			$message.=$diff_in_hrs.' hours.';
		}else{
			$message.=$diff_in_days.' days.';
		}
	}else{
		$message.=$diff.' seconds.';	
	}
	//$message.='<a href="">Do you wanna do it once again</a>';
	echo $message;
	Mage::log($message, null, 'export_images.log', true);

?>