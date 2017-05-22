<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);


$read  	= 	Mage::getSingleton('core/resource')->getConnection('core_read');
$write 	= 	Mage::getSingleton('core/resource')->getConnection('core_write');
	
//$tableName	=	Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
	
$selectQuery = "SELECT `rt`.`review_id`, `rt`.`created_at`,
		(select status_code from review_status where status_id=`rt`.`status_id`) as status , 
        `rdt`.`title`, `rdt`.`nickname`, 
        `rdt`.`detail` as review,
		
        (select value from catalog_product_entity_varchar where attribute_id =(select attribute_id from eav_attribute where attribute_code ='name' AND entity_type_id='4') AND entity_type_id = '4' AND store_id='0' AND entity_id=e.entity_id) 'product_name', `e` . sku as 'sku',
        
         `rdt`.`order_id` as 'order_id', 
        `rov`.`value` as rating	
FROM `catalog_product_entity` AS `e`
INNER JOIN `review` AS `rt` ON rt.entity_pk_value = e.entity_id
INNER JOIN `review_detail` AS `rdt` ON rdt.review_id = rt.review_id
INNER JOIN `rating_option_vote` AS `rov` ON rdt.review_id = rov.review_id
order by `rt`.`created_at` desc";
//LIMIT 0 , 30";
//echo $selectQuery;die;
$results       = $read->fetchAll($selectQuery); //fetchRow($sql), fetchOne($sql),...
//Zend_Debug::dump($results);

//echo count($results); die;
if(count($results)>0){
	
	//	CSV Export
	$filename = 'Reviews_' . date('d-m-Y') .'_' . date('H-i-s');

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$filename.'.csv');
	
	$output = fopen('php://output', 'w');		// Create a file pointer connected to the output stream
	
	$csvHeader 	= 	array('Review ID', 'Created On', 'Status', 'Title', 'Nickname', 'Review', 'Product Name', 'Product Sku', 'Order Id', 'Rating');
	fputcsv($output, $csvHeader);				// Output the column headings

	try {
		foreach($results as $data){
			//echo '<pre>';print_r($data);die;
			$review_id		=	$data['review_id'];
			$created_at		=	$data['created_at'];
			$status			=	$data['status'];
			$title			=	$data['title'];
			$nickname		=	$data['nickname'];
			$review			=	$data['review'];
			$product_name	=	$data['product_name'];
			$sku			=	$data['sku'];
			$order_id		=	$data['order_id'];
			$rating			=	$data['rating'];
			
			//	Add Data in csv
			fputcsv($output, array($review_id, $created_at, $status,$title,$nickname,$review,$product_name,$sku,$order_id,$rating));
		}
	} catch (Exception $e) {
		echo $e->getMessage();die;
	}
	fclose($output);
	//echo 'Reviews Exported Successfully';
}
?>