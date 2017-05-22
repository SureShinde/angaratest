<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes


function nodeToArray(Varien_Data_Tree_Node $node) 
{ 
	//	Get category data, category collection, category url, category name
	$result = array(); 
	$result['category_id'] 	= 	$node->getId(); 
	$result['parent_id'] 	= 	$node->getParentId(); 
	$result['name'] 		= 	$node->getName(); 
	$result['url_path'] 	= 	$node->getData('url_path');
	$result['is_active'] 	= 	$node->getIsActive(); 
	$result['position'] 	= 	$node->getPosition(); 
	$result['level'] 		= 	$node->getLevel(); 
	$result['children'] 	= 	array();
	
	foreach ($node->getChildren() as $child) { 
		$result['children'][] = nodeToArray($child); 
	}
	//echo '<pre>'; print_r($result); die;
	return $result; 
} 

function load_tree() {
	$tree = Mage::getResourceSingleton('catalog/category_tree')->load(); 
	$store = 1; 	$parentId = 1; 	
	$root = $tree->getNodeById($parentId);	
	if($root && $root->getId() == 1) { 
		$root->setName(Mage::helper('catalog')->__('Root')); 
	}
	$collection = Mage::getModel('catalog/category')->getCollection() 
					->setStoreId($store) 
					->addAttributeToSelect('name') 
					->addAttributeToSelect('url_path')
					->addAttributeToSelect('is_active')
					->addAttributeToFilter('is_active', 1)		//	Get only Active Categories
					//->load(1); die;
					;
	$tree->addCollectionData($collection, true); 
	return nodeToArray($root);
}

function print_tree($tree,$level) { 
	$level ++; 
	$siteURL		=	Mage::getBaseUrl();
	$excludeCategoryArray	=	array('358','295','282','246','247','248','372','97','336','406','407','408','409','414');			// Array of categories whom you want to exclude
	$todayDate				=	date('Y-m-d', time());
	
	foreach($tree as $item) { 
		//echo '<pre>'; print_r($item); 
		$categoryID	=	$item['category_id'];
		//echo '<br>Category Name->'.$item['name'];
		$allowed = checkAllowedCategory($categoryID);
		if($allowed){
			$categoryURL	=	$siteURL.$item['url_path'];
			$categoryLevel	=	$item['level'];
			//echo '<br>Category URL->'.$categoryURL;
			
			//	Rule for priority and changefreq
			$changefreq	=	'weekly';
			if($categoryLevel==1){		//	Main Category
				$changefreq	=	'daily';
				$priority	=	'0.9';
			}elseif($categoryLevel==2){
				$priority	=	'0.9';
			}elseif($categoryLevel==3){
				$priority	=	'0.85';
			}elseif($categoryLevel==4){
				$priority	=	'0.85';
			}else{
				$priority	=	'0.5';
			}
			
			//	Getting no of products in Category
			$productCount	=	Mage::getModel('catalog/category')->load($categoryID)->getProductCount();
			
			if(!in_array($categoryID,$excludeCategoryArray)){
				//	Getting data to write in xml file
				if($productCount!=0){
					$xmlOutput.=	'<url>';
						$xmlOutput.=	'<loc>'.$categoryURL.'</loc>';
						$xmlOutput.=	'<lastmod>'.$todayDate.'</lastmod>';
						$xmlOutput.=	'<changefreq>'.$changefreq.'</changefreq>';
						$xmlOutput.=	'<priority>'.$priority.'</priority>';
					$xmlOutput.=	'</url>';
				}
		
				str_repeat("    ", $level);
				$xmlOutput.= 	print_tree($item['children'],$level); 
				//	Getting data to write in xml file
			}
		}
	} 
	return $xmlOutput;
}

function checkAllowedCategory($categoryID){
	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	$write->beginTransaction();
	$tableName	=	Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_text');
	
	$query = "SELECT value FROM ". $tableName ." WHERE entity_id='".$categoryID."' AND attribute_id='55'";
	//echo $query;die;
	$results = $read->fetchRow($query);
	//echo '<pre>';print_r($results);die;
	$value = $results['value'];
	
	if($value != '' && stristr($value, 'NOINDEX,NOFOLLOW') !== false){
		return false;
	}
	return true;
}

//	Getting data to write in xml file
$xmlOutput	=	'<?xml version="1.0" encoding="UTF-8"?>';
$xmlOutput.=	'<urlset ';
$xmlOutput.=	'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
$xmlOutput.=	'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
$xmlOutput.=	'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
$xmlOutput.=	'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
//	Getting data to write in xml file
	
$tree = load_tree(); 
$xmlOutput .= print_tree($tree['children'],0);
$xmlOutput .= '</urlset>';
$siteURL = Mage::getBaseUrl();
//	Writing data to xml file
$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/';
$fileName	=	'sitemap.xml';
$filePath	=	$directoryPath.$fileName;
$file = fopen($filePath,"w");
if(fwrite($file,$xmlOutput)){	
	echo '<a href="'.$siteURL.$fileName.'"> Category Sitemap</a> generated successfully.';
	fclose($file); 
}else{
	echo $filePath .' is not writable.';
} ?>