<?php
class hp_hprcv_Block_Hprcv extends Mage_Core_Block_Template
{
	public function getimageforproduct($id,$mt_hp,$stone_count_hp){
		$root = $_SERVER['DOCUMENT_ROOT'];
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($id);
		$sku = $_product->getSku();
		$default_stonecount = 0;
		$default_metal = $_product->getAttributeText('metal');
		
		foreach ($_product->getOptions() as $o){
			if($o->getTitle()=="Number of stones"){
				$values = $o->getValues();
			 	foreach ($values as $v){
					if($v->getPrice() == "0.0000"){
						$default_stonecount = $v->getTitle();
				 	}
				}
			}			 
		}
		
		if($stone_count_hp != ''){
			$default_stonecount = $stone_count_hp;
		}
		
		$mtcode = '';
		if(strrpos($default_metal,'Silver') > -1){
			$mtcode = 'WG';
		}
		else if(strrpos($default_metal,'Yellow Gold') > -1){
			$mtcode = 'YG';
		}
		else if(strrpos($default_metal,'White Gold') > -1){
			$mtcode = 'WG';
		}
		else if(strrpos($default_metal,'Rose Gold') > -1){
			$mtcode = 'RG';
		}
		else if(strrpos($default_metal,'Platinum') > -1){
			$mtcode = 'WG';
		}
		
		$arr = array();
		$ic = 0;
		foreach ($_product->getOptions() as $o){
			$ic = $o->getTitle();
			$values = $o->getValues();
			$arr[$ic] = array();
			 foreach ($values as $v){
				 $arr[$ic][count($arr[$ic])] = $v->getTitle();
				 $arr[$ic][count($arr[$ic])] = $v->getPrice();
				 $arr[$ic][count($arr[$ic])] = $v->getSortOrder();
			}	 
		}
		
		$imagearray = array();
		$baseurl = $root . "/media/catalog/product/images/mothers/" . $sku . "/" . $default_stonecount . "/";
		
		$arrstones = array();
		for($i=1;$i<=$default_stonecount;$i++){
			$stonetitle = 'GN-Stone' . $i;
			for($j=0;$j<count($arr[$stonetitle]);$j=$j+3){
				if($arr[$stonetitle][$j + 2] == "0"){
					$arrstones[$i] = $this->getstonecode($arr[$stonetitle][$j]);
					$imagearray[count($imagearray)] = $baseurl . $sku . "_" . $arrstones[$i] . "_" . $i . ".png";
					break;
				}
			}
		}
		
		if($mt_hp != ''){
			$imagearray[count($imagearray)] = $baseurl . $sku . "_" . $mt_hp . ".png";
		}
		else{
			$imagearray[count($imagearray)] = $baseurl . $sku . "_" . $mtcode . ".png";
		}
		$coords = array();
		
		for($i=0;$i<count($arr['Coordinates']);$i = $i + 3){			
			if(stripos($arr['Coordinates'][$i],$default_stonecount . '|') > -1 and stripos($arr['Coordinates'][$i],$default_stonecount . '|') == 0){
				$coords =explode("|",  $arr['Coordinates'][$i]);
			}
		}
		
		$dest = imagecreatefrompng($root . "/media/catalog/product/images/mothers/blank.png");
		for($i=0;$i<=$default_stonecount;$i++){
			$src = imagecreatefrompng($imagearray[$i]);
			$x1 = imagesx($dest);
			$y1 = imagesy($dest);
			$x2 = imagesx($src);
			$y2 = imagesy($src);
			imagealphablending($dest, true);
			imagesavealpha($dest, true);
			imagealphablending($src, true);
			imagesavealpha($src, true);
			$arrcoords = array();
			if($i == $default_stonecount){
				$arrcoords[0] = "0";
				$arrcoords[1] = "0";
			}
			else{
				$arrcoords = explode(",",$coords[$i + 1]);
			}
			imagecopyresampled(
				$dest, $src,
				$arrcoords[0], $arrcoords[1], 0, 0,
				$x2, $y2,
				$x2, $y2);
			
			imagedestroy($src);
		}
		
		if($mt_hp == '' && $stone_count_hp ==''){
			imagepng($dest, $root . "/media/catalog/product/images/mothers/" . $sku . ".png", 9);
			$img = $root . "/media/catalog/product/images/mothers/" . $sku . ".png"; // File image location
			$newfilename = $root . "/media/catalog/product/images/mothers/" . $sku . ".png"; // New file name for thumb
			$w = 350; //150;
			$h = 350; //150;
			 
			$thumbnail = $this->resize($img, $w, $h, $newfilename);	
						
			/*$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$result = $db->query("SELECT * FROM `catalog_product_entity_media_gallery` where attribute_id='77' and entity_id='" . $_product->getId() . "'");
			if($result)
			{
				$rows = $result->fetch(PDO::FETCH_ASSOC);
				if(!$rows) 
				{
					$db->query("insert into `catalog_product_entity_media_gallery` set attribute_id='77' , entity_id='" . $_product->getId() . "', value =  '" . "images/mothers/" . $sku . ".png". "'");
					$db->query("insert into `catalog_product_entity_varchar` set entity_type_id = '4', attribute_id='74' ,store_id='0', entity_id='" . $_product->getId() . "', value =  '" . "images/mothers/" . $sku . ".png". "'");
					$db->query("insert into `catalog_product_entity_varchar` set entity_type_id = '4', attribute_id='75' ,store_id='0', entity_id='" . $_product->getId() . "', value =  '" . "images/mothers/" . $sku . ".png". "'");
					$db->query("insert into `catalog_product_entity_varchar` set entity_type_id = '4', attribute_id='76' ,store_id='0', entity_id='" . $_product->getId() . "', value =  '" . "images/mothers/" . $sku . ".png". "'");
				}
			}*/
		}
		else if($mt_hp != '' && $stone_count_hp == ''){
			imagepng($dest, $root . "/media/catalog/product/images/mothers/" . $sku . "_" . $mt_hp . ".png", 9);
		}
		else if($mt_hp != '' && $stone_count_hp != ''){
			imagepng($dest, $root . "/media/catalog/product/images/mothers/" . $sku . "_" . $mt_hp . "_" . $stone_count_hp . ".png", 9);
		}
		else if($mt_hp == '' && $stone_count_hp != ''){
			imagepng($dest, $root . "/media/catalog/product/images/mothers/" . $sku . "_" . $stone_count_hp . ".png", 9);
		}
		
		imagedestroy($dest);
	}
	
