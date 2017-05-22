<?php
error_reporting(0);
class Angara_Feeds_Block_Adminhtml_Sears extends Mage_Adminhtml_Block_Widget_Form
{
  	protected function _prepareForm()
  	{
	  	$listCombination=array();
		if(Mage::getStoreConfig('feeds/settings/active')==1){
	  	$totalColumnCount = 0;
		$searsDatas = Mage::getModel('feeds/feeds')->getCollection()
					->addFieldToFilter('marketplace','sears')->getData();
		$searsData = array_shift($searsDatas);			
		$entityId = $searsData['feeds_id'];
		$url = Mage::helper('adminhtml')->getUrl("feeds/adminhtml_feeds/edit/id/".$entityId);
		if($searsData['status']==2){
			Mage::getSingleton('core/session')->addError(Mage::helper('feeds')->__( 'You Can not generate disabled feeds.' ));
			//echo "You Can not generate disabled feeds.";
			Mage::app()
            	->getResponse()
                ->setRedirect($url);
		}
		//$allData=explode(',',$amazonData['headings3']);						
				$k=0;
				foreach($searsData as $key=>$val){
					if($val !=''){
						$countColumn = $k+count($val);
						$k=$countColumn;
					}
				}
				$i =1;
				$writeFile='marketplace-sears.csv';
				$fp = fopen($writeFile, 'w');
				if($searsData['headings1']!=''){
					fputcsv($fp,split(',',$searsData['headings1']));
				}
				if($searsData['headings2']!=''){
					fputcsv($fp,split(',',$searsData['headings2']));
				}
				if($searsData['headings3']!=''){;
					fputcsv($fp,split(',',$searsData['headings3']));
				}
				if(Mage::getStoreConfig('feeds/settings/is_test_sku_enabled')==1){
					$testSkuArray = explode(',',Mage::getStoreConfig('feeds/settings/test_sku'));//array('SR0645G','SR0206RL');//SR0206RL
					$productCount = count($testSkuArray);
					foreach($testSkuArray as $testSku){
						$model = Mage::getModel('catalog/product')->getCollection()
								->addAttributeToSelect('*')
								->addAttributeToFilter('sku', $testSku)
								->addAttributeToFilter('status', 1)
								->getData();
						$productId = $model[0]['entity_id'];
						if($productId!=''){
							$_productCollection = Mage::getSingleton('catalog/product')->load($productId);
							$listCombination[] = $this->generateFeeds($countColumn,$searsData,$_productCollection);
						}
					}
				}
				else{
						$_categoryId = Mage::getStoreConfig('feeds/amazon_feeds/categories');
						if($_categoryId==''){
							Mage::getSingleton('core/session')->addError(Mage::helper('feeds')->__( 'Please Select Category to generate feeds.' ));
							//echo "You Can not generate disabled feeds.";
							Mage::app()
								->getResponse()
								->setRedirect($url);
						}
						$_category = Mage::getModel('catalog/category')->load($_categoryId);
						
						$productCollections = Mage::getResourceModel('catalog/product_collection')
							->addCategoryFilter($_category)
							->addAttributeToSelect('*')
							->addAttributeToFilter('status','1');
						$productCollections->load();
						//$productCollections = Mage::getModel('catalog/product')->getCollection();
						$productCollections->getSelect()->where("sku NOT LIKE 'AM%' AND (`sku` NOT LIKE '%_OLD') AND (`sku` NOT LIKE 'FR%') AND (`sku` NOT LIKE 'AGIF%') AND (`sku` NOT LIKE 'SC0%') AND (`sku` NOT IN('JA0050','INS001','EMOP0001SC','OP0001SC','MANUAL'))");
						//$productCollections->getSelect()->limit(5);
						//echo "hhhh===".$productCollections->getSelect()->__toString();exit;
						$productCount = count($productCollections);
						
						foreach($productCollections as $productCollection){
							$_productCollection = Mage::getModel('catalog/product')->load($productCollection->getId());
							//echo 'hhhhh===='.$productCollection->getId();exit;
							//var_dump($_productCollection->getData());exit;
							$listCombination[] = $this->generateFeeds($countColumn,$searsData,$_productCollection);	
						}
				}
				for($j=0;$j<$productCount;$j++){
					for($i=0;$i<count($listCombination[$j]);$i++){
						fputcsv($fp, split("&&&",$listCombination[$j][$i]));
					}
				}
				fclose($fp);
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename='.$writeFile);
				header('Pragma: no-cache');
				readfile($writeFile);
				
		}
		else{
			Mage::getSingleton('core/session')->addError(Mage::helper('feeds')->__( 'Feeds Module is Disabled. To Genereate Feeds Please Enable the module.' ));
			Mage::app()
            	->getResponse()
                ->setRedirect($url);
		}
	}
	
