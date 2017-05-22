<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes


try {
	$cmsPageCollection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId());
	$cmsPageCollection->getSelect()->where('is_active = 1 AND identifier not like "more-about%" AND identifier not like "loose-gemstone%"');			//	Remove more-about Pages
	$cmsPageCollection->getSelect()->order('page_id DESC');
	//$cmsPageCollection->printLogQuery(true);die;
				
	if (count($cmsPageCollection) > 0) {
		$xmlOutput	=	'<?xml version="1.0" encoding="UTF-8"?>';
		$xmlOutput.=	'<urlset ';
      	$xmlOutput.=	'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
      	$xmlOutput.=	'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	    $xmlOutput.=	'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
		$xmlOutput.=	'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		
		$todayDate			=	date('Y-m-d', time());
		//	Adding home url
		$xmlOutput.=	'<url><loc>'.Mage::getBaseUrl().'</loc><lastmod>'.$todayDate.'</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>';
	
		$siteURL			=	Mage::getBaseUrl();
		$excludePagesArray	=	array('1','2','5','101','10','11','12','133','7','8','14','114','115','116','117','118','119','120','121','122','123','124','125','16','99');			// Array of cms pages whom you want to exclude
		
		foreach ($cmsPageCollection as $page){
			$PageData 	= 	$page->getData();
			$pageID	=	$PageData['page_id'];	
			$allowed = checkAllowedCmsPage($pageID);
			if($allowed){
				if(!in_array($pageID,$excludePagesArray)){
					//echo '<br>Page Title->'.$PageData['title'];
					$pageURL	=	$siteURL.$PageData['identifier'];	
					//	Remove existing Magento Pages
					//if(strstr($pageURL,'.html')){		
						$xmlOutput.=	'<url>';
							$xmlOutput.=	'<loc>'.$pageURL.'</loc>';
							$xmlOutput.=	'<lastmod>'.$todayDate.'</lastmod>';
							$xmlOutput.=	'<changefreq>monthly</changefreq>';
							$xmlOutput.=	'<priority>0.6</priority>';
						$xmlOutput.=	'</url>';
					//}
				}
			}	
		}
	} 
	$xmlOutput.=	'</urlset>';
	
	//	Writing data to xml file
	$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$fileName	=	'sitemap_cms_page.xml';
	$filePath	=	$directoryPath.$fileName;
	$file = fopen($filePath,"w");
	if(fwrite($file,$xmlOutput)){	
		echo '<a href="'.$siteURL.$fileName.'"> Cms Pages Sitemap</a> generated successfully.';
		fclose($file); 
	}else{
		echo $filePath .' is not writable.';
	}
	
} catch (Exception $e) {
    Mage::printException($e);
}

function checkAllowedCmsPage($pageID){
	$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	$write->beginTransaction();
	$tableName	=	Mage::getSingleton('core/resource')->getTableName('cms_page');
	
	$query = "SELECT layout_update_xml FROM ". $tableName ." WHERE page_id='".$pageID."'";
	//echo $query;die;
	$results = $read->fetchRow($query);
	//echo '<pre>';print_r($results);die;
	$value = $results['value'];
	
	if($value != '' && stristr($value, 'NOINDEX,NOFOLLOW') !== false){
		return false;
	}
	return true;
}?>