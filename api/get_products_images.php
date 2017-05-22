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
		
	$offset	=	Mage::app()->getRequest()->getParam('from');
	if($offset==''){
		$offset	=	'0';	
	}
	$limit	=	'1000';	
	$productData = Mage::getModel('catalog/product')
						->getCollection()
						//->addAttributeToFilter('status',1) 			//	only enabled product
						//->addAttributeToFilter('visibility', 4)		// catalog, search
						//->addAttributeToFilter('short_description',array('neq'=>'no_selection'))
						->addAttributeToFilter('type_id', array('eq' => 'configurable'))
						//->addAttributeToFilter('url_path',array('neq'=>''))
						->addAttributeToFilter('image', array('notnull' => '', 'neq' => 'no_selection'))
						->setPageSize($limit)
						->setCurPage($offset)
						//->load(1);die;
						;
	if (count($productData) > 0) {
		$mediaURL		=	'http://cdn.angara.com/media/catalog/product';//Mage::getBaseUrl();
		
		//	Create a file
		$io 	= 	new Varien_Io_File();
		$path 	= 	Mage::getBaseDir('var') . DS . 'export' . DS;
		//$name = 	md5(microtime());
		$name 	= 	'Image_Dimensions_'.$offset;//_' . date('d-m-Y') .'_' . date('H-i-s');
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
			//echo '<pre>';print_r($product);die;	
			//$productID		=	$product['entity_id'];
			$sku			=	$product['sku'];
			$imageUrl		=	$mediaURL.$product['image'];
			//$image 			= 	Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
			
			list($width, $height) 	= 	@getimagesize($imageUrl);					//	Check image dimensions
			$csvValues				=	array($sku, $imageUrl, $width, $height);	//	Creating data to write in csv	
			$io->streamWriteCsv($csvValues);										//	Write data in file
			$message.='<br>Data writed for sku '. $sku;	
		} 
	} 
	$io->streamClose();
	
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