<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);


$categories = Mage::getModel('catalog/category')
							->getCollection()
							->addAttributeToSelect('*');
//echo '<pre>';print_r($categories->getData());die;
if( $categories->count() > 1){
	foreach($categories as $cat){
		$category 		= 	Mage::getModel('catalog/category')->load($cat->getId());
		$categoryUrl	=  	Mage::getBaseUrl().$category->getUrlPath();
		if($cat->getId()!='1' && $cat->getId()!='2'){
			echo $cat->getName(). ' ---- '.$cat->getId().' --- <a href="'.$categoryUrl.'">'.$categoryUrl .'</a><br>';
		}
	}
}
	
