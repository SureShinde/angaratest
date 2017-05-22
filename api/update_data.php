<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);




/*
Run http://dev.angara.com/api/update_product_attribute_value_programmatically.php to update only 10 records

Run http://dev.angara.com/api/update_product_attribute_value_programmatically.php?limit=-1 to update all records
*/

/*$params		=	Mage::app()->getRequest()->getParams();
//print_r($params);die;
$limit		=	$params['limit'];
if($limit==-1){
	$limit	=	'';	
}else{
	$limit	=	'10';
}*/


$read  	= 	Mage::getSingleton('core/resource')->getConnection('core_read');
$write 	= 	Mage::getSingleton('core/resource')->getConnection('core_write');
	

$tableName	=	Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
	
$selectQuery = "SELECT item_id, order_id, created_at, product_id, sku from sales_flat_order_item where created_at > '2016-12-05 16:21:12' order by item_id desc;";
//echo $selectQuery;die;
$results       = $read->fetchAll($selectQuery); //fetchRow($sql), fetchOne($sql),...
//Zend_Debug::dump($results);

//echo count($results); die;
if(count($results)>0){
	foreach($results as $data){
		//echo '<pre>';print_r($data); //die;
		$itemId				=	$data['item_id'];
		$orderIncrementId	=	$data['order_id'];
		$sku				=	$data['sku'];
		$childProductSku	=	Mage::helper('function')->getRealChildSku($sku);
		
		//if($orderIncrementId == '33009'){
			//echo '<pre>';print_r($data); //die;
			//	Get vendor_lead_time of product
			$selectProductQuery = 	"SELECT * from catalog_product_entity_varchar where entity_id = (select entity_id from catalog_product_entity where sku='".$childProductSku."') && attribute_id = (SELECT attribute_id FROM eav_attribute where attribute_code = 'vendor_lead_time');";
			//echo $selectProductQuery;//die;
			
			$resultProduct			=	$read->fetchRow($selectProductQuery);
			$productVendorLeadTime	=	$resultProduct['value'];
			//echo $productVendorLeadTime;die;
			if($productVendorLeadTime){
				//	Update vendor_lead_time in table
				$updateQuery 		= 	"UPDATE sales_flat_order_item SET vendor_lead_time='".$productVendorLeadTime."' WHERE item_id='".$itemId."'";
				echo $updateQuery;

				try{
					$write->query($updateQuery);
					echo '<br>Vendor Lead Time updated to '.$productVendorLeadTime.' for sku '.$childProductSku.' with Order Increment Id '.$orderIncrementId;
				}catch(Mage_Core_Exception $e){
					echo $e->getMessage();	
				}
			}
		//}
	}
}
?>