	public function getimageforcartproduct($_item,$_options){
		$root = $_SERVER['DOCUMENT_ROOT'];
		$quoteid = $_item->getQuoteId();
		$itemid = $_item->getId();
		//var_dump($_item->getOptions());
		$arrO = array();
		
		foreach ($_options as $_option){
			$arrO[$_option['label']] = $_option['value'];
		}
		
		$gnlc = $arrO['GenOrLC'];
		if($gnlc == "Genuine"){
			$gnlc = "GN";
		}
		else{
			$gnlc = "LC";
		}
		
		$default_metal = $arrO['Metal Type'];
		$mtcode = '';
		if(strrpos($default_metal,'Silver') > -1){
			$mtcode = 'WG';
		}
		else if(strrpos($default_metal,'Yellow Gold') > -1){
			$mtcode = 'YG';
		}		
		else if(strrpos($default_metal,'White Gold') > -1){
			$mtcode = 'WG';
		}		
		else if(strrpos($default_metal,'Rose Gold') > -1){
			$mtcode = 'RG';
		}
		else if(strrpos($default_metal,'Platinum') > -1){
			$mtcode = 'WG';
		}
		else if(strrpos($default_metal,'10k Yellow Gold') > -1)
		{
			$mtcode = 'YG';
		}
		else if(strrpos($default_metal,'10k White Gold') > -1)
		{
			$mtcode = 'WG';
		}
		
		$default_stonecount = $arrO['Number of stones'];
		
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($_item->getProductId());
		$sku = $_product->getSku();		
		
		$arr = array();
		$ic = 0;
		foreach ($_product->getOptions() as $o){
			$ic = $o->getTitle();
			$values = $o->getValues();
			$arr[$ic] = array();
			 foreach ($values as $v) {
				 $arr[$ic][count($arr[$ic])] = $v->getTitle();
				 $arr[$ic][count($arr[$ic])] = $v->getPrice();
			}	 
		}
		
		$imagearray = array();
		$baseurl = $root . "/media/catalog/product/images/mothers/" . $sku . "/" . $default_stonecount . "/";
		for($i=1;$i<=$default_stonecount;$i++){
			$stonetitle = $gnlc . '-Stone' . $i;
			$imagearray[count($imagearray)] = $baseurl . $sku . "_" . $this->getstonecode($arrO[$stonetitle]) . "_" . $i . ".png";
		}
		
		$imagearray[count($imagearray)] = $baseurl . $sku . "_" . $mtcode . ".png";
		$coords = array();
		for($i=0;$i<count($arr['Coordinates']);$i = $i + 2){			
			if(stripos($arr['Coordinates'][$i],$default_stonecount . '|') > -1 and stripos($arr['Coordinates'][$i],$default_stonecount . '|') == 0){
				$coords =explode("|",  $arr['Coordinates'][$i]);
			}
		}
		
		$dest = imagecreatefrompng($root . "/media/catalog/product/images/mothers/blank.png");
		for($i=0;$i<=$default_stonecount;$i++){
			$src = imagecreatefrompng($imagearray[$i]);
			$x1 = imagesx($dest);
			$y1 = imagesy($dest);
			$x2 = imagesx($src);
			$y2 = imagesy($src);
			imagealphablending($dest, true);
			imagesavealpha($dest, true);
			imagealphablending($src, true);
			imagesavealpha($src, true);
			$arrcoords = array();
			if($i == $default_stonecount){
				$arrcoords[0] = "0";
				$arrcoords[1] = "0";
			}
			else{
				$arrcoords = explode(",",$coords[$i + 1]);
			}
			
			imagecopyresampled(
				$dest, $src,
				$arrcoords[0], $arrcoords[1], 0, 0,
				$x2, $y2,
				$x2, $y2);			
			
			imagedestroy($src);
		}
		
		imagepng($dest, $root . "/media/catalog/product/images/mothers/cartproducts/" . $itemid . ".png", 9);
		exec('chmod 777 ' . $root . "/media/catalog/product/images/mothers/cartproducts/" . $itemid . ".png");
		$img = $root . "/media/catalog/product/images/mothers/cartproducts/" . $itemid . ".png"; // File image location
		$newfilename = $root . "/media/catalog/product/images/mothers/cartproducts/" . $itemid . "_thumb.png"; // New file name for thumb
		$w = 75;
		$h = 75;
		 
		$thumbnail = $this->resize($img, $w, $h, $newfilename);
		imagedestroy($dest);
		
	}
	