	public function generateFeeds($countColumn,$amazonData,$_productCollection){
		for($i=1;$i<=$countColumn;$i++){
					$searchKeywords = explode(',',$_productCollection->getMetaKeywords());	
					$_productCollection->getTypeInstance(true)->getSetAttributes($_productCollection);
					$galleryData = $_productCollection->getData('media_gallery');
					if($_productCollection->getTypeId()!='configurable' && $_productCollection->getVisibility()=='4'){ 		// Get All Data of Simple Products with catalog,search visibility(no variation)
						/*if(isset($catName)){
							unset($catName);
						}
						$catIds = $_productCollection->getCategoryIds();
						foreach($catIds as $_catId){
							$_categories = Mage::getModel('catalog/category')->load($_catId);
							$catName[] = $_categories->getName();
						}*/
						if($_productCollection->getAttributeText('jewelry_type')=='Ring'){
							//$catvaldata = '';
							//$returnCatName = '';
							//$returnCatName = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Rings','cat_name');
							
							/*$catvaldata = Mage::getModel('feeds/sears')->getCollection()
								->addFieldToFilter('category_name',$returnCatName)->getData();*/
							$ringSizeArray = array(
											'3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13'
										);
								foreach($ringSizeArray as $ringSize){
									switch ($amazonData['column_'.$i]) {
									case 'Item ID':
										$sku[] = $column[$i][] = $_productCollection->getSku().'-'.$ringSize;
										break;
									case 'Item Class ID':
										$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Rings','cat_id');
										break;
									case 'Item Class Display Path':
											$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Rings','cat_name');
										break;
									case 'Active':
											$column[$i][] = 'Y';
										break;
									case 'Variation Group ID':
											$column[$i][] = $_productCollection->getSku();
										break;				
									case 'Title':
											$column[$i][] =  $_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Short Description':
											$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Long Description':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($_productCollection);
											foreach($_prods as $_prod){
												if($_prod['count']>1){
													$_prodNameLastChar=substr($_prod['name'],-1);
													if($_prodNameLastChar=='y'){
														$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
														$stoneName=$_prodNameWithoutLastChar.'ies';
													}
													else{
														$stoneName=$_prod['name'].'s';
													}
												}
												else{
													$stoneName=$_prod['name'];
												}
												$str[] = "<br />";
												$str[] = $_prod['type'] . " Information:<br />
										";
												
												if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
												$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
												}
												if(trim($_prod['size']) !='' && $_prod['size'] > 0){
												$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
												}
												
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
												$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
												}
												if(trim($_prod['grade'])!=''){
												$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
												}
												if(trim($_prod['setting'])!=''){
												$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
												}
											}
											$strValue = '';
											$strValue = implode('<br />',$str);
											$column[$i][] = $strValue;
										break;	
									case 'Manufacturer Model #':
											$column[$i][] = $_productCollection->getSku();
										break;	
									case 'Standard Price':
											$column[$i][] = $_productCollection->getPrice();
										break;
									case 'Shipping Override':
											$column[$i][] = '0';
										break;
									case 'Low Inventory Alert':
											$column[$i][] = '0';
										break;	
									case 'MAP Price Indicator':
											$column[$i][] = 'non-strict';
 										break;		
									case 'Brand Name':
											$column[$i][] = 'Angara.com';
										break;
									case 'Shipping Length':
											$column[$i][] = '13';
										break;
									case 'Shipping Width':
											$column[$i][] = '11';
										break;	
									case 'Shipping Height':
											$column[$i][] = '2';
										break;	
									case 'Shipping Weight':
											$column[$i][] = '1';
										break;	
									case 'Shipping Cost: 2 day':
											$column[$i][] = '12.95';
										break;
									case 'Shipping Cost: Next Day':
											$column[$i][] = '21.95';
										break;
									case 'Condition':
											$column[$i][] = 'New';
										break;
									case 'Web Exclusive':
											$column[$i][] = 'Y';
										break;	
									case 'Product Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'Swatch Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;	
									case 'Feature Image URL #1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'Feature Image URL #2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'Feature Image URL #3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'Feature Image URL #4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'Feature Image URL #5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'Feature Image URL #6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'Attribute Name 1':
											$column[$i][] ='978710_Number of Stones';
										break;	
									case 'Attribute Value 1':
											$column[$i][] =$this->getAttributValue($_productCollection,'978710_Number of Stones',$ringSize);
										break;
									case 'Attribute Name 2':
											$column[$i][] ='978810_Primary Stone Carat Weight';
										break;	
									case 'Attribute Value 2':
											$column[$i][] =$this->getAttributValue($_productCollection,'978810_Primary Stone Carat Weight',$ringSize);
										break;
									case 'Attribute Name 3':
										$column[$i][] ='3509_Total Carat Weight*';
										break;	
									case 'Attribute Value 3':
											$column[$i][] =$this->getAttributValue($_productCollection,'3509_Total Carat Weight*',$ringSize);
										break;
									case 'Attribute Name 4':
											$column[$i][] ='952210_Ring Size';
										break;	
									case 'Attribute Value 4':
											$column[$i][] =$this->getAttributValue($_productCollection,'952210_Ring Size',$ringSize);
										break;
									case 'Attribute Name 5':
											$column[$i][] ='978410_Gem Color';
										break;	
									case 'Attribute Value 5':
											$column[$i][] =$this->getAttributValue($_productCollection,'978410_Gem Color',$ringSize);
										break;
									case 'Attribute Name 6':
											$column[$i][] ='978010_Metal Type';
										break;	
									case 'Attribute Value 6':
											$column[$i][] =$this->getAttributValue($_productCollection,'978010_Metal Type',$ringSize);
										break;
									case 'Attribute Name 7':
											$column[$i][] ='6206_Gender';
										break;	
									case 'Attribute Value 7':
											$column[$i][] =$this->getAttributValue($_productCollection,'6206_Gender',$ringSize);
										break;
									case 'Attribute Name 8':
											$column[$i][] ='6210_Birthstone Month';
										break;	
									case 'Attribute Value 8':
											$column[$i][] =$this->getAttributValue($_productCollection,'6210_Birthstone Month',$ringSize);
										break;
									case 'Attribute Name 9':
											$column[$i][] ='978510_Gem Type';
										break;	
									case 'Attribute Value 9':
											$column[$i][] =$this->getAttributValue($_productCollection,'978510_Gem Type',$ringSize);
										break;
									case 'Attribute Name 10':
											$column[$i][] ='992910_Style';
										break;	
									case 'Attribute Value 10':
											$column[$i][] =$this->getAttributValue($_productCollection,'992910_Style',$ringSize);
										break;
									case 'Attribute Name 11':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] ='3524_Gold Karat';
										}	
										else{
											$column[$i][] ='';
										}	
										break;	
									case 'Attribute Value 11':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] =$this->getAttributValue($_productCollection,'3524_Gold Karat',$ringSize);
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'Attribute Name 12':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888310_Diamond Clarity';
											}
										break;	
									case 'Attribute Value 12':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888310_Diamond Clarity',$ringSize);
											}
										break;
									case 'Attribute Name 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888410_Diamond Color';
											}
										break;	
									case 'Attribute Value 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888410_Diamond Color',$ringSize);
											}
										break;
									case 'Attribute Name 14':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888210_Diamond Carat Weight';
											}
										break;
									case 'Attribute Value 14':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888210_Diamond Carat Weight',$ringSize);
											}
										break;
									default:	
										$column[$i][] = '';
								}	
							}
						}
						else if($_productCollection->getAttributeText('jewelry_type')=='Earrings'){
							//$catvaldata = '';
							//$returnCatName = '';
							//$returnCatName = $this->getCategoryValue($_productCollection->getPrice(),$_productCollection->getAttributeText('stone1_name'),$catName,'Earrings','cat_name');
							
							/*$catvaldata = Mage::getModel('feeds/sears')->getCollection()
								->addFieldToFilter('category_name',$returnCatName)->getData();*/
							switch ($amazonData['column_'.$i]){
									case 'Item ID':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'Item Class ID':
										$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Earrings','cat_id');
										break;
									case 'Item Class Display Path':
											$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Earrings','cat_name');
										break;
									case 'Active':
											$column[$i][] = 'Y';
										break;
									case 'Variation Group ID':
											$column[$i][] = $_productCollection->getSku();
										break;				
									case 'Title':
											$column[$i][] =  $_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Short Description':
											$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Long Description':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($_productCollection);
											foreach($_prods as $_prod){
												if($_prod['count']>1){
													$_prodNameLastChar=substr($_prod['name'],-1);
													if($_prodNameLastChar=='y'){
														$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
														$stoneName=$_prodNameWithoutLastChar.'ies';
													}
													else{
														$stoneName=$_prod['name'].'s';
													}
												}
												else{
													$stoneName=$_prod['name'];
												}
												$str[] = "<br />";
												$str[] = $_prod['type'] . " Information:<br />
										";
												
												if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
												$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
												}
												if(trim($_prod['size']) !='' && $_prod['size'] > 0){
												$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
												}
												
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
												$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
												}
												if(trim($_prod['grade'])!=''){
												$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
												}
												if(trim($_prod['setting'])!=''){
												$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
												}
											}
											$strValue = '';
											$strValue = implode('<br />',$str);
											$column[$i][] = $strValue;
										break;	
									case 'Manufacturer Model #':
											$column[$i][] = $_productCollection->getSku();
										break;	
									case 'Standard Price':
											$column[$i][] = $_productCollection->getPrice();
										break;
									case 'Shipping Override':
											$column[$i][] = '0';
										break;
									case 'Low Inventory Alert':
											$column[$i][] = '0';
										break;	
									case 'MAP Price Indicator':
											$column[$i][] = 'non-strict';
 										break;		
									case 'Brand Name':
											$column[$i][] = 'Angara.com';
										break;
									case 'Shipping Length':
											$column[$i][] = '13';
										break;
									case 'Shipping Width':
											$column[$i][] = '11';
										break;	
									case 'Shipping Height':
											$column[$i][] = '2';
										break;	
									case 'Shipping Weight':
											$column[$i][] = '1';
										break;	
									case 'Shipping Cost: 2 day':
											$column[$i][] = '12.95';
										break;
									case 'Shipping Cost: Next Day':
											$column[$i][] = '21.95';
										break;
									case 'Condition':
											$column[$i][] = 'New';
										break;
									case 'Web Exclusive':
											$column[$i][] = 'Y';
										break;	
									case 'Product Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'Swatch Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;	
									case 'Feature Image URL #1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'Feature Image URL #2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'Feature Image URL #3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'Feature Image URL #4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'Feature Image URL #5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'Feature Image URL #6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'Attribute Name 1':
											$column[$i][] ='978810_Primary Stone Carat Weight';
										break;	
									case 'Attribute Value 1':
											$column[$i][] =$this->getAttributValue($_productCollection,'978810_Primary Stone Carat Weight');
										break;
									case 'Attribute Name 2':
											$column[$i][] ='978710_Number of Stones';
										break;	
									case 'Attribute Value 2':
											$column[$i][] =$this->getAttributValue($_productCollection,'978710_Number of Stones');
										break;
									case 'Attribute Name 3':
										$column[$i][] ='3509_Total Carat Weight*';
										break;	
									case 'Attribute Value 3':
											$column[$i][] =$this->getAttributValue($_productCollection,'3509_Total Carat Weight*');
										break;
									case 'Attribute Name 4':
											$column[$i][] ='6210_Birthstone Month';
										break;	
									case 'Attribute Value 4':
											$column[$i][] =$this->getAttributValue($_productCollection,'6210_Birthstone Month');
										break;
									case 'Attribute Name 5':
											$column[$i][] ='978010_Metal Type';
										break;	
									case 'Attribute Value 5':
											$column[$i][] =$this->getAttributValue($_productCollection,'978010_Metal Type');
										break;
									case 'Attribute Name 6':
											$column[$i][] ='6206_Gender';
										break;	
									case 'Attribute Value 6':
											$column[$i][] =$this->getAttributValue($_productCollection,'6206_Gender');
										break;
									case 'Attribute Name 7':
											$column[$i][] ='978410_Gem Color';
										break;	
									case 'Attribute Value 7':
											$column[$i][] =$this->getAttributValue($_productCollection,'978410_Gem Color');
										break;
									case 'Attribute Name 8':
											$column[$i][] ='978510_Gem Type';
										break;	
									case 'Attribute Value 8':
											$column[$i][] =$this->getAttributValue($_productCollection,'978510_Gem Type');
										break;
									case 'Attribute Name 9':
											$column[$i][] ='983410_Earring Style';
										break;	
									case 'Attribute Value 9':
											$column[$i][] =$this->getAttributValue($_productCollection,'983410_Earring Style');
										break;
									case 'Attribute Name 10':
											$column[$i][] ='978610_Lab Created or Natural';
										break;	
									case 'Attribute Value 10':
											$column[$i][] =$this->getAttributValue($_productCollection,'978610_Lab Created or Natural');
										break;
									case 'Attribute Name 11':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] ='3524_Gold Karat';
										}	
										else{
											$column[$i][] ='';
										}	
										break;	
									case 'Attribute Value 11':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] =$this->getAttributValue($_productCollection,'3524_Gold Karat',$ringSize);
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'Attribute Name 12':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888310_Diamond Clarity';
											}
										break;	
									case 'Attribute Value 12':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888310_Diamond Clarity',$ringSize);
											}
										break;
									case 'Attribute Name 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888410_Diamond Color';
											}
										break;	
									case 'Attribute Value 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888410_Diamond Color',$ringSize);
											}
										break;
									case 'Attribute Name 14':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888210_Diamond Carat Weight';
											}
										break;
									case 'Attribute Value 14':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888210_Diamond Carat Weight',$ringSize);
											}
										break;	
									default:	
										$column[$i][] = '';
								}
						}
						else if($_productCollection->getAttributeText('jewelry_type')=='Pendant'){
							switch ($amazonData['column_'.$i]) {
								
									case 'Item ID':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'Item Class ID':
										$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Pendant','cat_id');
										break;
									case 'Item Class Display Path':
											$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Pendant','cat_name');
										break;
									case 'Active':
											$column[$i][] = 'Y';
										break;
									case 'Variation Group ID':
											$column[$i][] = $_productCollection->getSku();
										break;				
									case 'Title':
											$column[$i][] =  $_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Short Description':
											$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$_productCollection->getStone1Weight().'ct. '.$_productCollection->getShortDescription().' in '.$_productCollection->getAttributeText('metal1_type');
										break;
									case 'Long Description':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($_productCollection);
											foreach($_prods as $_prod){
												if($_prod['count']>1){
													$_prodNameLastChar=substr($_prod['name'],-1);
													if($_prodNameLastChar=='y'){
														$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
														$stoneName=$_prodNameWithoutLastChar.'ies';
													}
													else{
														$stoneName=$_prod['name'].'s';
													}
												}
												else{
													$stoneName=$_prod['name'];
												}
												$str[] = "<br />";
												$str[] = $_prod['type'] . " Information:<br />
										";
												
												if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
												$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
												}
												if(trim($_prod['size']) !='' && $_prod['size'] > 0){
												$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
												}
												
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
												$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
												}
												if(trim($_prod['grade'])!=''){
												$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
												}
												if(trim($_prod['setting'])!=''){
												$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
												}
											}
											$strValue = '';
											$strValue = implode('<br />',$str);
											$column[$i][] = $strValue;
										break;	
									case 'Manufacturer Model #':
											$column[$i][] = $_productCollection->getSku();
										break;	
									case 'Standard Price':
											$column[$i][] = $_productCollection->getPrice();
										break;
									case 'Shipping Override':
											$column[$i][] = '0';
										break;
									case 'Low Inventory Alert':
											$column[$i][] = '0';
										break;	
									case 'MAP Price Indicator':
											$column[$i][] = 'non-strict';
 										break;		
									case 'Brand Name':
											$column[$i][] = 'Angara.com';
										break;
									case 'Shipping Length':
											$column[$i][] = '13';
										break;
									case 'Shipping Width':
											$column[$i][] = '11';
										break;	
									case 'Shipping Height':
											$column[$i][] = '2';
										break;	
									case 'Shipping Weight':
											$column[$i][] = '1';
										break;	
									case 'Shipping Cost: 2 day':
											$column[$i][] = '12.95';
										break;
									case 'Shipping Cost: Next Day':
											$column[$i][] = '21.95';
										break;
									case 'Condition':
											$column[$i][] = 'New';
										break;
									case 'Web Exclusive':
											$column[$i][] = 'Y';
										break;	
									case 'Product Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'Swatch Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;	
									case 'Feature Image URL #1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'Feature Image URL #2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'Feature Image URL #3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'Feature Image URL #4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'Feature Image URL #5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'Feature Image URL #6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'Attribute Name 1':
											$column[$i][] ='978710_Number of Stones';
										break;	
									case 'Attribute Value 1':
											$column[$i][] =$this->getAttributValue($_productCollection,'978710_Number of Stones');
										break;
									case 'Attribute Name 2':
											$column[$i][] ='978010_Metal Type';
										break;	
									case 'Attribute Value 2':
											$column[$i][] =$this->getAttributValue($_productCollection,'978010_Metal Type');
										break;
									case 'Attribute Name 3':
										$column[$i][] ='978510_Gem Type';
										break;	
									case 'Attribute Value 3':
											$column[$i][] =$this->getAttributValue($_productCollection,'978510_Gem Type');
										break;
									case 'Attribute Name 4':
											$column[$i][] ='7365_Necklace Type';
										break;	
									case 'Attribute Value 4':
											$column[$i][] =$this->getAttributValue($_productCollection,'7365_Necklace Type');
										break;
									case 'Attribute Name 5':
											$column[$i][] ='6206_Gender';
										break;	
									case 'Attribute Value 5':
											$column[$i][] =$this->getAttributValue($_productCollection,'6206_Gender');
										break;
									case 'Attribute Name 6':
											$column[$i][] ='978410_Gem Color';
										break;	
									case 'Attribute Value 6':
											$column[$i][] =$this->getAttributValue($_productCollection,'978410_Gem Color');
										break;
									case 'Attribute Name 7':
											$column[$i][] ='978310_Chain Style';
										break;	
									case 'Attribute Value 7':
											$column[$i][] =$this->getAttributValue($_productCollection,'978310_Chain Style');
										break;
									case 'Attribute Name 8':
											$column[$i][] ='3509_Total Carat Weight*';
										break;	
									case 'Attribute Value 8':
											$column[$i][] =$this->getAttributValue($_productCollection,'3509_Total Carat Weight*');
										break;
									case 'Attribute Name 9':
											$column[$i][] ='1015910_Chain Included';
										break;	
									case 'Attribute Value 9':
											$column[$i][] =$this->getAttributValue($_productCollection,'1015910_Chain Included');
										break;
									case 'Attribute Name 10':
											$column[$i][] ='6210_Birthstone Month';
										break;	
									case 'Attribute Value 10':
											$column[$i][] =$this->getAttributValue($_productCollection,'6210_Birthstone Month');
										break;
									case 'Attribute Name 11':
											$column[$i][] ='999810_Pendant & Necklace Style';
										break;	
									case 'Attribute Value 11':
											$column[$i][] =$this->getAttributValue($_productCollection,'999810_Pendant & Necklace Style');
										break;
									case 'Attribute Name 12':
											$column[$i][] ='978610_Lab Created or Natural';
										break;	
									case 'Attribute Value 12':
											$column[$i][] =$this->getAttributValue($_productCollection,'978610_Lab Created or Natural');
										break;		
									case 'Attribute Name 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888310_Diamond Clarity';
											}
										break;	
									case 'Attribute Value 13':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888310_Diamond Clarity',$ringSize);
											}
										break;
									case 'Attribute Name 14':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] ='3524_Gold Karat';
										}	
										else{
											$column[$i][] ='';
										}	
										break;	
									case 'Attribute Value 14':
										if(strpos($_productCollection->getAttributeText('metal1_type'),'Gold')!== false){
											$column[$i][] =$this->getAttributValue($_productCollection,'3524_Gold Karat',$ringSize);
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'Attribute Name 15':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888410_Diamond Color';
											}
										break;	
									case 'Attribute Value 15':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888410_Diamond Color',$ringSize);
											}
										break;
									case 'Attribute Name 16':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] = '888210_Diamond Carat Weight';
											}
										break;
									case 'Attribute Value 16':
											if($_productCollection->getAttributeText('stone1_type')=='Gemstone'){
												$column[$i][] = '';	
											}
											else{
												$column[$i][] =$this->getAttributValue($_productCollection,'888210_Diamond Carat Weight',$ringSize);
											}
										break;
									default:	
										$column[$i][] = '';
									
							}
						}
						/*else if($_productCollection->getAttributeText('jewelry_type')=='Bracelet'){
							switch ($amazonData['column_'.$i]) {
								
									case 'Item ID':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'Item Class ID':
										$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Bracelet','cat_id');
										break;
									case 'Item Class Display Path':
											$column[$i][] = $this->getCategoryValue($_productCollection->getAttributeText('stone1_type'),$_productCollection->getAttributeText('metal1_type'),'Bracelet','cat_name');
										break;
									case 'Active':
											$column[$i][] = 'Y';
										break;
									case 'Variation Group ID':
											$column[$i][] = $_productCollection->getSku();
										break;				
									case 'Title':
											$column[$i][] =  $_productCollection->getShortDescription();
										break;
									case 'Short Description':
											$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$_productCollection->getShortDescription().". Get easy returns, FREE shipping worldwide, financing options & FREE gift with every purchase.";
										break;
									case 'Long Description':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($_productCollection);
											foreach($_prods as $_prod){
												if($_prod['count']>1){
													$_prodNameLastChar=substr($_prod['name'],-1);
													if($_prodNameLastChar=='y'){
														$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
														$stoneName=$_prodNameWithoutLastChar.'ies';
													}
													else{
														$stoneName=$_prod['name'].'s';
													}
												}
												else{
													$stoneName=$_prod['name'];
												}
												
												$str[] = $_prod['type'] . " Information:<br />
										";
												
												if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
												$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'] . "<br />";
												}
												if(trim($_prod['size']) !='' && $_prod['size'] > 0){
												$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'] . "<br />";
												}
												
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
												$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight']. "<br />";
												}
												if(trim($_prod['grade'])!=''){
												$str[] = "Quality Grade: &nbsp;" . $_prod['grade'] . "<br />";
												}
												if(trim($_prod['setting'])!=''){
												$str[] = "Setting Type: &nbsp;" . $_prod['setting'] . "<br />";	
												}
											}
											$strValue = '';
											$strValue = implode('<br />',$str);
											$column[$i][] = $strValue;
										break;	
									case 'Manufacturer Model #':
											$column[$i][] = $_productCollection->getSku();
										break;	
									case 'Standard Price':
											$column[$i][] = $_productCollection->getPrice();
										break;
									case 'Shipping Override':
											$column[$i][] = '0';
										break;
									case 'Low Inventory Alert':
											$column[$i][] = '0';
										break;	
									case 'MAP Price Indicator':
											$column[$i][] = 'non-strict';
 										break;		
									case 'Brand Name':
											$column[$i][] = 'Angara.com';
										break;
									case 'Shipping Length':
											$column[$i][] = '13';
										break;
									case 'Shipping Width':
											$column[$i][] = '11';
										break;	
									case 'Shipping Height':
											$column[$i][] = '2';
										break;	
									case 'Shipping Weight':
											$column[$i][] = '1';
										break;
									case 'Shipping Cost: 2 day':
											$column[$i][] = '12.95';
										break;
									case 'Shipping Cost: Next Day':
											$column[$i][] = '21.95';
										break;
									case 'Condition':
											$column[$i][] = 'New';
										break;	
									case 'Web Exclusive':
											$column[$i][] = 'Y';
										break;	
									case 'Product Image URL':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'Feature Image URL #1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'Feature Image URL #2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'Feature Image URL #3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'Feature Image URL #4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'Feature Image URL #5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'Feature Image URL #6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
											
									default:	
										$column[$i][] = '';	
							}
						}*/
						$skuCount = count($sku);
					}
					else{    // Get All Data of Configurable Products(variation-child sku's)
						$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($_productCollection->getId());
								foreach($childIds[0] as $key=>$val) // for all child sku's
								{
									//$associatedProduct = Mage::getModel('catalog/product')->load($val);
									$associatedProducts = Mage::getModel('catalog/product')->getCollection()
											->addAttributeToSelect('*')
											->addAttributeToFilter('entity_id', $val)
											->addAttributeToFilter('status', 1)
											->load();
											//echo "<pre>";print_r($associatedProduct->getData());
									$associatedProducts =$associatedProducts->getData();
									if($associatedProducts[0]['entity_id']!=''){
									$associatedProduct = Mage::getModel('catalog/product')->load($associatedProducts[0]['entity_id']);
									$associatedProduct->getTypeInstance(true)->getSetAttributes($associatedProduct);
									$galleryDataChild = $associatedProduct->getData('media_gallery');
									if($_productCollection->getAttributeText('jewelry_type')=='Ring'){
										$ringSizeArray = array(
											'3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13'
										);
										foreach($ringSizeArray as $ringSize){
											switch ($amazonData['column_'.$i]) {
												case 'Item ID':
													$sku[] = $column[$i][] = $associatedProduct->getSku().'-'.$ringSize;
													break;
												case 'Item Class ID':
													$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Rings','cat_id');
													break;
												case 'Item Class Display Path':
														$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Rings','cat_name');
													break;
												case 'Active':
														$column[$i][] = 'Y';
													break;
												case 'Variation Group ID':
														$column[$i][] = $associatedProduct->getSku();
													break;				
												case 'Title':
														$column[$i][] =  $associatedProduct->getStone1Weight().'ct. '.$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
													break;
												case 'Short Description':
													$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
														//$column[$i][] =  $associatedProduct->getShortDescription();
													break;
												case 'Long Description':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
													foreach($_prods as $_prod){
														if($_prod['count']>1){
															$_prodNameLastChar=substr($_prod['name'],-1);
															if($_prodNameLastChar=='y'){
																$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
																$stoneName=$_prodNameWithoutLastChar.'ies';
															}
															else{
																$stoneName=$_prod['name'].'s';
															}
														}
														else{
															$stoneName=$_prod['name'];
														}
														$str[] = "<br />";
														$str[] = $_prod['type'] . " Information:<br />
												";
														
														if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
														$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
														}
														if(trim($_prod['size']) !='' && $_prod['size'] > 0){
														$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
														}
														
														if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
														$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
														}
														if(trim($_prod['grade'])!=''){
														$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
														}
														if(trim($_prod['setting'])!=''){
														$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
														}
													}
													$strValue = '';
													$strValue = implode('<br />',$str);
													$column[$i][] = $strValue;	
													break;	
												case 'Manufacturer Model #':
														$parentSku[$i] = explode('-',$associatedProduct->getSku());		
														$parentSkus = $parentSku[$i][0].'-'.$parentSku[$i][1].'-'.$parentSku[$i][2];
														$column[$i][] = $parentSkus;
													break;	
												case 'Standard Price':
														$column[$i][] = $associatedProduct->getPrice();
													break;
												case 'Shipping Override':
														$column[$i][] = '0';
													break;
												case 'Low Inventory Alert':
														$column[$i][] = '0';
													break;	
												case 'MAP Price Indicator':
														$column[$i][] = 'non-strict';
													break;		
												case 'Brand Name':
														$column[$i][] = 'Angara.com';
													break;
												case 'Shipping Length':
														$column[$i][] = '13';
													break;
												case 'Shipping Width':
														$column[$i][] = '11';
													break;	
												case 'Shipping Height':
														$column[$i][] = '2';
													break;	
												case 'Shipping Weight':
														$column[$i][] = '1';
													break;	
												case 'Shipping Cost: 2 day':
														$column[$i][] = '12.95';
													break;
												case 'Shipping Cost: Next Day':
														$column[$i][] = '21.95';
													break;
												case 'Condition':
														$column[$i][] = 'New';
													break;
												case 'Web Exclusive':
														$column[$i][] = 'Y';
													break;	
												case 'Product Image URL':
														$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
													break;
												case 'Swatch Image URL':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;	
												case 'Feature Image URL #1':
													$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
													break;
												case 'Feature Image URL #2':
													$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
													break;
												case 'Feature Image URL #3':
													$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
													break;
												case 'Feature Image URL #4':
													$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
													break;
												case 'Feature Image URL #5':
													$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
													break;
												case 'Feature Image URL #6':
													$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
													break;
												case 'Attribute Name 1':
														$column[$i][] ='978710_Number of Stones';
													break;	
												case 'Attribute Value 1':
														$column[$i][] =$this->getAttributValue($associatedProduct,'978710_Number of Stones',$ringSize);
													break;
												case 'Attribute Name 2':
														$column[$i][] ='978810_Primary Stone Carat Weight';
													break;	
												case 'Attribute Value 2':
														$column[$i][] =$this->getAttributValue($associatedProduct,'978810_Primary Stone Carat Weight',$ringSize);
													break;
												case 'Attribute Name 3':
													$column[$i][] ='3509_Total Carat Weight*';
													break;	
												case 'Attribute Value 3':
														$column[$i][] =$this->getAttributValue($associatedProduct,'3509_Total Carat Weight*',$ringSize);
													break;
												case 'Attribute Name 4':
														$column[$i][] ='952210_Ring Size';
													break;	
												case 'Attribute Value 4':
														$column[$i][] =$this->getAttributValue($associatedProduct,'952210_Ring Size',$ringSize);
													break;
												case 'Attribute Name 5':
														$column[$i][] ='978410_Gem Color';
													break;	
												case 'Attribute Value 5':
														$column[$i][] =$this->getAttributValue($associatedProduct,'978410_Gem Color',$ringSize);
													break;
												case 'Attribute Name 6':
														$column[$i][] ='978010_Metal Type';
													break;	
												case 'Attribute Value 6':
														$column[$i][] =$this->getAttributValue($associatedProduct,'978010_Metal Type',$ringSize);
													break;
												case 'Attribute Name 7':
														$column[$i][] ='6206_Gender';
													break;	
												case 'Attribute Value 7':
														$column[$i][] =$this->getAttributValue($associatedProduct,'6206_Gender',$ringSize);
													break;
												case 'Attribute Name 8':
														$column[$i][] ='6210_Birthstone Month';
													break;	
												case 'Attribute Value 8':
														$column[$i][] =$this->getAttributValue($associatedProduct,'6210_Birthstone Month',$ringSize);
													break;
												case 'Attribute Name 9':
														$column[$i][] ='978510_Gem Type';
													break;	
												case 'Attribute Value 9':
														$column[$i][] =$this->getAttributValue($associatedProduct,'978510_Gem Type',$ringSize);
													break;
												case 'Attribute Name 10':
														$column[$i][] ='992910_Style';
													break;	
												case 'Attribute Value 10':
														$column[$i][] =$this->getAttributValue($associatedProduct,'992910_Style',$ringSize);
													break;
												case 'Attribute Name 11':
													if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
														$column[$i][] ='3524_Gold Karat';
													}	
													else{
														$column[$i][] ='';
													}	
													break;	
												case 'Attribute Value 11':
													if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
														$column[$i][] =$this->getAttributValue($associatedProduct,'3524_Gold Karat',$ringSize);
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'Attribute Name 12':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] = '888310_Diamond Clarity';
														}
													break;	
												case 'Attribute Value 12':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] =$this->getAttributValue($associatedProduct,'888310_Diamond Clarity',$ringSize);
														}
													break;
												case 'Attribute Name 13':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] = '888410_Diamond Color';
														}
													break;	
												case 'Attribute Value 13':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] =$this->getAttributValue($associatedProduct,'888410_Diamond Color',$ringSize);
														}
													break;
												case 'Attribute Name 14':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] = '888210_Diamond Carat Weight';
														}
													break;
												case 'Attribute Value 14':
														if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
															$column[$i][] = '';	
														}
														else{
															$column[$i][] =$this->getAttributValue($associatedProduct,'888210_Diamond Carat Weight',$ringSize);
														}
													break;
												default:	
													$column[$i][] = '';
												}
										}
									}
									else if($_productCollection->getAttributeText('jewelry_type')=='Earrings'){
										//$catvaldata = '';
										//$returnCatName = '';
										//$returnCatName = $this->getCategoryValue($associatedProduct->getPrice(),$associatedProduct->getAttributeText('stone1_name'),$catName,'Earrings','cat_name');
										
										/*$catvaldata = Mage::getModel('feeds/sears')->getCollection()
											->addFieldToFilter('category_name',$returnCatName)->getData();*/
										switch ($amazonData['column_'.$i]) {
											case 'Item ID':
												$sku[] = $column[$i][] = $associatedProduct->getSku();
												break;
											case 'Item Class ID':
												$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Earrings','cat_id');
												break;
											case 'Item Class Display Path':
													$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Earrings','cat_name');
												break;
											case 'Active':
													$column[$i][] = 'Y';
												break;
											case 'Variation Group ID':
													$column[$i][] = $associatedProduct->getSku();
												break;				
											case 'Title':
													$column[$i][] =  $associatedProduct->getStone1Weight().'ct. '.$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
												break;
											case 'Short Description':
													$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
												break;
											case 'Long Description':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
													foreach($_prods as $_prod){
														if($_prod['count']>1){
															$_prodNameLastChar=substr($_prod['name'],-1);
															if($_prodNameLastChar=='y'){
																$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
																$stoneName=$_prodNameWithoutLastChar.'ies';
															}
															else{
																$stoneName=$_prod['name'].'s';
															}
														}
														else{
															$stoneName=$_prod['name'];
														}
														$str[] = "<br />";
														$str[] = $_prod['type'] . " Information:<br />
												";
														
														if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
														$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
														}
														if(trim($_prod['size']) !='' && $_prod['size'] > 0){
														$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
														}
														
														if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
														$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
														}
														if(trim($_prod['grade'])!=''){
														$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
														}
														if(trim($_prod['setting'])!=''){
														$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
														}
													}
													$strValue = '';
													$strValue = implode('<br />',$str);
													$column[$i][] = $strValue;
												break;	
											case 'Manufacturer Model #':
													$parentSku[$i] = explode('-',$associatedProduct->getSku());		
													$parentSkus = $parentSku[$i][0].'-'.$parentSku[$i][1].'-'.$parentSku[$i][2];
													$column[$i][] = $parentSkus;
												break;	
											case 'Standard Price':
													$column[$i][] = $associatedProduct->getPrice();
												break;
											case 'Shipping Override':
													$column[$i][] = '0';
												break;
											case 'Low Inventory Alert':
													$column[$i][] = '0';
												break;	
											case 'MAP Price Indicator':
													$column[$i][] = 'non-strict';
												break;		
											case 'Brand Name':
													$column[$i][] = 'Angara.com';
												break;
											case 'Shipping Length':
													$column[$i][] = '13';
												break;
											case 'Shipping Width':
													$column[$i][] = '11';
												break;	
											case 'Shipping Height':
													$column[$i][] = '2';
												break;	
											case 'Shipping Weight':
													$column[$i][] = '1';
												break;
											case 'Shipping Cost: 2 day':
													$column[$i][] = '12.95';
												break;
											case 'Shipping Cost: Next Day':
													$column[$i][] = '21.95';
												break;
											case 'Condition':
													$column[$i][] = 'New';
												break;
											case 'Web Exclusive':
													$column[$i][] = 'Y';
												break;	
											case 'Product Image URL':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;
											case 'Swatch Image URL':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;	
											case 'Feature Image URL #1':
												$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
												break;
											case 'Feature Image URL #2':
												$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
												break;
											case 'Feature Image URL #3':
												$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
												break;
											case 'Feature Image URL #4':
												$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
												break;
											case 'Feature Image URL #5':
												$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
												break;
											case 'Feature Image URL #6':
												$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
												break;
											case 'Attribute Name 1':
													$column[$i][] ='978810_Primary Stone Carat Weight';
												break;	
											case 'Attribute Value 1':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978810_Primary Stone Carat Weight');
												break;
											case 'Attribute Name 2':
													$column[$i][] ='978710_Number of Stones';
												break;	
											case 'Attribute Value 2':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978710_Number of Stones');
												break;
											case 'Attribute Name 3':
												$column[$i][] ='3509_Total Carat Weight*';
												break;	
											case 'Attribute Value 3':
													$column[$i][] =$this->getAttributValue($associatedProduct,'3509_Total Carat Weight*');
												break;
											case 'Attribute Name 4':
													$column[$i][] ='6210_Birthstone Month';
												break;	
											case 'Attribute Value 4':
													$column[$i][] =$this->getAttributValue($associatedProduct,'6210_Birthstone Month');
												break;
											case 'Attribute Name 5':
													$column[$i][] ='978010_Metal Type';
												break;	
											case 'Attribute Value 5':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978010_Metal Type');
												break;
											case 'Attribute Name 6':
													$column[$i][] ='6206_Gender';
												break;	
											case 'Attribute Value 6':
													$column[$i][] =$this->getAttributValue($associatedProduct,'6206_Gender');
												break;
											case 'Attribute Name 7':
													$column[$i][] ='978410_Gem Color';
												break;	
											case 'Attribute Value 7':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978410_Gem Color');
												break;
											case 'Attribute Name 8':
													$column[$i][] ='978510_Gem Type';
												break;	
											case 'Attribute Value 8':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978510_Gem Type');
												break;
											case 'Attribute Name 9':
													$column[$i][] ='983410_Earring Style';
												break;	
											case 'Attribute Value 9':
													$column[$i][] =$this->getAttributValue($associatedProduct,'983410_Earring Style');
												break;
											case 'Attribute Name 10':
													$column[$i][] ='978610_Lab Created or Natural';
												break;	
											case 'Attribute Value 10':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978610_Lab Created or Natural');
												break;
											case 'Attribute Name 11':
												if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
													$column[$i][] ='3524_Gold Karat';
												}	
												else{
													$column[$i][] ='';
												}	
												break;	
											case 'Attribute Value 11':
												if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
													$column[$i][] =$this->getAttributValue($associatedProduct,'3524_Gold Karat',$ringSize);
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'Attribute Name 12':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] = '888310_Diamond Clarity';
													}
												break;	
											case 'Attribute Value 12':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888310_Diamond Clarity',$ringSize);
													}
												break;
											case 'Attribute Name 13':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] = '888410_Diamond Color';
													}
												break;	
											case 'Attribute Value 13':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888410_Diamond Color',$ringSize);
													}
												break;
											case 'Attribute Name 14':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] = '888210_Diamond Carat Weight';
													}
												break;
											case 'Attribute Value 14':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888210_Diamond Carat Weight',$ringSize);
													}
												break;
											default:	
												$column[$i][] = '';
												
												}
									}
									else if($_productCollection->getAttributeText('jewelry_type')=='Pendant'){
										switch ($amazonData['column_'.$i]) {
											case 'Item ID':
												$sku[] = $column[$i][] = $associatedProduct->getSku();
												break;
											case 'Item Class ID':
												$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Pendant','cat_id');
												break;
											case 'Item Class Display Path':
													$column[$i][] = $this->getCategoryValue($associatedProduct->getAttributeText('stone1_type'),$associatedProduct->getAttributeText('metal1_type'),'Pendant','cat_name');
												break;
											case 'Active':
													$column[$i][] = 'Y';
												break;
											case 'Variation Group ID':
													$column[$i][] = $associatedProduct->getSku();
												break;				
											case 'Title':
													$column[$i][] =  $associatedProduct->getStone1Weight().'ct. '.$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
												break;
											case 'Short Description':
													$column[$i][] =  "Enjoy style and luxury with Angara&acute;s beautiful ".$associatedProduct->getShortDescription().' in '.$associatedProduct->getAttributeText('metal1_type');
												break;
											case 'Long Description':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
													foreach($_prods as $_prod){
														if($_prod['count']>1){
															$_prodNameLastChar=substr($_prod['name'],-1);
															if($_prodNameLastChar=='y'){
																$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
																$stoneName=$_prodNameWithoutLastChar.'ies';
															}
															else{
																$stoneName=$_prod['name'].'s';
															}
														}
														else{
															$stoneName=$_prod['name'];
														}
														$str[] = "<br />";
														$str[] = $_prod['type'] . " Information:<br />
												";
														
														if(trim($_prod['count'])!='' && $_prod['count'] > 0){	
														$str[] = "Number of " . $_prod['shape']." ".$stoneName. ":&nbsp;" . $_prod['count'];
														}
														if(trim($_prod['size']) !='' && $_prod['size'] > 0){
														$str[] = "Approximate Dimensions:&nbsp;" . $_prod['size'];
														}
														
														if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
														$str[] = "Approximate Carat Total Weight: &nbsp;" . $_prod['weight'];
														}
														if(trim($_prod['grade'])!=''){
														$str[] = "Quality Grade: &nbsp;" . $_prod['grade'];
														}
														if(trim($_prod['setting'])!=''){
														$str[] = "Setting Type: &nbsp;" . $_prod['setting'];	
														}
													}
													$strValue = '';
													$strValue = implode('<br />',$str);
													$column[$i][] = $strValue;
												break;	
											case 'Manufacturer Model #':
													$parentSku[$i] = explode('-',$associatedProduct->getSku());		
													$parentSkus = $parentSku[$i][0].'-'.$parentSku[$i][1].'-'.$parentSku[$i][2];
													$column[$i][] = $parentSkus;
												break;	
											case 'Standard Price':
													$column[$i][] = $associatedProduct->getPrice();
												break;
											case 'Shipping Override':
													$column[$i][] = '0';
												break;
											case 'Low Inventory Alert':
													$column[$i][] = '0';
												break;	
											case 'MAP Price Indicator':
													$column[$i][] = 'non-strict';
												break;		
											case 'Brand Name':
													$column[$i][] = 'Angara.com';
												break;
											case 'Shipping Length':
													$column[$i][] = '13';
												break;
											case 'Shipping Width':
													$column[$i][] = '11';
												break;	
											case 'Shipping Height':
													$column[$i][] = '2';
												break;	
											case 'Shipping Weight':
													$column[$i][] = '1';
												break;
											case 'Shipping Cost: 2 day':
													$column[$i][] = '12.95';
												break;
											case 'Shipping Cost: Next Day':
													$column[$i][] = '21.95';
												break;
											case 'Condition':
													$column[$i][] = 'New';
												break;	
											case 'Web Exclusive':
													$column[$i][] = 'Y';
												break;	
											case 'Product Image URL':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;
											case 'Swatch Image URL':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;	
											case 'Feature Image URL #1':
												$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
												break;
											case 'Feature Image URL #2':
												$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
												break;
											case 'Feature Image URL #3':
												$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
												break;
											case 'Feature Image URL #4':
												$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
												break;
											case 'Feature Image URL #5':
												$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
												break;
											case 'Feature Image URL #6':
												$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
												break;
											case 'Attribute Name 1':
													$column[$i][] ='978710_Number of Stones';
												break;	
											case 'Attribute Value 1':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978710_Number of Stones');
												break;
											case 'Attribute Name 2':
													$column[$i][] ='978010_Metal Type';
												break;	
											case 'Attribute Value 2':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978010_Metal Type');
												break;
											case 'Attribute Name 3':
												$column[$i][] ='978510_Gem Type';
												break;	
											case 'Attribute Value 3':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978510_Gem Type');
												break;
											case 'Attribute Name 4':
													$column[$i][] ='7365_Necklace Type';
												break;	
											case 'Attribute Value 4':
													$column[$i][] =$this->getAttributValue($associatedProduct,'7365_Necklace Type');
												break;
											case 'Attribute Name 5':
													$column[$i][] ='6206_Gender';
												break;	
											case 'Attribute Value 5':
													$column[$i][] =$this->getAttributValue($associatedProduct,'6206_Gender');
												break;
											case 'Attribute Name 6':
													$column[$i][] ='978410_Gem Color';
												break;	
											case 'Attribute Value 6':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978410_Gem Color');
												break;
											case 'Attribute Name 7':
													$column[$i][] ='978310_Chain Style';
												break;	
											case 'Attribute Value 7':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978310_Chain Style');
												break;
											case 'Attribute Name 8':
													$column[$i][] ='3509_Total Carat Weight*';
												break;	
											case 'Attribute Value 8':
													$column[$i][] =$this->getAttributValue($associatedProduct,'3509_Total Carat Weight*');
												break;
											case 'Attribute Name 9':
													$column[$i][] ='1015910_Chain Included';
												break;	
											case 'Attribute Value 9':
													$column[$i][] =$this->getAttributValue($associatedProduct,'1015910_Chain Included');
												break;
											case 'Attribute Name 10':
													$column[$i][] ='6210_Birthstone Month';
												break;	
											case 'Attribute Value 10':
													$column[$i][] =$this->getAttributValue($associatedProduct,'6210_Birthstone Month');
												break;
											case 'Attribute Name 11':
													$column[$i][] ='999810_Pendant & Necklace Style';
												break;	
											case 'Attribute Value 11':
													$column[$i][] =$this->getAttributValue($associatedProduct,'999810_Pendant & Necklace Style');
												break;
											case 'Attribute Name 12':
													$column[$i][] ='978610_Lab Created or Natural';
												break;	
											case 'Attribute Value 12':
													$column[$i][] =$this->getAttributValue($associatedProduct,'978610_Lab Created or Natural');
												break;		
											case 'Attribute Name 13':
												if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
													$column[$i][] ='3524_Gold Karat';
												}	
												else{
													$column[$i][] ='';
												}	
												break;	
											case 'Attribute Value 13':
												if(strpos($associatedProduct->getAttributeText('metal1_type'),'Gold')!== false){
													$column[$i][] =$this->getAttributValue($associatedProduct,'3524_Gold Karat',$ringSize);
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'Attribute Name 14':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] = '888310_Diamond Clarity';
													}
												break;	
											case 'Attribute Value 14':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888310_Diamond Clarity',$ringSize);
													}
												break;
											case 'Attribute Name 15':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
		
														$column[$i][] = '888410_Diamond Color';
													}
												break;	
											case 'Attribute Value 15':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888410_Diamond Color',$ringSize);
													}
												break;
											case 'Attribute Name 16':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] = '888210_Diamond Carat Weight';
													}
												break;
											case 'Attribute Value 16':
													if($associatedProduct->getAttributeText('stone1_type')=='Gemstone'){
														$column[$i][] = '';	
													}
													else{
														$column[$i][] =$this->getAttributValue($associatedProduct,'888210_Diamond Carat Weight',$ringSize);
													}
												break;
											default:	
												$column[$i][] = '';
											
										}
									}
									}
								}
							$skuCount = count($sku);				
					}
					
						//$i++;
		}
		for($i=0;$i<$skuCount;$i++){
			//if($column[1][$i]!=''){
				$listCombination[$i] = $column[1][$i].'&&&'.$column[2][$i].'&&&'.$column[3][$i].'&&&'.$column[4][$i].'&&&'.$column[5][$i].'&&&'.$column[6][$i].'&&&'.$column[7][$i].'&&&'.$column[8][$i].'&&&'.$column[9][$i].'&&&'.$column[10][$i].'&&&'.$column[11][$i].'&&&'.$column[12][$i].'&&&'.$column[13][$i].'&&&'.$column[14][$i].'&&&'.$column[15][$i].'&&&'.$column[16][$i].'&&&'.$column[17][$i].'&&&'.$column[18][$i].'&&&'.$column[19][$i].'&&&'.$column[20][$i].'&&&'.$column[21][$i].'&&&'.$column[22][$i].'&&&'.$column[23][$i].'&&&'.$column[24][$i].'&&&'.$column[25][$i].'&&&'.$column[26][$i].'&&&'.$column[27][$i].'&&&'.$column[28][$i].'&&&'.$column[29][$i].'&&&'.$column[30][$i].'&&&'.$column[31][$i].'&&&'.$column[32][$i].'&&&'.$column[33][$i].'&&&'.$column[34][$i].'&&&'.$column[35][$i].'&&&'.$column[36][$i].'&&&'.$column[37][$i].'&&&'.$column[38][$i].'&&&'.$column[39][$i].'&&&'.$column[40][$i].'&&&'.$column[41][$i].'&&&'.$column[42][$i].'&&&'.$column[43][$i].'&&&'.$column[44][$i].'&&&'.$column[45][$i].'&&&'.$column[46][$i].'&&&'.$column[47][$i].'&&&'.$column[48][$i].'&&&'.$column[49][$i].'&&&'.$column[50][$i].'&&&'.$column[51][$i].'&&&'.$column[52][$i].'&&&'.$column[53][$i].'&&&'.$column[54][$i].'&&&'.$column[55][$i].'&&&'.$column[56][$i].'&&&'.$column[57][$i].'&&&'.$column[58][$i].'&&&'.$column[59][$i].'&&&'.$column[60][$i].'&&&'.$column[61][$i].'&&&'.$column[62][$i].'&&&'.$column[63][$i].'&&&'.$column[64][$i].'&&&'.$column[65][$i].'&&&'.$column[66][$i].'&&&'.$column[67][$i].'&&&'.$column[68][$i].'&&&'.$column[69][$i].'&&&'.$column[70][$i].'&&&'.$column[71][$i].'&&&'.$column[72][$i].'&&&'.$column[73][$i].'&&&'.$column[74][$i].'&&&'.$column[75][$i].'&&&'.$column[76][$i].'&&&'.$column[77][$i].'&&&'.$column[78][$i].'&&&'.$column[79][$i].'&&&'.$column[80][$i].'&&&'.$column[81][$i].'&&&'.$column[82][$i].'&&&'.$column[83][$i].'&&&'.$column[84][$i].'&&&'.$column[85][$i].'&&&'.$column[86][$i].'&&&'.$column[87][$i].'&&&'.$column[88][$i].'&&&'.$column[89][$i].'&&&'.$column[90][$i].'&&&'.$column[91][$i].'&&&'.$column[92][$i].'&&&'.$column[93][$i];
			//}
		}
		return $listCombination;
	}
	
	// Get All Stones Details	 
	function getStones($product){
		  $stones = array();
		  for($i = 1; $i <= $product->getStoneVariationCount(); $i++){
		   //$stoneName = $product->getAttributeText('stone'.$i.'_shape').'-'.$product->getAttributeText('stone'.$i.'_name').'-'.$product->getAttributeText('stone'.$i.'_grade');
		   //if(!isset($stones[$stoneName])){
			$stones[$i] = array(
			 'name'  => $product->getAttributeText('stone'.$i.'_name'),
			 'shape'  => $product->getAttributeText('stone'.$i.'_shape'),
			 'size'  => $product->getAttributeText('stone'.$i.'_size'),
			 'grade'  => $product->getAttributeText('stone'.$i.'_grade'),
			 'type'  => $product->getAttributeText('stone'.$i.'_type'),
			 'cut'  => $product->getAttributeText('stone'.$i.'_cut'),
			 'weight' => $product->getData('stone'.$i.'_weight'),
			 'count'  => $product->getData('stone'.$i.'_count'),
			 'setting'  => $product->getAttributeText('stone'.$i.'_setting'),
			 );
		  }
		  
		  // converting associative array to numeric array
		  return array_values($stones);
 	}
	
	public function getCategoryValue($stoneType,$metalType,$prodType,$catVal){
		// For Rings
		if($prodType=='Rings'){
			if($stoneType=='Gemstone'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Rings|Rings - Gemstones - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '722110'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Rings|Rings - Gemstones - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '722210'; 
					}
				}
			}
			else if($stoneType=='Diamond'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Rings|Rings - Diamonds - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '722310'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Rings|Rings - Diamonds - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '722410'; 
					}
				}
			}
		}
		
		
		// For Pendents
		if($prodType=='Pendant'){
			if($stoneType=='Gemstone'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Pendants & Necklaces|Pendants & Necklaces - Gemstones - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '731810'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Pendants & Necklaces|Pendants & Necklaces - Gemstones - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '731910'; 
					}
				}
			}
			else if($stoneType=='Diamond'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Pendants & Necklaces|Pendants & Necklaces - Diamonds - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '731610'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Pendants & Necklaces|Pendants & Necklaces - Diamonds - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '731710'; 
					}
				}
			}
		}
		
		// For Earrings
		if($prodType=='Earrings'){
			if($stoneType=='Gemstone'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Earrings|Earrings - Gemstones - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '713810'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Earrings|Earrings - Gemstones - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '713910'; 
					}
				}
			}
			else if($stoneType=='Diamond'){
				if($metalType=='14K Yellow Gold' || $metalType=='18K Yellow Gold' || $metalType=='14K White Gold' || $metalType=='18K White Gold'){
					if($catVal=='cat_name'){
						return 'Jewelry|Earrings|Earrings - Diamonds - Gold Setting';
					}
					else if($catVal=='cat_id'){
						return '713610'; 
					}
				}
				else{
					if($catVal=='cat_name'){
						return 'Jewelry|Earrings|Earrings - Diamonds - Other Metal Setting';
					}
					else if($catVal=='cat_id'){
						return '713710'; 
					}
				}
			}
		}
		
		// For Bracelet
		if($prodType=='Bracelet'){
			return '';
		}
	}
	
	public function getAttributValue($_product,$catval,$ringSize=0){
		switch ($catval) {
			case '978710_Number of Stones':
				$_prods = $this->getStones($_product);
				foreach($_prods as $_prod){
					$numberOfStones += $_prod['count'];
				}
				return $numberOfStones;
				break;	
			case '978810_Primary Stone Carat Weight':
				return $_product->getStone1Weight();
				break;	
			/*case '888210_Diamond Carat Weight':
				if(isset($str)){
					unset($str);
				}
				$_prods = $this->getStones($_product);
				$totDiaWt=0; 
				foreach($_prods as $_prod){
					$str='';
					if($_prod['type']=='Diamond'){
						if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
							$str = $totDiaWt+$_prod['weight'];
							$totDiaWt = $str;
						}
					}
				}
				return $totDiaWt;
				break;*/
			case '3509_Total Carat Weight*':
				if(isset($str)){
					unset($str);
				}
				if(isset($totalCaratWeight)){
					unset($totalCaratWeight);
				}
				$_prods = $this->getStones($_product);
				$totDiaWt=0;
				foreach($_prods as $_prod){
					$str='';
						if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
							$str = $totalCaratWeight+$_prod['weight'];
							$totalCaratWeight = $str;
						}
				}
				return $totalCaratWeight;
				break;	
			case '952210_Ring Size':
				return $ringSize;
				break;	
			case '978410_Gem Color':
				if($_product->getAttributeText('stone1_name')=='Emerald'){
					return 'Green';	
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Sapphire' || $_product->getAttributeText('stone1_name')=='Sapphire'){
					return 'Blue';
				}
				else if($_product->getAttributeText('stone1_name')=='Ruby'){
					return 'Red';
				}
				else if($_product->getAttributeText('stone1_name')=='Tanzanite'){
					return 'Blue';
				}
				else if($_product->getAttributeText('stone1_name')=='Amethyst'){
					return 'Purple';
				}
				else if($_product->getAttributeText('stone1_name')=='Opal'){
					return 'Multicolored';
				}
				else if($_product->getAttributeText('stone1_name')=='Garnet'){
					return 'Red';
				}
				else if($_product->getAttributeText('stone1_name')=='Aquamarine'){
					return 'Blue';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Pearl')!== false){
					return 'Pink';
				}
				else if($_product->getAttributeText('stone1_name')=='Peridot'){
					return 'Green';
				}
				else if($_product->getAttributeText('stone1_name')=='Citrine'){
					return 'Yellow';
				}
				else if($_product->getAttributeText('stone1_name')=='Pink Sapphire'){
					return 'Pink';
				}
				else if($_product->getAttributeText('stone1_name')=='White Sapphire'){
					return 'White';
				}
				else if($_product->getAttributeText('stone1_name')=='Diamond'){
					return 'White';
				}
				else if($_product->getAttributeText('stone1_name')=='Black Diamond' || $_product->getAttributeText('stone1_name')=='Enhanced Black Diamond'){
					return 'Black';
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Diamond' || $_product->getAttributeText('stone1_name')=='Enhanced Blue Diamond'){
					return 'Blue';
				}
				else if($_product->getAttributeText('stone1_name')=='Onyx' || $_product->getAttributeText('stone1_name')=='Black Onyx'){
					return 'Black';
				}
				else if($_product->getAttributeText('stone1_name')=='Moissanite'){
					return 'White';
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Sapphire' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'Blue';
				}
				else if($_product->getAttributeText('stone1_name')=='Emerald' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'Green';
				}
				else if($_product->getAttributeText('stone1_name')=='Ruby' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'Red';
				}
				break;	
			case '978010_Metal Type':
				if(isset($metalType)){
					unset($metalType);
				}
				if($_product->getTypeId()=='configurable'){
					if($_product->getAttributeText('default_metal1_type')=='Platinum'){
						$metalType='Platinum';
					}
					else if(strpos($_product->getAttributeText('default_metal1_type'),'Gold')!== false){
						$metalType='Solid gold';
					}
					else if($_product->getAttributeText('default_metal1_type')=='Silver'){
						$metalType='Sterling silver';
					}
					else{
						$metalType='';
					}
				}
				else{
					if($_product->getAttributeText('metal1_type')=='Platinum'){
						$metalType='Platinum';
					}
					else if(strpos($_product->getAttributeText('metal1_type'),'Gold')!== false){
						$metalType='Solid gold';
					}
					else if($_product->getAttributeText('metal1_type')=='Silver'){
						$metalType='Sterling silver';
					}
					else{
						$metalType='';
					}
				}
				return $metalType;
				break;	
			case '6206_Gender':
				if($_product->getAttributeText('jewelry_type')=='Ring'){
					return 'Unisex';	
				}
				else if($_product->getAttributeText('jewelry_type')=='Earrings'){
					return 'Girls';
				}
				else if($_product->getAttributeText('jewelry_type')=='Pendant'){
					return 'Girls';
				}
				else if($_product->getAttributeText('jewelry_type')=='Bracelet'){
					return 'Unisex';
				}
				else{
					return '';
				}
				break;	
			case '6210_Birthstone Month':
				if($_product->getAttributeText('stone1_name')=='Emerald'){
					return 'May';	
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Sapphire' || $_product->getAttributeText('stone1_name')=='Sapphire'){
					return 'September';
				}
				else if($_product->getAttributeText('stone1_name')=='Ruby'){
					return 'July';
				}
				else if($_product->getAttributeText('stone1_name')=='Tanzanite' || $_product->getAttributeText('stone1_name')=='Turquoise'){
					return 'December';
				}
				else if($_product->getAttributeText('stone1_name')=='Amethyst'){
					return 'February';
				}
				else if($_product->getAttributeText('stone1_name')=='Opal' || $_product->getAttributeText('stone1_name')=='Tourmaline'){
					return 'October';
				}
				else if($_product->getAttributeText('stone1_name')=='Garnet'){
					return 'January';
				}
				else if($_product->getAttributeText('stone1_name')=='Aquamarine'){
					return 'March';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Pearl')!== false){
					return 'June';
				}
				else if($_product->getAttributeText('stone1_name')=='Peridot' || $_product->getAttributeText('stone1_name')=='Black Onyx'){
					return 'August';
				}
				else if($_product->getAttributeText('stone1_name')=='Citrine' || $_product->getAttributeText('stone1_name')=='Topaz'){
					return 'November';
				}
				else if($_product->getAttributeText('stone1_name')=='Pink Sapphire' || $_product->getAttributeText('stone1_name')=='White Sapphire'){
					return 'September';
				}
				else if($_product->getAttributeText('stone1_name')=='Diamond'){
					return 'April';
				}
				else if($_product->getAttributeText('stone1_name')=='Black Diamond' || $_product->getAttributeText('stone1_name')=='Enhanced Black Diamond'){
					return 'April';
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Diamond' || $_product->getAttributeText('stone1_name')=='Quartz'){
					return 'April';
				}
				else if($_product->getAttributeText('stone1_name')=='Onyx'){
					return 'N/A';
				}
				else if($_product->getAttributeText('stone1_name')=='Moissanite'){
					return 'N/A';
				}
				else if($_product->getAttributeText('stone1_name')=='Blue Sapphire' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'September';
				}
				else if($_product->getAttributeText('stone1_name')=='Emerald' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'May';
				}
				else if($_product->getAttributeText('stone1_name')=='Ruby' && $_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'July';
				}
				break;	
			case '978510_Gem Type':
				if($_product->getAttributeText('stone1_name')=='Blue Sapphire' || $_product->getAttributeText('stone1_name')=='Pink Sapphire' || $_product->getAttributeText('stone1_name')=='White Sapphire'){
					return 'Sapphire';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Pearl')!== false){
					return 'Pearl';
				}
				else if($_product->getAttributeText('stone1_name')=='Black Onyx'){
					return 'Onyx';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Topaz')!== false){
					return 'Topaz';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Diamond')!== false){
					return 'Diamond';
				}
				else if(strpos($_product->getAttributeText('stone1_name'),'Moissanite')!== false){
					return 'Moissanite';
				}
				else{
					return $_product->getAttributeText('stone1_name');
				}
				break;	
			case '992910_Style':
				return $this->getStyles($_product->getAttributeText('jewelry_styles'),$_product->getAttributeText('jewelry_type'));
				break;	
			case '3524_Gold Karat':
				if(isset($goldCarat)){
					unset($goldCarat);
				}
				if($_product->getTypeId()=='configurable'){
					if(strpos($_product->getAttributeText('default_metal1_type'),'10K')!== false){
						$goldCarat = '10k';	
					}
					else if(strpos($_product->getAttributeText('default_metal1_type'),'12K')!== false){
						$goldCarat = '12k';	
					}
					else if(strpos($_product->getAttributeText('default_metal1_type'),'14K')!== false){
						$goldCarat = '14k';	
					}
					else if(strpos($_product->getAttributeText('default_metal1_type'),'18K')!== false){
						$goldCarat = '18k';	
					}
					else{
						$goldCarat = '';
					}
				}
				else{
					if(strpos($_product->getAttributeText('metal1_type'),'10K')!== false){
						$goldCarat = '10k';	
					}
					else if(strpos($_product->getAttributeText('metal1_type'),'12K')!== false){
						$goldCarat = '12k';	
					}
					else if(strpos($_product->getAttributeText('metal1_type'),'14K')!== false){
						$goldCarat = '14k';	
					}
					else if(strpos($_product->getAttributeText('metal1_type'),'18K')!== false){
						$goldCarat = '18k';	
					}
					else{
						$goldCarat = '';
					}
				}
				return $goldCarat;
				break;
			case '888310_Diamond Clarity':
				if(strpos($_product->getAttributeText('stone1_name'),'Diamond')!==false){
					$stName = $_product->getAttributeText('stone1_name');
					$stGrade = $_product->getAttributeText('stone1_grade');
					$collection = Mage::getModel('feeds/amazon')->getCollection()
						->addFieldToFilter('stone_name',array('eq' => $stName))
						->addFieldToFilter('stone_grade',array('eq' => $stGrade))->getData();
				}
				if($collection[0]['stone_clarity']=='I2' || $collection[0]['stone_clarity']=='I1' || $collection[0]['stone_clarity']=='I1-I3' || $collection[0]['stone_clarity']=='I1,I3' || $collection[0]['stone_clarity']=='I2,I3' || $collection[0]['stone_clarity']=='I2-I3' || $collection[0]['stone_clarity']=='I4' || $collection[0]['stone_clarity']=='I1-I2' || $collection[0]['stone_clarity']=='I1-I4' || $collection[0]['stone_grade']=='A' || $collection[0]['stone_grade']=='AA'){
					return 'I';
				}
				else if($collection[0]['stone_clarity']=='VS'){
					return 'VS';
				}
				else if($collection[0]['stone_clarity']=='SI2' || $collection[0]['stone_grade']=='AAA'){
					return 'SI';
				}
				else{
					return '';	
				}
				break;
			case '888410_Diamond Color':
				if(strpos($_product->getAttributeText('stone1_name'),'Diamond')!==false){
					$stName = $_product->getAttributeText('stone1_name');
					$stGrade = $_product->getAttributeText('stone1_grade');
					$collection = Mage::getModel('feeds/amazon')->getCollection()
						->addFieldToFilter('stone_name',array('eq' => $stName))
						->addFieldToFilter('stone_grade',array('eq' => $stGrade))->getData();
				}
				if($collection[0]['stone_color']=='Black (Color Enhanced)' || $collection[0]['stone_color']=='Black' || $collection[0]['stone_color']=='Blue (Color Enhanced)' || $collection[0]['stone_color']=='Blue' || $collection[0]['stone_color']=='Yellow'){
					return 'Colored Diamond';
				}
				else if($collection[0]['stone_color']=='GH Black (Color Enhanced)' || $collection[0]['stone_color']=='GH' || $collection[0]['stone_color']=='G-H' || $collection[0]['stone_color']=='GH, Black (Color Enhanced)' || $collection[0]['stone_color']=='GH, Blue (Color Enhanced)' || $collection[0]['stone_color']=='H' || $collection[0]['stone_color']=='I' || $collection[0]['stone_color']=='G' || $collection[0]['stone_color']=='J' || $collection[0]['stone_color']=='JK'){
					return 'Near Colorless G-J';
				}
				else{
					return '';
				}
				break;
			case '888210_Diamond Carat Weight':
				$totDiaWt = $_product->getAttributeText('stone1_weight');
				if($totDiaWt>=1 && $totDiaWt< 1.5){
					return '1 up to 1-1/2';
				}
				else if($totDiaWt>= 0.1 && $totDiaWt< 0.25){
					return '1/10 up to 1/4';
				}
				else if($totDiaWt>= 0.25 && $totDiaWt< 0.5){
					return '1/4 up to 1/2';
				}
				else if($totDiaWt>= 0.5 && $totDiaWt< 0.75){
					return '1/2 up to 3/4';
				}
				else if($totDiaWt>= 0.75 && $totDiaWt< 1){
					return '3/4 up to 1';
				}
				else if($totDiaWt>= 1.5 && $totDiaWt< 2){
					return '1-1/2 up to 2';
				}
				else if($totDiaWt>= 2 && $totDiaWt< 3){
					return '2 up to 3';
				}
				else if($totDiaWt>=3){
					return '3 and over';
				}
				else{
					return 'Diamond accent';
				}
				break;
			case '983410_Earring Style':
				if(count($_product->getAttributeText('jewelry_styles'))>1){
					return $this->getStyles($_product->getAttributeText('jewelry_styles'),$_product->getAttributeText('jewelry_type'));	
				}
				else{
					$jwarray = array();
					if(isset($jwarray)){
						unset($jwarray);
					}
					$jwarray = $_product->getAttributeText('jewelry_styles');
					return $this->getStyles($jwarray,$_product->getAttributeText('jewelry_type'));
				}
				break;	
			case '978610_Lab Created or Natural':
				if($_product->getAttributeText('stone1_grade')=='Lab Created'){
					return 'Lab created';
				}
				else{
					return 'Natural';
				}
				break;	
			case '7365_Necklace Type':
				return 'Necklace';
				break;	
			case '978310_Chain Style':
				return 'Cable';
				break;
			case '1015910_Chain Included';
				return 'Yes';
				break;	
			case '999810_Pendant & Necklace Style':
				if(count($_product->getAttributeText('jewelry_styles'))>1){
					return $this->getStyles($_product->getAttributeText('jewelry_styles'),$_product->getAttributeText('jewelry_type'));
				}
				else{
					$pjwarray = array();
					if(isset($pjwarray)){
						unset($pjwarray);
					}
					$pjwarray = $_product->getAttributeText('jewelry_styles');
					return $this->getStyles($pjwarray,$_product->getAttributeText('jewelry_type'));
				}
				break;	
			default:
				return '';
		}
	}
	
	public function getStyles($jewelryStyle,$jewelryType){
		if($jewelryType=='Ring'){
			if(in_array('Solitaire',$jewelryStyle)){
				return 'Solitaires';
			}
			else if(in_array('Three Stone',$jewelryStyle)){
				return 'Three-stone rings';
			}
			else if(in_array('Eternity',$jewelryStyle)){
				return 'Eternity rings';
			}
			else{
				return 'Accents';
			}
		}
		else if($jewelryType=='Earrings'){
			if(in_array('Studs',$jewelryStyle)){
				return 'Stud';
			}
			else if(in_array('Solitaire',$jewelryStyle)){
				return 'Stud';
			}
			else if(in_array('Halo',$jewelryStyle)){
				return 'Stud';
			}
			else if(in_array('Cocktail',$jewelryStyle)){
				return 'Stud';
			}
			else if(in_array('Star',$jewelryStyle)){
				return 'Stud';
			}
			else if(in_array('Hoops',$jewelryStyle)){
				return 'Hoop';
			}
			else if(in_array('Dangle',$jewelryStyle)){
				return 'Drop or Dangle';
			}
			else if(in_array('Leverback',$jewelryStyle)){
				return 'Drop or Dangle';
			}
			else if(in_array('Journey',$jewelryStyle)){
				return 'Drop or Dangle';
			}
			else if(in_array('Fashion',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Designer',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Heart',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Art Deco',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Five Stone',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Critters',$jewelryStyle)){
				return 'Post';
			}
			else if(in_array('Floral',$jewelryStyle)){
				return 'Post';
			}
			else{
				return '';
			}
		}
		else if($jewelryType=='Pendant'){
			if(in_array('Solitaire',$jewelryStyle)){
				return 'Solitaire';
			}
			else{
				return 'Fashion';
			}
		}
	}
}
