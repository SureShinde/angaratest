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


$storeId    	= 	Mage::app()->getStore()->getId();   
$proxy			=	new SoapClient('http://'.$_SERVER['HTTP_HOST'].'/index.php/api?wsdl=1');
$sessionId		=	$proxy->login('VaseemApi','thisismyapikey4');		//	$proxy->login('apiUser', 'apiKey');
$customerID		=	'4753';		//	vaseem.ansari@angara.com

$csv			= 	new Varien_File_Csv();
$data   		= 	$csv->getData('tags.csv'); //path to csv
array_shift($data);
//echo '<pre>';print_r($data);die;
foreach($data as $a => $tagsArray){
	//echo '<pre>'; print_r($tagsArray);die;
	$sku	=	$tagsArray[0];
	$tag	=	$tagsArray[1];
	//echo '<br>'.$sku.'->'.$tag;die;
	$productId 	= 	Mage::getModel("catalog/product")->getIdBySku( $sku );
	if($productId==''){
		echo 'Product with Sku '.$sku.' does not Exist.'; 	
	}else{
		//$data 		= 	array('product_id' => 2, 'store' => 'default', 'customer_id' => 10002, 'tag' => "First 'Second tag' Third");
		//if(strstr($tag,' ')){
			$data 		= 	array('product_id' => $productId, 'store' => 'default', 'customer_id' => $customerID, 'tag' => "'$tag'");
		/*}else{
			$data 		= 	array('product_id' => $productId, 'store' => 'default', 'customer_id' => $customerID, 'tag' => $tag);
		}*/
		$addResult 	= 	$proxy->call($sessionId,"product_tag.add",array($data));
		//$tagsArray	=	explode(" ", $tag);
		echo $tag. ' added to sku-> '. $sku .' with Product-> '.$productId.'<br>';
	}
}
?>