	public function getstonecode($val){
		if($val == 'Alexandrite'){
			return 'AL';
		}
		else if($val == 'Amethyst'){
			return 'AM';
		}
		else if($val == 'Aquamarine'){
			return 'AQ';
		}
		else if($val == 'Citrine'){
			return 'CI';
		}
		else if($val == 'Diamond'){
			return 'DI';
		}
		else if($val == 'Emerald'){
			return 'EM';
		}
		else if($val == 'Garnet'){
			return 'GA';
		}
		else if($val == 'Peridot'){
			return 'PE';
		}
		else if($val == 'Pink Tourmaline'){
			return 'PT';
		}
		else if($val == 'Ruby'){
			return 'RU';
		}
		else if($val == 'Blue Sapphire'){
			return 'SA';
		}
		else if($val == 'Tanzanite'){
			return 'TA';
		}
		else if($val == 'Lemon Quartz'){
			return 'LQ';
		}
		else if($val == 'Pink Amethyst'){
			return 'AMP';
		}
		else if($val == 'Smoky Quartz'){
			return 'SQ';
		}
		else if($val == 'Green Amethyst'){
			return 'AMG';	
		}
		else if($val == 'Chrysopherous'){
			return 'CH';	
		}
		else if($val == 'Carnelian'){
			return 'CR';	
		}
		else if($val == 'Blue Topaz'){
			return 'BT';	
		}
		else if($val == 'Rose Quartz'){
			return 'RQ';	
		}	
		else if($val == 'Turquoise'){
			return 'TQ';	
		}
		else if($val == 'Lapis Lazuli'){
			return 'LL';	
		}
	}
	
	public function resize($img, $w, $h, $newfilename) {
 
		//Check if GD extension is loaded
	 	if (!extension_loaded('gd') && !extension_loaded('gd2')) {
	  		trigger_error("GD is not loaded", E_USER_WARNING);
	  	return false;
	 	}
	 
	 	//Get Image size info
	 	$imgInfo = getimagesize($img);
	 	switch ($imgInfo[2]) {
	  		case 1: $im = imagecreatefromgif($img); break;
	  		case 2: $im = imagecreatefromjpeg($img);  break;
	  		case 3: $im = imagecreatefrompng($img); break;
	  		default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
	 	}
	 
	 	//If image dimension is smaller, do not resize
	 	if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
	  		$nHeight = $imgInfo[1];
	  		$nWidth = $imgInfo[0];
	 	}
		else{
			//yeah, resize it, but keep it proportional
	  		if ($w/$imgInfo[0] > $h/$imgInfo[1]) {
	   			$nWidth = $w;
	   			$nHeight = $imgInfo[1]*($w/$imgInfo[0]);
	  		}
			else{
	   			$nWidth = $imgInfo[0]*($h/$imgInfo[1]);
	   			$nHeight = $h;
	  		}
	 	}
		
		$nWidth = round($nWidth);
		$nHeight = round($nHeight);
	 
	 	$newImg = imagecreatetruecolor($nWidth, $nHeight);
	 
