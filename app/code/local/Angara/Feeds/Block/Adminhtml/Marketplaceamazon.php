<?php
error_reporting(0);
class Angara_Feeds_Block_Adminhtml_Marketplaceamazon extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  	$stoneType1='';
		$stoneType2='';
		$stoneType3='';
		$stoneWeight1='';
		$stoneWeight1='';
		$stoneWeight1='';
		$totalGemWt = '';
		$totalDiaWt = '';
		$countColumn='';
		$amazonData=array();
		$listCombination=array();
		
		if(Mage::getStoreConfig('feeds/settings/active')==1){
	  	$totalColumnCount = 0;
		$isUkEnabled = Mage::getStoreConfig('feeds/uk_feeds/is_uk_enabled');
		if($isUkEnabled==1){
			$amazonDatas = Mage::getModel('feeds/feeds')->getCollection()
						->addFieldToFilter('marketplace','marketplace-amazon-uk')->getData();
		}
		else{
			$amazonDatas = Mage::getModel('feeds/feeds')->getCollection()
						->addFieldToFilter('marketplace','marketplace-amazon-us')->getData();
		}
		$amazonData = array_shift($amazonDatas);			
		$entityId = $amazonData['feeds_id'];
		$url = Mage::helper('adminhtml')->getUrl("feeds/adminhtml_feeds/edit/id/".$entityId);
		if($amazonData['status']==2){
			Mage::getSingleton('core/session')->addError(Mage::helper('feeds')->__( 'You Can not generate disabled feeds.' ));
			//echo "You Can not generate disabled feeds.";
			Mage::app()
            	->getResponse()
                ->setRedirect($url);
		}
		//$allData=explode(',',$amazonData['headings3']);						
				$k=0;
				foreach($amazonData as $key=>$val){
					if($val !=''){
						$countColumn = $k+count($val);
						$k=$countColumn;
					}
				}
				$i =1;
				$writeFile='marketplace-amazon.csv';
				$fp = fopen($writeFile, 'w');
				if($amazonData['headings1']!=''){
					fputcsv($fp,split(',',$amazonData['headings1']));
				}
				if($amazonData['headings2']!=''){
					fputcsv($fp,split(',',$amazonData['headings2']));
				}
				if($amazonData['headings3']!=''){;
					fputcsv($fp,split(',',$amazonData['headings3']));
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
							$_productCollection = Mage::getSingleton('catalog/product')->load($productId);//getCollection() for all
							$listCombination[] = $this->generateFeeds($countColumn,$amazonData,$_productCollection);
						}
					}
				}
				else{
						//SELECT `e`.* FROM `catalog_product_entity` AS `e` WHERE (`sku` NOT LIKE 'AM%') AND (`sku` NOT LIKE '%_OLD') AND (`sku` NOT IN('JA0050','INS001','EMOP0001SC','OP0001SC'))
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
							->addAttributeToSelect('*');
						$productCollections->load();
						//$productCollections = Mage::getModel('catalog/product')->getCollection();
						$productCollections->getSelect()->where("sku NOT LIKE 'AM%' AND (`sku` NOT LIKE '%_OLD') AND (`sku` NOT LIKE 'FR%') AND (`sku` NOT LIKE 'AGIF%') AND (`sku` NOT LIKE 'SC0%') AND (`sku` NOT IN('JA0050','INS001','EMOP0001SC','OP0001SC','MANUAL'))");
						//$productCollections->getSelect()->limit(5);
						$productCount = count($productCollections);
						//echo $productCollections->getSelect()->__toString();exit;
						// Product Collection to generate feed excluded Free,OLD and mothers sku's
						foreach($productCollections as $productCollection){
							$_productCollection = Mage::getModel('catalog/product')->load($productCollection->getId());
							//echo 'hhhhh===='.$productCollection->getId();exit;
							//var_dump($_productCollection->getData());exit;
							$listCombination[] = $this->generateFeeds($countColumn,$amazonData,$_productCollection);	
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
		//var_dump($_productCollection->getData());exit;//MediaGalleryImages
		//echo $_productCollection->getAttributeText('jewelry_type');exit;
		$isUkEnabled = Mage::getStoreConfig('feeds/uk_feeds/is_uk_enabled');
		for($i=1;$i<=$countColumn;$i++){
					//foreach($allData as $val){

					$searchKeywords = explode(', ',$_productCollection->getMetaKeyword());	
					$_productCollection->getTypeInstance(true)->getSetAttributes($_productCollection);
					$galleryData = $_productCollection->getData('media_gallery');
					if($_productCollection->getTypeId()!='configurable' && $_productCollection->getVisibility()=='4'){ 		// Get All Data of Simple Products with catalog,search visibility(no variation)
						if($_productCollection->getAttributeText('jewelry_type')=='Ring'){
							if($isUkEnabled==1){
							$ringSizeArray = array(
										'F','G','H','I','J1/2','K1/2','L1/2','M1/2','N1/2','O1/2','P1/2','Q1/2','R1/2','S1/2','T1/2','U1/2','V1/2','W1/2','X1/2','Z'
									);
							}
							else{
								$ringSizeArray = array(
											'3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13'
										);
							}
								foreach($ringSizeArray as $ringSize){
									switch ($amazonData['column_'.$i]) {
									case 'item_sku':
										$sku[] = $column[$i][] = $_productCollection->getSku().'-'.$ringSize;
										break;
									case 'item_name':
										$column[$i][] = $_productCollection->getShortDescription();
										break;	
									case 'manufacturer':
										$column[$i][] = 'Angara';
										break;
									case 'brand_name':
										$column[$i][] = 'Angara.com';
										break;
									case 'product_description':
											$stone1nm = $_productCollection->getAttributeText('stone1_name');
											$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
											$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
											$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1grd = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1grd = $_productCollection->getAttributeText('stone1_Grade');
											}
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2grd = $_productCollection->getAttributeText('stone2_grade');	
											}
											else{
												$stone2grd = $_productCollection->getAttributeText('stone2_Grade');
											}
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3grd = $_productCollection->getAttributeText('stone3_grade');
											}
											else{
												$stone3grd = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$_productCollection->getSku(),'description');
										break;
									case 'update_delete':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
										break;
									case 'currency':
										if($isUkEnabled==1){
											$column[$i][] = 'GBP';
										}
										else{
											$column[$i][] = 'USD';
										}
										break;
									case 'list_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();	
										}
										break;
									case 'fulfillment_latency':
										$leadTime = '';
										$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
										$column[$i][] = $_productCollection->getVendorLeadTime()+$leadTime;
										break;
									case 'standard_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;		
									case 'recommended_browse_nodes':
											$column[$i][] = $this->getBrowseNodes($_productCollection->getAttributeText('jewelry_styles'),$_productCollection->getAttributeText('jewelry_type'));
										break;
									case 'seasons':
											$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
										break;		
									case 'display_dimensions_unit_of_measure':
										$column[$i][] = 'MM';
										break;
									case 'catalog_number':
										$column[$i][] = $_productCollection->getSku().'-rings';
										break;
									case 'generic_keywords1':
										if($searchKeywords[0]!=''){
											$column[$i][] = $searchKeywords[0];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords2':
										if($searchKeywords[1]!=''){
											$column[$i][] = $searchKeywords[1];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords3':
										if($searchKeywords[2]!=''){
											$column[$i][] = $searchKeywords[2];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords4':
										if($searchKeywords[3]!=''){
											$column[$i][] = $searchKeywords[3];
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords5':
										if($searchKeywords[4]!=''){
											$column[$i][] = $searchKeywords[4];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'main_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'swatch_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'other_image_url1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'other_image_url2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'other_image_url3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'other_image_url4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'other_image_url5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'other_image_ur6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'other_image_url7':
										$column[$i][] = $galleryData['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][7]['file']:'';
										break;
									case 'other_image_url8':
										$column[$i][] = $galleryData['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][8]['file']:'';
										break;								
									case 'prop_65':
										$column[$i][] = 'FALSE';
										break;
									case 'designer':
										$column[$i][] = 'Angara';
										break;
									case 'total_metal_weight_unit_of_measure':
										$column[$i][] = 'GR';
										break;
									case 'total_diamond_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
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
										if($totDiaWt!=''){
											$column[$i][] = $totDiaWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'total_gem_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
										$totGemWt=0;
										foreach($_prods as $_prod){
											$str='';
											if($_prod['type']=='Gemstone'){
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
													$str = $totGemWt+$_prod['weight'];
													$totGemWt = $str;
												}
											}
										}
										if($totGemWt!=''){
											$column[$i][] = $totGemWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'metal_type':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_type');
										break;
									case 'metal_stamp':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_stamp');
										break;
									case 'setting_type':
										if($_productCollection->getStone1Setting()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
										}else{
											$column[$i][] = '';
										}
										break;
									case 'number_of_stones':
										if(isset($countStone)){
											unset($countStone);
										}
										$_prods = $this->getStones($_productCollection);
										$totCount=0;
										foreach($_prods as $_prod){
											$countStone='';
												if(trim($_prod['count'])!=''){
													$countStone = $totCount+$_prod['count'];
													$totCount = $countStone;
												}
										}
										if($totCount!=''){
											$column[$i][] = $totCount;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'is_resizable':
										$column[$i][] = 'TRUE';
										break;
									case 'ring_sizing_lower_range':
										if($isUkEnabled==1){
											$column[$i][] = 'F';	
										}
										else{
											$column[$i][] = '3';	
										}
										break;
									case 'ring_sizing_upper_range':
										if($isUkEnabled==1){
											$column[$i][] = 'Z';	
										}
										else{
											$column[$i][] = '13';	
										}
										break;
									case 'ring_size':
										$column[$i][] = $ringSize;
										break;	
									case 'gem_type1':
										if($_productCollection->getStone1Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type2':
										if($_productCollection->getStone2Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone2_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type3':
										if($_productCollection->getStone3Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone3_name');
										}
										else{
											$column[$i][]='';
										}
										break;																
									case 'stone_cut1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'cut');
										break;
									case 'stone_cut2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'cut');
										break;
									case 'stone_cut3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'cut');
										break;
									case 'stone_color1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'color');
										break;
									case 'stone_color2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'color');
										break;
									case 'stone_color3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'color');
										break;
									case 'stone_clarity1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'clarity');
										break;
									case 'stone_clarity2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'clarity');
										break;
									case 'stone_clarity3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'clarity');
										break;
									case 'stone_shape1':
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
										break;
									case 'stone_shape2':
										$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
										break;
									case 'stone_shape3':
										$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
										break;
									case 'stone_creation_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'creation');
										break;
									case 'stone_creation_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'creation');
										break;
									case 'stone_creation_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'creation');
										break;
									case 'stone_treatment_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'treatment');
										break;
									case 'stone_treatment_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'treatment');
										break;
									case 'stone_treatment_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'treatment');
										break;
									case 'stone_dimensions_unit_of_measure1':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure2':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure3':
										$column[$i][] = 'MM';
										break;
									/*case 'total_diamond_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	
									case 'total_gem_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	*/
									case 'item_display_diameter':
										$column[$i][] = '17';
										break;
									case 'item_display_height':
										$column[$i][] = '25';
										break;
									case 'item_display_width':
										$column[$i][] = '8';
										break;
									case 'item_display_length':
										$column[$i][] = '21';
										break;				
									case 'stone_height1':
										//$column[$i][] = $this->getStoneDiamentions($_productCollection->getAttributeText('stone1_size'),'height1');
										$column[$i][] = $this->getMergedSizes($_productCollection,'height1');
										break;
									case 'stone_height2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height2');
										break;
									case 'stone_height3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height3');
										break;
									case 'stone_length1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length1');
										break;
									case 'stone_length2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length2');
										break;
									case 'stone_length3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length3');
										break;
									case 'stone_width1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width1');
										break;
									case 'stone_width2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width2');
										break;
									case 'stone_width3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width3');
										break;
									case 'stone_weight1':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt1');
										break;
									case 'stone_weight2':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt2');
										break;
									case 'stone_weight3':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt3');
										break;
									case 'is_lab_created1':
										if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created2':
										if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created3':
										if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;			
									case 'pearl_type':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'pearl_shape':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'size_per_pearl':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_size');
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'number_of_pearls':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getStone1Count();
										}
										else{
											$column[$i][] = '';
										}
										break;																																
									case 'model':
										$column[$i][] = 'ANG-R-'.$_productCollection->getSku();
										break;
									case 'feed_product_type':
										$column[$i][] = 'FineRing';
										break;
									case 'item_type':
										$column[$i][] = 'rings';
										break;
									case 'parent_child':
										$column[$i][] = 'child';
										break;
									case 'parent_sku':
										$column[$i][] = $_productCollection->getSku();
										break;	
									case 'variation_theme':
										$column[$i][] = 'RingSize';	
										/*if($_productCollection->getAttributeText('stone1_grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_grade')=='Simulated'){
											$column[$i][] = 'RingSize';	
										}
										else{
											$column[$i][] = 'MetalType-RingSize';
										}*/
										break;
									case 'relationship_type':
										$column[$i][] = 'Variation';
										break;	
									case 'quantity':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
										break;
									case 'bullet_point1':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
										break;
									case 'bullet_point2':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
										break;
									case 'bullet_point3':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
										break;
									case 'bullet_point4':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
										break;
									case 'bullet_point5':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
										break;
									case 'target_audience_keywords1':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
										break;
									case 'target_audience_keywords2':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
										break;													
									case 'target_audience_keywords3':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
										break;
									case 'thesaurus_subject_keywords1':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'thesaurus_subject_keywords2':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'thesaurus_subject_keywords3':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
										break;	
									case 'thesaurus_subject_keywords4':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'thesaurus_subject_keywords5':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
										break;
									case 'specific_uses_keywords1':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'specific_uses_keywords2':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'specific_uses_keywords3':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
										break;
									case 'specific_uses_keywords4':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'specific_uses_keywords5':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
										break;		
									default:	
										$column[$i][] = '';
								}	
							}
								// Parent Sku for Ring Size
								switch ($amazonData['column_'.$i]) {
									case 'item_sku':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'item_name':
										$column[$i][] = $_productCollection->getShortDescription();
										break;	
									case 'manufacturer':
										$column[$i][] = 'Angara';
										break;
									case 'brand_name':
										$column[$i][] = 'Angara.com';
										break;
									case 'product_description':
											$stone1nm = $_productCollection->getAttributeText('stone1_name');
											$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
											$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
											$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1grd = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1grd = $_productCollection->getAttributeText('stone1_Grade');
											}
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2grd = $_productCollection->getAttributeText('stone2_grade');	
											}
											else{
												$stone2grd = $_productCollection->getAttributeText('stone2_Grade');
											}
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3grd = $_productCollection->getAttributeText('stone3_grade');
											}
											else{
												$stone3grd = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$_productCollection->getSku(),'description');
										break;
									case 'update_delete':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
										break;
									case 'display_dimensions_unit_of_measure':
										$column[$i][] = 'MM';
										break;
									case 'catalog_number':
										$column[$i][] = $_productCollection->getSku().'-rings';
										break;
									case 'generic_keywords1':
										if($searchKeywords[0]!=''){
											$column[$i][] = $searchKeywords[0];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords2':
										if($searchKeywords[1]!=''){
											$column[$i][] = $searchKeywords[1];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords3':
										if($searchKeywords[2]!=''){
											$column[$i][] = $searchKeywords[2];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords4':
										if($searchKeywords[3]!=''){
											$column[$i][] = $searchKeywords[3];
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords5':
										if($searchKeywords[4]!=''){
											$column[$i][] = $searchKeywords[4];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'main_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'swatch_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'other_image_url1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'other_image_url2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'other_image_url3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'other_image_url4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'other_image_url5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'other_image_ur6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'other_image_url7':
										$column[$i][] = $galleryData['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][7]['file']:'';
										break;
									case 'other_image_url8':
										$column[$i][] = $galleryData['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][8]['file']:'';
										break;								
									case 'prop_65':
										$column[$i][] = 'FALSE';
										break;
									case 'designer':
										$column[$i][] = 'Angara';
										break;
									case 'total_metal_weight_unit_of_measure':
										$column[$i][] = 'GR';
										break;
									case 'total_diamond_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
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
										if($totDiaWt!=''){
											$column[$i][] = $totDiaWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'total_gem_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
										$totGemWt=0;
										foreach($_prods as $_prod){
											$str='';
											if($_prod['type']=='Gemstone'){
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
													$str = $totGemWt+$_prod['weight'];
													$totGemWt = $str;
												}
											}
										}
										if($totGemWt!=''){
											$column[$i][] = $totGemWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'metal_type':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_type');
										break;
									case 'metal_stamp':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_stamp');
										break;
									case 'setting_type':
										if($_productCollection->getStone1Setting()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
										}else{
											$column[$i][] = '';
										}
										break;
									case 'number_of_stones':
										if(isset($countStone)){
											unset($countStone);
										}
										$_prods = $this->getStones($_productCollection);
										$totCount=0;
										foreach($_prods as $_prod){
											$countStone='';
												if(trim($_prod['count'])!=''){
													$countStone = $totCount+$_prod['count'];
													$totCount = $countStone;
												}
										}
										if($totCount!=''){
											$column[$i][] = $totCount;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'is_resizable':
										$column[$i][] = 'TRUE';
										break;
									case 'ring_sizing_lower_range':
										if($isUkEnabled==1){
											$column[$i][] = 'F';	
										}
										else{
											$column[$i][] = '3';	
										}
										break;
									case 'ring_sizing_upper_range':
										if($isUkEnabled==1){
											$column[$i][] = 'Z';	
										}
										else{
											$column[$i][] = '13';	
										}
										break;
									case 'gem_type1':
										if($_productCollection->getStone1Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type2':
										if($_productCollection->getStone2Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone2_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type3':
										if($_productCollection->getStone3Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone3_name');
										}
										else{
											$column[$i][]='';
										}
										break;						
									case 'stone_cut1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'cut');
										break;
									case 'stone_cut2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'cut');
										break;
									case 'stone_cut3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'cut');
										break;
									case 'stone_color1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'color');
										break;
									case 'stone_color2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'color');
										break;
									case 'stone_color3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'color');
										break;
									case 'stone_clarity1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'clarity');
										break;
									case 'stone_clarity2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'clarity');
										break;
									case 'stone_clarity3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'clarity');

										break;
									case 'stone_shape1':
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
										break;
									case 'stone_shape2':
										$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
										break;
									case 'stone_shape3':
										$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
										break;
									case 'stone_creation_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'creation');
										break;
									case 'stone_creation_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'creation');
										break;
									case 'stone_creation_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'creation');
										break;
									case 'stone_treatment_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'treatment');
										break;
									case 'stone_treatment_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'treatment');
										break;
									case 'stone_treatment_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'treatment');
										break;
									case 'stone_dimensions_unit_of_measure1':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure2':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure3':
										$column[$i][] = 'MM';
										break;
									/*case 'total_diamond_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	
									case 'total_gem_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	*/
									case 'item_display_diameter':
										$column[$i][] = '17';
										break;
									case 'item_display_height':
										$column[$i][] = '25';
										break;
									case 'item_display_width':
										$column[$i][] = '8';
										break;
									case 'item_display_length':
										$column[$i][] = '21';
										break;
									case 'stone_height1':
										//$column[$i][] = $this->getStoneDiamentions($_productCollection->getAttributeText('stone1_size'),'height1');
										$column[$i][] = $this->getMergedSizes($_productCollection,'height1');
										break;
									case 'stone_height2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height2');
										break;
									case 'stone_height3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height3');
										break;
									case 'stone_length1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length1');
										break;
									case 'stone_length2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length2');
										break;
									case 'stone_length3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length3');
										break;
									case 'stone_width1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width1');
										break;
									case 'stone_width2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width2');
										break;
									case 'stone_width3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width3');
										break;
									case 'stone_weight1':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt1');
										break;
									case 'stone_weight2':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt2');
										break;
									case 'stone_weight3':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt3');
										break;
									case 'is_lab_created1':
										if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created2':
										if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created3':
										if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;			
									case 'pearl_type':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'pearl_shape':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'size_per_pearl':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_size');
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'number_of_pearls':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getStone1Count();
										}
										else{
											$column[$i][] = '';
										}
										break;																																
									case 'model':
										$column[$i][] = 'ANG-R-'.$_productCollection->getSku();
										break;
									case 'feed_product_type':
										$column[$i][] = 'FineRing';
										break;
									case 'item_type':
										$column[$i][] = 'rings';
										break;
									case 'parent_child':
										$column[$i][] = 'Parent';
										break;
									case 'variation_theme':
										$column[$i][] = 'RingSize';	
										/*if($_productCollection->getAttributeText('stone1_grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_grade')!='Simulated'){
												$column[$i][] = 'RingSize';	
											}
											else{
												$column[$i][] = 'MetalType-RingSize';
											}*/
										break;
									/*case 'variation_theme':
										$column[$i][] = 'Variation';
										break;	*/
									/*case 'quantity':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
										break;
									case 'list_price':
										$column[$i][] = $_productCollection->getPrice();
										break;*/
									case 'bullet_point1':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
										break;
									case 'bullet_point2':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
										break;
									case 'bullet_point3':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
										break;
									case 'bullet_point4':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
										break;
									case 'bullet_point5':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
										break;
									case 'target_audience_keywords1':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
										break;
									case 'target_audience_keywords2':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
										break;													
									case 'target_audience_keywords3':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
										break;
									case 'thesaurus_subject_keywords1':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'thesaurus_subject_keywords2':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'thesaurus_subject_keywords3':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
										break;	
									case 'thesaurus_subject_keywords4':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'thesaurus_subject_keywords5':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
										break;
									case 'specific_uses_keywords1':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'specific_uses_keywords2':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'specific_uses_keywords3':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
										break;
									case 'specific_uses_keywords4':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'specific_uses_keywords5':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
										break;	
									default:	
										$column[$i][] = '';
								}
						}
						else if($_productCollection->getAttributeText('jewelry_type')=='Earrings'){
								switch ($amazonData['column_'.$i]) {
									case 'item_sku':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'item_name':
										$column[$i][] = $_productCollection->getShortDescription();
										break;	
									case 'manufacturer':
										$column[$i][] = 'Angara';
										break;
									case 'brand_name':
										$column[$i][] = 'Angara.com';
										break;
									case 'product_description':
										$stone1nm = $_productCollection->getAttributeText('stone1_name');
										$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
										$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
										$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
										if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1grd = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1grd = $_productCollection->getAttributeText('stone1_Grade');
											}
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2grd = $_productCollection->getAttributeText('stone2_grade');	
											}
											else{
												$stone2grd = $_productCollection->getAttributeText('stone2_Grade');
											}
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3grd = $_productCollection->getAttributeText('stone3_grade');
											}
											else{
												$stone3grd = $_productCollection->getAttributeText('stone3_Grade');
											}
										$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$_productCollection->getSku(),'description');
										break;
									case 'update_delete':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
										break;
									case 'currency':
										if($isUkEnabled==1){
											$column[$i][] = 'GBP';
										}
										else{
											$column[$i][] = 'USD';
										}
										break;
									case 'list_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;
									case 'fulfillment_latency':
										$leadTime = '';
										$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
										$column[$i][] = $_productCollection->getVendorLeadTime()+$leadTime;
										break;
									case 'standard_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;		
									case 'recommended_browse_nodes':
										$column[$i][] = $this->getBrowseNodes($_productCollection->getAttributeText('jewelry_styles'),$_productCollection->getAttributeText('jewelry_type'));
										break;
									case 'seasons':
											$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
										break;
									case 'display_dimensions_unit_of_measure':
										$column[$i][] = 'MM';
										break;
									case 'catalog_number':
										$column[$i][] = $_productCollection->getSku().'-earrings';
										break;
									case 'generic_keywords1':
										if($searchKeywords[0]!=''){
											$column[$i][] = $searchKeywords[0];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords2':
										if($searchKeywords[1]!=''){
											$column[$i][] = $searchKeywords[1];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords3':
										if($searchKeywords[2]!=''){
											$column[$i][] = $searchKeywords[2];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords4':
										if($searchKeywords[3]!=''){
											$column[$i][] = $searchKeywords[3];
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords5':
										if($searchKeywords[4]!=''){
											$column[$i][] = $searchKeywords[4];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'main_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'swatch_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'other_image_url1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'other_image_url2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'other_image_url3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'other_image_url4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'other_image_url5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'other_image_ur6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'other_image_url7':
										$column[$i][] = $galleryData['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][7]['file']:'';
										break;
									case 'other_image_url8':
										$column[$i][] = $galleryData['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][8]['file']:'';
										break;	
									case 'prop_65':
										$column[$i][] = 'FALSE';
										break;
									case 'designer':
										$column[$i][] = 'Angara';
										break;
									case 'total_metal_weight_unit_of_measure':
										$column[$i][] = 'GR';
										break;
									case 'total_diamond_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
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
										if($totDiaWt!=''){
											$column[$i][] = $totDiaWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'total_gem_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
										$totGemWt=0;
										foreach($_prods as $_prod){
											$str='';
											if($_prod['type']=='Gemstone'){
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
													$str = $totGemWt+$_prod['weight'];
													$totGemWt = $str;
												}
											}
										}
										if($totGemWt!=''){
											$column[$i][] = $totGemWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'metal_type':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_type');
										break;
									case 'metal_stamp':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_stamp');
										break;
									case 'setting_type':
										if($_productCollection->getStone1Setting()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
										}else{
											$column[$i][] = '';
										}
										break;
									case 'number_of_stones':
										if(isset($countStone)){
											unset($countStone);
										}
										$_prods = $this->getStones($_productCollection);
										$totCount=0;
										foreach($_prods as $_prod){
											$countStone='';
												if(trim($_prod['count'])!=''){
													$countStone = $totCount+$_prod['count'];
													$totCount = $countStone;
												}
										}
										if($totCount!=''){
											$column[$i][] = $totCount;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'gem_type1':
										if($_productCollection->getStone1Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type2':
										if($_productCollection->getStone2Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone2_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type3':
										if($_productCollection->getStone3Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone3_name');
										}
										else{
											$column[$i][]='';
										}
										break;																
									case 'stone_cut1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'cut');
										break;
									case 'stone_cut2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'cut');
										break;
									case 'stone_cut3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'cut');
										break;
									case 'stone_color1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'color');
										break;
									case 'stone_color2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'color');
										break;
									case 'stone_color3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'color');
										break;
									case 'stone_clarity1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'clarity');
										break;
									case 'stone_clarity2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'clarity');
										break;
									case 'stone_clarity3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'clarity');

										break;
									case 'stone_shape1':
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
										break;
									case 'stone_shape2':
										$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
										break;
									case 'stone_shape3':
										$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
										break;
									case 'stone_creation_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'creation');
										break;
									case 'stone_creation_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'creation');
										break;
									case 'stone_creation_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'creation');
										break;
									case 'stone_treatment_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'treatment');
										break;
									case 'stone_treatment_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'treatment');
										break;
									case 'stone_treatment_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'treatment');
										break;
									case 'stone_dimensions_unit_of_measure1':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure2':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure3':
										$column[$i][] = 'MM';
										break;
									/*case 'total_diamond_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	
									case 'total_gem_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	*/
									case 'item_display_diameter':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
										break;
									case 'item_display_height':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
										break;
									case 'item_display_width':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
										break;
									case 'item_display_length':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
										break;	
									case 'stone_height1':
										//$column[$i][] = $this->getStoneDiamentions($_productCollection->getAttributeText('stone1_size'),'height1');
										$column[$i][] = $this->getMergedSizes($_productCollection,'height1');
										break;
									case 'stone_height2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height2');
										break;
									case 'stone_height3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height3');
										break;
									case 'stone_length1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length1');
										break;
									case 'stone_length2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length2');
										break;
									case 'stone_length3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length3');
										break;
									case 'stone_width1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width1');
										break;
									case 'stone_width2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width2');
										break;
									case 'stone_width3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width3');
										break;
									case 'stone_weight1':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt1');
										break;
									case 'stone_weight2':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt2');
										break;
									case 'stone_weight3':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt3');
										break;	
									case 'pearl_type':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'is_lab_created1':
										if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created2':
										if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created3':
										if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;	
									case 'pearl_shape':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'size_per_pearl':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_size');
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'number_of_pearls':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getStone1Count();
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'back_finding':
										$column[$i][] = $_productCollection->getAttributeText('butterfly1_type');
										break;																													
									case 'model':
										$column[$i][] = 'ANG-R-'.$_productCollection->getSku();
										break;
									case 'feed_product_type':
										$column[$i][] = 'FineEarring';
										break;
									case 'item_type':
										$column[$i][] = 'earrings';
										break;
									case 'parent_child':
										$column[$i][] = 'child';
										break;
									/*case 'relationship_type':
										$column[$i][] = 'MetalType';
										break;	
									case 'variation_theme':
										$column[$i][] = 'Variation';
										break;			*/
									case 'quantity':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
										break;
									case 'bullet_point1':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
										break;
									case 'bullet_point2':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
										break;
									case 'bullet_point3':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
										break;
									case 'bullet_point4':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
										break;
									case 'bullet_point5':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
										break;
									case 'target_audience_keywords1':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
										break;
									case 'target_audience_keywords2':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
										break;													
									case 'target_audience_keywords3':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
										break;
									case 'thesaurus_subject_keywords1':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'thesaurus_subject_keywords2':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'thesaurus_subject_keywords3':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
										break;	
									case 'thesaurus_subject_keywords4':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'thesaurus_subject_keywords5':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
										break;
									case 'specific_uses_keywords1':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'specific_uses_keywords2':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'specific_uses_keywords3':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
										break;
									case 'specific_uses_keywords4':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'specific_uses_keywords5':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
										break;	
									default:	
										$column[$i][] = '';	
								}
						}
						else if($_productCollection->getAttributeText('jewelry_type')=='Pendant'){
							switch ($amazonData['column_'.$i]) {
									case 'item_sku':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'item_name':
										$column[$i][] = $_productCollection->getShortDescription();
										break;	
									case 'manufacturer':
										$column[$i][] = 'Angara';
										break;
									case 'brand_name':
										$column[$i][] = 'Angara.com';
										break;
									case 'product_description':
										$stone1nm = $_productCollection->getAttributeText('stone1_name');
										$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
										$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
										$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
										if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1grd = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1grd = $_productCollection->getAttributeText('stone1_Grade');
											}
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2grd = $_productCollection->getAttributeText('stone2_grade');	
											}
											else{
												$stone2grd = $_productCollection->getAttributeText('stone2_Grade');
											}
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3grd = $_productCollection->getAttributeText('stone3_grade');
											}
											else{
												$stone3grd = $_productCollection->getAttributeText('stone3_Grade');
											}
										$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$_productCollection->getSku(),'description');
										break;
									case 'update_delete':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
										break;
									case 'currency':
										if($isUkEnabled==1){
											$column[$i][] = 'GBP';
										}
										else{
											$column[$i][] = 'USD';
										}
										break;
									case 'list_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;
									case 'fulfillment_latency':
										$leadTime = '';
										$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
										$column[$i][] = $_productCollection->getVendorLeadTime()+$leadTime;
										break;
									case 'standard_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;		
									case 'recommended_browse_nodes':
										$column[$i][] = $this->getBrowseNodes($_productCollection->getAttributeText('jewelry_styles'),$_productCollection->getAttributeText('jewelry_type'));
										break;
									case 'seasons':
											$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
										break;
									case 'display_dimensions_unit_of_measure':
										$column[$i][] = 'MM';
										break;
									case 'catalog_number':
										$column[$i][] = $_productCollection->getSku().'-pendant';
										break;
									case 'generic_keywords1':
										if($searchKeywords[0]!=''){
											$column[$i][] = $searchKeywords[0];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords2':
										if($searchKeywords[1]!=''){
											$column[$i][] = $searchKeywords[1];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords3':
										if($searchKeywords[2]!=''){
											$column[$i][] = $searchKeywords[2];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords4':
										if($searchKeywords[3]!=''){
											$column[$i][] = $searchKeywords[3];
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords5':
										if($searchKeywords[4]!=''){
											$column[$i][] = $searchKeywords[4];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'main_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'swatch_image_url':
										$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$_productCollection->getImage();
										break;
									case 'other_image_url1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][1]['file']:'';
										break;
									case 'other_image_url2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][2]['file']:'';
										break;
									case 'other_image_url3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][3]['file']:'';
										break;
									case 'other_image_url4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][4]['file']:'';
										break;
									case 'other_image_url5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][5]['file']:'';
										break;
									case 'other_image_ur6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][6]['file']:'';
										break;
									case 'other_image_url7':
										$column[$i][] = $galleryData['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][7]['file']:'';
										break;
									case 'other_image_url8':
										$column[$i][] = $galleryData['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][8]['file']:'';
										break;	
									case 'prop_65':
										$column[$i][] = 'FALSE';
										break;
									case 'designer':
										$column[$i][] = 'Angara';
										break;
									case 'total_metal_weight_unit_of_measure':
										$column[$i][] = 'GR';
										break;
									case 'total_diamond_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
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
										if($totDiaWt!=''){
											$column[$i][] = $totDiaWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'total_gem_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
										$totGemWt=0;
										foreach($_prods as $_prod){
											$str='';
											if($_prod['type']=='Gemstone'){
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
													$str = $totGemWt+$_prod['weight'];
													$totGemWt = $str;
												}
											}
										}
										if($totGemWt!=''){
											$column[$i][] = $totGemWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'metal_type':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_type');
										break;
									case 'metal_stamp':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_stamp');
										break;
									case 'setting_type':
										if($_productCollection->getStone1Setting()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
										}else{
											$column[$i][] = '';
										}
										break;
									case 'number_of_stones':
										if(isset($countStone)){
											unset($countStone);
										}
										$_prods = $this->getStones($_productCollection);
										$totCount=0;
										foreach($_prods as $_prod){
											$countStone='';
												if(trim($_prod['count'])!=''){
													$countStone = $totCount+$_prod['count'];
													$totCount = $countStone;
												}
										}
										if($totCount!=''){
											$column[$i][] = $totCount;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'gem_type1':
										if($_productCollection->getStone1Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type2':
										if($_productCollection->getStone2Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone2_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type3':
										if($_productCollection->getStone3Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone3_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'clasp_type':
											$column[$i][] = 'Lobster Clasp';
										break;
									case 'chain_type':
										if($_productCollection->getChain1Type()!=''){
											$column[$i][] = $_productCollection->getAttributeText('chain1_type');	
										}
										else{
											$column[$i][] = '';
										}
										break;																
									case 'stone_cut1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'cut');
										break;
									case 'stone_cut2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'cut');
										break;
									case 'stone_cut3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'cut');
										break;
									case 'stone_color1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'color');
										break;
									case 'stone_color2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'color');
										break;
									case 'stone_color3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'color');
										break;
									case 'stone_clarity1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'clarity');
										break;
									case 'stone_clarity2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'clarity');
										break;
									case 'stone_clarity3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'clarity');

										break;
									case 'stone_shape1':
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
										break;
									case 'stone_shape2':
										$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
										break;
									case 'stone_shape3':
										$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
										break;
									case 'stone_creation_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'creation');
										break;
									case 'stone_creation_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'creation');
										break;
									case 'stone_creation_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'creation');
										break;
									case 'stone_treatment_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'treatment');
										break;
									case 'stone_treatment_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'treatment');
										break;
									case 'stone_treatment_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'treatment');
										break;
									case 'stone_dimensions_unit_of_measure1':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure2':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure3':
										$column[$i][] = 'MM';
										break;
									/*case 'total_diamond_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	
									case 'total_gem_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	*/
										case 'item_display_diameter':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
										break;
									case 'item_display_height':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
										break;
									case 'item_display_width':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
										break;
									case 'item_display_length':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
										break;
									case 'stone_height1':
										//$column[$i][] = $this->getStoneDiamentions($_productCollection->getAttributeText('stone1_size'),'height1');
										$column[$i][] = $this->getMergedSizes($_productCollection,'height1');
										break;
									case 'stone_height2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height2');
										break;
									case 'stone_height3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height3');
										break;
									case 'stone_length1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length1');
										break;
									case 'stone_length2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length2');
										break;
									case 'stone_length3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length3');
										break;
									case 'stone_width1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width1');
										break;
									case 'stone_width2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width2');
										break;
									case 'stone_width3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width3');
										break;
									case 'stone_weight1':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt1');
										break;
									case 'stone_weight2':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt2');
										break;
									case 'stone_weight3':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt3');
										break;
									case 'is_lab_created1':
										if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created2':
										if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created3':
										if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;	
									case 'pearl_type':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'pearl_shape':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'size_per_pearl':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_size');
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'number_of_pearls':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getStone1Count();
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'model':
										$column[$i][] = 'ANG-R-'.$_productCollection->getSku();
										break;
									case 'feed_product_type':
										$column[$i][] = 'FineNecklaceBraceletAnklet';
										break;
									case 'item_type':
										$column[$i][] = 'pendant-necklaces';
										break;
									case 'parent_child':
										$column[$i][] = 'child';
										break;
									/*case 'variation_theme':
										$column[$i][] = 'Variation';
										break;
									case 'relationship_type':
										$column[$i][] = 'MetalType';
										break;				*/
									case 'quantity':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
										break;
									case 'bullet_point1':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
										break;
									case 'bullet_point2':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
										break;
									case 'bullet_point3':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
										break;
									case 'bullet_point4':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
										break;
									case 'bullet_point5':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
										break;
									case 'target_audience_keywords1':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
										break;
									case 'target_audience_keywords2':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
										break;													
									case 'target_audience_keywords3':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
										break;
									case 'thesaurus_subject_keywords1':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'thesaurus_subject_keywords2':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'thesaurus_subject_keywords3':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
										break;	
									case 'thesaurus_subject_keywords4':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'thesaurus_subject_keywords5':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
										break;
									case 'specific_uses_keywords1':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'specific_uses_keywords2':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'specific_uses_keywords3':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
										break;
									case 'specific_uses_keywords4':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'specific_uses_keywords5':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
										break;	
									default:	
										$column[$i][] = '';	
							}
						}
						else if($_productCollection->getAttributeText('jewelry_type')=='Bracelet'){
							switch ($amazonData['column_'.$i]) {
									
									case 'item_sku':
										$sku[] = $column[$i][] = $_productCollection->getSku();
										break;
									case 'item_name':
										$column[$i][] = $_productCollection->getShortDescription();
										break;	
									case 'manufacturer':
										$column[$i][] = 'Angara';
										break;
									case 'brand_name':
										$column[$i][] = 'Angara.com';
										break;
									case 'product_description':
										$stone1nm = $_productCollection->getAttributeText('stone1_name');
										$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
										$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
										$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
										if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1grd = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1grd = $_productCollection->getAttributeText('stone1_Grade');
											}
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2grd = $_productCollection->getAttributeText('stone2_grade');	
											}
											else{
												$stone2grd = $_productCollection->getAttributeText('stone2_Grade');
											}
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3grd = $_productCollection->getAttributeText('stone3_grade');
											}
											else{
												$stone3grd = $_productCollection->getAttributeText('stone3_Grade');
											}
										$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$_productCollection->getSku(),'description');
										break;
									case 'update_delete':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
										break;
									case 'currency':
										if($isUkEnabled==1){
											$column[$i][] = 'GPB';
										}
										else{
											$column[$i][] = 'USD';
										}
										break;
									case 'list_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;
									case 'fulfillment_latency':
										$leadTime = '';
										$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
										$column[$i][] = $_productCollection->getVendorLeadTime()+$leadTime;
										break;
									case 'standard_price':
										if($isUkEnabled==1){
											$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
											$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
											$column[$i][] = number_format($_productCollection->getPrice()*$conversionRate*$taxUk,2);
										}
										else{
											$column[$i][] = $_productCollection->getPrice();
										}
										break;		
									case 'recommended_browse_nodes':
										$column[$i][] = $this->getBrowseNodes($_productCollection->getAttributeText('jewelry_styles'),$_productCollection->getAttributeText('jewelry_type'));
										break;
									case 'seasons':
											$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
										break;
									case 'display_dimensions_unit_of_measure':
										$column[$i][] = 'MM';
										break;
									case 'catalog_number':
										$column[$i][] = $_productCollection->getSku().'-bracelet';
										break;
									case 'generic_keywords1':
										if($searchKeywords[0]!=''){
											$column[$i][] = $searchKeywords[0];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords2':
										if($searchKeywords[1]!=''){
											$column[$i][] = $searchKeywords[1];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords3':
										if($searchKeywords[2]!=''){
											$column[$i][] = $searchKeywords[2];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords4':
										if($searchKeywords[3]!=''){
											$column[$i][] = $searchKeywords[3];
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'generic_keywords5':
										if($searchKeywords[4]!=''){
											$column[$i][] = $searchKeywords[4];											
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'main_image_url':
										$column[$i][] = $_productCollection->getImage();
										break;
									case 'swatch_image_url':
										$column[$i][] = $_productCollection->getImage();
										break;
									case 'other_image_url1':
										$column[$i][] = $galleryData['images'][1]['file']!=''?$galleryData['images'][1]['file']:'';
										break;
									case 'other_image_url2':
										$column[$i][] = $galleryData['images'][2]['file']!=''?$galleryData['images'][2]['file']:'';
										break;
									case 'other_image_url3':
										$column[$i][] = $galleryData['images'][3]['file']!=''?$galleryData['images'][3]['file']:'';
										break;
									case 'other_image_url4':
										$column[$i][] = $galleryData['images'][4]['file']!=''?$galleryData['images'][4]['file']:'';
										break;
									case 'other_image_url5':
										$column[$i][] = $galleryData['images'][5]['file']!=''?$galleryData['images'][5]['file']:'';
										break;
									case 'other_image_ur6':
										$column[$i][] = $galleryData['images'][6]['file']!=''?$galleryData['images'][6]['file']:'';
										break;
									case 'other_image_url7':
										$column[$i][] = $galleryData['images'][7]['file']!=''?$galleryData['images'][7]['file']:'';
										break;
									case 'other_image_url8':
										$column[$i][] = $galleryData['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryData['images'][8]['file']:'';
										break;	
									case 'prop_65':
										$column[$i][] = 'FALSE';
										break;
									case 'designer':
										$column[$i][] = 'Angara';
										break;
									case 'total_metal_weight_unit_of_measure':
										$column[$i][] = 'GR';
										break;
									case 'total_diamond_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
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
										if($totDiaWt!=''){
											$column[$i][] = $totDiaWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'total_gem_weight':
										if(isset($str)){
											unset($str);
										}
										$_prods = $this->getStones($_productCollection);
										$totGemWt=0;
										foreach($_prods as $_prod){
											$str='';
											if($_prod['type']=='Gemstone'){
												if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
													$str = $totGemWt+$_prod['weight'];
													$totGemWt = $str;
												}
											}
										}
										if($totGemWt!=''){
											$column[$i][] = $totGemWt;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'metal_type':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_type');
										break;
									case 'metal_stamp':
										$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('metal1_type'),'metal_stamp');
										break;
									case 'setting_type':
										if($_productCollection->getStone1Setting()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
										}else{
											$column[$i][] = '';
										}
										break;
									case 'number_of_stones':
										if(isset($countStone)){
											unset($countStone);
										}
										$_prods = $this->getStones($_productCollection);
										$totCount=0;
										foreach($_prods as $_prod){
											$countStone='';
												if(trim($_prod['count'])!=''){
													$countStone = $totCount+$_prod['count'];
													$totCount = $countStone;
												}
										}
										if($totCount!=''){
											$column[$i][] = $totCount;	
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'gem_type1':
										if($_productCollection->getStone1Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type2':
										if($_productCollection->getStone2Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone2_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'gem_type3':
										if($_productCollection->getStone3Name()!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone3_name');
										}
										else{
											$column[$i][]='';
										}
										break;
									case 'stone_cut1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'cut');
										break;
									case 'stone_cut2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'cut');
										break;
									case 'stone_cut3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'cut');
										break;
									case 'stone_color1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'color');
										break;
									case 'stone_color2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'color');
										break;
									case 'stone_color3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'color');
										break;
									case 'stone_clarity1':
											if($_productCollection->getAttributeText('stone1_grade')!=''){
												$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
											}
											else{
												$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'clarity');
										break;
									case 'stone_clarity2':
											if($_productCollection->getAttributeText('stone2_grade')!=''){
												$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
											}
											else{
												$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'clarity');
										break;
									case 'stone_clarity3':
											if($_productCollection->getAttributeText('stone3_grade')!=''){
												$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
											}
											else{
												$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
											}
											$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'clarity');

										break;
									case 'stone_shape1':
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
										break;
									case 'stone_shape2':
										$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
										break;
									case 'stone_shape3':
										$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
										break;
									case 'stone_creation_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'creation');
										break;
									case 'stone_creation_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'creation');
										break;
									case 'stone_creation_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'creation');
										break;
									case 'stone_treatment_method1':
										if($_productCollection->getAttributeText('stone1_grade')!=''){
											$stone1Grade = $_productCollection->getAttributeText('stone1_grade');		
										}
										else{
											$stone1Grade = $_productCollection->getAttributeText('stone1_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stone1Grade,'treatment');
										break;
									case 'stone_treatment_method2':
										if($_productCollection->getAttributeText('stone2_grade')!=''){
											$stone2Grade = $_productCollection->getAttributeText('stone2_grade');		
										}
										else{
											$stone2Grade = $_productCollection->getAttributeText('stone2_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stone2Grade,'treatment');
										break;
									case 'stone_treatment_method3':
										if($_productCollection->getAttributeText('stone3_grade')!=''){
											$stone3Grade = $_productCollection->getAttributeText('stone3_grade');		
										}
										else{
											$stone3Grade = $_productCollection->getAttributeText('stone3_Grade');
										}
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stone3Grade,'treatment');
										break;
									case 'stone_dimensions_unit_of_measure1':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure2':
										$column[$i][] = 'MM';
										break;
									case 'stone_dimensions_unit_of_measure3':
										$column[$i][] = 'MM';
										break;
									/*case 'total_diamond_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	
									case 'total_gem_weight_unit_of_measure':
										$column[$i][] = 'ct.';
										break;	*/
									case 'item_display_diameter':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
										break;
									case 'item_display_height':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
										break;
									case 'item_display_width':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
										break;
									case 'item_display_length':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
										break;	
									case 'stone_height1':
										//$column[$i][] = $this->getStoneDiamentions($_productCollection->getAttributeText('stone1_size'),'height1');
										$column[$i][] = $this->getMergedSizes($_productCollection,'height1');
										break;
									case 'stone_height2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height2');
										break;
									case 'stone_height3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'height3');
										break;
									case 'stone_length1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length1');
										break;
									case 'stone_length2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length2');
										break;
									case 'stone_length3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'length3');
										break;
									case 'stone_width1':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width1');
										break;
									case 'stone_width2':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width2');
										break;
									case 'stone_width3':
										$column[$i][] = $this->getMergedSizes($_productCollection,'width3');
										break;
									case 'stone_weight1':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt1');
										break;
									case 'stone_weight2':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt2');
										break;
									case 'stone_weight3':
										$column[$i][] = $this->getMergedWeight($_productCollection,'wt3');
										break;
									case 'is_lab_created1':
										if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created2':
										if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;
									case 'is_lab_created3':
										if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
										$column[$i][]='TRUE';
										else{
											$column[$i][]='';
										}
										break;	
									case 'pearl_type':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_name');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'pearl_shape':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'size_per_pearl':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getAttributeText('stone1_size');
										}
										else{
											$column[$i][] = '';
										}
										break;	
									case 'number_of_pearls':
										if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
											$column[$i][] = $_productCollection->getStone1Count();
										}
										else{
											$column[$i][] = '';
										}
										break;
									case 'back_finding':
										$column[$i][] = $_productCollection->getAttributeText('butterfly1_type');
										break;																													
									case 'model':
										$column[$i][] = 'ANG-R-'.$_productCollection->getSku();
										break;
									case 'feed_product_type':
										$column[$i][] = 'FineNecklaceBraceletAnklet';
										break;
									case 'item_type':
										$column[$i][] = 'bracelets';
										break;
									case 'parent_child':
										$column[$i][] = 'child';
										break;
									/*case 'relationship_type':
										$column[$i][] = 'MetalType';
										break;	
									case 'variation_theme':
										$column[$i][] = 'Variation';
										break;			*/
									case 'quantity':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
										break;
									case 'bullet_point1':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
										break;
									case 'bullet_point2':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
										break;
									case 'bullet_point3':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
										break;
									case 'bullet_point4':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
										break;
									case 'bullet_point5':
										$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
										break;
									case 'target_audience_keywords1':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
										break;
									case 'target_audience_keywords2':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
										break;													
									case 'target_audience_keywords3':
										$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
										break;
									case 'thesaurus_subject_keywords1':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'thesaurus_subject_keywords2':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'thesaurus_subject_keywords3':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
										break;	
									case 'thesaurus_subject_keywords4':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'thesaurus_subject_keywords5':
										$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
										break;
									case 'specific_uses_keywords1':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
										break;
									case 'specific_uses_keywords2':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
										break;
									case 'specific_uses_keywords3':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
										break;
									case 'specific_uses_keywords4':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
										break;
									case 'specific_uses_keywords5':
											$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
										break;	
									default:	
										$column[$i][] = '';	
							}
						}
						$skuCount = count($sku);
					}
					
					else{    // Get All Data of Configurable Products(variation-child sku's)<br />
						$chkMetal = Mage::getModel('catalog/product_type_configurable')->getConfigurableAttributesAsArray($_productCollection);
						if(isset($label)){
							unset($label);
						}
						foreach($chkMetal as $chkMtl){
							$label[] = $chkMtl['label'];
						}
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
									/*$parentSku[$i] = explode('-',$associatedProduct->getSku());		
									$parentSkus = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
									$temp[] = $parentSkus;
									$temp = array_unique($temp);
									$maxCount = count($temp);*/
									//generate sku
									$associatedProduct->getTypeInstance(true)->getSetAttributes($associatedProduct);
									$galleryDataChild = $associatedProduct->getData('media_gallery');
									if($_productCollection->getAttributeText('jewelry_type')=='Ring'){
										if($isUkEnabled==1){
										$ringSizeArray = array(
													'F','G','H','I','J1/2','K1/2','L1/2','M1/2','N1/2','O1/2','P1/2','Q1/2','R1/2','S1/2','T1/2','U1/2','V1/2','W1/2','X1/2','Z'
													);
										}
										else{
											$ringSizeArray = array(
														'3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13'
													);
										}
										foreach($ringSizeArray as $ringSize){
											switch ($amazonData['column_'.$i]) {
											case 'item_sku':
												$sku[] = $column[$i][] = $associatedProduct->getSku().'-'.$ringSize;
												break;
											case 'item_name':
												$getMetal = explode('-',$associatedProduct->getSku());
													if($getMetal[1]=='WG'){
														$descMetal = ' in 14k White Gold';
													}
													elseif($getMetal[1]=='YG'){
														$descMetal = ' in 14k Yellow Gold';
													}
													elseif($getMetal[1]=='PT'){
														$descMetal = ' in Platinum';
													}
													elseif($getMetal[1]=='SL'){
														$descMetal = ' in Silver';
													}
													else{
														$descMetal = '';
													}
												$column[$i][] = $associatedProduct->getShortDescription().$descMetal;
												break;	
											case 'manufacturer':
												$column[$i][] = 'Angara';
												break;
											case 'brand_name':
												$column[$i][] = 'Angara.com';
												break;
											case 'product_description':
												$stone1nm = $associatedProduct->getAttributeText('stone1_name');
												$stone2nm = $associatedProduct->getAttributeText('stone2_name')!=''?$associatedProduct->getAttributeText('stone2_name'):'';
												$stone3nm = $associatedProduct->getAttributeText('stone3_name')!=''?$associatedProduct->getAttributeText('stone3_name'):'';
												$stone4nm = $associatedProduct->getAttributeText('stone4_name')!=''?1:'0';
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1grd = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1grd = $associatedProduct->getAttributeText('stone1_Grade');
												}
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2grd = $associatedProduct->getAttributeText('stone2_grade');	
												}
												else{
													$stone2grd = $associatedProduct->getAttributeText('stone2_Grade');
												}
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3grd = $associatedProduct->getAttributeText('stone3_grade');
												}
												else{
													$stone3grd = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$associatedProduct->getSku(),'description');
												break;
											case 'update_delete':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
												break;
											case 'currency':
												if($isUkEnabled==1){
													$column[$i][] = 'GBP';
												}
												else{
													$column[$i][] = 'USD';
												}
												break;
											case 'list_price':
												if($isUkEnabled==1){
													$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
													$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
													$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
												}
												else{
													$column[$i][] = $associatedProduct->getPrice();
												}
												break;
											case 'fulfillment_latency':
												$leadTime = '';
												$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
												$column[$i][] = $associatedProduct->getVendorLeadTime()+$leadTime;
												break;
											case 'standard_price':
												if($isUkEnabled==1){
													$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
													$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
													$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
												}
												else{
													$column[$i][] = $associatedProduct->getPrice();
												}
												break;		
											case 'recommended_browse_nodes':
												$column[$i][] = $this->getBrowseNodes($associatedProduct->getAttributeText('jewelry_styles'),$associatedProduct->getAttributeText('jewelry_type'));
												break;
											case 'seasons':
													$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
												break;
											case 'display_dimensions_unit_of_measure':
												$column[$i][] = 'MM';
												break;
											case 'catalog_number':
												$column[$i][] = $associatedProduct->getSku().'-rings';
												break;
											case 'generic_keywords1':
												if($searchKeywords[0]!=''){
													$column[$i][] = $searchKeywords[0];											
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'generic_keywords2':
												if($searchKeywords[1]!=''){
													$column[$i][] = $searchKeywords[1];											
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'generic_keywords3':
												if($searchKeywords[2]!=''){
													$column[$i][] = $searchKeywords[2];											
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'generic_keywords4':
												if($searchKeywords[3]!=''){
													$column[$i][] = $searchKeywords[3];
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'generic_keywords5':
												if($searchKeywords[4]!=''){
													$column[$i][] = $searchKeywords[4];											
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'main_image_url':
												$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;
											case 'swatch_image_url':
												$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
												break;
											case 'other_image_url1':
												$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
												break;
											case 'other_image_url2':
												$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
												break;
											case 'other_image_url3':
												$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
												break;
											case 'other_image_url4':
												$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
												break;
											case 'other_image_url5':
												$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
												break;
											case 'other_image_ur6':
												$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
												break;
											case 'other_image_url7':
												$column[$i][] = $galleryDataChild['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][7]['file']:'';
												break;
											case 'other_image_url8':
												$column[$i][] = $galleryDataChild['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][8]['file']:'';
												break;	
											case 'prop_65':
												$column[$i][] = 'FALSE';
												break;
											case 'designer':
												$column[$i][] = 'Angara';
												break;
											case 'total_metal_weight_unit_of_measure':
												$column[$i][] = 'GR';
												break;
											case 'total_diamond_weight':
												if(isset($str)){
													unset($str);
												}
												$_prods = $this->getStones($associatedProduct);
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
												if($totDiaWt!=''){
													$column[$i][] = $totDiaWt;	
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'total_gem_weight':
												if(isset($str)){
													unset($str);
												}
												$_prods = $this->getStones($associatedProduct);
												$totGemWt=0;
												foreach($_prods as $_prod){
													$str='';
													if($_prod['type']=='Gemstone'){
														if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
															$str = $totGemWt+$_prod['weight'];
															$totGemWt = $str;
														}
													}
												}
												if($totGemWt!=''){
													$column[$i][] = $totGemWt;	
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'metal_type':
												$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_type');
												break;
											case 'metal_stamp':
												$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_stamp');
												break;
											case 'setting_type':
												if($associatedProduct->getStone1Setting()!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone1_setting');	
												}else{
													$column[$i][] = '';
												}
												break;
											case 'number_of_stones':
												if(isset($countStone)){
													unset($countStone);
												}
												$_prods = $this->getStones($associatedProduct);
												$totCount=0;
												foreach($_prods as $_prod){
													$countStone='';
														if(trim($_prod['count'])!=''){
															$countStone = $totCount+$_prod['count'];
															$totCount = $countStone;
														}
												}
												if($totCount!=''){
													$column[$i][] = $totCount;	
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'is_resizable':
												$column[$i][] = 'TRUE';
												break;
											case 'ring_sizing_lower_range':
												if($isUkEnabled==1){
													$column[$i][] = 'F';	
												}
												else{
													$column[$i][] = '3';	
												}
												break;
											case 'ring_sizing_upper_range':
												if($isUkEnabled==1){
													$column[$i][] = 'Z';	
												}
												else{
													$column[$i][] = '13';	
												}
												break;
											case 'gem_type1':
												if($associatedProduct->getStone1Name()!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
												}
												else{
													$column[$i][]='';
												}
												break;
											case 'gem_type2':
												if($associatedProduct->getStone2Name()!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone2_name');
												}
												else{
													$column[$i][]='';
												}
												break;
											case 'gem_type3':
												if($associatedProduct->getStone3Name()!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone3_name');
												}
												else{
													$column[$i][]='';
												}
												break;																
											case 'stone_cut1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'cut');
												break;
											case 'stone_cut2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'cut');
												break;
											case 'stone_cut3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'cut');
												break;
											case 'stone_color1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'color');
												break;
											case 'stone_color2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'color');
												break;
											case 'stone_color3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'color');
												break;
											case 'stone_clarity1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'clarity');
												break;
											case 'stone_clarity2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'clarity');
												break;
											case 'stone_clarity3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'clarity');
		
												break;
											case 'stone_shape1':
												$column[$i][] = $associatedProduct->getAttributeText('stone1_shape')?$associatedProduct->getAttributeText('stone1_shape'):'';
												break;
											case 'stone_shape2':
												$column[$i][] = $associatedProduct->getAttributeText('stone2_shape')?$associatedProduct->getAttributeText('stone2_shape'):'';
												break;
											case 'stone_shape3':
												$column[$i][] = $associatedProduct->getAttributeText('stone3_shape')?$associatedProduct->getAttributeText('stone3_shape'):'';
												break;
											case 'stone_creation_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'creation');
												break;
											case 'stone_creation_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'creation');
												break;
											case 'stone_creation_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'creation');
												break;
											case 'stone_treatment_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'treatment');
												break;
											case 'stone_treatment_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'treatment');
												break;
											case 'stone_treatment_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'treatment');
												break;
											case 'stone_dimensions_unit_of_measure1':
												$column[$i][] = 'MM';
												break;
											case 'stone_dimensions_unit_of_measure2':
												$column[$i][] = 'MM';
												break;
											case 'stone_dimensions_unit_of_measure3':
												$column[$i][] = 'MM';
												break;
											/*case 'total_diamond_weight_unit_of_measure':
												$column[$i][] = 'ct.';
												break;	
											case 'total_gem_weight_unit_of_measure':
												$column[$i][] = 'ct.';
												break;	*/
											case 'item_display_diameter':
												$column[$i][] = '17';
												break;
											case 'item_display_height':
												$column[$i][] = '25';
												break;
											case 'item_display_width':
												$column[$i][] = '8';
												break;
											case 'item_display_length':
												$column[$i][] = '21';
												break;
											case 'stone_height1':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'height1');
												break;
											case 'stone_height2':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'height2');
												break;
											case 'stone_height3':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'height3');
												break;
											case 'stone_length1':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'length1');
												break;
											case 'stone_length2':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'length2');
												break;
											case 'stone_length3':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'length3');
												break;
											case 'stone_width1':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'width1');
												break;
											case 'stone_width2':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'width2');
												break;
											case 'stone_width3':
												$column[$i][] = $this->getMergedSizes($associatedProduct,'width3');
												break;
											case 'stone_weight1':
												$column[$i][] = $this->getMergedWeight($associatedProduct,'wt1');
												break;
											case 'stone_weight2':
												$column[$i][] = $this->getMergedWeight($associatedProduct,'wt2');
												break;
											case 'stone_weight3':
												$column[$i][] = $this->getMergedWeight($associatedProduct,'wt3');
												break;
											case 'is_lab_created1':
												if($associatedProduct->getAttributeText('stone1_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone1_Grade')=='Simulated')
												$column[$i][]='TRUE';
												else{
													$column[$i][]='';
												}
												break;
											case 'is_lab_created2':
												if($associatedProduct->getAttributeText('stone2_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone2_Grade')=='Simulated')
												$column[$i][]='TRUE';
												else{
													$column[$i][]='';
												}
												break;
											case 'is_lab_created3':
												if($associatedProduct->getAttributeText('stone3_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone3_Grade')=='Simulated')
												$column[$i][]='TRUE';
												else{
													$column[$i][]='';
												}
												break;	
											case 'pearl_type':
												if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'pearl_shape':
												if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone1_shape');
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'size_per_pearl':
												if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
													$column[$i][] = $associatedProduct->getAttributeText('stone1_size');
												}
												else{
													$column[$i][] = '';
												}
												break;	
											case 'number_of_pearls':
												if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
													$column[$i][] = $associatedProduct->getStone1Count();
												}
												else{
													$column[$i][] = '';
												}
												break;
											case 'model':
												$column[$i][] = 'ANG-R-'.$associatedProduct->getSku();
												break;
											case 'feed_product_type':
												$column[$i][] = 'FineRing';
												break;
											case 'item_type':
												$column[$i][] = 'rings';
												break;
											case 'parent_child':
												$column[$i][] = 'child';
												break;
											case 'parent_sku':
												$parentSku[$i] = explode('-',$associatedProduct->getSku());		
												$column[$i][] = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
												break;
											case 'ring_size':
												$column[$i][] = $ringSize;
												break;		
											case 'variation_theme':
												if(in_array('Metal Type',$label)){
													$column[$i][] = 'MetalType-RingSize';
												}
												else{
													$column[$i][] = 'RingSize';	
												}
												break;
											case 'relationship_type':
												$column[$i][] = 'Variation';
												break;			
											case 'quantity':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
												break;
											case 'bullet_point1':
												$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
												break;
											case 'bullet_point2':
												$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);


												break;
											case 'bullet_point3':
												$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
												break;
											case 'bullet_point4':
												$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
												break;
											case 'bullet_point5':
												$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
												break;
											case 'target_audience_keywords1':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
												break;
											case 'target_audience_keywords2':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
												break;													
											case 'target_audience_keywords3':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
												break;
											case 'thesaurus_subject_keywords1':
												$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),1);
												break;
											case 'thesaurus_subject_keywords2':
												$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),2);
												break;
											case 'thesaurus_subject_keywords3':
												$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),3);
												break;	
											case 'thesaurus_subject_keywords4':
												$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),4);
												break;
											case 'thesaurus_subject_keywords5':
												$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),5);
												break;
											case 'specific_uses_keywords1':
												$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),1);
												break;
											case 'specific_uses_keywords2':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),2);
												break;
											case 'specific_uses_keywords3':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),3);
												break;
											case 'specific_uses_keywords4':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),4);
												break;
											case 'specific_uses_keywords5':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),5);
												break;	
											default:	
												$column[$i][] = '';
											}
										}
									}
									else if($_productCollection->getAttributeText('jewelry_type')=='Earrings'){
										switch ($amazonData['column_'.$i]) {
										case 'item_sku':
											$sku[] = $column[$i][] = $associatedProduct->getSku();
											break;
										case 'item_name':
											$getMetal = explode('-',$associatedProduct->getSku());
												if($getMetal[1]=='WG'){
													$descMetal = ' in 14k White Gold';
												}
												elseif($getMetal[1]=='YG'){
													$descMetal = ' in 14k Yellow Gold';
												}
												elseif($getMetal[1]=='PT'){
													$descMetal = ' in Platinum';
												}
												elseif($getMetal[1]=='SL'){
													$descMetal = ' in Silver';
												}
												else{
													$descMetal = '';
												}
											$column[$i][] = $associatedProduct->getShortDescription().$descMetal;
											break;	
										case 'manufacturer':
											$column[$i][] = 'Angara';
											break;
										case 'brand_name':
											$column[$i][] = 'Angara.com';
											break;
										case 'product_description':
											$stone1nm = $associatedProduct->getAttributeText('stone1_name');
											$stone2nm = $associatedProduct->getAttributeText('stone2_name')!=''?$associatedProduct->getAttributeText('stone2_name'):'';
											$stone3nm = $associatedProduct->getAttributeText('stone3_name')!=''?$associatedProduct->getAttributeText('stone3_name'):'';
											$stone4nm = $associatedProduct->getAttributeText('stone4_name')!=''?1:'0';
											if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1grd = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1grd = $associatedProduct->getAttributeText('stone1_Grade');
												}
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2grd = $associatedProduct->getAttributeText('stone2_grade');	
												}
												else{
													$stone2grd = $associatedProduct->getAttributeText('stone2_Grade');
												}
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3grd = $associatedProduct->getAttributeText('stone3_grade');
												}
												else{
													$stone3grd = $associatedProduct->getAttributeText('stone3_Grade');
												}
											$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$associatedProduct->getSku(),'description');
											break;
										case 'update_delete':
											$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
											break;
										case 'currency':
											if($isUkEnabled==1){
												$column[$i][] = 'GBP';
											}
											else{
												$column[$i][] = 'USD';
											}
											break;
										case 'list_price':
											if($isUkEnabled==1){
												$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
												$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
												$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
											}
											else{
												$column[$i][] = $associatedProduct->getPrice();
											}
											break;
										case 'fulfillment_latency':
											$leadTime = '';
											$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
											$column[$i][] = $associatedProduct->getVendorLeadTime()+$leadTime;
											break;
										case 'standard_price':
											if($isUkEnabled==1){
												$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
												$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
												$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
											}
											else{
												$column[$i][] = $associatedProduct->getPrice();
											}
											break;		
										case 'recommended_browse_nodes':
											$column[$i][] = $this->getBrowseNodes($associatedProduct->getAttributeText('jewelry_styles'),$associatedProduct->getAttributeText('jewelry_type'));
											break;
										case 'seasons':
												$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
											break;	
										case 'display_dimensions_unit_of_measure':
											$column[$i][] = 'MM';
											break;
										case 'catalog_number':
											$column[$i][] = $associatedProduct->getSku().'-earrings';
											break;
										case 'generic_keywords1':
											if($searchKeywords[0]!=''){
												$column[$i][] = $searchKeywords[0];											
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'generic_keywords2':
											if($searchKeywords[1]!=''){
												$column[$i][] = $searchKeywords[1];											
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'generic_keywords3':
											if($searchKeywords[2]!=''){
												$column[$i][] = $searchKeywords[2];											
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'generic_keywords4':
											if($searchKeywords[3]!=''){
												$column[$i][] = $searchKeywords[3];
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'generic_keywords5':
											if($searchKeywords[4]!=''){
												$column[$i][] = $searchKeywords[4];											
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'main_image_url':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
											break;
										case 'swatch_image_url':
											$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
											break;
										case 'other_image_url1':
											$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
											break;
										case 'other_image_url2':
											$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
											break;
										case 'other_image_url3':
											$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
											break;
										case 'other_image_url4':
											$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
											break;
										case 'other_image_url5':
											$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
											break;
										case 'other_image_ur6':
											$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
											break;
										case 'other_image_url7':
											$column[$i][] = $galleryDataChild['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][7]['file']:'';
											break;
										case 'other_image_url8':
											$column[$i][] = $galleryDataChild['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][8]['file']:'';
											break;	
										case 'prop_65':
											$column[$i][] = 'FALSE';
											break;
										case 'designer':
											$column[$i][] = 'Angara';
											break;
										case 'total_metal_weight_unit_of_measure':
											$column[$i][] = 'GR';
											break;
										case 'total_diamond_weight':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($associatedProduct);
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
											if($totDiaWt!=''){
												$column[$i][] = $totDiaWt;	
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'total_gem_weight':
											if(isset($str)){
												unset($str);
											}
											$_prods = $this->getStones($associatedProduct);
											$totGemWt=0;
											foreach($_prods as $_prod){
												$str='';
												if($_prod['type']=='Gemstone'){
													if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
														$str = $totGemWt+$_prod['weight'];
														$totGemWt = $str;
													}
												}
											}
											if($totGemWt!=''){
												$column[$i][] = $totGemWt;	
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'metal_type':
												$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_type');
												break;
										case 'metal_stamp':
											$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_stamp');
											break;
										case 'setting_type':
											if($associatedProduct->getStone1Setting()!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone1_setting');	
											}else{
												$column[$i][] = '';
											}
											break;
										case 'number_of_stones':
											if(isset($countStone)){
												unset($countStone);
											}
											$_prods = $this->getStones($associatedProduct);
											$totCount=0;
											foreach($_prods as $_prod){
												$countStone='';
													if(trim($_prod['count'])!=''){
														$countStone = $totCount+$_prod['count'];
														$totCount = $countStone;
													}
											}
											if($totCount!=''){
												$column[$i][] = $totCount;	
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'gem_type1':
											if($associatedProduct->getStone1Name()!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
											}
											else{
												$column[$i][]='';
											}
											break;
										case 'gem_type2':
											if($associatedProduct->getStone2Name()!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone2_name');
											}
											else{
												$column[$i][]='';
											}
											break;
										case 'gem_type3':
											if($associatedProduct->getStone3Name()!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone3_name');
											}
											else{
												$column[$i][]='';
											}
											break;																
										case 'stone_cut1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'cut');
												break;
											case 'stone_cut2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'cut');
												break;
											case 'stone_cut3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'cut');
												break;
											case 'stone_color1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'color');
												break;
											case 'stone_color2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'color');
												break;
											case 'stone_color3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'color');
												break;
											case 'stone_clarity1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'clarity');
												break;
											case 'stone_clarity2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'clarity');
												break;
											case 'stone_clarity3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'clarity');
		
												break;
											case 'stone_shape1':
												$column[$i][] = $associatedProduct->getAttributeText('stone1_shape')?$associatedProduct->getAttributeText('stone1_shape'):'';
												break;
											case 'stone_shape2':
												$column[$i][] = $associatedProduct->getAttributeText('stone2_shape')?$associatedProduct->getAttributeText('stone2_shape'):'';
												break;
											case 'stone_shape3':
												$column[$i][] = $associatedProduct->getAttributeText('stone3_shape')?$associatedProduct->getAttributeText('stone3_shape'):'';
												break;
											case 'stone_creation_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'creation');
												break;
											case 'stone_creation_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'creation');
												break;
											case 'stone_creation_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'creation');
												break;
											case 'stone_treatment_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'treatment');
												break;
											case 'stone_treatment_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'treatment');
												break;
											case 'stone_treatment_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'treatment');
												break;
										case 'stone_dimensions_unit_of_measure1':
											$column[$i][] = 'MM';
											break;
										case 'stone_dimensions_unit_of_measure2':
											$column[$i][] = 'MM';
											break;
										case 'stone_dimensions_unit_of_measure3':
											$column[$i][] = 'MM';
											break;
										/*case 'total_diamond_weight_unit_of_measure':
											$column[$i][] = 'ct.';
											break;	
										case 'total_gem_weight_unit_of_measure':
											$column[$i][] = 'ct.';
											break;*/
											case 'item_display_diameter':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
												break;
											case 'item_display_height':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
												break;
											case 'item_display_width':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
												break;
											case 'item_display_length':
												$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
												break;
										case 'stone_height1':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'height1');
											break;
										case 'stone_height2':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'height2');
											break;
										case 'stone_height3':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'height3');
											break;
										case 'stone_length1':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'length1');
											break;
										case 'stone_length2':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'length2');
											break;
										case 'stone_length3':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'length3');
											break;
										case 'stone_width1':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'width1');
											break;
										case 'stone_width2':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'width2');
											break;
										case 'stone_width3':
											$column[$i][] = $this->getMergedSizes($associatedProduct,'width3');
											break;
										case 'stone_weight1':
											$column[$i][] = $this->getMergedWeight($associatedProduct,'wt1');
											break;
										case 'stone_weight2':
											$column[$i][] = $this->getMergedWeight($associatedProduct,'wt2');
											break;
										case 'stone_weight3':
											$column[$i][] = $this->getMergedWeight($associatedProduct,'wt3');
											break;
										case 'is_lab_created1':
											if($associatedProduct->getAttributeText('stone1_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone1_Grade')=='Simulated')
											$column[$i][]='TRUE';
											else{
												$column[$i][]='';
											}
											break;
										case 'is_lab_created2':
											if($associatedProduct->getAttributeText('stone2_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone2_Grade')=='Simulated')
											$column[$i][]='TRUE';
											else{
												$column[$i][]='';
											}
											break;
										case 'is_lab_created3':
											if($associatedProduct->getAttributeText('stone3_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone3_Grade')=='Simulated')
											$column[$i][]='TRUE';
											else{
												$column[$i][]='';
											}
											break;	
										case 'pearl_type':
											if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'pearl_shape':
											if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone1_shape');
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'size_per_pearl':
											if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
												$column[$i][] = $associatedProduct->getAttributeText('stone1_size');
											}
											else{
												$column[$i][] = '';
											}
											break;	
										case 'number_of_pearls':
											if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
												$column[$i][] = $associatedProduct->getStone1Count();
											}
											else{
												$column[$i][] = '';
											}
											break;
										case 'back_finding':
											$column[$i][] = $associatedProduct->getAttributeText('butterfly1_type');
											break;																													
										case 'model':
											$column[$i][] = 'ANG-R-'.$associatedProduct->getSku();
											break;
										case 'feed_product_type':
											$column[$i][] = 'FineEarring';
											break;
										case 'item_type':
											$column[$i][] = 'earrings';
											break;
										case 'parent_child':
											$column[$i][] = 'child';
											break;
										case 'parent_sku':
												$parentSku[$i] = explode('-',$associatedProduct->getSku());		
												$column[$i][] = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
												break;
										case 'variation_theme':
											if(in_array('Metal Type',$label)){
												$column[$i][] = 'MetalType';
											}
											else{
												$column[$i][] = '';	
											}
											break;
										case 'relationship_type':
											$column[$i][] = 'Variation';
											break;	
										case 'quantity':
											$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
											break;
										case 'bullet_point1':
											$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
											break;
										case 'bullet_point2':
											$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
											break;
										case 'bullet_point3':
											$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
											break;
										case 'bullet_point4':
											$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
											break;
										case 'bullet_point5':
											$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
											break;
										case 'target_audience_keywords1':
											$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
											break;
										case 'target_audience_keywords2':
											$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
											break;													
										case 'target_audience_keywords3':
											$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
											break;
										case 'thesaurus_subject_keywords1':
											$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),1);
											break;
										case 'thesaurus_subject_keywords2':
											$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),2);
											break;
										case 'thesaurus_subject_keywords3':
											$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),3);
											break;	
										case 'thesaurus_subject_keywords4':
											$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),4);
											break;
										case 'thesaurus_subject_keywords5':
											$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),5);
											break;
										case 'specific_uses_keywords1':
											$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),1);
											break;
										case 'specific_uses_keywords2':
												$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),2);
											break;
										case 'specific_uses_keywords3':
												$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),3);
											break;
										case 'specific_uses_keywords4':
												$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),4);
											break;
										case 'specific_uses_keywords5':
												$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),5);
											break;	
										default:	
											$column[$i][] = '';	
									}
							}
									else if($_productCollection->getAttributeText('jewelry_type')=='Pendant'){
										switch ($amazonData['column_'.$i]) {
												case 'item_sku':
													$sku[] = $column[$i][] = $associatedProduct->getSku();
													break;
												case 'item_name':
													$getMetal = explode('-',$associatedProduct->getSku());
													if($getMetal[1]=='WG'){
														$descMetal = ' in 14k White Gold';
													}
													elseif($getMetal[1]=='YG'){
														$descMetal = ' in 14k Yellow Gold';
													}
													elseif($getMetal[1]=='PT'){
														$descMetal = ' in Platinum';
													}
													elseif($getMetal[1]=='SL'){
														$descMetal = ' in Silver';
													}
													else{
														$descMetal = '';
													}
												$column[$i][] = $associatedProduct->getShortDescription().$descMetal;
													break;	
												case 'manufacturer':
													$column[$i][] = 'Angara';
													break;
												case 'brand_name':
													$column[$i][] = 'Angara.com';
													break;
												case 'product_description':
													$stone1nm = $associatedProduct->getAttributeText('stone1_name');
													$stone2nm = $associatedProduct->getAttributeText('stone2_name')!=''?$associatedProduct->getAttributeText('stone2_name'):'';
													$stone3nm = $associatedProduct->getAttributeText('stone3_name')!=''?$associatedProduct->getAttributeText('stone3_name'):'';
													$stone4nm = $associatedProduct->getAttributeText('stone4_name')!=''?1:'0';
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1grd = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1grd = $associatedProduct->getAttributeText('stone1_Grade');
													}
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2grd = $associatedProduct->getAttributeText('stone2_grade');	
													}
													else{
														$stone2grd = $associatedProduct->getAttributeText('stone2_Grade');
													}
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3grd = $associatedProduct->getAttributeText('stone3_grade');
													}
													else{
														$stone3grd = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$associatedProduct->getSku(),'description');
													break;
												case 'update_delete':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
													break;
												case 'currency':
													if($isUkEnabled==1){
														$column[$i][] = 'GBP';
													}
													else{
														$column[$i][] = 'USD';
													}
													break;
												case 'list_price':
													if($isUkEnabled==1){
														$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
														$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
														$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
													}
													else{
														$column[$i][] = $associatedProduct->getPrice();
													}
													break;
												case 'fulfillment_latency':
													$leadTime = '';
													$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
													$column[$i][] = $associatedProduct->getVendorLeadTime()+$leadTime;
													break;
												case 'standard_price':
													if($isUkEnabled==1){
														$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
														$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
														$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
													}
													else{
														$column[$i][] = $associatedProduct->getPrice();
													}
													break;		
												case 'recommended_browse_nodes':
													$column[$i][] = $this->getBrowseNodes($associatedProduct->getAttributeText('jewelry_styles'),$associatedProduct->getAttributeText('jewelry_type'));
													break;
												case 'seasons':
														$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
													break;
												case 'display_dimensions_unit_of_measure':
													$column[$i][] = 'MM';
													break;
												case 'catalog_number':
													$column[$i][] = $associatedProduct->getSku().'-pendant';
													break;
												case 'generic_keywords1':
													if($searchKeywords[0]!=''){
														$column[$i][] = $searchKeywords[0];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords2':
													if($searchKeywords[1]!=''){
														$column[$i][] = $searchKeywords[1];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords3':
													if($searchKeywords[2]!=''){
														$column[$i][] = $searchKeywords[2];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords4':
													if($searchKeywords[3]!=''){
														$column[$i][] = $searchKeywords[3];
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords5':
													if($searchKeywords[4]!=''){
														$column[$i][] = $searchKeywords[4];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'main_image_url':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
													break;
												case 'swatch_image_url':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
													break;
												case 'other_image_url1':
													$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
													break;
												case 'other_image_url2':
													$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
													break;
												case 'other_image_url3':
													$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
													break;
												case 'other_image_url4':
													$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
													break;
												case 'other_image_url5':
													$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
													break;
												case 'other_image_ur6':
													$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
													break;
												case 'other_image_url7':
													$column[$i][] = $galleryDataChild['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][7]['file']:'';
													break;
												case 'other_image_url8':
													$column[$i][] = $galleryDataChild['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][8]['file']:'';
													break;	
												case 'prop_65':
													$column[$i][] = 'FALSE';
													break;
												case 'designer':
													$column[$i][] = 'Angara';
													break;
												case 'total_metal_weight_unit_of_measure':
													$column[$i][] = 'GR';
													break;
												case 'total_diamond_weight':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
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
													if($totDiaWt!=''){
														$column[$i][] = $totDiaWt;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'total_gem_weight':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
													$totGemWt=0;
													foreach($_prods as $_prod){
														$str='';
														if($_prod['type']=='Gemstone'){
															if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
																$str = $totGemWt+$_prod['weight'];
																$totGemWt = $str;
															}
														}
													}
													if($totGemWt!=''){
														$column[$i][] = $totGemWt;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'metal_type':
													$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_type');
													break;
												case 'metal_stamp':
													$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_stamp');
													break;
												case 'setting_type':
													if($associatedProduct->getStone1Setting()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_setting');	
													}else{
														$column[$i][] = '';
													}
													break;
												case 'number_of_stones':
													if(isset($countStone)){
														unset($countStone);
													}
													$_prods = $this->getStones($associatedProduct);
													$totCount=0;
													foreach($_prods as $_prod){
														$countStone='';
															if(trim($_prod['count'])!=''){
																$countStone = $totCount+$_prod['count'];
																$totCount = $countStone;
															}
													}
													if($totCount!=''){
														$column[$i][] = $totCount;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'gem_type1':
													if($associatedProduct->getStone1Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'gem_type2':
													if($associatedProduct->getStone2Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone2_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'gem_type3':
													if($associatedProduct->getStone3Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone3_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'clasp_type':
														$column[$i][]='Lobster Clasp';
													break;
												case 'chain_type':
													if($associatedProduct->getChain1Type()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('chain1_type');
													}
													else{
														$column[$i][] = '';
													}
													break;																
												case 'stone_cut1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'cut');
												break;
											case 'stone_cut2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'cut');
												break;
											case 'stone_cut3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'cut');
												break;
											case 'stone_color1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'color');
												break;
											case 'stone_color2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'color');
												break;
											case 'stone_color3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'color');
												break;
											case 'stone_clarity1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'clarity');
												break;
											case 'stone_clarity2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'clarity');
												break;
											case 'stone_clarity3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'clarity');
		
												break;
											case 'stone_shape1':
												$column[$i][] = $associatedProduct->getAttributeText('stone1_shape')?$associatedProduct->getAttributeText('stone1_shape'):'';
												break;
											case 'stone_shape2':
												$column[$i][] = $associatedProduct->getAttributeText('stone2_shape')?$associatedProduct->getAttributeText('stone2_shape'):'';
												break;
											case 'stone_shape3':
												$column[$i][] = $associatedProduct->getAttributeText('stone3_shape')?$associatedProduct->getAttributeText('stone3_shape'):'';
												break;
											case 'stone_creation_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'creation');
												break;
											case 'stone_creation_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'creation');
												break;
											case 'stone_creation_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'creation');
												break;
											case 'stone_treatment_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'treatment');
												break;
											case 'stone_treatment_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'treatment');
												break;
											case 'stone_treatment_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'treatment');
												break;
												case 'stone_dimensions_unit_of_measure1':
													$column[$i][] = 'MM';
													break;
												case 'stone_dimensions_unit_of_measure2':
													$column[$i][] = 'MM';
													break;
												case 'stone_dimensions_unit_of_measure3':
													$column[$i][] = 'MM';
													break;
												/*case 'total_diamond_weight_unit_of_measure':
													$column[$i][] = 'ct.';

													break;	
												case 'total_gem_weight_unit_of_measure':
													$column[$i][] = 'ct.';
													break;	*/
													case 'item_display_diameter':
														$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
														break;
													case 'item_display_height':
														$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
														break;
													case 'item_display_width':
														$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
														break;
													case 'item_display_length':
														$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
														break;
												case 'stone_height1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height1');
													break;
												case 'stone_height2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height2');
													break;
												case 'stone_height3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height3');
													break;
												case 'stone_length1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length1');
													break;
												case 'stone_length2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length2');
													break;
												case 'stone_length3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length3');
													break;
												case 'stone_width1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width1');
													break;
												case 'stone_width2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width2');
													break;
												case 'stone_width3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width3');
													break;
												case 'stone_weight1':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt1');
													break;
												case 'stone_weight2':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt2');
													break;
												case 'stone_weight3':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt3');
													break;
												case 'is_lab_created1':
													if($associatedProduct->getAttributeText('stone1_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone1_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;
												case 'is_lab_created2':
													if($associatedProduct->getAttributeText('stone2_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone2_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;
												case 'is_lab_created3':
													if($associatedProduct->getAttributeText('stone3_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone3_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;	
												case 'pearl_type':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'pearl_shape':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_shape');
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'size_per_pearl':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_size');
													}
													else{
														$column[$i][] = '';
													}
													break;	
												case 'number_of_pearls':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getStone1Count();
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'back_finding':
													$column[$i][] = $associatedProduct->getAttributeText('butterfly1_type');
													break;																												
												case 'model':
													$column[$i][] = 'ANG-R-'.$associatedProduct->getSku();
													break;
												case 'feed_product_type':
													$column[$i][] = 'FineNecklaceBraceletAnklet';
													break;
												case 'item_type':
													$column[$i][] = 'pendant-necklaces';
													break;
												case 'parent_child':
													$column[$i][] = 'child';
													break;
												case 'parent_sku':
													$parentSku[$i] = explode('-',$associatedProduct->getSku());		
													$column[$i][] = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
													break;
												case 'variation_theme':
													if(in_array('Metal Type',$label)){
														$column[$i][] = 'MetalType';
													}
													else{
														$column[$i][] = '';	
													}
													break;
												case 'relationship_type':
													$column[$i][] = 'Variation';
													break;	
												case 'quantity':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty')!=''?Mage::getStoreConfig('feeds/amazon_feeds/feeds_qty'):'';
													break;
												case 'bullet_point1':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
													break;
												case 'bullet_point2':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
													break;
												case 'bullet_point3':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
													break;
												case 'bullet_point4':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
													break;
												case 'bullet_point5':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
													break;
												case 'target_audience_keywords1':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
													break;
												case 'target_audience_keywords2':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
													break;													
												case 'target_audience_keywords3':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
													break;
												case 'catalog_number':
													$column[$i][] = $associatedProduct->getSku().'-pendant';
													break;
												case 'thesaurus_subject_keywords1':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),1);
													break;
												case 'thesaurus_subject_keywords2':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),2);
													break;
												case 'thesaurus_subject_keywords3':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),3);
													break;	
												case 'thesaurus_subject_keywords4':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),4);
													break;
												case 'thesaurus_subject_keywords5':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),5);
													break;
												case 'specific_uses_keywords1':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),1);
													break;
												case 'specific_uses_keywords2':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),2);
													break;
												case 'specific_uses_keywords3':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),3);
													break;
												case 'specific_uses_keywords4':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),4);
													break;
												case 'specific_uses_keywords5':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),5);
													break;	
												default:	
													$column[$i][] = '';	
										}
									}
									else if($_productCollection->getAttributeText('jewelry_type')=='Bracelet'){
										switch ($amazonData['column_'.$i]) {
												case 'item_sku':
													$sku[] .= $column[$i][] = $associatedProduct->getSku();
													break;
												case 'item_name':
													$getMetal = explode('-',$associatedProduct->getSku());
													if($getMetal[1]=='WG'){
														$descMetal = ' in 14k White Gold';
													}
													elseif($getMetal[1]=='YG'){
														$descMetal = ' in 14k Yellow Gold';
													}
													elseif($getMetal[1]=='PT'){
														$descMetal = ' in Platinum';
													}
													elseif($getMetal[1]=='SL'){
														$descMetal = ' in Silver';
													}
													else{
														$descMetal = '';
													}
												$column[$i][] = $associatedProduct->getShortDescription().$descMetal;
													break;	
												case 'manufacturer':
													$column[$i][] = 'Angara';
													break;
												case 'brand_name':
													$column[$i][] = 'Angara.com';
													break;
												case 'product_description':
													$stone1nm = $associatedProduct->getAttributeText('stone1_name');
													$stone2nm = $associatedProduct->getAttributeText('stone2_name')!=''?$associatedProduct->getAttributeText('stone2_name'):'';
													$stone3nm = $associatedProduct->getAttributeText('stone3_name')!=''?$associatedProduct->getAttributeText('stone3_name'):'';
													$stone4nm = $associatedProduct->getAttributeText('stone4_name')!=''?1:'0';
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1grd = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1grd = $associatedProduct->getAttributeText('stone1_Grade');
													}
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2grd = $associatedProduct->getAttributeText('stone2_grade');	
													}
													else{
														$stone2grd = $associatedProduct->getAttributeText('stone2_Grade');
													}
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3grd = $associatedProduct->getAttributeText('stone3_grade');
													}
													else{
														$stone3grd = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stone1grd,$stone2grd,$stone3grd,$associatedProduct->getSku(),'description');
													break;
												case 'update_delete':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
													break;
												case 'currency':
													if($isUkEnabled==1){
														$column[$i][] = 'GBP';
													}
													else{
														$column[$i][] = 'USD';
													}
													break;
												case 'list_price':
													if($isUkEnabled==1){
														$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
														$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
														$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
													}
													else{
														$column[$i][] = $associatedProduct->getPrice();
													}
													break;
												case 'fulfillment_latency':
													$leadTime = '';
													$leadTime = Mage::getStoreConfig('feeds/amazon_feeds/lead_time')!=''?Mage::getStoreConfig('feeds/amazon_feeds/lead_time'):'0';
													$column[$i][] = $associatedProduct->getVendorLeadTime()+$leadTime;
													break;
												case 'standard_price':
													if($isUkEnabled==1){
														$conversionRate = Mage::getStoreConfig('feeds/uk_feeds/conversion_rate');
														$taxUk = Mage::getStoreConfig('feeds/uk_feeds/tax_rate');
														$column[$i][] = number_format($associatedProduct->getPrice()*$conversionRate*$taxUk,2);
													}
													else{
														$column[$i][] = $associatedProduct->getPrice();
													}
													break;		
												case 'recommended_browse_nodes':
													$column[$i][] = $this->getBrowseNodes($associatedProduct->getAttributeText('jewelry_styles'),$associatedProduct->getAttributeText('jewelry_type'));
													break;
												case 'seasons':
														$column[$i][] = Mage::getStoreConfig('feeds/uk_feeds/season_uk');
													break;
												case 'display_dimensions_unit_of_measure':
													$column[$i][] = 'MM';
													break;
												case 'catalog_number':
													$column[$i][] = $associatedProduct->getSku().'-bracelet';
													break;
												case 'generic_keywords1':
													if($searchKeywords[0]!=''){
														$column[$i][] = $searchKeywords[0];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords2':
													if($searchKeywords[1]!=''){
														$column[$i][] = $searchKeywords[1];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords3':
													if($searchKeywords[2]!=''){
														$column[$i][] = $searchKeywords[2];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords4':
													if($searchKeywords[3]!=''){
														$column[$i][] = $searchKeywords[3];
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'generic_keywords5':
													if($searchKeywords[4]!=''){
														$column[$i][] = $searchKeywords[4];											
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'main_image_url':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
													break;
												case 'swatch_image_url':
													$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$associatedProduct->getImage();
													break;
												case 'other_image_url1':
													$column[$i][] = $galleryDataChild['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][1]['file']:'';
													break;
												case 'other_image_url2':
													$column[$i][] = $galleryDataChild['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][2]['file']:'';
													break;
												case 'other_image_url3':
													$column[$i][] = $galleryDataChild['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][3]['file']:'';
													break;
												case 'other_image_url4':
													$column[$i][] = $galleryDataChild['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][4]['file']:'';
													break;
												case 'other_image_url5':
													$column[$i][] = $galleryDataChild['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][5]['file']:'';
													break;
												case 'other_image_ur6':
													$column[$i][] = $galleryDataChild['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][6]['file']:'';
													break;
												case 'other_image_url7':
													$column[$i][] = $galleryDataChild['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][7]['file']:'';
													break;
												case 'other_image_url8':
														$column[$i][] = $galleryDataChild['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataChild['images'][8]['file']:'';
													break;	
												case 'prop_65':
													$column[$i][] = 'FALSE';
													break;
												case 'designer':
													$column[$i][] = 'Angara';
													break;
												case 'total_metal_weight_unit_of_measure':
													$column[$i][] = 'GR';
													break;
												case 'total_diamond_weight':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
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
													if($totDiaWt!=''){
														$column[$i][] = $totDiaWt;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'total_gem_weight':
													if(isset($str)){
														unset($str);
													}
													$_prods = $this->getStones($associatedProduct);
													$totGemWt=0;
													foreach($_prods as $_prod){
														$str='';
														if($_prod['type']=='Gemstone'){
															if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
																$str = $totGemWt+$_prod['weight'];
																$totGemWt = $str;
															}
														}
													}
													if($totGemWt!=''){
														$column[$i][] = $totGemWt;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'metal_type':
													$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_type');
													break;
												case 'metal_stamp':
													$column[$i][] = $this->getMetalValues($associatedProduct->getAttributeText('metal1_type'),'metal_stamp');
													break;
												case 'setting_type':
													if($associatedProduct->getStone1Setting()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_setting');	
													}else{
														$column[$i][] = '';
													}
													break;
												case 'number_of_stones':
													if(isset($countStone)){
														unset($countStone);
													}
													$_prods = $this->getStones($associatedProduct);
													$totCount=0;
													foreach($_prods as $_prod){
														$countStone='';
															if(trim($_prod['count'])!=''){
																$countStone = $totCount+$_prod['count'];
																$totCount = $countStone;
															}
													}
													if($totCount!=''){
														$column[$i][] = $totCount;	
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'gem_type1':
													if($associatedProduct->getStone1Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'gem_type2':
													if($associatedProduct->getStone2Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone2_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'gem_type3':
													if($associatedProduct->getStone3Name()!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone3_name');
													}
													else{
														$column[$i][]='';
													}
													break;
												case 'stone_cut1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'cut');
												break;
											case 'stone_cut2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'cut');
												break;
											case 'stone_cut3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'cut');
												break;
											case 'stone_color1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'color');
												break;
											case 'stone_color2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'color');
												break;
											case 'stone_color3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'color');
												break;
											case 'stone_clarity1':
													if($associatedProduct->getAttributeText('stone1_grade')!=''){
														$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
													}
													else{
														$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'clarity');
												break;
											case 'stone_clarity2':
													if($associatedProduct->getAttributeText('stone2_grade')!=''){
														$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
													}
													else{
														$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'clarity');
												break;
											case 'stone_clarity3':
													if($associatedProduct->getAttributeText('stone3_grade')!=''){
														$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
													}
													else{
														$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
													}
													$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'clarity');
		
												break;
											case 'stone_shape1':
												$column[$i][] = $associatedProduct->getAttributeText('stone1_shape')?$associatedProduct->getAttributeText('stone1_shape'):'';
												break;
											case 'stone_shape2':
												$column[$i][] = $associatedProduct->getAttributeText('stone2_shape')?$associatedProduct->getAttributeText('stone2_shape'):'';
												break;
											case 'stone_shape3':
												$column[$i][] = $associatedProduct->getAttributeText('stone3_shape')?$associatedProduct->getAttributeText('stone3_shape'):'';
												break;
											case 'stone_creation_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'creation');
												break;
											case 'stone_creation_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'creation');
												break;
											case 'stone_creation_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'creation');
												break;
											case 'stone_treatment_method1':
												if($associatedProduct->getAttributeText('stone1_grade')!=''){
													$stone1Grade = $associatedProduct->getAttributeText('stone1_grade');		
												}
												else{
													$stone1Grade = $associatedProduct->getAttributeText('stone1_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone1_name'),$stone1Grade,'treatment');
												break;
											case 'stone_treatment_method2':
												if($associatedProduct->getAttributeText('stone2_grade')!=''){
													$stone2Grade = $associatedProduct->getAttributeText('stone2_grade');		
												}
												else{
													$stone2Grade = $associatedProduct->getAttributeText('stone2_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone2_name'),$stone2Grade,'treatment');
												break;
											case 'stone_treatment_method3':
												if($associatedProduct->getAttributeText('stone3_grade')!=''){
													$stone3Grade = $associatedProduct->getAttributeText('stone3_grade');		
												}
												else{
													$stone3Grade = $associatedProduct->getAttributeText('stone3_Grade');
												}
												$column[$i][] = $this->getValues($associatedProduct->getAttributeText('stone3_name'),$stone3Grade,'treatment');
												break;
												case 'stone_dimensions_unit_of_measure1':
													$column[$i][] = 'MM';
													break;
												case 'stone_dimensions_unit_of_measure2':
													$column[$i][] = 'MM';
													break;
												case 'stone_dimensions_unit_of_measure3':
													$column[$i][] = 'MM';
													break;
												/*case 'total_diamond_weight_unit_of_measure':
													$column[$i][] = 'ct.';
													break;	
												case 'total_gem_weight_unit_of_measure':
													$column[$i][] = 'ct.';
													break;	*/
												case 'item_display_diameter':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
													break;
												case 'item_display_height':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
													break;
												case 'item_display_width':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
													break;
												case 'item_display_length':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
													break;	
												case 'stone_height1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height1');
													break;
												case 'stone_height2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height2');
													break;
												case 'stone_height3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'height3');
													break;
												case 'stone_length1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length1');
													break;
												case 'stone_length2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length2');
													break;
												case 'stone_length3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'length3');
													break;
												case 'stone_width1':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width1');
													break;
												case 'stone_width2':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width2');
													break;
												case 'stone_width3':
													$column[$i][] = $this->getMergedSizes($associatedProduct,'width3');
													break;
												case 'stone_weight1':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt1');
													break;
												case 'stone_weight2':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt2');
													break;
												case 'stone_weight3':
													$column[$i][] = $this->getMergedWeight($associatedProduct,'wt3');
													break;
												case 'is_lab_created1':
													if($associatedProduct->getAttributeText('stone1_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone1_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;
												case 'is_lab_created2':
													if($associatedProduct->getAttributeText('stone2_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone2_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;
												case 'is_lab_created3':
													if($associatedProduct->getAttributeText('stone3_Grade')=='Lab Created' || $associatedProduct->getAttributeText('stone3_Grade')=='Simulated')
													$column[$i][]='TRUE';
													else{
														$column[$i][]='';
													}
													break;	
												case 'pearl_type':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_name');
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'pearl_shape':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_shape');
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'size_per_pearl':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getAttributeText('stone1_size');
													}
													else{
														$column[$i][] = '';
													}
													break;	
												case 'number_of_pearls':
													if(strpos($associatedProduct->getAttributeText('stone1_name'),'Pearl')!=''){
														$column[$i][] = $associatedProduct->getStone1Count();
													}
													else{
														$column[$i][] = '';
													}
													break;
												case 'model':
													$column[$i][] = 'ANG-R-'.$associatedProduct->getSku();
													break;
												case 'feed_product_type':
													$column[$i][] = 'FineNecklaceBraceletAnklet';
													break;
												case 'item_type':
													$column[$i][] = 'bracelets';
													break;
												case 'parent_child':
													$column[$i][] = 'child';
													break;
												case 'parent_sku':
													$parentSku[$i] = explode('-',$associatedProduct->getSku());		
													$column[$i][] = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
													break;
												case 'variation_theme':
													if(in_array('Metal Type',$label)){
														$column[$i][] = 'MetalType';
													}
													else{
														$column[$i][] = '';	
													}
													break;
												case 'relationship_type':
													$column[$i][] = 'Variation';
													break;	
												case 'quantity':
													$column[$i][] = '5';
													break;
												case 'bullet_point1':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
													break;
												case 'bullet_point2':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
													break;
												case 'bullet_point3':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
													break;
												case 'bullet_point4':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
													break;
												case 'bullet_point5':
													$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
													break;
												case 'target_audience_keywords1':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
													break;
												case 'target_audience_keywords2':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
													break;													
												case 'target_audience_keywords3':
													$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
													break;
												case 'catalog_number':
													$column[$i][] = $associatedProduct->getSku().'-bracelet';
													break;
												case 'thesaurus_subject_keywords1':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),1);
													break;
												case 'thesaurus_subject_keywords2':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),2);
													break;
												case 'thesaurus_subject_keywords3':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),3);
													break;	
												case 'thesaurus_subject_keywords4':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),4);
													break;
												case 'thesaurus_subject_keywords5':
													$column[$i][] = $this->getSubjectMatter($associatedProduct->getAttributeText('jewelry_type'),5);
													break;
												case 'specific_uses_keywords1':
													$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),1);
													break;
												case 'specific_uses_keywords2':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),2);
													break;
												case 'specific_uses_keywords3':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),3);
													break;
												case 'specific_uses_keywords4':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),4);
													break;
												case 'specific_uses_keywords5':
														$column[$i][] = $this->getUsedFor($associatedProduct->getAttributeText('jewelry_type'),5);
													break;	
												default:	
													$column[$i][] = '';	
										}
									}
									}
								}
								//$skuCount = count($sku);				
					}
					
						//$i++;
		}
			// Put Configurable Sku after variation - parent sku's
		if($_productCollection->getTypeId()=='configurable' && $_productCollection->getVisibility()=='4'){
				$searchKeywords = explode(', ',$_productCollection->getMetaKeyword());
				$parentSkus = array();
				$temp = array();
				$stoneWt1=array();
				$stoneWt2=array();
				$stoneWt3=array();
				$stone_size1=array();
				$stone_size2=array();
				$stone_size3=array();
				$stoneGrade1 = array();
				$stoneGrade2 = array();
				$stoneGrade3 = array();
				$galleryDataParent=array();
				$imageParent =array();
				$totalDiaWt = array();
				$totalGemWt = array();
				$maxCount = '';
				if(isset($temp)){
					unset($temp);
				}
				if(isset($stoneWt1)){
					unset($stoneWt1);
				}
				if(isset($stoneWt2)){
					unset($stoneWt2);
				}
				if(isset($stoneWt3)){
					unset($stoneWt3);
				}
				if(isset($stone_size1)){
					unset($stone_size1);
				}
				if(isset($stone_size2)){
					unset($stone_size2);
				}
				if(isset($stone_size3)){
					unset($stone_size3);
				}
				if(isset($stoneGrade1)){
					unset($stoneGrade1);
				}
				if(isset($stoneGrade2)){
					unset($stoneGrade2);
				}
				if(isset($stoneGrade3)){
					unset($stoneGrade3);
				}
				if(isset($galleryDataParent)){
					unset($galleryDataParent);
				}
				if(isset($imageParent)){
					unset($imageParent);
				}
				if(isset($totalDiaWt)){
					unset($totalDiaWt);
				}
				if(isset($totalGemWt)){
					unset($totalGemWt);
				}
				if(isset($totalCount)){
					unset($totalCount);
				}
				if(isset($height1)){
					unset($height1);
				}
				if(isset($height2)){
					unset($height2);
				}
				if(isset($height3)){
					unset($height3);
				}
				if(isset($width1)){
					unset($width1);
				}
				if(isset($width2)){
					unset($width2);
				}
				if(isset($width3)){
					unset($width3);
				}
				if(isset($length1)){
					unset($length1);
				}
				if(isset($length2)){
					unset($length2);
				}
				if(isset($length3)){
					unset($length3);
				}
				if(isset($label)){
					unset($label);
				}
				$chkMetal = Mage::getModel('catalog/product_type_configurable')->getConfigurableAttributesAsArray($_productCollection);
				
				foreach($chkMetal as $chkMtl){
					$label[] = $chkMtl['label'];
				}
				
				$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($_productCollection->getId());
				
				foreach($childIds[0] as $key=>$value) // for all child sku's
				{
					if(isset($associatedProd)){
						unset($associatedProd);
					}		
					$associatedProducts = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('*')
										->addAttributeToFilter('entity_id', $value)
										->addAttributeToFilter('status', 1)
										->load();
											//echo "<pre>";print_r($associatedProduct->getData());
					$associatedProducts =$associatedProducts->getData();
					if($associatedProducts[0]['entity_id']!=''){
					$associatedProd = Mage::getModel('catalog/product')->load($associatedProducts[0]['entity_id']);
					$parentSku[$i] = explode('-',$associatedProd->getSku());		
					$parentSkus = $parentSku[$i][0].'-'.$parentSku[$i][3].'-'.$parentSku[$i][2];
					$temp[] = $parentSkus;
					$temp = array_unique($temp);
					$maxCount = count($temp);
						if(isset($strDia)){
							unset($strDia);
						}
						if(isset($strGem)){
							unset($strGem);
						}
						if(isset($stCount)){
							unset($stCount);
						}
						if(isset($_prod)){
							unset($_prod);
						}
						if(isset($_prods)){
							unset($_prods);
						}
						$_prods = $this->getStones($associatedProd);
						$totDiaWt=0;
						$totGemWt=0;
						$totCount=0;
						foreach($_prods as $_prod){
							$strDia='';
							$strGem='';
							$stCount='';
							if($_prod['type']=='Diamond'){
								if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
									$strDia = $totDiaWt+$_prod['weight'];
									$totDiaWt = $strDia;
								}
							}
							if($_prod['type']=='Gemstone'){
								if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
									$strGem = $totGemWt+$_prod['weight'];
									$totGemWt = $strGem;
								}
							}
							if(trim($_prod['count'])!=''){
								$stCount = $totCount+$_prod['count'];
								$totCount = $stCount;
							}
						}
						$totalDiaWt[] = $totDiaWt;
						$totalGemWt[] = $totGemWt;
						$totalCount[] = $totCount;						
						/*$stoneWt1 	= array_unique($stoneWt1);
						$stoneWt2 	= array_unique($stoneWt2);
						$stoneWt3 	= array_unique($stoneWt3);*/
						$height1[] = $this->getMergedSizes($associatedProd,'height1');
						$height2[] = $this->getMergedSizes($associatedProd,'height2');
						$height3[] = $this->getMergedSizes($associatedProd,'height3');
						$width1[] = $this->getMergedSizes($associatedProd,'width1');
						$width2[] = $this->getMergedSizes($associatedProd,'width2');
						$width3[] = $this->getMergedSizes($associatedProd,'width3');
						$length1[] = $this->getMergedSizes($associatedProd,'length1');
						$length2[] = $this->getMergedSizes($associatedProd,'length2');
						$length3[] = $this->getMergedSizes($associatedProd,'length3');							
						$stoneWt1[] = $this->getMergedWeight($associatedProd,'wt1');
						$stoneWt2[] = $this->getMergedWeight($associatedProd,'wt2');
						$stoneWt3[] = $this->getMergedWeight($associatedProd,'wt3');
						
						/*$stone_size1 = array_unique($stone_size1);
						$stone_size2 = array_unique($stone_size2);
						$stone_size3 = array_unique($stone_size3);*/
						
						$stoneGrade1[] = $associatedProd->getAttributeText('stone1_Grade');//$parentSku[$i][2];
						$stoneGrade2[] = $associatedProd->getAttributeText('stone2_Grade');
						$stoneGrade3[] = $associatedProd->getAttributeText('stone3_Grade');
						/*$stoneGrade1 = array_unique($stoneGrade1);
						$stoneGrade2 = array_unique($stoneGrade2);
						$stoneGrade3 = array_unique($stoneGrade3);*/
						
						$galleryDataParent[] = $associatedProd->getData('media_gallery');
						$imageParent[] = $associatedProd->getImage();
					}
				}
				/*$stoneWt1 = array_combine(range(1, count($stoneWt1)), array_values($stoneWt1));
				$stoneWt2 = array_combine(range(1, count($stoneWt2)), array_values($stoneWt2));
				$stoneWt3 = array_combine(range(1, count($stoneWt3)), array_values($stoneWt3));
				$stone_size1 = array_combine(range(1, count($stone_size1)), array_values($stone_size1));
				$stone_size2 = array_combine(range(1, count($stone_size2)), array_values($stone_size2));
				$stone_size3 = array_combine(range(1, count($stone_size3)), array_values($stone_size3));
				$stoneGrade1 = array_combine(range(1, count($stoneGrade1)), array_values($stoneGrade1));
				$stoneGrade2 = array_combine(range(1, count($stoneGrade2)), array_values($stoneGrade2));
				$stoneGrade3 = array_combine(range(1, count($stoneGrade3)), array_values($stoneGrade3));*/
				$wt=0;
			for($j=1;$j<=$maxCount;$j++){
				if($_productCollection->getAttributeText('jewelry_type')=='Ring'){
						for($i=1;$i<=$countColumn;$i++){
							switch ($amazonData['column_'.$i]) {
								case 'item_sku':
									$val = $skuParent[] = $column[$i][] = array_shift($temp);
									break;
								case 'item_name':
									$column[$i][] = $_productCollection->getShortDescription();
									break;	
								case 'manufacturer':
									$column[$i][] = 'Angara';
									break;
								case 'brand_name':
									$column[$i][] = 'Angara.com';
									break;
								case 'product_description':
									$stone1nm = $_productCollection->getAttributeText('stone1_name')!=''?$_productCollection->getAttributeText('stone1_name'):'';
									$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
									$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
									$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
									$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stoneGrade1[$wt],$stoneGrade2[$wt],$stoneGrade3[$wt],$_productCollection->getSku(),'description');
									break;
								case 'update_delete':
									$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
									break;
								case 'display_dimensions_unit_of_measure':
									$column[$i][] = 'MM';
									break;
								case 'catalog_number':
									$column[$i][] = $val.'-rings';
									break;
								case 'generic_keywords1':
									if($searchKeywords[0]!=''){
										$column[$i][] = $searchKeywords[0];											
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'generic_keywords2':
									if($searchKeywords[1]!=''){
										$column[$i][] = $searchKeywords[1];											
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'generic_keywords3':
									if($searchKeywords[2]!=''){
										$column[$i][] = $searchKeywords[2];											
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'generic_keywords4':
									if($searchKeywords[3]!=''){
										$column[$i][] = $searchKeywords[3];
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'generic_keywords5':
									if($searchKeywords[4]!=''){
										$column[$i][] = $searchKeywords[4];											
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'main_image_url':
									$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
									break;
								case 'swatch_image_url':
									$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
									break;
								case 'other_image_url1':
									$column[$i][] = $galleryDataParent[$wt]['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][1]['file']:'';
									break;
								case 'other_image_url2':
									$column[$i][] = $galleryDataParent[$wt]['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][2]['file']:'';
									break;
								case 'other_image_url3':
									$column[$i][] = $galleryDataParent[$wt]['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][3]['file']:'';
									break;
								case 'other_image_url4':
									$column[$i][] = $galleryDataParent[$wt]['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][4]['file']:'';
									break;
								case 'other_image_url5':
									$column[$i][] = $galleryDataParent[$wt]['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][5]['file']:'';
									break;
								case 'other_image_ur6':
									$column[$i][] = $galleryDataParent[$wt]['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][6]['file']:'';
									break;
								case 'other_image_url7':
									$column[$i][] = $galleryDataParent[$wt]['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][7]['file']:'';
									break;
								case 'other_image_url8':
										$column[$i][] = $galleryDataParent[$wt]['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][8]['file']:'';
									break;	
								case 'prop_65':
									$column[$i][] = 'FALSE';
									break;
								case 'designer':
									$column[$i][] = 'Angara';
									break;
								case 'total_metal_weight_unit_of_measure':
									$column[$i][] = 'GR';
									break;
								case 'total_diamond_weight':
										if($totalDiaWt[$wt]!='' || $totalDiaWt[$wt]>0){
											$column[$i][] = $totalDiaWt[$wt];	
										}
										else{
											$column[$i][] = '';
										}
									break;
								case 'total_gem_weight':
									if($totalGemWt[$wt]!='' || $totalGemWt[$wt]>0){
										$column[$i][] = $totalGemWt[$wt];	
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'setting_type':
									if($_productCollection->getStone1Setting()!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
									}else{
										$column[$i][] = '';
									}
									break;
								case 'number_of_stones':
									if($totalCount!=''){
										$column[$i][] = $totalCount[$wt];
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'is_resizable':
									$column[$i][] = 'TRUE';
									break;
								case 'ring_sizing_lower_range':
									if($isUkEnabled==1){
										$column[$i][] = 'F';	
									}
									else{
										$column[$i][] = '3';	
									}
									break;
								case 'ring_sizing_upper_range':
									if($isUkEnabled==1){
										$column[$i][] = 'Z';	
									}
									else{
										$column[$i][] = '13';	
									}
									break;
								case 'gem_type1':
									if($_productCollection->getStone1Name()!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone1_name');
									}
									else{
										$column[$i][]='';
									}
									break;
								case 'gem_type2':
									if($_productCollection->getStone2Name()!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone2_name');
									}
									else{
										$column[$i][]='';
									}
									break;
								case 'gem_type3':
									if($_productCollection->getStone3Name()!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone3_name');
									}
									else{
										$column[$i][]='';
									}
									break;																
								case 'stone_cut1':
									//if($wt<=count($stoneGrade1)){
										//$stoneGr1 = $stoneGrade1[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGrade1[$wt],'cut');
									//}
									break;
								case 'stone_cut2':
									//if($wt<=count($stoneGrade2)){
										//$stoneGr2 = $stoneGrade2[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGrade2[$wt],'cut');
									//}
									break;
								case 'stone_cut3':
									//if($wt<=count($stoneGrade3)){
										//$stoneGr3 = $stoneGrade3[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGrade3[$wt],'cut');
									//}
									break;
								case 'stone_color1':
									//if($wt<=count($stoneGrade1)){
										//$stoneGr1 = $stoneGrade1[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGrade1[$wt],'color');
									//}
									break;
								case 'stone_color2':
									//if($wt<=count($stoneGrade2)){
										//$stoneGr2 = $stoneGrade2[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGrade2[$wt],'color');
									//}
									break;
								case 'stone_color3':
									//if($wt<=count($stoneGrade3)){
										//$stoneGr3 = $stoneGrade3[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGrade3[$wt],'color');
									//}
									break;
								case 'stone_clarity1':
									//if($wt<=count($stoneGrade1)){
										//$stoneGr1 = $stoneGrade1[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGrade1[$wt],'clarity');
									//}
									break;
								case 'stone_clarity2':
									//if($wt<=count($stoneGrade2)){
										//$stoneGr2 = $stoneGrade2[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGrade2[$wt],'clarity');
									//}
									break;
								case 'stone_clarity3':
									//if($wt<=count($stoneGrade3)){
										//$stoneGr3 = $stoneGrade3[$wt];
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGrade3[$wt],'clarity');
									//}
									break;
								case 'stone_shape1':
									$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
									break;
								case 'stone_shape2':
									$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
									break;
								case 'stone_shape3':
									$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
									break;
								case 'stone_creation_method1':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGrade1[$wt],'creation');
									break;
								case 'stone_creation_method2':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGrade2[$wt],'creation');
									break;
								case 'stone_creation_method3':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGrade3[$wt],'creation');
									break;
								case 'stone_treatment_method1':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGrade1[$wt],'treatment');
									break;
								case 'stone_treatment_method2':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGrade2[$wt],'treatment');
									break;
								case 'stone_treatment_method3':
										$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGrade3	[$wt],'treatment');
									break;
								case 'stone_dimensions_unit_of_measure1':
									$column[$i][] = 'MM';
									break;
								case 'stone_dimensions_unit_of_measure2':
									$column[$i][] = 'MM';
									break;
								case 'stone_dimensions_unit_of_measure3':
									$column[$i][] = 'MM';
									break;
								case 'item_display_diameter':
									$column[$i][] = '17';
									break;
								case 'item_display_height':
									$column[$i][] = '25';
									break;
								case 'item_display_width':
									$column[$i][] = '8';
									break;
								case 'item_display_length':
									$column[$i][] = '21';
									break;
								case 'stone_height1':
									$column[$i][] = $height1[$wt];
									break;
								case 'stone_height2':
									$column[$i][] = $height2[$wt];
									break;
								case 'stone_height3':
									$column[$i][] = $height3[$wt];
									break;
								case 'stone_length1':
									$column[$i][] = $length1[$wt];
									break;
								case 'stone_length2':
									$column[$i][] = $length2[$wt];
									break;
								case 'stone_length3':
									$column[$i][] = $length3[$wt];
									break;
								case 'stone_width1':
									$column[$i][] = $width1[$wt];
									break;
								case 'stone_width2':
									$column[$i][] = $width2[$wt];
									break;
								case 'stone_width3':
									$column[$i][] = $width3[$wt];
									break;
								case 'stone_weight1':
										$column[$i][] = $stoneWt1[$wt]!=''?$stoneWt1[$wt]:'';
									break;
								case 'stone_weight2':
										$column[$i][] = $stoneWt2[$wt]!=''?$stoneWt2[$wt]:'';
									break;
								case 'stone_weight3':
										$column[$i][] = $stoneWt3[$wt]!=''?$stoneWt3[$wt]:'';
									break;
								case 'is_lab_created1':
									if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
									$column[$i][]='TRUE';
									else{
										$column[$i][]='';
									}
									break;
								case 'is_lab_created2':
									if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
									$column[$i][]='TRUE';
									else{
										$column[$i][]='';
									}
									break;
								case 'is_lab_created3':
									if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
									$column[$i][]='TRUE';
									else{
										$column[$i][]='';
									}
									break;	
								case 'pearl_type':
									if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone1_name');
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'pearl_shape':
									if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'size_per_pearl':
									if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
										$column[$i][] = $_productCollection->getAttributeText('stone1_size');
									}
									else{
										$column[$i][] = '';
									}
									break;	
								case 'number_of_pearls':
									if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
										$column[$i][] = $_productCollection->getStone1Count();
									}
									else{
										$column[$i][] = '';
									}
									break;
								case 'model':
									$column[$i][] = 'ANG-R-'.$val;
									break;
								case 'feed_product_type':
									$column[$i][] = 'FineRing';
									break;
								case 'item_type':
									$column[$i][] = 'rings';
									break;
								case 'variation_theme':
									if(in_array('Metal Type',$label)){
										$column[$i][] = 'MetalType-RingSize';
									}
									else{
										$column[$i][] = 'RingSize';	
									}
									break;
								case 'parent_child':
									$column[$i][] = 'parent';
									break;
								case 'bullet_point1':
									$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
									break;
								case 'bullet_point2':
									$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
									break;
								case 'bullet_point3':
									$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
									break;
								case 'bullet_point4':
									$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
									break;
								case 'bullet_point5':
									$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
									break;
								case 'target_audience_keywords1':
									$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
									break;
								case 'target_audience_keywords2':
									$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
									break;													
								case 'target_audience_keywords3':
									$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
									break;
								case 'thesaurus_subject_keywords1':
									$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
									break;
								case 'thesaurus_subject_keywords2':
									$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
									break;
								case 'thesaurus_subject_keywords3':
									$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
									break;	
								case 'thesaurus_subject_keywords4':
									$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
									break;
								case 'thesaurus_subject_keywords5':
									$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
									break;
								case 'specific_uses_keywords1':
									$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
									break;
								case 'specific_uses_keywords2':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
									break;
								case 'specific_uses_keywords3':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
									break;
								case 'specific_uses_keywords4':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
									break;
								case 'specific_uses_keywords5':
										$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
									break;	
								default:	
									$column[$i][] = '';
							}
						}
				}
				else if($_productCollection->getAttributeText('jewelry_type')=='Earrings'){
					for($i=1;$i<=$countColumn;$i++){
						switch ($amazonData['column_'.$i]) {
						case 'item_sku':
							$val = $skuParent[] = $column[$i][] = array_shift($temp);
							break;
						case 'item_name':
							$column[$i][] = $_productCollection->getShortDescription();
							break;	
						case 'manufacturer':
							$column[$i][] = 'Angara';
							break;
						case 'brand_name':
							$column[$i][] = 'Angara.com';
							break;
						case 'product_description':
							$stone1nm = $_productCollection->getAttributeText('stone1_name')!=''?$_productCollection->getAttributeText('stone1_name'):'';
							$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
							$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
							$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
									$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stoneGrade1[$wt],$stoneGrade2[$wt],$stoneGrade3[$wt],$_productCollection->getSku(),'description');
							break;
						case 'update_delete':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
							break;
						/*case 'currency':
							$column[$i][] = 'USD';
							break;
						case 'list_price':
							$column[$i][] = $_productCollection->getPrice();
							break;*/
						case 'display_dimensions_unit_of_measure':
							$column[$i][] = 'MM';
							break;
						case 'catalog_number':
							$column[$i][] = $val.'-earrings';
							break;
						case 'generic_keywords1':
							if($searchKeywords[0]!=''){
								$column[$i][] = $searchKeywords[0];											
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'generic_keywords2':
							if($searchKeywords[1]!=''){
								$column[$i][] = $searchKeywords[1];											
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'generic_keywords3':
							if($searchKeywords[2]!=''){
								$column[$i][] = $searchKeywords[2];											
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'generic_keywords4':
							if($searchKeywords[3]!=''){
								$column[$i][] = $searchKeywords[3];
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'generic_keywords5':
							if($searchKeywords[4]!=''){
								$column[$i][] = $searchKeywords[4];											
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'main_image_url':
							$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
							break;
						case 'swatch_image_url':
							$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
							break;
						case 'other_image_url1':
							$column[$i][] = $galleryDataParent[$wt]['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][1]['file']:'';
							break;
						case 'other_image_url2':
							$column[$i][] = $galleryDataParent[$wt]['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][2]['file']:'';
							break;
						case 'other_image_url3':
							$column[$i][] = $galleryDataParent[$wt]['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][3]['file']:'';
							break;
						case 'other_image_url4':
							$column[$i][] = $galleryDataParent['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][4]['file']:'';
							break;
						case 'other_image_url5':
							$column[$i][] = $galleryDataParent[$wt]['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][5]['file']:'';
							break;
						case 'other_image_ur6':
							$column[$i][] = $galleryDataParent[$wt]['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][6]['file']:'';
							break;
						case 'other_image_url7':
							$column[$i][] = $galleryDataParent[$wt]['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][7]['file']:'';
							break;
						case 'other_image_url8':
								$column[$i][] = $galleryDataParent[$wt]['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][8]['file']:'';
							break;	
						case 'prop_65':
							$column[$i][] = 'FALSE';
							break;
						case 'designer':
							$column[$i][] = 'Angara';
							break;
						case 'total_metal_weight_unit_of_measure':
							$column[$i][] = 'GR';
							break;
						case 'total_diamond_weight':
								if($totalDiaWt[$wt]!='' || $totalDiaWt[$wt]>0){
									$column[$i][] = $totalDiaWt[$wt];	
								}
								else{
									$column[$i][] = '';
								}
							break;
						case 'total_gem_weight':
							if($totalGemWt[$wt]!='' || $totalGemWt[$wt]>0){
								$column[$i][] = $totalGemWt[$wt];	
							}
							else{
								$column[$i][] = '';
							}
							break;
						/*case 'metal_type':
							$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_type');
							break;
						case 'metal_stamp':
							$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_stamp');
							break;*/
						case 'setting_type':
							if($_productCollection->getStone1Setting()!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
							}else{
								$column[$i][] = '';
							}
							break;
						case 'number_of_stones':
							if($totalCount!=''){
								$column[$i][] = $totalCount[$wt];
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'gem_type1':
							if($_productCollection->getStone1Name()!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone1_name');
							}
							else{
								$column[$i][]='';
							}
							break;
						case 'gem_type2':
							if($_productCollection->getStone2Name()!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone2_name');
							}
							else{
								$column[$i][]='';
							}
							break;
						case 'gem_type3':
							if($_productCollection->getStone3Name()!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone3_name');
							}
							else{
								$column[$i][]='';
							}
							break;																
						case 'stone_cut1':
							if($wt<=count($stoneGrade1)){
								$stoneGr1 = $stoneGrade1[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'cut');
							}
							break;
						case 'stone_cut2':
							if($wt<=count($stoneGrade2)){
								$stoneGr2 = $stoneGrade2[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'cut');
							}
							break;
						case 'stone_cut3':
							if($wt<=count($stoneGrade3)){
								$stoneGr3 = $stoneGrade3[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'cut');
							}
							break;
						case 'stone_color1':
							if($wt<=count($stoneGrade1)){
								$stoneGr1 = $stoneGrade1[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'color');
							}
							break;
						case 'stone_color2':
							if($wt<=count($stoneGrade2)){
								$stoneGr2 = $stoneGrade2[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'color');
							}
							break;
						case 'stone_color3':
							if($wt<=count($stoneGrade3)){
								$stoneGr3 = $stoneGrade3[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'color');
							}
							break;
						case 'stone_clarity1':
							if($wt<=count($stoneGrade1)){
								$stoneGr1 = $stoneGrade1[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'clarity');
							}
							break;
						case 'stone_clarity2':
							if($wt<=count($stoneGrade2)){
								$stoneGr2 = $stoneGrade2[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'clarity');
							}
							break;
						case 'stone_clarity3':
							if($wt<=count($stoneGrade3)){
								$stoneGr3 = $stoneGrade3[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'clarity');
							}
							break;
						case 'stone_shape1':
							$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
							break;
						case 'stone_shape2':
							$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
							break;
						case 'stone_shape3':
							$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
							break;
						case 'stone_creation_method1':
							if($wt<=count($stoneGrade1)){
								$stoneGr1 = $stoneGrade1[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'creation');
							}
							break;
						case 'stone_creation_method2':
							if($wt<=count($stoneGrade2)){
								$stoneGr2 = $stoneGrade2[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'creation');
							}
							break;
						case 'stone_creation_method3':
							if($wt<=count($stoneGrade3)){
								$stoneGr3 = $stoneGrade3[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'creation');
							}
							break;
						case 'stone_treatment_method1':
							if($wt<=count($stoneGrade1)){
								$stoneGr1 = $stoneGrade1[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'treatment');
							}
							break;
						case 'stone_treatment_method2':
							if($wt<=count($stoneGrade2)){
								$stoneGr2 = $stoneGrade2[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'treatment');
							}
							break;
						case 'stone_treatment_method3':
							if($wt<=count($stoneGrade3)){
								$stoneGr3 = $stoneGrade3[$wt];
								$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'treatment');
							}
							break;
						case 'stone_dimensions_unit_of_measure1':
							$column[$i][] = 'MM';
							break;
						case 'stone_dimensions_unit_of_measure2':
							$column[$i][] = 'MM';
							break;
						case 'stone_dimensions_unit_of_measure3':
							$column[$i][] = 'MM';
							break;
						case 'item_display_diameter':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
							break;
						case 'item_display_height':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
							break;
						case 'item_display_width':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
							break;
						case 'item_display_length':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
							break;	
						case 'stone_height1':
							$column[$i][] = $height1[$wt];
							break;
						case 'stone_height2':
							$column[$i][] = $height2[$wt];
							break;
						case 'stone_height3':
							$column[$i][] = $height3[$wt];
							break;
						case 'stone_length1':
							$column[$i][] = $length1[$wt];
							break;
						case 'stone_length2':
							$column[$i][] = $length2[$wt];
							break;
						case 'stone_length3':
							$column[$i][] = $length3[$wt];
							break;
						case 'stone_width1':
							$column[$i][] = $width1[$wt];
							break;
						case 'stone_width2':
							$column[$i][] = $width2[$wt];
							break;
						case 'stone_width3':
							$column[$i][] = $width3[$wt];
							break;
						case 'stone_weight1':
								$column[$i][] = $stoneWt1[$wt]!=''?$stoneWt1[$wt]:'';
							break;
						case 'stone_weight2':
								$column[$i][] = $stoneWt2[$wt]!=''?$stoneWt2[$wt]:'';
							break;
						case 'stone_weight3':
								$column[$i][] = $stoneWt3[$wt]!=''?$stoneWt3[$wt]:'';
							break;
						case 'is_lab_created1':
							if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
							$column[$i][]='TRUE';
							else{
								$column[$i][]='';
							}
							break;
						case 'is_lab_created2':
							if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
							$column[$i][]='TRUE';
							else{
								$column[$i][]='';
							}
							break;
						case 'is_lab_created3':
							if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
							$column[$i][]='TRUE';
							else{
								$column[$i][]='';
							}
							break;	
						case 'pearl_type':
							if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone1_name');
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'pearl_shape':
							if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'size_per_pearl':
							if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
								$column[$i][] = $_productCollection->getAttributeText('stone1_size');
							}
							else{
								$column[$i][] = '';
							}
							break;	
						case 'number_of_pearls':
							if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
								$column[$i][] = $_productCollection->getStone1Count();
							}
							else{
								$column[$i][] = '';
							}
							break;
						case 'back_finding':
							$column[$i][] = $_productCollection->getAttributeText('butterfly1_type');
							break;																												
						case 'model':
							$column[$i][] = 'ANG-R-'.$val;
							break;
						case 'feed_product_type':
							$column[$i][] = 'FineEarring';
							break;
						case 'item_type':
							$column[$i][] = 'earrings';
							break;
						case 'parent_child':
							$column[$i][] = 'parent';
							break;
						case 'variation_theme':
							if(in_array('Metal Type',$label)){
								$column[$i][] = 'MetalType';
							}
							else{
								$column[$i][] = '';	
							}
							break;
						/*case 'variation_theme':
							$column[$i][] = 'Variation';
							break;			
						case 'quantity':
							$column[$i][] = '5';
							break;*/
						case 'bullet_point1':
							$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
							break;
						case 'bullet_point2':
							$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
							break;
						case 'bullet_point3':
							$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
							break;
						case 'bullet_point4':
							$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
							break;
						case 'bullet_point5':
							$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
							break;
						case 'target_audience_keywords1':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
							break;
						case 'target_audience_keywords2':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
							break;													
						case 'target_audience_keywords3':
							$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
							break;
						case 'thesaurus_subject_keywords1':
							$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
							break;
						case 'thesaurus_subject_keywords2':
							$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
							break;
						case 'thesaurus_subject_keywords3':
							$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
							break;	
						case 'thesaurus_subject_keywords4':
							$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
							break;
						case 'thesaurus_subject_keywords5':
							$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
							break;
						case 'specific_uses_keywords1':
							$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
							break;
						case 'specific_uses_keywords2':
								$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
							break;
						case 'specific_uses_keywords3':
								$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
							break;
						case 'specific_uses_keywords4':
								$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
							break;
						case 'specific_uses_keywords5':
								$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
							break;	
						default:	
							$column[$i][] = '';	
					}
				}
				}
				else if($_productCollection->getAttributeText('jewelry_type')=='Pendant'){
					for($i=1;$i<=$countColumn;$i++){
					switch ($amazonData['column_'.$i]) {
							case 'item_sku':
								$val = $skuParent[] = $column[$i][] = array_shift($temp);
								break;
							case 'item_name':
								$column[$i][] = $_productCollection->getShortDescription();
								break;	
							case 'manufacturer':
								$column[$i][] = 'Angara';
								break;
							case 'brand_name':
								$column[$i][] = 'Angara.com';
								break;
							case 'product_description':
								$stone1nm = $_productCollection->getAttributeText('stone1_name')!=''?$_productCollection->getAttributeText('stone1_name'):'';
								$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
								$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
								$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
								$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stoneGrade1[$wt],$stoneGrade2[$wt],$stoneGrade3[$wt],$_productCollection->getSku(),'description');
								break;
							case 'update_delete':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
								break;
							/*case 'currency':
								$column[$i][] = 'USD';
								break;
							case 'list_price':
								$column[$i][] = $_productCollection->getPrice();
								break;*/
							case 'display_dimensions_unit_of_measure':
								$column[$i][] = 'MM';
								break;
							case 'catalog_number':
								$column[$i][] = $val.'-pendant';
								break;
							case 'generic_keywords1':
							if($searchKeywords[0]!=''){
								$column[$i][] = $searchKeywords[0];											
							}
							else{
								$column[$i][] = '';
							}
							break;
							case 'generic_keywords2':
								if($searchKeywords[1]!=''){
									$column[$i][] = $searchKeywords[1];											
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'generic_keywords3':
								if($searchKeywords[2]!=''){
									$column[$i][] = $searchKeywords[2];											
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'generic_keywords4':
								if($searchKeywords[3]!=''){
									$column[$i][] = $searchKeywords[3];
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'generic_keywords5':
								if($searchKeywords[4]!=''){
									$column[$i][] = $searchKeywords[4];											
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'main_image_url':
								$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
								break;
							case 'swatch_image_url':
								$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
								break;
							case 'other_image_url1':
								$column[$i][] = $galleryDataParent[$wt]['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][1]['file']:'';
								break;
							case 'other_image_url2':
								$column[$i][] = $galleryDataParent[$wt]['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][2]['file']:'';
								break;
							case 'other_image_url3':
								$column[$i][] = $galleryDataParent[$wt]['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][3]['file']:'';
								break;
							case 'other_image_url4':
								$column[$i][] = $galleryDataParent[$wt]['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][4]['file']:'';
								break;
							case 'other_image_url5':
								$column[$i][] = $galleryDataParent[$wt]['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][5]['file']:'';
								break;
							case 'other_image_ur6':
								$column[$i][] = $galleryDataParent[$wt]['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][6]['file']:'';
								break;
							case 'other_image_url7':
								$column[$i][] = $galleryDataParent[$wt]['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][7]['file']:'';
								break;
							case 'other_image_url8':
									$column[$i][] = $galleryDataParent[$wt]['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][8]['file']:'';
								break;	
							case 'prop_65':
								$column[$i][] = 'FALSE';
								break;
							case 'designer':
								$column[$i][] = 'Angara';
								break;
							case 'total_metal_weight_unit_of_measure':
								$column[$i][] = 'GR';
								break;
							case 'total_diamond_weight':
									if($totalDiaWt[$wt]!='' || $totalDiaWt[$wt]>0){
										$column[$i][] = $totalDiaWt[$wt];	
									}
									else{
										$column[$i][] = '';
									}
								break;
							case 'total_gem_weight':
								if($totalGemWt[$wt]!='' || $totalGemWt[$wt]>0){
									$column[$i][] = $totalGemWt[$wt];	
								}
								else{
									$column[$i][] = '';
								}
								break;
							/*case 'metal_type':
								$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_type');
								break;
							case 'metal_stamp':
								$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_stamp');
								break;*/
							case 'setting_type':
								if($_productCollection->getStone1Setting()!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
								}else{
									$column[$i][] = '';
								}
								break;
							case 'number_of_stones':
								if($totalCount!=''){
									$column[$i][] = $totalCount[$wt];
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'gem_type1':
								if($_productCollection->getStone1Name()!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone1_name');
								}
								else{
									$column[$i][]='';
								}
								break;
							case 'gem_type2':
								if($_productCollection->getStone2Name()!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone2_name');
								}
								else{
									$column[$i][]='';
								}
								break;
							case 'gem_type3':
								if($_productCollection->getStone3Name()!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone3_name');
								}
								else{
									$column[$i][]='';
								}
								break;
							case 'clasp_type':
									$column[$i][]='Lobster Clasp';
								break;
							case 'chain_type':
								if($_productCollection->getChain1Type()!=''){
									$column[$i][] = $_productCollection->getAttributeText('chain1_type');
								}
								else{
									$column[$i][] = '';
								}
								break;																
							case 'stone_cut1':
								if($wt<=count($stoneGrade1)){
									$stoneGr1 = $stoneGrade1[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'cut');
								}
								break;
							case 'stone_cut2':
								if($wt<=count($stoneGrade2)){
									$stoneGr2 = $stoneGrade2[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'cut');
								}
								break;
							case 'stone_cut3':
								if($wt<=count($stoneGrade3)){
									$stoneGr3 = $stoneGrade3[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'cut');
								}
								break;
							case 'stone_color1':
								if($wt<=count($stoneGrade1)){
									$stoneGr1 = $stoneGrade1[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'color');
								}
								break;
							case 'stone_color2':
								if($wt<=count($stoneGrade2)){
									$stoneGr2 = $stoneGrade2[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'color');
								}
								break;
							case 'stone_color3':
								if($wt<=count($stoneGrade3)){
									$stoneGr3 = $stoneGrade3[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'color');
								}
								break;
							case 'stone_clarity1':
								if($wt<=count($stoneGrade1)){
									$stoneGr1 = $stoneGrade1[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'clarity');
								}
								break;
							case 'stone_clarity2':
								if($wt<=count($stoneGrade2)){
									$stoneGr2 = $stoneGrade2[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'clarity');
								}
								break;
							case 'stone_clarity3':
								if($wt<=count($stoneGrade3)){
									$stoneGr3 = $stoneGrade3[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'clarity');
								}
								break;
							case 'stone_shape1':
								$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
								break;
							case 'stone_shape2':
								$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
								break;
							case 'stone_shape3':
								$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
								break;
							case 'stone_creation_method1':
								if($wt<=count($stoneGrade1)){
									$stoneGr1 = $stoneGrade1[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'creation');
								}
								break;
							case 'stone_creation_method2':
								if($wt<=count($stoneGrade2)){
									$stoneGr2 = $stoneGrade2[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'creation');
								}
								break;
							case 'stone_creation_method3':
								if($wt<=count($stoneGrade3)){
									$stoneGr3 = $stoneGrade3[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'creation');
								}
								break;
							case 'stone_treatment_method1':
								if($wt<=count($stoneGrade1)){
									$stoneGr1 = $stoneGrade1[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'treatment');
								}
								break;
							case 'stone_treatment_method2':
								if($wt<=count($stoneGrade2)){
									$stoneGr2 = $stoneGrade2[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'treatment');
								}
								break;
							case 'stone_treatment_method3':
								if($wt<=count($stoneGrade3)){
									$stoneGr3 = $stoneGrade3[$wt];
									$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'treatment');
								}
								break;
							case 'stone_dimensions_unit_of_measure1':
								$column[$i][] = 'MM';
								break;
							case 'stone_dimensions_unit_of_measure2':
								$column[$i][] = 'MM';
								break;
							case 'stone_dimensions_unit_of_measure3':
								$column[$i][] = 'MM';
								break;
							case 'item_display_diameter':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
								break;
							case 'item_display_height':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
								break;
							case 'item_display_width':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
								break;
							case 'item_display_length':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
								break;	
							case 'stone_height1':
								$column[$i][] = $height1[$wt];
								break;
							case 'stone_height2':
								$column[$i][] = $height2[$wt];
								break;
							case 'stone_height3':
								$column[$i][] = $height3[$wt];
								break;
							case 'stone_length1':
								$column[$i][] = $length1[$wt];
								break;
							case 'stone_length2':
								$column[$i][] = $length2[$wt];
								break;
							case 'stone_length3':
								$column[$i][] = $length3[$wt];
								break;
							case 'stone_width1':
								$column[$i][] = $width1[$wt];
								break;
							case 'stone_width2':
								$column[$i][] = $width2[$wt];
								break;
							case 'stone_width3':
								$column[$i][] = $width3[$wt];
								break;
							case 'stone_weight1':
									$column[$i][] = $stoneWt1[$wt]!=''?$stoneWt1[$wt]:'';
								break;
							case 'stone_weight2':
									$column[$i][] = $stoneWt2[$wt]!=''?$stoneWt2[$wt]:'';
								break;
							case 'stone_weight3':
									$column[$i][] = $stoneWt3[$wt]!=''?$stoneWt3[$wt]:'';
								break;
							case 'is_lab_created1':
								if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
								$column[$i][]='TRUE';
								else{
									$column[$i][]='';
								}
								break;
							case 'is_lab_created2':
								if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
								$column[$i][]='TRUE';
								else{
									$column[$i][]='';
								}
								break;
							case 'is_lab_created3':
								if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
								$column[$i][]='TRUE';
								else{
									$column[$i][]='';
								}
								break;	
							case 'pearl_type':
								if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone1_name');
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'pearl_shape':
								if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
									$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'size_per_pearl':
								if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
									$column[$i][] = $_productCollection->getAttributeText('default_stone1_size');
								}
								else{
									$column[$i][] = '';
								}
								break;	
							case 'number_of_pearls':
								if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
									$column[$i][] = $_productCollection->getStone1Count();
								}
								else{
									$column[$i][] = '';
								}
								break;
							case 'back_finding':
								$column[$i][] = $_productCollection->getAttributeText('butterfly1_type');
								break;																												
							case 'model':
								$column[$i][] = 'ANG-R-'.$val;
								break;
							case 'feed_product_type':
								$column[$i][] = 'FineNecklaceBraceletAnklet';
								break;
							case 'item_type':
								$column[$i][] = 'pendant-necklaces';
								break;
							case 'parent_child':
								$column[$i][] = 'parent';
								break;
							case 'variation_theme':
								if(in_array('Metal Type',$label)){
									$column[$i][] = 'MetalType';
								}
								else{
									$column[$i][] = '';	
								}
								break;
							/*case 'variation_theme':
								$column[$i][] = 'Variation';
								break;			
							case 'quantity':
								$column[$i][] = '5';
								break;
							case 'list_price':
								$column[$i][] = $_productCollection->getPrice();
								break;*/
							case 'bullet_point1':
								$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
								break;
							case 'bullet_point2':
								$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
								break;
							case 'bullet_point3':
								$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
								break;
							case 'bullet_point4':
								$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
								break;
							case 'bullet_point5':
								$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
								break;
							case 'target_audience_keywords1':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
								break;
							case 'target_audience_keywords2':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
								break;													
							case 'target_audience_keywords3':
								$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
								break;
							case 'catalog_number':
								$column[$i][] = $_productCollection->getSku().'-pendant';
								break;
							case 'thesaurus_subject_keywords1':
								$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
								break;
							case 'thesaurus_subject_keywords2':
								$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
								break;
							case 'thesaurus_subject_keywords3':
								$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
								break;	
							case 'thesaurus_subject_keywords4':
								$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
								break;
							case 'thesaurus_subject_keywords5':
								$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
								break;
							case 'specific_uses_keywords1':
								$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
								break;
							case 'specific_uses_keywords2':
									$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
								break;
							case 'specific_uses_keywords3':
									$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
								break;
							case 'specific_uses_keywords4':
									$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
								break;
							case 'specific_uses_keywords5':
									$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
								break;	
							default:	
								$column[$i][] = '';	
						}
					}
				}
				else if($_productCollection->getAttributeText('jewelry_type')=='Bracelet'){
					for($i=1;$i<=$countColumn;$i++){
					switch ($amazonData['column_'.$i]) {
					case 'item_sku':
						$val = $skuParent[] = $column[$i][] = array_shift($temp);
						break;
					case 'item_name':
						$column[$i][] = $_productCollection->getShortDescription();
						break;	
					case 'manufacturer':
						$column[$i][] = 'Angara';
						break;
					case 'brand_name':
						$column[$i][] = 'Angara.com';
						break;
					case 'product_description':
						$stone1nm = $_productCollection->getAttributeText('stone1_name')!=''?$_productCollection->getAttributeText('stone1_name'):'';
						$stone2nm = $_productCollection->getAttributeText('stone2_name')!=''?$_productCollection->getAttributeText('stone2_name'):'';
						$stone3nm = $_productCollection->getAttributeText('stone3_name')!=''?$_productCollection->getAttributeText('stone3_name'):'';
						$stone4nm = $_productCollection->getAttributeText('stone4_name')!=''?1:'0';
						$column[$i][] =  $this->getValuesDescription($stone1nm,$stone2nm,$stone3nm,$stone4nm,$stoneGrade1[$wt],$stoneGrade2[$wt],$stoneGrade3[$wt],$_productCollection->getSku(),'description');
						break;
					case 'update_delete':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/update_delete')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/update_delete'):'';
						break;
					/*case 'currency':
						$column[$i][] = 'USD';
						break;
					case 'list_price':
						$column[$i][] = $_productCollection->getPrice();
						break;*/
					case 'display_dimensions_unit_of_measure':
						$column[$i][] = 'MM';
						break;
					case 'catalog_number':
						$column[$i][] = $val.'-bracelet';
						break;
					case 'generic_keywords1':
						if($searchKeywords[0]!=''){
							$column[$i][] = $searchKeywords[0];											
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'generic_keywords2':
						if($searchKeywords[1]!=''){
							$column[$i][] = $searchKeywords[1];											
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'generic_keywords3':
						if($searchKeywords[2]!=''){
							$column[$i][] = $searchKeywords[2];											
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'generic_keywords4':
						if($searchKeywords[3]!=''){
							$column[$i][] = $searchKeywords[3];
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'generic_keywords5':
						if($searchKeywords[4]!=''){
							$column[$i][] = $searchKeywords[4];											
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'main_image_url':
						$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
						break;
					case 'swatch_image_url':
						$column[$i][] = 'http://feeds.angara.com/media/catalog/product'.$imageParent[$wt];
						break;
					case 'other_image_url1':
						$column[$i][] = $galleryDataParent[$wt]['images'][1]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][1]['file']:'';
						break;
					case 'other_image_url2':
						$column[$i][] = $galleryDataParent[$wt]['images'][2]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][2]['file']:'';
						break;
					case 'other_image_url3':
						$column[$i][] = $galleryDataParent[$wt]['images'][3]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][3]['file']:'';
						break;
					case 'other_image_url4':
						$column[$i][] = $galleryDataParent[$wt]['images'][4]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][4]['file']:'';
						break;
					case 'other_image_url5':
						$column[$i][] = $galleryDataParent[$wt]['images'][5]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][5]['file']:'';
						break;
					case 'other_image_ur6':
						$column[$i][] = $galleryDataParent[$wt]['images'][6]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][6]['file']:'';
						break;
					case 'other_image_url7':
						$column[$i][] = $galleryDataParent[$wt]['images'][7]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][7]['file']:'';
						break;
					case 'other_image_url8':
							$column[$i][] = $galleryDataParent[$wt]['images'][8]['file']!=''?'http://feeds.angara.com/media/catalog/product'.$galleryDataParent[$wt]['images'][8]['file']:'';
						break;	
					case 'prop_65':
						$column[$i][] = 'FALSE';
						break;
					case 'designer':
						$column[$i][] = 'Angara';
						break;
					case 'total_metal_weight_unit_of_measure':
						$column[$i][] = 'GR';
						break;
					case 'total_diamond_weight':
							if($totalDiaWt[$wt]!='' || $totalDiaWt[$wt]>0){
								$column[$i][] = $totalDiaWt[$wt];	
							}
							else{
								$column[$i][] = '';
							}
						break;
					case 'total_gem_weight':
						if($totalGemWt[$wt]!='' || $totalGemWt[$wt]>0){
							$column[$i][] = $totalGemWt[$wt];	
						}
						else{
							$column[$i][] = '';
						}
						break;
					/*case 'metal_type':
						$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_type');
						break;
					case 'metal_stamp':
						$column[$i][] = $this->getMetalValues($_productCollection->getAttributeText('default_metal1_type'),'metal_stamp');
						break;*/
					case 'setting_type':
						if($_productCollection->getStone1Setting()!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone1_setting');	
						}else{
							$column[$i][] = '';
						}
						break;
					case 'number_of_stones':
						if($totalCount!=''){
							$column[$i][] = $totalCount[$wt];
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'gem_type1':
						if($_productCollection->getStone1Name()!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone1_name');
						}
						else{
							$column[$i][]='';
						}
						break;
					case 'gem_type2':
						if($_productCollection->getStone2Name()!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone2_name');
						}
						else{
							$column[$i][]='';
						}
						break;
					case 'gem_type3':
						if($_productCollection->getStone3Name()!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone3_name');
						}
						else{
							$column[$i][]='';
						}
						break;
					case 'stone_cut1':
						if($wt<=count($stoneGrade1)){
							$stoneGr1 = $stoneGrade1[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'cut');
						}
						break;
					case 'stone_cut2':
						if($wt<=count($stoneGrade2)){
							$stoneGr2 = $stoneGrade2[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'cut');
						}
						break;
					case 'stone_cut3':
						if($wt<=count($stoneGrade3)){
							$stoneGr3 = $stoneGrade3[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'cut');
						}
						break;
					case 'stone_color1':
						if($wt<=count($stoneGrade1)){
							$stoneGr1 = $stoneGrade1[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'color');
						}
						break;
					case 'stone_color2':
						if($wt<=count($stoneGrade2)){
							$stoneGr2 = $stoneGrade2[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'color');
						}
						break;
					case 'stone_color3':
						if($wt<=count($stoneGrade3)){
							$stoneGr3 = $stoneGrade3[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'color');
						}
						break;
					case 'stone_clarity1':
						if($wt<=count($stoneGrade1)){
							$stoneGr1 = $stoneGrade1[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'clarity');
						}
						break;
					case 'stone_clarity2':
						if($wt<=count($stoneGrade2)){
							$stoneGr2 = $stoneGrade2[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'clarity');
						}
						break;
					case 'stone_clarity3':
						if($wt<=count($stoneGrade3)){
							$stoneGr3 = $stoneGrade3[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'clarity');
						}
						break;
					case 'stone_shape1':
						$column[$i][] = $_productCollection->getAttributeText('stone1_shape')?$_productCollection->getAttributeText('stone1_shape'):'';
						break;
					case 'stone_shape2':
						$column[$i][] = $_productCollection->getAttributeText('stone2_shape')?$_productCollection->getAttributeText('stone2_shape'):'';
						break;
					case 'stone_shape3':
						$column[$i][] = $_productCollection->getAttributeText('stone3_shape')?$_productCollection->getAttributeText('stone3_shape'):'';
						break;
					case 'stone_creation_method1':
						if($wt<=count($stoneGrade1)){
							$stoneGr1 = $stoneGrade1[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'creation');
						}
						break;
					case 'stone_creation_method2':
						if($wt<=count($stoneGrade2)){
							$stoneGr2 = $stoneGrade2[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'creation');
						}
						break;
					case 'stone_creation_method3':
						if($wt<=count($stoneGrade3)){
							$stoneGr3 = $stoneGrade3[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'creation');
						}
						break;
					case 'stone_treatment_method1':
						if($wt<=count($stoneGrade1)){
							$stoneGr1 = $stoneGrade1[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone1_name'),$stoneGr1,'treatment');
						}
						break;
					case 'stone_treatment_method2':
						if($wt<=count($stoneGrade2)){
							$stoneGr2 = $stoneGrade2[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone2_name'),$stoneGr2,'treatment');
						}
						break;
					case 'stone_treatment_method3':
						if($wt<=count($stoneGrade3)){
							$stoneGr3 = $stoneGrade3[$wt];
							$column[$i][] = $this->getValues($_productCollection->getAttributeText('stone3_name'),$stoneGr3,'treatment');
						}
						break;
					case 'stone_dimensions_unit_of_measure1':
						$column[$i][] = 'MM';
						break;
					case 'stone_dimensions_unit_of_measure2':
						$column[$i][] = 'MM';
						break;
					case 'stone_dimensions_unit_of_measure3':
						$column[$i][] = 'MM';
						break;
					case 'item_display_diameter':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_diameter')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_diameter'):'';
						break;
					case 'item_display_height':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_height')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_height'):'';
						break;
					case 'item_display_width':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_width')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_width'):'';
						break;
					case 'item_display_length':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/display_length')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/display_length'):'';
						break;	
					case 'stone_height1':
						$column[$i][] = $height1[$wt];
						break;
					case 'stone_height2':
						$column[$i][] = $height2[$wt];
						break;
					case 'stone_height3':
						$column[$i][] = $height3[$wt];
						break;
					case 'stone_length1':
						$column[$i][] = $length1[$wt];
						break;
					case 'stone_length2':
						$column[$i][] = $length2[$wt];
						break;
					case 'stone_length3':
						$column[$i][] = $length3[$wt];
						break;
					case 'stone_width1':
						$column[$i][] = $width1[$wt];
						break;
					case 'stone_width2':
						$column[$i][] = $width2[$wt];
						break;
					case 'stone_width3':
						$column[$i][] = $width3[$wt];
						break;
					case 'stone_weight1':
							$column[$i][] = $stoneWt1[$wt]!=''?$stoneWt1[$wt]:'';
						break;
					case 'stone_weight2':
							$column[$i][] = $stoneWt2[$wt]!=''?$stoneWt2[$wt]:'';
						break;
					case 'stone_weight3':
							$column[$i][] = $stoneWt3[$wt]!=''?$stoneWt3[$wt]:'';
						break;
					case 'is_lab_created1':
						if($_productCollection->getAttributeText('stone1_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone1_Grade')=='Simulated')
						$column[$i][]='TRUE';
						else{
							$column[$i][]='';
						}
						break;
					case 'is_lab_created2':
						if($_productCollection->getAttributeText('stone2_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone2_Grade')=='Simulated')
						$column[$i][]='TRUE';
						else{
							$column[$i][]='';
						}
						break;
					case 'is_lab_created3':
						if($_productCollection->getAttributeText('stone3_Grade')=='Lab Created' || $_productCollection->getAttributeText('stone3_Grade')=='Simulated')
						$column[$i][]='TRUE';
						else{
							$column[$i][]='';
						}
						break;	
					case 'pearl_type':
						if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone1_name');
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'pearl_shape':
						if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
							$column[$i][] = $_productCollection->getAttributeText('stone1_shape');
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'size_per_pearl':
						if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
							$column[$i][] = $_productCollection->getAttributeText('default_stone1_size');
						}
						else{
							$column[$i][] = '';
						}
						break;	
					case 'number_of_pearls':
						if(strpos($_productCollection->getAttributeText('stone1_name'),'Pearl')!=''){
							$column[$i][] = $_productCollection->getStone1Count();
						}
						else{
							$column[$i][] = '';
						}
						break;
					case 'back_finding':
						$column[$i][] = $_productCollection->getAttributeText('butterfly1_type');
						break;																												
					case 'model':
						$column[$i][] = 'ANG-R-'.$val;
						break;
					case 'feed_product_type':
						$column[$i][] = 'FineNecklaceBraceletAnklet';
						break;
					case 'item_type':
						$column[$i][] = 'bracelets';
						break;
					case 'parent_child':
						$column[$i][] = 'parent';
						break;
					case 'variation_theme':
						if(in_array('Metal Type',$label)){
							$column[$i][] = 'MetalType';
						}
						else{
							$column[$i][] = '';	
						}
						break;
					/*case 'variation_theme':
						$column[$i][] = 'Variation';
						break;			
					case 'quantity':
						$column[$i][] = '5';
						break;
					case 'list_price':
						$column[$i][] = $_productCollection->getPrice();
						break;*/
					case 'bullet_point1':
						$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),1);
						break;
					case 'bullet_point2':
						$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),2);
						break;
					case 'bullet_point3':
						$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),3);
						break;
					case 'bullet_point4':
						$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),4);
						break;
					case 'bullet_point5':
						$column[$i][] = $this->getDescription($_productCollection->getAttributeText('jewelry_type'),$_productCollection->getDescription(),5);
						break;
					case 'target_audience_keywords1':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience1')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience1'):'';
						break;
					case 'target_audience_keywords2':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience2')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience2'):'';
						break;													
					case 'target_audience_keywords3':
						$column[$i][] = Mage::getStoreConfig('feeds/amazon_feeds/target_audience3')!='' ? Mage::getStoreConfig('feeds/amazon_feeds/target_audience3'):'';
						break;
					case 'catalog_number':
						$column[$i][] = $_productCollection->getSku().'-bracelet';
						break;
					case 'thesaurus_subject_keywords1':
						$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),1);
						break;
					case 'thesaurus_subject_keywords2':
						$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),2);
						break;
					case 'thesaurus_subject_keywords3':
						$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),3);
						break;	
					case 'thesaurus_subject_keywords4':
						$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),4);
						break;
					case 'thesaurus_subject_keywords5':
						$column[$i][] = $this->getSubjectMatter($_productCollection->getAttributeText('jewelry_type'),5);
						break;
					case 'specific_uses_keywords1':
						$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),1);
						break;
					case 'specific_uses_keywords2':
							$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),2);
						break;
					case 'specific_uses_keywords3':
							$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),3);
						break;
					case 'specific_uses_keywords4':
							$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),4);
						break;
					case 'specific_uses_keywords5':
							$column[$i][] = $this->getUsedFor($_productCollection->getAttributeText('jewelry_type'),5);
						break;	
					default:	
						$column[$i][] = '';	
					}
				}
			}
				$wt++;
			}
			$skuFinal=array();
			$skuFinal = array_merge($sku,$skuParent);
			$skuCount = count($skuFinal);
	}
		for($i=0;$i<$skuCount;$i++){
			//if($column[1][$i]!=''){
				$listCombination[$i] = $column[1][$i].'&&&'.$column[2][$i].'&&&'.$column[3][$i].'&&&'.$column[4][$i].'&&&'.$column[5][$i].'&&&'.$column[6][$i].'&&&'.$column[7][$i].'&&&'.$column[8][$i].'&&&'.$column[9][$i].'&&&'.$column[10][$i].'&&&'.$column[11][$i].'&&&'.$column[12][$i].'&&&'.$column[13][$i].'&&&'.$column[14][$i].'&&&'.$column[15][$i].'&&&'.$column[16][$i].'&&&'.$column[17][$i].'&&&'.$column[18][$i].'&&&'.$column[19][$i].'&&&'.$column[20][$i].'&&&'.$column[21][$i].'&&&'.$column[22][$i].'&&&'.$column[23][$i].'&&&'.$column[24][$i].'&&&'.$column[25][$i].'&&&'.$column[26][$i].'&&&'.$column[27][$i].'&&&'.$column[28][$i].'&&&'.$column[29][$i].'&&&'.$column[30][$i].'&&&'.$column[31][$i].'&&&'.$column[32][$i].'&&&'.$column[33][$i].'&&&'.$column[34][$i].'&&&'.$column[35][$i].'&&&'.$column[36][$i].'&&&'.$column[37][$i].'&&&'.$column[38][$i].'&&&'.$column[39][$i].'&&&'.$column[40][$i].'&&&'.$column[41][$i].'&&&'.$column[42][$i].'&&&'.$column[43][$i].'&&&'.$column[44][$i].'&&&'.$column[45][$i].'&&&'.$column[46][$i].'&&&'.$column[47][$i].'&&&'.$column[48][$i].'&&&'.$column[49][$i].'&&&'.$column[50][$i].'&&&'.$column[51][$i].'&&&'.$column[52][$i].'&&&'.$column[53][$i].'&&&'.$column[54][$i].'&&&'.$column[55][$i].'&&&'.$column[56][$i].'&&&'.$column[57][$i].'&&&'.$column[58][$i].'&&&'.$column[59][$i].'&&&'.$column[60][$i].'&&&'.$column[61][$i].'&&&'.$column[62][$i].'&&&'.$column[63][$i].'&&&'.$column[64][$i].'&&&'.$column[65][$i].'&&&'.$column[66][$i].'&&&'.$column[67][$i].'&&&'.$column[68][$i].'&&&'.$column[69][$i].'&&&'.$column[70][$i].'&&&'.$column[71][$i].'&&&'.$column[72][$i].'&&&'.$column[73][$i].'&&&'.$column[74][$i].'&&&'.$column[75][$i].'&&&'.$column[76][$i].'&&&'.$column[77][$i].'&&&'.$column[78][$i].'&&&'.$column[79][$i].'&&&'.$column[80][$i].'&&&'.$column[81][$i].'&&&'.$column[82][$i].'&&&'.$column[83][$i].'&&&'.$column[84][$i].'&&&'.$column[85][$i].'&&&'.$column[86][$i].'&&&'.$column[87][$i].'&&&'.$column[88][$i].'&&&'.$column[89][$i].'&&&'.$column[90][$i].'&&&'.$column[91][$i].'&&&'.$column[92][$i].'&&&'.$column[93][$i].'&&&'.$column[94][$i].'&&&'.$column[95][$i].'&&&'.$column[96][$i].'&&&'.$column[97][$i].'&&&'.$column[98][$i].'&&&'.$column[99][$i].'&&&'.$column[100][$i].'&&&'.$column[101][$i].'&&&'.$column[102][$i].'&&&'.$column[103][$i].'&&&'.$column[104][$i].'&&&'.$column[105][$i].'&&&'.$column[106][$i].'&&&'.$column[107][$i].'&&&'.$column[108][$i].'&&&'.$column[109][$i].'&&&'.$column[110][$i].'&&&'.$column[111][$i].'&&&'.$column[112][$i].'&&&'.$column[113][$i].'&&&'.$column[114][$i].'&&&'.$column[115][$i].'&&&'.$column[116][$i].'&&&'.$column[117][$i].'&&&'.$column[118][$i].'&&&'.$column[119][$i].'&&&'.$column[120][$i].'&&&'.$column[121][$i].'&&&'.$column[122][$i].'&&&'.$column[123][$i].'&&&'.$column[124][$i].'&&&'.$column[125][$i].'&&&'.$column[126][$i].'&&&'.$column[127][$i].'&&&'.$column[128][$i].'&&&'.$column[129][$i].'&&&'.$column[130][$i].'&&&'.$column[131][$i].'&&&'.$column[132][$i].'&&&'.$column[133][$i].'&&&'.$column[134][$i].'&&&'.$column[135][$i].'&&&'.$column[136][$i].'&&&'.$column[137][$i].'&&&'.$column[138][$i].'&&&'.$column[139][$i].'&&&'.$column[140][$i].'&&&'.$column[141][$i].'&&&'.$column[142][$i].'&&&'.$column[143][$i].'&&&'.$column[144][$i].'&&&'.$column[145][$i].'&&&'.$column[146][$i].'&&&'.$column[147][$i].'&&&'.$column[148][$i].'&&&'.$column[149][$i].'&&&'.$column[150][$i].'&&&'.$column[151][$i].'&&&'.$column[152][$i].'&&&'.$column[153][$i].'&&&'.$column[154][$i].'&&&'.$column[155][$i].'&&&'.$column[156][$i].'&&&'.$column[157][$i].'&&&'.$column[158][$i].'&&&'.$column[159][$i].'&&&'.$column[160][$i].'&&&'.$column[161][$i].'&&&'.$column[162][$i].'&&&'.$column[163][$i].'&&&'.$column[164][$i].'&&&'.$column[165][$i].'&&&'.$column[166][$i].'&&&'.$column[167][$i].'&&&'.$column[168][$i].'&&&'.$column[169][$i].'&&&'.$column[170][$i].'&&&'.$column[171][$i].'&&&'.$column[172][$i].'&&&'.$column[173][$i].'&&&'.$column[174][$i].'&&&'.$column[175][$i].'&&&'.$column[176][$i].'&&&'.$column[177][$i].'&&&'.$column[178][$i].'&&&'.$column[179][$i].'&&&'.$column[180][$i].'&&&'.$column[181][$i].'&&&'.$column[182][$i].'&&&'.$column[183][$i].'&&&'.$column[184][$i].'&&&'.$column[185][$i].'&&&'.$column[186][$i].'&&&'.$column[187][$i].'&&&'.$column[188][$i].'&&&'.$column[189][$i].'&&&'.$column[190][$i].'&&&'.$column[191][$i].'&&&'.$column[192][$i].'&&&'.$column[193][$i].'&&&'.$column[194][$i].'&&&'.$column[195][$i].'&&&'.$column[196][$i].'&&&'.$column[197][$i].'&&&'.$column[198][$i].'&&&'.$column[199][$i].'&&&'.$column[200][$i].'&&&'.$column[201][$i].'&&&'.$column[202][$i].'&&&'.$column[203][$i].'&&&'.$column[204][$i].'&&&'.$column[205][$i].'&&&'.$column[206][$i].'&&&'.$column[207][$i].'&&&'.$column[208][$i].'&&&'.$column[209][$i].'&&&'.$column[210][$i].'&&&'.$column[211][$i].'&&&'.$column[212][$i].'&&&'.$column[213][$i].'&&&'.$column[214][$i].'&&&'.$column[215][$i].'&&&'.$column[216][$i].'&&&'.$column[217][$i].'&&&'.$column[218][$i];
			//}
		}
		return $listCombination;
	}
	
	public function getDescription($jewelryType,$description,$descNumber){
		if($description!=''){
			$removeUL1 = substr($description,4);
			$removeUL2 = substr($removeUL1,0,-5);
			$getData = explode('<li>',$removeUL2);	
		}
		else{
			$getData = '';
			//return;
		}
		if($descNumber == '1'){
			if(substr($getData[1],0,-5)!=''){
				$desc1 = substr($getData[1],0,-5);
			}
			else{
				$desc1 = 'Gorgeous Angara Gift Box comes with every purchase.';
			}
			return $desc1;
		}
		else if($descNumber == '2'){
			if($jewelryType=='Ring'){
				if(substr($getData[2],0,-5)!=''){
					$desc2 = substr($getData[2],0,-5);
				}
				else if(substr($getData[1],0,-5)!=''){
					$desc2 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else{
					$desc2 = 'One Free Ring Resizing.';
				}
			}
			else{
				if(substr($getData[2],0,-5)!=''){
					$desc2 = substr($getData[2],0,-5);
				}
				else if(substr($getData[1],0,-5)!=''){
					$desc2 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else{
					$desc2='';
				}
			}
			return $desc2;
		}
		else if($descNumber == '3'){
			if($jewelryType=='Ring'){
				if(substr($getData[3],0,-5)!=''){
					$desc3 = substr($getData[3],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!=''){
					$desc3 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)==''){
					$desc3 = 'One Free Ring Resizing.';
				}
				else{
					$desc3 = '';
				}
			}
			else{
				if(substr($getData[3],0,-5)!=''){
					$desc3 = substr($getData[3],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!=''){
					$desc3 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)==''){
					$desc3 = '';
				}
				else{
					$desc3='';
				}
			}
			return $desc3;
		}
		else if($descNumber == '4'){
			if($jewelryType=='Ring'){
				if(substr($getData[4],0,-5)!=''){
					$desc4 = substr($getData[4],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!=''){
					$desc4 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)==''){
					$desc4 = 'One Free Ring Resizing.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)=='' && substr($getData[3],0,-5)==''){
					$desc4 = '';
				}
				else{
					$desc4 = '';
				}
			}
			else{
				if(substr($getData[4],0,-5)!=''){
					$desc4 = substr($getData[4],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!=''){
					$desc4 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)==''){
					$desc4 = '';
				}
				else{
					$desc4='';
				}
			}
			return $desc4;
		}
		else if($descNumber == '5'){
			if($jewelryType=='Ring'){
				if(substr($getData[5],0,-5)!=''){
					$desc5 = substr($getData[5],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!='' && substr($getData[4],0,-5)!=''){
					$desc5 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!='' && substr($getData[4],0,-5)==''){
					$desc5 = 'One Free Ring Resizing.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)==''){
					$desc5 = '';
				}
				else{
					$desc5 = '';
				}
			}
			else{
				if(substr($getData[5],0,-5)!=''){
					$desc4 = substr($getData[5],0,-5);
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!='' && substr($getData[4],0,-5)!=''){
					$desc5 = 'Gorgeous Angara Gift Box comes with every purchase.';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)!='' && substr($getData[4],0,-5)==''){
					$desc5 = '';
				}
				else if(substr($getData[1],0,-5)!='' && substr($getData[2],0,-5)!='' && substr($getData[3],0,-5)==''){
					$desc5 = '';
				}
				else{
					$desc5='';
				}
			}
			return $desc5;
		}
	}
	
	// Get Subject Matter condition wise
	public function getSubjectMatter($jewelryType,$descNumber){
		if($jewelryType=='Ring'){
			if($descNumber==1){
					$subjectMatter1 = 'birthstones';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'christmas';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'valentines-day';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'wedding';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'engagement';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Earrings'){
			if($descNumber==1){
					$subjectMatter1 = 'anniversary';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'christmas';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'valentines-day';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'mothers-day';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'birthday';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Pendant'){
			if($descNumber==1){
					$subjectMatter1 = 'anniversary';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'christmas';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'valentines-day';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'birthday';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'mothers-day';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Bracelet'){
			if($descNumber==1){
					$subjectMatter1 = 'birthday';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'graduation';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'valentines-day';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'christmas';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'graduation';
					return $subjectMatter5;
			}
		}
	}
	
	public function getUsedFor($jewelryType,$descNumber){
		
		if($jewelryType=='Ring'){
			if($descNumber==1){
					$subjectMatter1 = 'anniversary';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'engagement';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'christmas';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'wedding';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'engagement';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Earrings'){
			if($descNumber==1){
					$subjectMatter1 = 'anniversary';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'birthday';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'christmas';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'mothers-day';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'valentines-day';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Pendant'){
			if($descNumber==1){
					$subjectMatter1 = 'anniversary';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'birthday';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'christmas';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'mothers-day';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'valentines-day';
					return $subjectMatter5;
			}
		}
		else if($jewelryType=='Bracelet'){
			if($descNumber==1){
					$subjectMatter1 = 'birthday';
					return $subjectMatter1;
			}
			else if($descNumber==2){
					$subjectMatter2 = 'graduation';
					return $subjectMatter2;
			}
			else if($descNumber==3){
					$subjectMatter3 = 'valentines-day';
					return $subjectMatter3;
			}
			else if($descNumber==4){
					$subjectMatter4 = 'christmas';
					return $subjectMatter4;
			}
			else if($descNumber==5){
					$subjectMatter5 = 'graduation';
					return $subjectMatter5;
			}
		}
	
	}
	
	public function getValues($stone1Name,$stone1Grade,$val){
		if(strpos($stone1Name,'Pearl')!=''){
				$stone1Name = 'Pearl';	
			}
			if($stone1Name=='Diamond' && $stone1Grade=='Lab Created'){
				$stone1Grade = 'Simulated';
			}
			if($stone2Name=='Diamond' && $stone2Grade=='Lab Created'){
				$stone2Grade = 'Simulated';
			}
			if($stone3Name=='Diamond' && $stone3Grade=='Lab Created'){
				$stone3Grade = 'Simulated';
			}
			$collection = Mage::getModel('feeds/amazon')->getCollection()
						->addFieldToFilter('stone_name',array('eq' => $stone1Name))
						->addFieldToFilter('stone_grade',array('eq' => $stone1Grade))->getData();
						
			if($val=='cut'){
					return $collection[0]['stone_cut'];	
				}
				else if($val=='color'){
					return $collection[0]['stone_color'];				
				}
				else if($val=='clarity'){
					return $collection[0]['stone_clarity'];				
				}
				else if($val=='creation'){
					return $collection[0]['stone_creation'];				
				}
				else if($val=='treatment'){
					return $collection[0]['stone_treatment'];				
				}					
	}
	public function getValuesDescription($stone1Name,$stone2Name,$stone3Name,$stone4Name,$stone1Grade,$stone2Grade,$stone3Grade,$sku,$val){
		//echo $stone1Name.','.$stone2Name.','.$stone3Name.','.$stone1Grade.','.$stone2Grade.','.$stone3Grade.','.$val;exit;
			if(isset($collection)){
				unset($collection);
			}
			if(strpos($stone1Name,'Pearl')!=''){
				$stone1Name = 'Pearl';	
			}
			if($stone1Name=='Diamond' && $stone1Grade=='Lab Created'){
				$stone1Grade = 'Simulated';
			}
			else if($stone2Name=='Diamond' && $stone2Grade=='Lab Created'){
				$stone2Grade = 'Simulated';
			}
			else if($stone3Name=='Diamond' && $stone3Grade=='Lab Created'){
				$stone3Grade = 'Simulated';
			}
			
			$collections = Mage::getModel('feeds/amazon')->getCollection();
			
			if($stone1Name!='' && $stone2Name=='' && $stone3Name==''){
				$collections->getSelect()->where("stone_name='".$stone1Name."' AND stone_grade='".$stone1Grade."'");
				$collection = $collections->getData();	
			}
			else if($stone1Name!='' && $stone2Name!='' && $stone3Name==''){
				$collections->getSelect()->where("stone_name='".$stone1Name."' AND stone_grade='".$stone1Grade."' OR stone_name='".$stone2Name."' AND stone_grade='".$stone2Grade."'");
				$collection = $collections->getData();
			}
			else if($stone1Name!='' && $stone2Name!='' && $stone3Name!=''){
				$collections->getSelect()->where("stone_name='".$stone1Name."' AND stone_grade='".$stone1Grade."' OR stone_name='".$stone2Name."' AND stone_grade='".$stone2Grade."' OR stone_name='".$stone3Name."' AND stone_grade='".$stone3Grade."'");
				$collection = $collections->getData();
			}
			
				if($val=='description'){
					if($stone1Grade=="Lab Created" || $stone1Grade=="Simulated" || $stone2Grade=="Lab Created" || $stone2Grade=="Simulated" || $stone3Grade=="Lab Created" || $stone3Grade=="Simulated" || substr($sku,0,3)=='SD_'){
						if($stone4Name==1){
						return $data = "* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";
						}
						else{
							return $data = "* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result.";
						}
					}
					else{
						if($stone1Name!='' && $stone2Name=='' && $stone3Name==''){
								$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
						}
						else if($stone1Name!='' && $stone2Name!='' && $stone3Name==''){
							if($stone1Name == $stone2Name){
							$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";		
							}
							else{
							$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>*<b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
							}
						}
						else if($stone1Name!='' && $stone2Name!='' && $stone3Name!=''){
							if($stone1Name == $stone2Name){
								if($stone1Name == $stone3Name){
									if($stone4Name==1){
										$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";			
									}
									else{
							$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
									}
								}
								else{
									if($stone4Name==1){
										$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>*<b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";		
									}
									else{
							$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>*<b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
									}
								}
							}
							else if($stone1Name == $stone3Name){
								if($stone4Name==1){
									$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>*<b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";
								}
								else{
									$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>*<b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
								}
							}
							else{
								if($stone2Name == $stone3Name){
									if($stone4Name==1){
										$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";				
									}
									else{
						$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
									}
							}
								else{
									if($stone4Name==1){
										$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>* <b>".$collection[2]['stone_name']." Quality Grade-".$collection[2]['grade']."(".$collection[2]['stone_grade'].")</b>:".$collection[2]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.<br/><br/>* The mentioned stone size is average. The total carat weight of stones is same as mentioned in product details.";	
									}
									else{
									$data = "* <b>".$collection[0]['stone_name']." Quality Grade-".$collection[0]['grade']."(".$collection[0]['stone_grade'].")</b>:".$collection[0]['description']."<br/><br/>* <b>".$collection[1]['stone_name']." Quality Grade-".$collection[1]['grade']."(".$collection[1]['stone_grade'].")</b>:".$collection[1]['description']."<br/><br/>* <b>".$collection[2]['stone_name']." Quality Grade-".$collection[2]['grade']."(".$collection[2]['stone_grade'].")</b>:".$collection[2]['description']."<br/><br/>* <b>Higher Quality Philosophy</b> - Our jewelry is meant to last for generations. The focus is on maximizing beauty rather than on saving gold or gemstone weight by reducing quality. While our competitors have reduced weight to 2-3 grams for most products as gold costs have gone up, our average product weight remains at 3-5 grams. Even if this philosophy and commitment to fine jewelry costs us more, we believe that the customer will be much happier as a result. <br/><br/>* <b>Direct From The Source:</b>With manufacturing and sourcing offices in Jaipur, India (commonly known as the World's Gemstone Capital), Thailand (where the majority of the world's rubies and sapphires are cut and polished), and the USA, we are able to source the best quality gemstones directly from the mines and manufacturers. Further, we manufacture the large majority of our gemstone jewelry - thus, we cut out all middlemen and are able to offer unparalleled prices. Independent appraisal tests have verified that our jewelry routinely appraises at 150-250% of purchase price.";
									}
								}
							}
							
						}
					return $data;
				}
		}
		else{
				return '';
		}
	}
	
	public function getMergedWeight($product,$wtVal){
		if(isset($_prod)){
			unset($_prod);
		}
		$_prod = $this->getStones($product);
		if(count($_prod)==1){
			if($wtVal=='wt1'){
				return $_prod[0]['weight'];
			}
		}
		else if(count($_prod)==2){
			if($wtVal=='wt1'){
				return $_prod[0]['weight'];
			}
			if($wtVal=='wt2'){
				return $_prod[1]['weight'];
			}
		}
		else if(count($_prod)==3){
			if($wtVal=='wt1'){
				return $_prod[0]['weight'];
			}
			if($wtVal=='wt2'){
				return $_prod[1]['weight'];
			}
			if($wtVal=='wt3'){
				return $_prod[2]['weight'];
			}
		}
		else{
			if($wtVal=='wt1'){
				return $_prod[0]['weight'];
			}
			if($wtVal=='wt2'){
				return $_prod[1]['weight'];
			}
			$wtarr='';
			$temp=0;
			for($i=2;$i<count($_prod);$i++){	
					$wtarr = $temp+$_prod[$i]['weight'];
					$temp=$wtarr;
			}
			if($wtVal=='wt3'){
				return $wtarr;
			}
		}	
	}
	
	public function getMergedSizes($product,$val){
		if(isset($_prod)){
			unset($_prod);
		}
		if(isset($stoneSize)){
			unset($stoneSize);
		}
		if(isset($stoneSize1)){
			unset($stoneSize1);
		}
		if(isset($stoneSize2)){
			unset($stoneSize2);
		}
		if(isset($stoneSizes)){
			unset($stoneSizes);
		}
		if(isset($stoneSizes1)){
			unset($stoneSizes1);
		}
		if(isset($stoneSizes2)){
			unset($stoneSizes2);
		}
		$checkString = '';
		$checkString1 = '';
		$checkString2 = '';
		$_prod = $this->getStones($product);
		if(count($_prod)==1){
			$checkString=strpos($_prod[0]['size'], 'x');
				if($checkString!=''){
					$stoneSizes = explode('x',$_prod[0]['size']);
					if($val=='length1'){
						if($stoneSizes[0]!=''){
							return $stoneSizes[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes)>2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return $stoneSizes[1];
							}else{
								return '';
							}
						}
						if($val=='height1'){
							if($stoneSizes[2]!=''){
								return substr($stoneSizes[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes)==2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return substr($stoneSizes[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length1'){
						if($stoneSize[0]!=''){
							return substr($stoneSize[0],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width1'){
						if($stoneSize[0]!=''){
							return substr($stoneSize[0],0,-2);
						}else{
							return '';
						}
					}
				}
			}
			else if(count($_prod)==2){
				$checkString=strpos($_prod[0]['size'], 'x');
				if($checkString!=''){
					$stoneSizes = explode('x',$_prod[0]['size']);
					if($val=='length1'){
						if($stoneSizes[0]!=''){
							return $stoneSizes[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes)>2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return $stoneSizes[1];
							}else{
								return '';
							}
						}
						if($val=='height1'){
							if($stoneSizes[2]!=''){
								return substr($stoneSizes[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes)==2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return substr($stoneSizes[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
				
				$checkString1=strpos($_prod[1]['size'], 'x');
				if($checkString1!=''){
					$stoneSizes1 = explode('x',$_prod[1]['size']);
					if($val=='length2'){
						if($stoneSizes1[0]!=''){
							return $stoneSizes1[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes1)>2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return $stoneSizes1[1];
							}else{
								return '';
							}
						}
						if($val=='height2'){
							if($stoneSizes1[2]!=''){
								return substr($stoneSizes1[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes1)==2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return substr($stoneSizes1[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
			}
			else if(count($_prod)==3){
				$checkString=strpos($_prod[0]['size'], 'x');
				if($checkString!=''){
					$stoneSizes = explode('x',$_prod[0]['size']);
					if($val=='length1'){
						if($stoneSizes[0]!=''){
							return $stoneSizes[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes)>2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return $stoneSizes[1];
							}else{
								return '';
							}
						}
						if($val=='height1'){
							if($stoneSizes[2]!=''){
								return substr($stoneSizes[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes)==2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return substr($stoneSizes[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
				
				$checkString1=strpos($_prod[1]['size'], 'x');
				if($checkString1!=''){
					$stoneSizes1 = explode('x',$_prod[1]['size']);
					if($val=='length2'){
						if($stoneSizes1[0]!=''){
							return $stoneSizes1[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes1)>2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return $stoneSizes1[1];
							}else{
								return '';
							}
						}
						if($val=='height2'){
							if($stoneSizes1[2]!=''){
								return substr($stoneSizes1[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes1)==2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return substr($stoneSizes1[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
				
				$checkString2=strpos($_prod[2]['size'], 'x');
				if($checkString2!=''){
					$stoneSizes2 = explode('x',$_prod[2]['size']);
					if($val=='length3'){
						if($stoneSizes2[0]!=''){
							return $stoneSizes2[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes2)>2){
						if($val=='width3'){
							if($stoneSizes2[1]!=''){
								return $stoneSizes2[1];
							}else{
								return '';
							}
						}
						if($val=='height3'){
							if($stoneSizes2[2]!=''){
								return substr($stoneSizes2[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes2)==2){
						if($val=='width3'){
							if($stoneSizes2[1]!=''){
								return substr($stoneSizes2[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length3'){
						if($_prod[2]['size']!=''){
							return substr($_prod[2]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width3'){
						if($_prod[2]['size']!=''){
							return substr($_prod[2]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
			}
			else{
				$checkString=strpos($_prod[0]['size'], 'x');
				if($checkString!=''){
					$stoneSizes = explode('x',$_prod[0]['size']);
					if($val=='length1'){
						if($stoneSizes[0]!=''){
							return $stoneSizes[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes)>2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return $stoneSizes[1];
							}else{
								return '';
							}
						}
						if($val=='height1'){
							if($stoneSizes[2]!=''){
								return substr($stoneSizes[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes)==2){
						if($val=='width1'){
							if($stoneSizes[1]!=''){
								return substr($stoneSizes[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width1'){
						if($_prod[0]['size']!=''){
							return substr($_prod[0]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
				
				$checkString1=strpos($_prod[1]['size'], 'x');
				if($checkString1!=''){
					$stoneSizes1 = explode('x',$_prod[1]['size']);
					if($val=='length2'){
						if($stoneSizes1[0]!=''){
							return $stoneSizes1[0];
						}else{
							return '';
						}
					}
					if(count($stoneSizes1)>2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return $stoneSizes1[1];
							}else{
								return '';
							}
						}
						if($val=='height2'){
							if($stoneSizes1[2]!=''){
								return substr($stoneSizes1[2],0,-2);
							}else{
								return '';
							}
						}	
					}
					else if(count($stoneSizes1)==2){
						if($val=='width2'){
							if($stoneSizes1[1]!=''){
								return substr($stoneSizes1[1],0,-2);
							}else{
								return '';
							}
						}
					}
				}
				else{
					// code for first position withou x
					if($val=='length2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
					if($val=='width2'){
						if($_prod[1]['size']!=''){
							return substr($_prod[1]['size'],0,-2);
						}else{
							return '';
						}
					}
				}
				
				
				$starr='';
				$temp=0;
				$num=0;
				for($i=2;$i<count($_prod);$i++){	
						$starr = $temp+substr($_prod[$i]['size'],0,-2);
						$temp=$starr;
						$num++;
				}
				$starr = $starr/$num;
				if($val=='length3'){
					if($starr!=''){
						return number_format($starr,2);
					}else{
						return '';
					}
				}
				if($val=='width3'){
					if($starr!=''){
						return number_format($starr,2);
					}else{
						return '';
					}
				}
			}	
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
	public function getMetalValues($metalType,$val){
			if($metalType!=''){
				$metalType = strtolower($metalType);
				if($metalType=='14k white gold'){
					if($val=='metal_type'){
						return 'White Gold';
					}
					else if($val=='metal_stamp'){
						return '14K';
					}
					else{
						return '';
					}
				}
				if($metalType=='10k white gold'){
					if($val=='metal_type'){
						return 'White Gold';
					}
					else if($val=='metal_stamp'){
						return '10K';
					}
					else{
						return '';
					}
				}
				if($metalType=='18k white gold'){
					if($val=='metal_type'){
						return 'White Gold';
					}
					else if($val=='metal_stamp'){
						return '18K';
					}
					else{
						return '';
					}
				}
				if($metalType=='14k yellow gold'){
					if($val=='metal_type'){
						return 'Yellow Gold';
					}
					else if($val=='metal_stamp'){
						return '14K';
					}
					else{
						return '';
					}
				}
				if($metalType=='10k yellow gold'){
					if($val=='metal_type'){
						return 'Yellow Gold';
					}
					else if($val=='metal_stamp'){
						return '10K';
					}
					else{
						return '';
					}
				}
				if($metalType=='18k yellow gold'){
					if($val=='metal_type'){
						return 'Yellow Gold';
					}
					else if($val=='metal_stamp'){
						return '18K';
					}
					else{
						return '';
					}
				}
				if($metalType=='14k two tone gold'){
					if($val=='metal_type'){
						return 'White-and-yellow-gold';
					}
					else if($val=='metal_stamp'){
						return '14K';
					}
					else{
						return '';
					}
				}
				if($metalType=='10k two tone gold'){
					if($val=='metal_type'){
						return 'White-and-yellow-gold';
					}
					else if($val=='metal_stamp'){
						return '10K';
					}
					else{
						return '';
					}
				}
				if($metalType=='18k two tone gold'){
					if($val=='metal_type'){
						return 'White-and-yellow-gold';
					}
					else if($val=='metal_stamp'){
						return '18K';
					}
					else{
						return '';
					}
				}
				if($metalType=='platinum'){
					if($val=='metal_type'){
						return 'Platinum';
					}
					else if($val=='metal_stamp'){
						return 'plat-950';
					}
					else{
						return '';
					}
				}
				if($metalType=='silver'){
					if($val=='metal_type'){
						return 'Sterling-silver';
					}
					else if($val=='metal_stamp'){
						return '925-sterling';
					}
					else{
						return '';
					}
				}
		}
	}
	
	public function getBrowseNodes($jewelryStyle,$jewelryType){
		if($jewelryType=='Ring'){
			if(in_array('Solitaire',$jewelryStyle)){
				return '197400031';
			}
			else if(in_array('Three Stone',$jewelryStyle)){
				return '197393031';
			}
			else if(in_array('Bridal Sets',$jewelryStyle)){
				return '197395031';
			}
			else if(in_array('Eternity',$jewelryStyle)){
				return '197398031';
			}
			else if(in_array('Cluster',$jewelryStyle)){
				return '197396031';
			}
			else if(in_array('Wedding Bands',$jewelryStyle)){
				return '197402031';
			}
			else if(in_array('Fashion',$jewelryStyle)){
				return '197399031';
			}
			else if(in_array('Engagement',$jewelryStyle)){
				return '197397031';
			}
			else{
				return '197392031';
			}
		}
		else if($jewelryType=='Earrings'){
			if(in_array('Studs',$jewelryStyle)){
				return '197372031';
			}
			else if(in_array('Hoop',$jewelryStyle)){
				return '197369031';
			}
			else if(in_array('Dangle',$jewelryStyle)){
				return '197368031';
			}
			else{
				return '197366031';
			}
		}
		else if($jewelryType=='Pendant'){
			if(in_array('Heart',$jewelryStyle)){
				return '197387031';
			}
			else if(in_array('Cross',$jewelryStyle)){
				return '197386031';
			}
			else{
				return '197385031';
			}
		}
		else if($jewelryType=='Bracelet'){
			return '197406031';
		} 
	}
}