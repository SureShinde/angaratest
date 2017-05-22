<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes

try {
	$product_collection = Mage::getModel('catalog/product')->getCollection(); 	//	Getting the product collection
	$product_collection->addAttributeToSelect('url_path');
	$product_collection->addAttributeToFilter('status', 1); 					// 	Enabled
	$product_collection->addAttributeToFilter('visibility', 4);					// 	catalog, search
	$product_collection->addAttributeToFilter('url_path',array('neq'=>''));
	$product_collection->addAttributeToSort('entity_id', 'DESC');				//	Order by entity_id
	$product_collection->getSelect();
	//$product_collection->getSelect()->limit(4);  			
	//echo $product_collection->load(1); die;									
	if(count($product_collection) > 0) {
		$xmlOutput = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlOutput.= '<urlset ';
      	$xmlOutput.= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
      	$xmlOutput.= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	    $xmlOutput.= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
		$xmlOutput.= 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
	
		$siteURL = Mage::getBaseUrl();
		foreach ($product_collection as $product){
			$allowed = checkAllowedProduct($product->getId());
			if($allowed){
				$stockInven = $product->getStockItem();		//	get stock items
				$isInStock = (int)$stockInven->getIsInStock();
				
				$productUrl = $siteURL.$product['url_path'];
				if(!stristr($productUrl,'x.html')){
					$xmlOutput .= '<url>';
					$xmlOutput .= '<loc>'.$productUrl.'</loc>';
					$xmlOutput .= '<changefreq>monthly</changefreq>';
					if($isInStock==1){ 
						$xmlOutput .= '<priority>0.7</priority>';
					}else{
						$xmlOutput .= '<priority>0.1</priority>';
					}
					$xmlOutput .= '</url>';
				}
			}
		} 
	} 
	$xmlOutput .= '</urlset>';
	
	//	Writing data to xml file
	$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$fileName		=	'sitemap1.xml';
	$filePath		=	$directoryPath.$fileName;
	$file 			= 	fopen($filePath,"w");
	if(fwrite($file,$xmlOutput)){	
		echo '<a href="'.$siteURL.$fileName.'"> Product URL\'s Sitemap</a> generated successfully.';
		fclose($file); 
	}else{
		echo $filePath .' is not writable.';
	}
} catch (Exception $e) {
    Mage::printException($e);
}

function checkAllowedProduct($productID){
	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	$write->beginTransaction();
	$tableName	=	Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_text');
	
	$query = "SELECT value FROM ". $tableName ." WHERE entity_id='".$productID."' AND attribute_id='95'";
	//echo $query;die;
	$results = $read->fetchRow($query);
	//echo '<pre>';print_r($results);die;
	$value = $results['value'];
	
	if($value != '' && stristr($value, 'NOINDEX,NOFOLLOW') !== false){
		return false;
	}
	return true;
}?>