	 	/* Check if this image is PNG or GIF, then set if Transparent*/  
	 	if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
	  		imagealphablending($newImg, false);
	  		imagesavealpha($newImg,true);
	  		$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
	  		imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
	 	}
		
	 	imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
	 
	 	//Generate the file, and rename it to $newfilename
	 	switch ($imgInfo[2]) {
	  		case 1: imagegif($newImg,$newfilename); break;
	  		case 2: imagejpeg($newImg,$newfilename);  break;
	  		case 3: imagepng($newImg,$newfilename); break;
	  		default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
	 	}
	return $newfilename;
	}	
	
	//CUSTOMJ RELATED CODE STARTS FROM HERE.
	public function getrootpath(){
		return  "/media/catalog/product/images/customj/";
	}
	
	public function getOtImage($sku){
		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($_product->getImagesku()!=''){
			$sku = $_product->getImagesku();
		}
		$root = $_SERVER['DOCUMENT_ROOT'];
		$root = $root . $this->getrootpath() . $sku . "/angle_other/";
		$arr = array();
		if ($handle = opendir($root)){
			while (false !== ($entry = readdir($handle))){
				if ($entry != "." && $entry != ".."){
					$arr[] = $entry;
				}
			}
			closedir($handle);
		}
		return $arr;
	}
	
	public function getCountCustomImages($sku){
		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		
		if($_product->getImagesku()!=''){
			$sku = $_product->getImagesku();
		}
		$root = $_SERVER['DOCUMENT_ROOT'];
		$root = $root . $this->getrootpath() . $sku;
		$arr = array();
		if ($handle = opendir($root)){
			while (false !== ($entry = readdir($handle))){
				if ($entry != "." && $entry != ".."){
					$arr[] = $entry;
				}
			}
			closedir($handle);
		}
		// start modification for PinIt - Pankaj Jalora
		/*$i=1;
		$cnt = 0;
		while($i){
			$i=0;
			for($j=0;$j<count($arr);$j++){
				if($arr[$j]== 'angle_' . ($cnt + 1)){
					$cnt++;
					$i = 1;
				}
			}			
		}
		return $cnt;*/
		// end modification for PinIt - Pankaj Jalora
	return $arr;
	}
	
	public function getDefaultGrade($_product){
		foreach ($_product->getOptions() as $o){
			if($o->getTitle() == 'Stone Quality'){
				$values = $o->getValues();
                foreach ($values as $v){
					if($v->getSortOrder() == "0"){
						$title = $v->getTitle();
						$arr = explode("|",$title);
						return $arr[0];
					}
				}
			}
		}
	return '';
	}
	
	public function getCoords($_product,$angle){
		foreach ($_product->getOptions() as $o){
			if($o->getTitle() == 'coordinates'){
				$values = $o->getValues();
                foreach ($values as $v){
					$title = $v->getTitle();
					$arr = explode("|",$title);
					if($angle == $arr[0])
					return $arr;
				}
			}
		}
	}
	
	public function returnimage($sku,$shortDiscription,$metaltype,$grade,$angle,$stonetype,$_product,$forceReplace = 0){
		if($_product->getImagesku()!=''){
			$sku = $_product->getImagesku();
		}		
		
		$shortDiscription = str_replace(" ", "-", $shortDiscription);
		$path = $this->getrootpath() . $sku . "/" . $angle . "/" . $shortDiscription . "-" . $sku;
		if($metaltype){
		 	$path = $path . "-" . $this->metalTextShort($metaltype);
		}
		
		if($grade){
			$path = $path . "-" . $grade;
		}
		
		if($stonetype){
			$path = $path . "-" . $this->stoneTextShort($stonetype);
		}
		
		$path = $path  . ".png";
		$strpath = $_SERVER['DOCUMENT_ROOT'] . $path;
		$strpath = str_replace("//", "/", $strpath);
		if(!file_exists($strpath) || (Mage::isOverwriteCustomImages() || $forceReplace)){		
			// Code	Added by Vaseem for image Zoom	588
			$zoomEnable	=	Mage::helper('function')->imageZoomSku($sku);
			//echo 'sku->'.$sku;			echo 'zoomEnable->'.$zoomEnable; die;
			if($zoomEnable==1){
				$dest = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . $this->getrootpath() . "blank_zoom.png");
			}
			else{	
				$dest = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . $this->getrootpath() . "blank.png");
			}
			// Code	Added by Vaseem for image Zoom	588
			if($grade){
				// this does mean that the jewelary has a stone or more
				$coords = $this->getCoords($_product,$angle);
				for($i=1;$i<count($coords);$i++){
					$strsrc = $_SERVER['DOCUMENT_ROOT'] . $this->getrootpath() . $sku . "/" . $angle . "/" . $sku . "_" . $this->stoneTextShort($stonetype) . "_" . $grade . "_" . $i . ".png";
					$strsrc = str_replace("//", "/", $strsrc);
					$src = imagecreatefrompng($strsrc);
					$x1 = imagesx($dest);
					$y1 = imagesy($dest);
					$x2 = imagesx($src);
					$y2 = imagesy($src);
					imagealphablending($dest, true);
					imagesavealpha($dest, true);
					imagealphablending($src, true);
					imagesavealpha($src, true);
					$arrcoords = array();
					$arrcoords = explode(",",$coords[$i]);
					imagecopyresampled(
						$dest, $src,
						$arrcoords[0], $arrcoords[1], 0, 0,
						$x2, $y2,
						$x2, $y2);					
					
					imagedestroy($src);
				}
			}
			
			if($metaltype){
				// this does mean that the jewelary has metal
				$strsrc = $_SERVER['DOCUMENT_ROOT'] . $this->getrootpath() . $sku . "/" . $angle . "/" . $sku . "_" . $this->metalTextShortforImage($metaltype) . ".png";
				$strsrc = str_replace("//", "/", $strsrc);
				$src = imagecreatefrompng($strsrc);
				$x1 = imagesx($dest);
				$y1 = imagesy($dest);
				$x2 = imagesx($src);
				$y2 = imagesy($src);
				imagealphablending($dest, true);
				imagesavealpha($dest, true);
				imagealphablending($src, true);
				imagesavealpha($src, true);
				imagecopyresampled(
					$dest, $src,
					0, 0, 0, 0,
					$x2, $y2,
					$x2, $y2);				
				
				imagedestroy($src);
			}
			
			imagepng($dest, $strpath, 9);
			//exec('chmod 777 ' . $strpath); // commented by anil jain
			imagedestroy($dest);
		}
		return "http://" .  $_SERVER['HTTP_HOST'] . $path;
	}
	
	public function ifcustomj($id){
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($id);
		return $_product->getCustomj();
	}
		
	public function changeDefaultImage($_product,$url){		
		$arr = explode('/media/catalog/product/',$url);
		$url = $arr[1];
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$main_sql = "SELECT * FROM `catalog_product_entity_media_gallery` where attribute_id='77' and entity_id='".$_product->getId()."'";
		$result1 = $db->query($main_sql);
		$rows_dt1 = $result1->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_dt1)>0){
			$sql1 = "update catalog_product_entity_media_gallery set value='" . $url . "' where attribute_id='77' and entity_id='" . $_product->getId() . "'";
			$rs1 = $db->query($sql1);
		}
		else{
			$sql_ins1 = "INSERT INTO catalog_product_entity_media_gallery (value_id,attribute_id,entity_id,value) values (NULL,'77','".$_product->getId()."', '".$url."')";
			$rs1 = $db->query($sql_ins1);
		}
		
		$rel_sql2 = "SELECT * FROM `catalog_product_entity_varchar` where store_id='0' and entity_type_id='4' and attribute_id='74' and entity_id='".$_product->getId()."'";
		$result2 = $db->query($rel_sql2);
		$rows_dt2 = $result2->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_dt2)>0){
			$sql2 = "update catalog_product_entity_varchar set value='".$url."' where store_id='0' and entity_type_id='4' and attribute_id='74' 
			and entity_id='".$_product->getId()."'";
			$rs2 = $db->query($sql2);
		}
		else{
			$sql_ins2="INSERT INTO catalog_product_entity_varchar (store_id,entity_type_id,attribute_id,entity_id,value) values (0,'4','74','".$_product->getId()."', '".$url."')";
			$rs2 = $db->query($sql_ins2);
		}
		
		$rel_sql3 = "SELECT * FROM `catalog_product_entity_varchar` where store_id='0' and entity_type_id='4' and attribute_id='75' and entity_id='".$_product->getId()."'";
		$result3 = $db->query($rel_sql3);
		$rows_dt3 = $result3->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_dt3)>0){
			$sql3 = "update catalog_product_entity_varchar set value='".$url."' where store_id='0' and entity_type_id='4' and attribute_id='75' 
			and entity_id='".$_product->getId()."'";
			$rs3 = $db->query($sql3);
		}
		else{
			$sql_ins3="INSERT INTO catalog_product_entity_varchar (store_id,entity_type_id,attribute_id,entity_id,value) values (0,'4','75','".$_product->getId()."', '".$url."')";
			$rs3 = $db->query($sql_ins3);
		}
		
		$rel_sql4 = "SELECT * FROM `catalog_product_entity_varchar` where store_id='0' and entity_type_id='4' and attribute_id='76' and entity_id='".$_product->getId()."'";
		$result4 = $db->query($rel_sql4);
		$rows_dt4 = $result4->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_dt4)>0){
			$sql4 = "update catalog_product_entity_varchar set value='".$url."' where store_id='0' and entity_type_id='4' and attribute_id='76' 
			and entity_id='".$_product->getId()."'";
			$rs4 = $db->query($sql4);
		}
		else{
			$sql_ins4="INSERT INTO catalog_product_entity_varchar (store_id,entity_type_id,attribute_id,entity_id,value) values (0,'4','76','".$_product->getId()."', '".$url."')";
			$rs4 = $db->query($sql_ins4);
		}
		/*
		echo '<pre>';print_r($rows_dt);echo '</pre>';exit;
		if($result)
		{			
			echo '<br>In 111';
			$rows = $result->fetch(PDO::FETCH_ASSOC);
			if($rows) 
			{
				echo '<br>In 222';
				$sql1 = "update catalog_product_entity_media_gallery set value='" . $url . "' where attribute_id='77' and entity_id='" . $_product->getId() . "'";
				$rs1 = $db->query($sql1);
				
				$sql2 = "update catalog_product_entity_varchar set value='" . $url . "' where store_id='0' and entity_type_id = '4' and attribute_id='74' and entity_id='" . $_product->getId() . "'";
				$rs2 = $db->query($sql2);
				
				$sql3 = "update catalog_product_entity_varchar set value='" . $url . "' where store_id='0' and entity_type_id = '4' and attribute_id='75' and entity_id='" . $_product->getId() . "'";
				$rs3 = $db->query($sql3);
				
				$sql4 = "update catalog_product_entity_varchar set value='" . $url . "' where store_id='0' and entity_type_id = '4' and attribute_id='76' and entity_id='" . $_product->getId() . "'";
				$rs4 = $db->query($sql4);
				if(!$rs1 || !$rs2 || !$rs3 || !$rs4){
					//echo $sql1.'<br><br>'.$sql2.'<br>'.$sql3.'<br>'.$sql4;
					echo '<br>updation unsuccess sku: '.$_product->getSku();
				}				
			}else{				
				echo '<br>rows are null unsuccess sku: '.$_product->getSku();
				$sql_ins1 = "INSERT INTO catalog_product_entity_media_gallery (value_id,attribute_id,entity_id,value) values (NULL,'77','".$_product->getId()."', '".$url."')";
				$rs1 = $db->query($sql_ins1);
			}
		}else{
			echo '<br>result is null unsuccess sku: '.$_product->getSku();
		}
		*/
	}
	
	public function writeAllImageCombinations($_product){
		$arr_SS = $this->getStoneSizesOfProduct($_product);
		$arr_GD =  $this->getGradesOfProduct($_product);
		$arr_MT =  $this->getMetalsOfProduct($_product);
		$shortDiscription = $_product->getShortDescription();
		$shortDiscription = str_replace(" ", "-", $shortDiscription);
		$stonetype = $_product->getAttributeText('de_stone_type');
		$sku = $_product->getSku();
		$angleCount = 4;//$this->getCountCustomImages($sku);
		$mainroot = $_SERVER['DOCUMENT_ROOT'];
		for($i=1;$i<=$angleCount;$i++){
			$angle = 'angle_' . $i;
			//$path = $this->getrootpath() . $sku . "/" . $angle . "/" . $shortDiscription . "-" . $sku;
			$gdtext = '';
			$mttext = '';
			for($imt=-1;$imt<count($arr_MT);$imt++){
				if(!is_dir($mainroot.$this->getrootpath().$sku.'/'.$angle)){
					continue;	
				}
				
				$cont = 0;
				
				if(count($arr_MT)==0 && $imt == -1){
					$cont = 1;
				}
				else if($imt > -1){
					$cont = 1;
					$mttext = $arr_MT[$imt];
				}
				if($cont){
					for($igd = -1;$igd<count($arr_GD);$igd++){
						$cont = 0;
						if(count($arr_GD)==0 && $igd == -1){
							$cont = 1;
						}
						else if($igd > -1){
							$cont = 1;
							$gdtext = $arr_GD[$igd];
						}
						if($cont){
							$imgaddr = $this->returnimage($sku,$shortDiscription,$mttext,$gdtext,$angle,$stonetype,$_product,1);
							
							$defaultGrade = $this->getDefaultGrade($_product);
							$defaultMetal = $_product->getAttributeText('metal_type');
							//echo '<br><br>'.$defaultGrade.' == '.$gdtext.' && '.$mttext.' == '.$defaultMetal.' && '.$angle;
							if($defaultGrade == $gdtext && $mttext == $defaultMetal && $angle == "angle_1"){								
								$this->changeDefaultImage($_product,$imgaddr);
							}
							echo "<div style='float:left;width:360px'><div><img src='" . $imgaddr . "'/></div><div>" . $imgaddr . "</div></div>";
						}
					}
				}
			}			
		}
	}
	
	// start modification for PinIt bug id 447 - Anil Jain
	public function fetchAllCustomJImageCombinations($_product){
		$customjImgArr = array();
		$arr_SS = $this->getStoneSizesOfProduct($_product);
		$arr_GD =  $this->getGradesOfProduct($_product);
		$arr_MT =  $this->getMetalsOfProduct($_product);
		
		$shortDiscription = $_product->getShortDescription();
		$shortDiscription = str_replace(" ", "-", $shortDiscription);
		$stonetype = $_product->getAttributeText('de_stone_type');
		
		$sku = $_product->getSku();
		
		// start modification for PinIt - Pankaj Jalora
		$angleArr = $this->getCountCustomImages($sku);		
		foreach($angleArr as $angle){
			if(in_array($angle, $angleArr)){
				//$path = $this->getrootpath() . $sku . "/" . $angle . "/" . $shortDiscription . "-" . $sku;
				$gdtext = '';
				$mttext = '';
				for($imt=-1;$imt<count($arr_MT);$imt++){
					$cont = 0;					
					if(count($arr_MT)==0 && $imt == -1){
						$cont = 1;
					}
					else if($imt > -1){
						$cont = 1;
						$mttext = $arr_MT[$imt];
					}
					
					if($cont){
						for($igd = -1;$igd<count($arr_GD);$igd++){
							$cont = 0;
							if(count($arr_GD)==0 && $igd == -1){
								$cont = 1;
							}
							else if($igd > -1){
								$cont = 1;
								$gdtext = $arr_GD[$igd];
							}
							if($cont){
								$imgaddr = $this->returnimage($sku,$shortDiscription,$mttext,$gdtext,$angle,$stonetype,$_product,1);
								$customjImgArr[] = $imgaddr;						
							}
						}
					}
				}		
			}				
		}
		// end modification for PinIt bug id 447 - Pankaj Jalora
	return $customjImgArr;
	}
	// end modification for PinIt - Anil Jain
	
	public function getGradesOfProduct($_product){
		$arr = array();
		foreach ($_product->getOptions() as $o){
			if($o->getTitle() == 'Stone Quality'){
				$values = $o->getValues();
                foreach ($values as $v){
					$title = $v->getTitle();
					$arr1 = explode("|",$title);
					$isf = 0;
					for($i=0;$i<count($arr);$i++){
						if($arr[$i] == $arr1[0]){
							$isf = 1;
						}
					}
					if($isf==0){
						$arr[count($arr)] = $arr1[0];
					}
				}
			}
		}
		return $arr;
	}
	
	public function getMetalsOfProduct($_product){
		$arr = array();
		foreach ($_product->getOptions() as $o){
			if($o->getTitle() == 'Metal Type'){
				$values = $o->getValues();
                foreach ($values as $v){
					$title = $v->getTitle();
					$arr1 = explode("|",$title);
					$isf = 0;
					for($i=0;$i<count($arr);$i++){
						if($arr[$i] == $arr1[0]){
							$isf = 1;
						}
					}
					if($isf==0){
						$arr[count($arr)] = $arr1[0];
					}
				}
			}
		}
		return $arr;
	}
	
	public function getStoneSizesOfProduct($_product){
		$arr = array();
		foreach ($_product->getOptions() as $o){
			if($o->getTitle() == 'Stone Size'){
				$values = $o->getValues();
                foreach ($values as $v){
					$title = $v->getTitle();
					$arr[count($arr)] = $title;
				}
			}
		}
		return $arr;
	}
	
	public function metalTextShort($text){
		$metalTextShort = array();
		$metalTextShort['Silver'] = 'SL';
		$metalTextShort['Platinum'] = 'PT';
		$metalTextShort['White Gold'] = 'WG';
		$metalTextShort['Yellow Gold'] = 'YG';
		$metalTextShort['Rose Gold'] = 'RG';
		return $metalTextShort[$text];
	}
	
	public function metalTextShortforImage($text){
		$metalTextShortforImage = array();
		$metalTextShortforImage['Silver'] = 'WG';
		$metalTextShortforImage['Platinum'] = 'WG';
		$metalTextShortforImage['White Gold'] = 'WG';
		$metalTextShortforImage['Yellow Gold'] = 'YG';
		$metalTextShortforImage['Rose Gold'] = 'RG';
		return $metalTextShortforImage[$text];
	}
	
	public function stoneTextShort($text){
		$stoneTextShort = array();
		$stoneTextShort['Blue Sapphire'] = 'SA';
		$stoneTextShort['Emerald'] = 'EM';
		$stoneTextShort['Ruby'] = 'RU';
		$stoneTextShort['Tanzanite'] = 'TA';
		$stoneTextShort['Aquamarine'] = 'AQ';
		$stoneTextShort['Citrine'] = 'CI';
		$stoneTextShort['Diamond'] = 'DI';
		$stoneTextShort['Color Diamond'] = 'NCD';
		$stoneTextShort['Amethyst'] = 'AM';
		$stoneTextShort['Pink Tourmaline'] = 'PT';
		$stoneTextShort['Pink Sapphire'] = 'PS';
		$stoneTextShort['Peridot'] = 'PE';
		$stoneTextShort['Yellow Sapphire'] = 'YS';
		$stoneTextShort['Garnet'] = 'GA';
		$stoneTextShort['Simulated Diamond'] = 'DL';
		$stoneTextShort['Lab Created Ruby'] = 'RL';
		$stoneTextShort['Lab Created Sapphire'] = 'SL';
		$stoneTextShort['Lab Created Emerald'] = 'EL';
		$stoneTextShort['White Sapphire'] = 'WS';
		$stoneTextShort['Lab Created Tanzanite'] = 'TL';
		$stoneTextShort['Akoya Cultured Pearl'] = 'PAC';
		$stoneTextShort['Freshwater Pearl'] = 'PF';
		$stoneTextShort['Black Tahitian Cultured Pearl'] = 'PBC';
		$stoneTextShort['Gold South Sea Cultured Pearl'] = 'PGC';
		$stoneTextShort['Freshwater Cultured Pearl'] = 'PFC';
		$stoneTextShort['Tahitian Cultured Pearl'] = 'PTC';
		$stoneTextShort['White South Sea Cultured Pearl'] = 'PWC';
		$stoneTextShort['Swiss Blue Topaz'] = 'BTS';
		$stoneTextShort['Lemon Quartz'] = 'LQ';
		$stoneTextShort['Pink Amethyst'] = 'AMP';
		$stoneTextShort['Smoky Quartz'] = 'SQ';						
		$stoneTextShort['Opal'] = 'OP';
		$stoneTextShort['Green Amethyst'] = 'AMG';	
		$stoneTextShort['Chrysopherous'] = 'CH';
		$stoneTextShort['Carnelian'] = 'CR';
		$stoneTextShort['Blue Topaz'] = 'BT';		
		$stoneTextShort['Rose Quartz'] = 'RQ';	
		$stoneTextShort['Turquoise'] = 'TQ';	
		$stoneTextShort['Lapis Lazuli'] = 'LL';	
		
		return $stoneTextShort[$text];
	}
	
	public function getCartImageForCustomj($_item,$_options,$_product){
		$root = $_SERVER['DOCUMENT_ROOT'];
		$quoteid = $_item->getQuoteId();
		$itemid = $_item->getId();
		$arr = array();
		
		foreach ($_options as $_option){
			$arr[$_option['label']] = $_option['value'];
		}
		
		$gdtext = '';
		$mttext = '';
		if(isset($arr['Stone Quality'])){
			$gdtextarr = explode('|',$arr['Stone Quality']);
			$gdtext = $gdtextarr[0];
		}
		
		if(isset($arr['Metal Type'])){
			$mttextarr = explode('|',$arr['Metal Type']);
			$mttext = $mttextarr[0];
		}
		
		$imgaddr = $this->returnimage($_product->getSku(),$_product->getShortDescription(),$mttext,$gdtext,'angle_1',$_product->getAttributeText('de_stone_type'),$_product);
		//$imgaddr = 'http://www.angara.com/media/catalog/product/images/customj/SE0104S/angle_1/Round-Sapphire-Solitaire-Studs-SE0104S-YG-AAAA-SA.png';
		$url = $_SERVER['DOCUMENT_ROOT'] . $this->getrootpath() . "/cartimages/" . $itemid . ".png";
		$url = str_replace("//", "/", $url);
		//echo $url;exit;
		fopen($url, 'w');
		if(copy($imgaddr,$url)){
			//echo "yes";
		}
		else{
			//echo "no";
		}
		/*$ch = curl_init($imgaddr);
		$fp = fopen($url, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		echo "don"; exit;*/
	}
	
	public function pdfform($html){		//echo '<pre>'; print_r($_REQUEST); die;?>
		<html>
		<head>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>
			var editing = false;
			$(function(){
				$('a').click(function(e){
					e.stopPropagation();
					if(!$(this).hasClass('converted')){
						convertTxt2Input($(this));
					}
				})
				
				$('#editpdf').toggle(function(){
					editing = true;
					$('a').each(function(element) {
						if(!$(this).hasClass('converted')){
							convertTxt2Input($(this));
						}
					});
					
				},function(){
					editing = false;
					$('.editor').each(function(){
						var element = $(this);
						element.parent().html(element.val())
							.removeClass('converted');
					})
				})
				
				$('#pdfform').submit(function(){
					$('.editor').each(function(){
						var element = $(this);
						element.parent().html(element.val())
							.removeClass('converted');
					})
					$('#pdfhtml').val($('#pdfhtmldiv').html());
					//$('#pdfform').submit();
				})
			})
			
			function convertTxt2Input(txt){
				var input = $('<input class="editor" type="text">').val(txt.text());
				txt
					.html(input)
					.addClass('converted');
				
				input
					.focusout(function(){
						txt.html(input.val())
						.removeClass('converted');
					})
					
				$('body').click(function(){
					if(!editing){
						txt.html(input.val())
							.removeClass('converted');
					}
				})
			}
		</script>
		</head>
		<body>
		<div id="pdfhtmldiv">
			<?php echo $html; ?>
		
            <div style="display:none;" id="pagebutton">
                <form action="/hprcv/Index/backendpdfview" name="pdfform" id="pdfform" method="post">
                    <input id="pdfhtml" type="hidden" name="pdfhtml" />
                    <input id="editpdf" type="button" name="editpdf" value="Edit Certificate">
                    <input type="submit" name="generatepdf" value="Generate HTML" />
                    <input type="hidden" name="certificate_id" value="<?php echo $this->getRequest()->getParam('certificate_id');?>" />
                </form>
            </div>
		</div>
        </body>
		</html>
		<?php
	}
}
?>