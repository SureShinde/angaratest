<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes

try {
	$productData = Mage::getModel('catalog/product')
				->getCollection()
				->addAttributeToFilter('status',1) 			//	only enabled product
				->addAttributeToFilter('visibility', 4)		// catalog, search
				->addAttributeToFilter('short_description',array('neq'=>'no_selection'))
				->addAttributeToFilter('url_path',array('neq'=>''))
				->addAttributeToFilter('image', array('notnull' => '', 'neq' => 'no_selection'))
				//->load(1);die;
				;
	if (count($productData) > 0) {
		$xmlOutput	=	'<?xml version="1.0" encoding="UTF-8"?>';
		$xmlOutput.=	'<urlset ';
      	$xmlOutput.=	'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
      	$xmlOutput.=	'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	    $xmlOutput.=	'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
		$xmlOutput.=	'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	
		$siteURL		=	Mage::getBaseUrl();
		foreach ($productData as $product){
			$productID			=	$product['entity_id'];
			$image 				= 	Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
			//	Remove https from product images url
			//$image 				= 	'https://192.168.1.162/media/catalog/product/images/mothers/AMRW1085.png';
			/*if(stristr($image,'https')){
				$image 			= 	str_replace('https','http',$image);
			}*/
			$shortdescription	=	$product['short_description'];
			$productUrl			=	$siteURL.$product['url_path'];
			//	Remove sku with x.html from sitemap
			if(!stristr($productUrl,'x.html')){
				$xmlOutput.=	'<url>';
				$xmlOutput.=	'<loc>'.$productUrl.'</loc>';
				$xmlOutput.=	'<image:image>';
				$xmlOutput.=	'<image:loc>'.$image.'</image:loc>';
				$xmlOutput.=	'<image:title>'.rssFormat($shortdescription).'</image:title>';
				$xmlOutput.=	'</image:image>';
				
				//	Check child products of associated products
				/*if($product['type_id']=='configurable'){
					$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);   
					$noOfAssociateProducts	=	count($childProducts); 
					if($noOfAssociateProducts>0){
						foreach($childProducts as $child){
							$_child = Mage::getModel('catalog/product')->load($child->getId());
							$_childImage =  Mage::getModel('catalog/product_media_config')->getMediaUrl($_child->getImage()); 
							
							$xmlOutput.=	'<image:image>';
							$xmlOutput.=	'<image:loc>'.$_childImage.'</image:loc>';
							$xmlOutput.=	'<image:title>'.rssFormat($shortdescription).'</image:title>';
							$xmlOutput.=	'</image:image>';
						}
					}
				}*/
				
				//	Get media gallery images
				/*$mediaGalleryImages	=	getProductImages($productID);
				if(count($mediaGalleryImages)>0){
					//echo '<pre>'; print_r($mediaGalleryImages); die;
					foreach ($mediaGalleryImages as $mediaImage) {
						$mediaImage	=	Mage::getBaseUrl().'media/catalog/product/'.$mediaImage;
						$mediaImage	=	str_replace('product//','product/',$mediaImage);
						//	Removing duplicate images
						if($image!=$mediaImage){
							$xmlOutput.=	'<image:image>';
							$xmlOutput.=	'<image:loc>'.$mediaImage.'</image:loc>';
							$xmlOutput.=	'<image:title>'.rssFormat($shortdescription).'</image:title>';
							$xmlOutput.=	'</image:image>';
						}
					}
				}*/
				$xmlOutput.=	'</url>';
			}	//	end if
		} 
	} 
	$xmlOutput.=	'</urlset>';
	
	//	Writing data to xml file
	$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$fileName	=	'image-sitemap.xml';
	$filePath	=	$directoryPath.$fileName;
	$file = fopen($filePath,"w");
	if(fwrite($file,$xmlOutput)){	
		echo '<a href="'.$siteURL.$fileName.'">Image Sitemap</a> generated successfully.';
		fclose($file); 
	}else{
		echo $filePath .' is not writable.';
	}
	
} catch (Exception $e) {
    Mage::printException($e);
}

//	Function to produce output in proper rss compatible format
function rssFormat($string) 
{		
    $string = strip_tags($string);
	$string = str_replace('&', '&amp;', $string);
    return $string;
}

/*function getProductImages($productId){
	$galleryImages	=	array();
	$entityTypeId 	= 	Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();			//	4 for Product
	$product 		= 	Mage::getModel('catalog/product')->load($productId);
	$mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entityTypeId, 'media_gallery');
	$gallery 		= $product->getMediaGalleryImages();
	foreach ($gallery as $image){
		$galleryImages[]	=	$image->getFile();
	}
	return $galleryImages;	
}*/
?>