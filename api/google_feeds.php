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


if( isset($_POST['sku']) ){
	$sku     =       trim($_POST['sku']);
	if($sku){
		$data   =       explode(',',$sku);
		//var_dump($data);die;
		if(count($data)>0){
			$result	=	array();
			foreach($data as $sku){
				if($sku){
					$result[]	=	getData($sku);
				}
			}
			//echo '<pre>';print_r($result);die;
			$finalData	=	array(array('ID', 'Price', 'Link', 'Main Image', 'Side Image1', 'Side Image2', 'Side Image3', 'Side Image4', 'Side Image5'));
			foreach($result as $key => $value){
				//echo '<pre>';print_r($value);die;
				foreach($value as $key2 => $value2){
					//echo '<pre>';print_r($value2);die;
					foreach($value2 as $data){
						//echo '<pre>';print_r($data);die;
						$finalData[]	=	$data;
					}
				}
			}
			//echo '<pre>';print_r($finalData);die;
			outputCsv($finalData);
		}
	}
	//echo '<br><a href="">Do you wanna do it once again</a>';
}else{ ?>

<!DOCTYPE html>
<html lang="en">
    <div class="row">
        <form role="form" method="post" enctype="multipart/form-data" name="frm" action="">
          <div class="form-group">
            <textarea name="sku" cols="50" rows="15" placeholder="Please enter comma separated master sku(s) ie. SR0112S"></textarea>
            <button name="submit" type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
    </div>
</html>
<?php }

function getData( $sku ) {
	if(!$sku) {return false;}
	$store 				= 	Mage::app()->getStore();
	$configProductID 	= 	Mage::getModel("catalog/product")->getIdBySku( $sku ); 
	$product 			= 	Mage::getModel('catalog/product')->load($configProductID);
	//echo '<pre>'	;print_r($product->getData());
	
	$result		=	array();
	if( $product->getTypeId() == "simple" ){	
		echo 'product is simple';
	}elseif($product->isConfigurable()){
		//$productTypeIns = 	$product->getTypeInstance(true);
		
		$configProductURL	=	$store->getBaseUrl($store::URL_TYPE_WEB).$product->getUrlPath();
		
		$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
		//$childIds 		= 	$productTypeIns->getChildrenIds();
		//echo '<pre>';print_r($childIds);die;		
		if(count($childIds[0])>0){
			
			//	Get super attributes
			$superAttributes	=	getSuperAttributes($product);
			//echo '<pre>';print_r($superAttributes);
			//	?stone1_grade=AAAA&metal1_type=14K+Yellow+Gold	
			$i=0;
			foreach($childIds[0] as $key=>$val) {
				//echo '<br>'.$key;die;
				$childProduct 			= 	Mage::getModel('catalog/product')->load($key); 
				$productMediaConfig 	= 	Mage::getModel('catalog/product_media_config');
				//$baseImageUrl 			= 	$productMediaConfig->getMediaUrl($childProduct->getImage());
				$result[$configProductID][$i]['sku']			=	$childProduct->getSku();
				$result[$configProductID][$i]['price']			=	$childProduct->getPrice();
				
				//	creating child product url
				if( count($superAttributes) > 0 ){
					$urlString	=	'?';
					foreach($superAttributes as $attributeCode){
						if($attributeCode=='stone1_grade'){
							$urlString.=	$attributeCode. '=' .$childProduct->getAttributeText($attributeCode).'+&';
						}else{
							$urlString.=	$attributeCode. '=' .$childProduct->getAttributeText($attributeCode).'&';	
						}
					}
					$urlString		=	str_replace(' ', '+', $urlString);
					$urlString		=	substr($urlString,0,-1);
				}
				$result[$configProductID][$i]['link']				=	$configProductURL . $urlString;
				
				//	Product Media Gallery Images
				$gallery = $childProduct->getMediaGalleryImages();
				if(count($gallery) > 0){
					$j=1;
					foreach ($gallery as $image){
						$imagePath 										= 	$productMediaConfig->getMediaUrl($image['file']);
						$result[$configProductID][$i]['image_'.$j]		=	$imagePath;	
						$j++;
					}
					//echo '<pre>';print_r($result);die;
				}
				//	Product Media Gallery Images
				$i++;			
			}
			//echo '<pre>';print_r($result);die;
		}
	}
	return $result;
}




function outputCsv($dataToWrite){
	//$filePath = "F:/wamp/www/angara.git/media/exportcsv/Google_Feeds.csv";
	$fileName   = 	'Google_Feeds_'.date('d-m-Y-h_i_s').'.csv';
	$filePath 	= 	Mage::getBaseDir('media') . DS . 'exportedcsv' . DS .$fileName;		//	file path of the CSV file in which the data to be saved
	$mage_csv 	= 	new Varien_File_Csv(); 					//	Mage CSV
	$mage_csv->saveData($filePath, $dataToWrite);		//	write to csv file

	//	Prompt file download
	if (! is_file ( $filePath ) || ! is_readable ( $filePath )) {
		throw new Exception ( );
	}
	Mage::app()->getFrontController()->getResponse ()
				->setHttpResponseCode ( 200 )
				->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
				 ->setHeader ( 'Pragma', 'public', true )
				->setHeader ( 'Content-type', 'application/force-download' )
				->setHeader ( 'Content-Length', filesize($filePath) )
				->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filePath) );
	Mage::app()->getFrontController()->getResponse ()->clearBody ();
	Mage::app()->getFrontController()->getResponse ()->sendHeaders ();
	readfile ( $filePath );
}


/*
	retruns super attribute of configurable product
*/
function getSuperAttributes($_product){
	$_attributes 		= 	$_product->getTypeInstance(true)->getConfigurableAttributes($_product); 
	foreach($_attributes as $attribute) { 
		$attributeId		 = 	$attribute->getProductAttribute()->getId();  
		$attributeCode[] 	 = 	$attribute->getProductAttribute()->getAttributeCode(); 				//	Get super attributes of configurable product
	}	
	return $attributeCode;
}



function getAttributeName($attributeID){
        $resource       =       Mage::getSingleton('core/resource');
        $read           =       $resource->getConnection('core_read');
        $table1         =       $resource->getTableName('eav/attribute');

        $query          =       "select attribute_code from $table1 where attribute_id='$attributeID'";
        //echo $query;
        $result         =       $read->fetchOne($query);
        return $result;
}

function getAttributeValues($attributeId, $attributeOptionID){
        $resource       =       Mage::getSingleton('core/resource');
        $read           =       $resource->getConnection('core_read');
        $table2         =       $resource->getTableName('eav/attribute_option_value');

        $query          =       "SELECT value from $table2 where option_id ='4157'";
        //echo $query;
        $result         =       $read->fetchOne($query);
        return $result;
}
?>
