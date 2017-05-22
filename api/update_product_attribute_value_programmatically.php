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
ini_set('max_execution_time', 3000);    // 300 seconds = 5 minutes


$params		=	Mage::app()->getRequest()->getParams();
//print_r($params);die;
$limit		=	$params['limit'];
if($limit==-1){
	$limit	=	'';	
}else{
	$limit	=	'10';
}

$productCollection = Mage::getModel('catalog/product')->getCollection()
									->addAttributeToFilter('status',1)
									//->addAttributeToFilter('visibility',4)
									//->addAttributeToFilter('sku', array('like' => '%-SL-%'))
									->addAttributeToFilter('price',array('lt'=>'250'))
									//->addAttributeToFilter('vendor_lead_time', array('neq' => ''))
									->setPageSize($limit)  
									//->load(1);die;
									;
//echo '<pre>';print_r($productCollection->getData());die;
//$ids = $productCollection->getAllIds();
//echo '<pre>';print_r($ids);die;
if( $productCollection->getSize() > 0){
	foreach($productCollection as $product){
		//echo '<pre>';print_r($product);die;
		$sku		=	$product->getSku();
		$productID	=	$product->getId();
		try{
			updateAttribute($productID);
			echo '<br>Vendor Lead Time updated for '.$sku;
		}catch(Mage_Core_Exception $e){
			echo $e->getMessage();	
		}
	}
}else{
	echo 'There is nothing to update.';
}


function updateAttribute($productID) {
	$read  	= Mage::getSingleton('core/resource')->getConnection('core_read');
	$write 	= Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$tableName	=	Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
	
	$updateQuery = "UPDATE ". $tableName ." SET value = value + 20 WHERE entity_id='".$productID."' AND attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'vendor_lead_time')";
	//echo $updateQuery;die;
	$write->query($updateQuery);
}
?>
