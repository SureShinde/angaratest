<?php
class Angara_Helloworld_IndexController extends Mage_Core_Controller_Front_Action {

	public $groupName = 'Default';

	public function indexAction() {
	    //remove our previous echo
	    //echo 'Hello Index!';
	    $this->loadLayout();
	    $this->renderLayout();
	}
	public function clearcacheAction(){
		$key = $this->getRequest()->getParam('url');
		if($key){
			Mage::helper('helloworld')->clearDataInCache(md5($key));
			echo "Cache cleared for the given url.";
		}
		else{
			echo "No url given to clear cache for.";
		}
	}
	public function goodbyeAction() {
    	$this->loadLayout();
    	$this->renderLayout();          
	}
	public function paramsAction() {
	    echo '<dl>';            
	    foreach($this->getRequest()->getParams() as $key=>$value) {
	        echo '<dt><strong>Param: </strong>'.$key.'</dt>';
	        echo '<dt><strong>Value: </strong>'.$value.'</dt>';
	    }
	    echo '</dl>';
	}
	
	public function minmaxpriceAction(){
		//	Getting list of all configurable products
		ini_set('memory_limit', '8192M');
		$collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
									->addAttributeToFilter('status', 1)
									->addAttributeToFilter('type_id', array('eq' => 'configurable'))
									//->addAttributeToFilter('entity_id', array('eq' => '20188'))
									->addAttributeToSort('entity_id', 'DESC')
									//->load(1);die;
									;
		$resource 	= 	Mage::getSingleton('core/resource');
		$write 		= 	$resource->getConnection('core_write');		// reading the database resource
		if($collectionConfigurable){
			$childIdsArray	=	array();
			foreach ($collectionConfigurable as $_configurableproduct) {
				$configurableProductId	=	$_configurableproduct->getId(); 
				$childIds 				= 	Mage::getModel('catalog/product_type_configurable')
											->getChildrenIds($configurableProductId);	//	Get child products id (only ids)
				$childIdsArray			=	$childIds[0];
	
				$noOfAssociateProducts	=	count($childIdsArray); 
				if($noOfAssociateProducts>1){
					$firstElement	=	array_shift($childIdsArray);
					array_unshift($childIdsArray,$firstElement);				// Push the element on top of array
					$childPrice		=	array();
					foreach($childIdsArray as $child){
						$_child = Mage::getModel('catalog/product')->load($child);
						if($_child->getStatus() == 1)
							$childPrice[] =  $_child->getPrice();
					}
					//echo '<pre>'; print_r($childPrice); die;
					$_minimalPriceValue	=	min($childPrice);
					$_maxPriceValue		=	max($childPrice);
					//	Lets update the minimum price in DB
					
					
					$updatefieldsData 		= 	array('min_price' => $_minimalPriceValue,'max_price' => $_maxPriceValue);
					$updateWhereCondition	=	" entity_id = '".$configurableProductId."' ";
					if(	$write->update('catalog_product_index_price', $updatefieldsData, $updateWhereCondition)	){
						//echo 'Record updated successfully for configurable product ID - '.$configurableProductId.' with minimum price '.$_minimalPriceValue.'<br>';
					}else{
						//echo 'Record not updated for configurable product ID - '.$configurableProductId.'<br>';
					}
				}	//	end if
			}	//	end foreach
		}	//	end if
		echo "Min Max Price Updated.";
	}
	
	public function setAttributeToAttributeSetAtOneTimeAction(){
		$attributeName = $this->getRequest()->getParam('attributecode');
		if($attributeName){
			$allAttributeSetsIds = $this->getRequest()->getParam('attributeset');
			/*echo "<pre>";
			print_r($allAttributeSetsIds);
			exit;*/
			//echo $attributeName;exit;
			foreach($allAttributeSetsIds as $allAttributeSetsId){
				//echo $allAttributeSetsId;exit;
				$this->addAttributeToSetById($attributeName,$allAttributeSetsId);
			}
			echo "done";
		}
		else{
			echo "Attribute Name field can't be left blank.";
		}
	}
	public function studAction(){
		echo "stud exit remove";exit;
		
		
		/*$this->deleteAttribute('stud_weight');
		$this->deleteAttribute('diamond_color');
		$this->deleteAttribute('default_stud_weight');
		$this->deleteAttribute('default_stone1_setting');
		$this->deleteAttribute('default_stone1_shape');
		$this->deleteAttribute('default_stone1_setting');
		$this->deleteAttributeSet('Diamond Studs');
		echo "done";exit;*/
		$studSets =	array('Diamond Studs');
		
		$studSet =	'Diamond Studs';
		
		//0.014,0.025,0.125,0.135,0.2,0.25,0.32,0.325,0.375,0.375,0.38,0.45,0.5,0.63,0.72,0.75,0.9,1
		//
		$studWeightArray = array(
			'<sup>1</sup>&frasl;<sub>4</sub>','<sup>1</sup>&frasl;<sub>2</sub>','<sup>2</sup>&frasl;<sub>3</sub>','<sup>3</sup>&frasl;<sub>4</sub>','<sup>8</sup>&frasl;<sub>9</sub>','1','1 <sup>1</sup>&frasl;<sub>2</sub>','1 <sup>4</sup>&frasl;<sub>5</sub>','2'
			//'0.25','0.5','0.65','0.75','0.9','1','1.5','1.8','2'
		);
		
		
		$studQualityArray =array(
		
			'GH-VS2','GH-SI2','IJ-I1','KM-I2'
		);
		
		$studSettingArray = array(
		
			'4 Prong Basket','Martini','Bezel Martini', 'Margarita'
		);
		
		$diamondColorArray = array(
		
			'White','Blue','Black'
		);
		
		$studopt = array(
			'Martini', 'Margarita'
		);
		
		$studShapeArray	=	array('Round','Princess');
		
		
		echo "Creating Attributes Sets and Attributes";
		
		// Create Dimond Stud Set n related Attribute
		$this->createAttributeSet('Diamond Studs');
		
		$this->createAttribute('Stud Weight', 'stud_weight', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('stud_weight', $studWeightArray);
		
		$this->createAttribute('Diamond Color', 'diamond_color', array(
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('diamond_color', $diamondColorArray);
		
		$this->createAttribute('Default Stud Weight', 'default_stud_weight', array(
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('default_stud_weight', $studWeightArray);
		
		$this->createAttribute('Default Stone Setting', 'default_setting_style', array(
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('default_setting_style', $studSettingArray);
		
		$this->createAttribute('Default Stone Shape', 'default_stone1_shape', array(
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('default_stone1_shape', $studShapeArray);
		
		$this->createAttribute('Setting Style', 'setting_style', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $studSets);
		$this->addAttributeOption('setting_style', $studSettingArray);
		
		// Add Attribute Option To existing Attribute
		$this->addAttributeOption('default_stone1_grade', $studQualityArray);
		$this->addAttributeOption('stone1_grade', $studQualityArray);
		$this->addAttributeOption('stone2_grade', $studQualityArray);
		$this->addAttributeOption('stone1_setting', $studopt);
		
		
		//Assign existing Attribute To Dimond Stud Set
		
		$this->addAttributeToSet('stone1_grade',$studSet);
		$this->addAttributeToSet('metal1_type', $studSet);
		$this->addAttributeToSet('stone1_shape', $studSet);
		$this->addAttributeToSet('stone1_setting', $studSet);
		$this->addAttributeToSet('stone1_name', $studSet);
		$this->addAttributeToSet('stone1_count', $studSet);
		$this->addAttributeToSet('stone1_cut', $studSet);
		$this->addAttributeToSet('stone1_type', $studSet);
		
		$this->addAttributeToSet('stone2_grade', $studSet);
		$this->addAttributeToSet('stone2_shape', $studSet);
		$this->addAttributeToSet('stone2_setting', $studSet);
		$this->addAttributeToSet('stone2_name', $studSet);
		$this->addAttributeToSet('stone2_count', $studSet);
		$this->addAttributeToSet('stone2_cut', $studSet);
		$this->addAttributeToSet('stone2_type', $studSet);
		
		$this->addAttributeToSet('post1_type', $studSet);
		$this->addAttributeToSet('butterfly1_type', $studSet);
		$this->addAttributeToSet('stopper1_type', $studSet);
		//$this->addAttributeToSet('filterable_stone_shapes', $studSet);
		//$this->addAttributeToSet('filterable_stone_names', $studSet);
		//$this->addAttributeToSet('filterable_carat_weight_ranges', $studSet);
		//$this->addAttributeToSet('filterable_metal_types', $studSet);
		$this->addAttributeToSet('jewelry_styles', $studSet);
		//$this->addAttributeToSet('filterable_stone_grades', $studSet);

		$this->addAttributeToSet('default_stone1_grade', $studSet);
		$this->addAttributeToSet('default_metal1_type', $studSet);
		$this->addAttributeToSet('stone_variation_count', $studSet);
		$this->addAttributeToSet('metal_variation_count', $studSet);
		$this->addAttributeToSet('video', $studSet);
		$this->addAttributeToSet('jewelry_type', $studSet);
		$this->addAttributeToSet('master_sku', $studSet);	
	
		echo "done";
	}
	
	public function setupmoreAction(){
		
		echo "remove exit";exit;
		// managing set array
		$allStoneSets = array(
			'Ring with 11 variation of stone', 
			'Ring with 12 variation of stone', 
			'Ring with 13 variation of stone', 
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			
			'Pendant with 11 variation of stone', 
			'Pendant with 12 variation of stone', 
			'Pendant with 13 variation of stone', 
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			
			'Earrings with 11 variation of stone', 
			'Earrings with 12 variation of stone', 
			'Earrings with 13 variation of stone', 
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			
			'Two Tone Ring with 11 variation of stone',
			'Two Tone Ring with 12 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Two Tone Pendant with 11 variation of stone',
			'Two Tone Pendant with 12 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Two Tone Earrings with 11 variation of stone',
			'Two Tone Earrings with 12 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		
		);
		$twoMetalForMetal = array(
			'Two Tone Ring with 11 variation of stone',
			'Two Tone Ring with 12 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Two Tone Pendant with 11 variation of stone',
			'Two Tone Pendant with 12 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Two Tone Earrings with 11 variation of stone',
			'Two Tone Earrings with 12 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		
		);
		$twoMetal6s = array(
			'Two Tone Ring with 6 variation of stone', 
			'Two Tone Ring with 7 variation of stone', 
			'Two Tone Ring with 8 variation of stone', 
			'Two Tone Ring with 9 variation of stone',
			'Two Tone Ring with 10 variation of stone',
			
			'Two Tone Pendant with 6 variation of stone', 
			'Two Tone Pendant with 7 variation of stone', 
			'Two Tone Pendant with 8 variation of stone', 
			'Two Tone Pendant with 9 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			
			'Two Tone Earrings with 6 variation of stone', 
			'Two Tone Earrings with 7 variation of stone', 
			'Two Tone Earrings with 8 variation of stone', 
			'Two Tone Earrings with 9 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			
		
		);
		
		$twoMetal7s = array(
			'Two Tone Ring with 7 variation of stone', 
			'Two Tone Ring with 8 variation of stone', 
			'Two Tone Ring with 9 variation of stone',
			'Two Tone Ring with 10 variation of stone',
			
			'Two Tone Pendant with 7 variation of stone', 
			'Two Tone Pendant with 8 variation of stone', 
			'Two Tone Pendant with 9 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			
			'Two Tone Earrings with 7 variation of stone', 
			'Two Tone Earrings with 8 variation of stone', 
			'Two Tone Earrings with 9 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			
		
		);
		$twoMetal8s = array(
			'Two Tone Ring with 8 variation of stone', 
			'Two Tone Ring with 9 variation of stone',
			'Two Tone Ring with 10 variation of stone',
			
			'Two Tone Pendant with 8 variation of stone', 
			'Two Tone Pendant with 9 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			
			'Two Tone Earrings with 8 variation of stone', 
			'Two Tone Earrings with 9 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			
		
		);
		$twoMetal9s = array(
			'Two Tone Ring with 9 variation of stone',
			'Two Tone Ring with 10 variation of stone',
			
			'Two Tone Pendant with 9 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			
			'Two Tone Earrings with 9 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			
		
		);
		
		$twoMetal10s = array(
			'Two Tone Ring with 10 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			
		
		);
		
		$elevanStoneSet = array(
			'Ring with 11 variation of stone',
			'Ring with 12 variation of stone',
			'Ring with 13 variation of stone',
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			'Two Tone Ring with 11 variation of stone',
			'Two Tone Ring with 12 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Pendant with 11 variation of stone',
			'Pendant with 12 variation of stone',
			'Pendant with 13 variation of stone',
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 11 variation of stone',
			'Two Tone Pendant with 12 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Earrings with 11 variation of stone',
			'Earrings with 12 variation of stone',
			'Earrings with 13 variation of stone',
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 11 variation of stone',
			'Two Tone Earrings with 12 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		
		$twelveStoneSet = array(
			'Ring with 12 variation of stone',
			'Ring with 13 variation of stone',
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			'Two Tone Ring with 12 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Pendant with 12 variation of stone',
			'Pendant with 13 variation of stone',
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 12 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Earrings with 12 variation of stone',
			'Earrings with 13 variation of stone',
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 12 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		$thirteenStoneSet = array(
			'Ring with 13 variation of stone',
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Pendant with 13 variation of stone',
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Earrings with 13 variation of stone',
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		$fourteenStoneSet = array(
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		$fifteenStoneSet = array(
			'Ring with 15 variation of stone',
			'Two Tone Ring with 15 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		$allRingSets = array(
			'Ring with 11 variation of stone',
			'Ring with 12 variation of stone',
			'Ring with 13 variation of stone',
			'Ring with 14 variation of stone',
			'Ring with 15 variation of stone',
			'Two Tone Ring with 11 variation of stone',
			'Two Tone Ring with 12 variation of stone',
			'Two Tone Ring with 13 variation of stone',
			'Two Tone Ring with 14 variation of stone',
			'Two Tone Ring with 15 variation of stone',
		);
		
		$allPendantSets = array(
			'Pendant with 11 variation of stone',
			'Pendant with 12 variation of stone',
			'Pendant with 13 variation of stone',
			'Pendant with 14 variation of stone',
			'Pendant with 15 variation of stone',
			'Two Tone Pendant with 6 variation of stone',
			'Two Tone Pendant with 7 variation of stone',
			'Two Tone Pendant with 8 variation of stone',
			'Two Tone Pendant with 9 variation of stone',
			'Two Tone Pendant with 10 variation of stone',
			'Two Tone Pendant with 11 variation of stone',
			'Two Tone Pendant with 12 variation of stone',
			'Two Tone Pendant with 13 variation of stone',
			'Two Tone Pendant with 14 variation of stone',
			'Two Tone Pendant with 15 variation of stone',
		);
		
		$allEarringsSets = array(
			'Earrings with 11 variation of stone',
			'Earrings with 12 variation of stone',
			'Earrings with 13 variation of stone',
			'Earrings with 14 variation of stone',
			'Earrings with 15 variation of stone',
			'Two Tone Earrings with 6 variation of stone',
			'Two Tone Earrings with 7 variation of stone',
			'Two Tone Earrings with 8 variation of stone',
			'Two Tone Earrings with 9 variation of stone',
			'Two Tone Earrings with 10 variation of stone',
			'Two Tone Earrings with 11 variation of stone',
			'Two Tone Earrings with 12 variation of stone',
			'Two Tone Earrings with 13 variation of stone',
			'Two Tone Earrings with 14 variation of stone',
			'Two Tone Earrings with 15 variation of stone',
		);
		
		
		
		
		// creating attribute sets
		
		
		
		
		$this->createAttributeSet('Ring with 11 variation of stone');
		$this->createAttributeSet('Ring with 12 variation of stone');
		$this->createAttributeSet('Ring with 13 variation of stone');
		$this->createAttributeSet('Ring with 14 variation of stone');
		$this->createAttributeSet('Ring with 15 variation of stone');
		
		$this->createAttributeSet('Pendant with 11 variation of stone');
		$this->createAttributeSet('Pendant with 12 variation of stone');
		$this->createAttributeSet('Pendant with 13 variation of stone');
		$this->createAttributeSet('Pendant with 14 variation of stone');
		$this->createAttributeSet('Pendant with 15 variation of stone');
		
		$this->createAttributeSet('Earrings with 11 variation of stone');
		$this->createAttributeSet('Earrings with 12 variation of stone');
		$this->createAttributeSet('Earrings with 13 variation of stone');
		$this->createAttributeSet('Earrings with 14 variation of stone');
		$this->createAttributeSet('Earrings with 15 variation of stone');
		
		$this->createAttributeSet('Two Tone Ring with 6 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 7 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 8 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 9 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 10 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 11 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 12 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 13 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 14 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 15 variation of stone');
		
		$this->createAttributeSet('Two Tone Pendant with 6 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 7 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 8 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 9 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 10 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 11 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 12 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 13 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 14 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 15 variation of stone');
		
		$this->createAttributeSet('Two Tone Earrings with 6 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 7 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 8 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 9 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 10 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 11 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 12 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 13 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 14 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 15 variation of stone');
		
		# @todo add more options in array
		# @todo remove X.0 size if X is there (e.g. 4.0 and 4 both are present)
		$stonesArray = array('Amazonite', 'Amethyst', 'Aquamarine', 'Blue Topaz', 'Swiss Blue Topaz', 'Carnelian', 'Citrine', 'Colored Diamond', 'Diamond', 'Emerald', 'Garnet', 'Green Amethyst', 'Lapis Lazuli', 'Lemon Quartz', 'Opal', 'Pearl', 'Peridot', 'Pink Amethyst', 'Pink Sapphire', 'Pink Tourmaline', 'Rose Quartz', 'Ruby', 'Sapphire', 'Smoky Quartz', 'Tanzanite', 'Turquoise', 'White Sapphire', 'Yellow Sapphire', 'Natural Pink Diamond', 'Akoya Cultured Pearl', 'Black Tahitian Cultured Pearl', 'Freshwater Cultured Pearl');
		$stoneShapesArray = array('Baguette', 'Ball', 'Cushion', 'Drop', 'Emerald', 'Half Moon', 'Heart', 'Marquise', 'Oval', 'Pear', 'Round', 'Square', 'Trillion', 'Asscher', 'Radiant', 'Trapezoid', 'Rectangle' ,'Princess');
		$stoneSizesArray = array('0.6mm', '0.7mm', '0.8mm', '0.9mm','0.95mm', '1mm', '1.1mm', '1.15mm', '1.15x0.8mm', '1.1x0.8mm', '1.2mm', '1.25mm', '1.25x1x0.75mm', '1.2x0.8mm', '1.3mm', '1.3x0.8mm', '1.4mm', '1.4x0.8mm', '1.5mm', '1.5x1mm', '1.5x1.25x1mm', '1.5x1x0.75mm', '1.5x3mm', '1.6mm', '1.7mm', '1.75x0.75x0.5mm', '1.75x1.25x1mm', '1.75x1.5x1mm', '1.75x1.5x1.25mm', '1.75x1x0.5mm', '1.75x1x0.75mm', '1.8mm', '1.9mm', '10mm', '10.5mm', '10x5mm', '10x7mm', '10x8mm', '11mm', '11x9mm', '12mm', '12x10mm', '12x6mm', '12x8mm', '13mm', '14mm', '14x10mm', '14x12mm', '14x9mm', '15mm', '15x10mm', '16mm', '16x12mm', '16x8mm', '17mm', '18mm', '19mm', '1x0.6mm', '1x0.8mm', '2mm', '2.1mm', '2.2mm', '2.25x1.25x0.75mm', '2.25x1x0.5mm', '2.3mm', '2.4mm', '2.5mm', '2.6mm', '2.7mm', '2.8mm', '2.9mm', '20mm', '2x1.25x1mm', '2x1.5x1mm', '2x1.5x1.25mm', '2x1x0.5mm', '2x1x0.75mm', '2x1.5mm', '3.5x2x2.5mm', '4x2.5x3mm', '4.5x3x3.5mm', '5.5x3.5mm', '3mm', '3.1mm', '3.2mm', '3.3mm', '3.4mm', '3.5mm', '3.6mm', '3.7mm', '3.8mm', '3.9mm', '3x1.5mm', '3x2mm', '4mm', '4.1mm', '4.2mm', '4.3mm', '4.4mm', '4.5mm', '4.6mm', '4.7mm', '4.8mm', '4.9mm', '4x2mm', '4x3mm', '5mm', '5.1mm', '5.2mm', '5.3mm', '5.4mm', '5.5mm', '5.6mm', '5.7mm', '5.8mm', '5.9mm', '5x2.5mm', '5x3mm', '5x4mm', '6mm', '6.1mm', '6.2mm', '6.3mm', '6.4mm', '6.5mm', '6.5x4.5mm', '6.6mm', '6.7mm', '6.8mm', '6.9mm', '6x3mm', '6x4mm', '7mm', '7.5mm', '7.5x5.5mm', '7x3.5mm', '7x5mm', '8mm', '8.5mm', '8.5x6.5mm', '8x4mm', '8x5mm', '8x6mm', '9mm', '9.5mm', '9.5x6mm', '9x6mm', '9x6.5mm', '9x7mm', '10x6mm', '14x8mm');
		$stoneGradesArray = array('A', 'AA', 'AAA', 'AAAA', 'Lab Created', 'J I2', 'I I1', 'H SI2', 'G-H VS', 'J-M I2-I3', 'H-I I1', 'H SI1-SI2', 'G-H VS1-VS2', 'JK I2-I3', 'GH I1', 'H I4', 'GH I2-I3', 'GH Blue I2-I3', 'I1-I4', 'JK I1-I3', 'GH Black I1-I3', 'GH I2,I3', 'GH Blue I1,I2', 'GH I1,I3', 'GH I4', 'JK I1', 'Blue I1', 'GH Blue I1', 'Blue (Color Enhanced) I2', 'Black (Color Enhanced) I2', 'Black I1', 'VS1-VS2', 'SI1-SI2', 'JM I2-I3', 'H I I1', 'I3', 'Blue I1', 'GH Black (Color Enhanced) I1-I3', 'JK I2 I3', 'I I2', 'Yellow I1', 'GH, Blue (Color Enhanced) I2-I3', 'GH, Black (Color Enhanced) I1-I3', 'GH, Blue (Color Enhanced) I1-I2', 'GH, Blue (Color Enhanced) I1');
		$stoneTypesArray = array('Gemstone', 'Diamond');
		$stoneCutsArray = array('Asscher', 'Brilliant', 'Cabochon', 'Emerald', 'Faceted', 'Princess', 'Radiant', 'Step Cut', 'Sugarloaf');
		$metalTypesArray = array('Silver', '18K Yellow Gold', '18K White Gold','10K White Gold', '10K Yellow Gold', '14K White Gold', '14K Yellow Gold', '14K Rose Gold', 'Platinum' , '14K Two Tone Gold', '10K Two Tone Gold','18K Two Tone Gold', '14K Tricolor Gold', '10K Tricolor Gold', '18K Tricolor Gold', 'Two Tone Gold', '18k White & 14k Rose Gold');
		$filterableMetalTypesArray = array('Silver', 'White Gold', 'Yellow Gold', 'Platinum','Rose Gold' , 'Two Tone Gold', 'Tricolor Gold');
		$filterableStoneGradesArray = array('A - Good', 'AA - Better', 'AAA - Best', 'AAAA - Heirloom', 'Lab Created', 'J I2', 'I I1', 'H SI2', 'G-H VS', 'J-M I2-I3', 'H-I I1', 'H SI1-SI2', 'G-H VS1-VS2', 'JK I2-I3', 'GH I1', 'H I4', 'GH I2-I3', 'GH Blue I2-I3', 'I1-I4', 'JK I1-I3', 'GH Black I1-I3', 'GH I2,I3', 'GH I4', 'JK I1', 'Blue I1', 'GH Blue I1', 'Blue (Color Enhanced) I2', 'Black (Color Enhanced) I2', 'Black I1', 'VS1-VS2', 'SI1-SI2', 'JM I2-I3', 'H I I1', 'I3', 'Blue I1', 'GH Black (Color Enhanced) I1-I3', 'JK I2 I3', 'I I2', 'Yellow I1', 'GH, Blue (Color Enhanced) I2-I3', 'GH, Black (Color Enhanced) I1-I3', 'GH, Blue (Color Enhanced) I1-I2', 'GH, Blue (Color Enhanced) I1');
		$chainTypesArray = array('Box', 'Curb', 'Cable', 'Rope', 'Link', 'Wheat');
		$postTypesArray = array('Single Notch', 'Double Notch', 'Threaded', 'V-cut Post');
		$butterflyTypesArray = array('Push Back', 'Screw Back', 'Hinged Clip', 'Shepherd Hook', 'Lever Back', 'Fishhook');
		$stopperTypesArray = array('Plastic Round', 'Rubber Round');
		$filterableCaratWeightRangesArray = array('0.01 - 0.50', '0.51 - 1.00', '1.01 - 1.50', '1.51 - 3.00', 'Over 3.01');
		$jewelryStylesArray = array('Antique','Art Deco', 'Bridal Sets', 'Cathedral','Celtic','Charm','Cluster','Cocktail','Critters','Cross','Dangle','Designer','Engagement', 'Eternity', 'Fashion','Five Stone','Floral','Halo','Heart','Hoop','Journey','Key','Nine Stone','Seven Stone','Snow Flakes','Solitaire','Split Shank','Stackable','Studs','Three Stone','V-Bale','Vintage', 'Wedding Bands');
		$stoneSettingsArray = array('Bar','Bezel','Channel','Flush','Gypsy','Invisible','Pave','Peg','Pressure','Prong','Semi Bezel','Strung on Silk Thread','Tension','Top Drill');
		
		$jewelryType = array('Ring', 'Pendant', 'Earrings', 'Bracelet');
		
		echo "Creating attributes";

		// Crating attributes
		// Stones
		
				
		$this->createAttribute('Accent Stone 10', 'stone11_name', array(
							'is_used_for_promo_rules'       => '1',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_name', $stonesArray);
		//echo "done-test";exit;
		$this->createAttribute('Accent Stone 11', 'stone12_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 12', 'stone13_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 13', 'stone14_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 14', 'stone15_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_name', $stonesArray);
		
		//Multiple Attribute Set upload
		
		// Shapes
		$this->createAttribute('Accent Stone 10 Shape', 'stone11_shape', array(
						'is_required'                   => '0',
						'backend_type'                => 'int',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 11 Shape', 'stone12_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 12 Shape', 'stone13_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 13 Shape', 'stone14_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 14 Shape', 'stone15_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_shape', $stoneShapesArray);
		
		//Multiple Attribute Set upload
		
		// Sizes
		$this->createAttribute('Accent Stone 10 Size', 'stone11_size', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 11 Size', 'stone12_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 12 Size', 'stone13_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 13 Size', 'stone14_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 14 Size', 'stone15_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_size', $stoneSizesArray);
		
		//Multiple Attribute Set upload
		
		// Grades
		$this->createAttribute('Accent Stone 10 Grade', 'stone11_grade', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 11 Grade', 'stone12_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 12 Grade', 'stone13_grade', array(
                        'is_required'                   => '0',
						'backend_type'                => 'int',
						), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 13 Grade', 'stone14_grade', array(
						'is_required'                   => '0',
						'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 14 Grade', 'stone15_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_grade', $stoneGradesArray);
		
		// Multiple Sets Add
		
		// Types
		$this->createAttribute('Accent Stone 10 Type', 'stone11_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 11 Type', 'stone12_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 12 Type', 'stone13_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 13 Type', 'stone14_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 14 Type', 'stone15_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_type', $stoneTypesArray);
		
		//Multiple Sets Add
		
		// Cuts
		$this->createAttribute('Accent Stone 10 Cut', 'stone11_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 11 Cut', 'stone12_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 12 Cut', 'stone13_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 13 Cut', 'stone14_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 14 Cut', 'stone15_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_cut', $stoneCutsArray);
		
		//Multiple Sets Add
		
		// Settings
		$this->createAttribute('Accent Stone 10 Setting', 'stone11_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->addAttributeOption('stone11_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 11 Setting', 'stone12_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->addAttributeOption('stone12_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 12 Setting', 'stone13_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->addAttributeOption('stone13_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 13 Setting', 'stone14_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->addAttributeOption('stone14_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 14 Setting', 'stone15_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fifteenStoneSet);
		$this->addAttributeOption('stone15_setting', $stoneSettingsArray);
		
		//Multiple Sets Add
		
		// Weights
		$this->createAttribute('Accent Stone 10 Weight', 'stone11_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->createAttribute('Accent Stone 11 Weight', 'stone12_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->createAttribute('Accent Stone 12 Weight', 'stone13_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->createAttribute('Accent Stone 13 Weight', 'stone14_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->createAttribute('Accent Stone 14 Weight', 'stone15_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $fifteenStoneSet);
		
		//Multiple Sets Add
		
		// Counts
		$this->createAttribute('Accent Stone 10 Count', 'stone11_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $elevanStoneSet);
		$this->createAttribute('Accent Stone 11 Count', 'stone12_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $twelveStoneSet);
		$this->createAttribute('Accent Stone 12 Count', 'stone13_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $thirteenStoneSet);
		$this->createAttribute('Accent Stone 13 Count', 'stone14_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $fourteenStoneSet);
		$this->createAttribute('Accent Stone 14 Count', 'stone15_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $fifteenStoneSet);
						
		//Multiple Sets Add
						
		// Metals
		
		foreach($allStoneSets as $allStoneSet){
			$this->addAttributeToSet('stone1_name',$allStoneSet);
			$this->addAttributeToSet('stone1_name',$allStoneSet);
			$this->addAttributeToSet('stone2_name',$allStoneSet);
			$this->addAttributeToSet('stone3_name',$allStoneSet);
			$this->addAttributeToSet('stone4_name',$allStoneSet);
			$this->addAttributeToSet('stone5_name',$allStoneSet);
			$this->addAttributeToSet('stone6_name',$allStoneSet);
			$this->addAttributeToSet('stone7_name',$allStoneSet);
			$this->addAttributeToSet('stone8_name',$allStoneSet);
			$this->addAttributeToSet('stone9_name',$allStoneSet);
			$this->addAttributeToSet('stone10_name',$allStoneSet);
			
			$this->addAttributeToSet('stone1_shape',$allStoneSet);
			$this->addAttributeToSet('stone2_shape',$allStoneSet);
			$this->addAttributeToSet('stone3_shape',$allStoneSet);
			$this->addAttributeToSet('stone4_shape',$allStoneSet);
			$this->addAttributeToSet('stone5_shape',$allStoneSet);
			$this->addAttributeToSet('stone6_shape',$allStoneSet);
			$this->addAttributeToSet('stone7_shape',$allStoneSet);
			$this->addAttributeToSet('stone8_shape',$allStoneSet);
			$this->addAttributeToSet('stone9_shape',$allStoneSet);
			$this->addAttributeToSet('stone10_shape',$allStoneSet);
			
			$this->addAttributeToSet('stone1_size',$allStoneSet);
			$this->addAttributeToSet('stone2_size',$allStoneSet);
			$this->addAttributeToSet('stone3_size',$allStoneSet);
			$this->addAttributeToSet('stone4_size',$allStoneSet);
			$this->addAttributeToSet('stone5_size',$allStoneSet);
			$this->addAttributeToSet('stone6_size',$allStoneSet);
			$this->addAttributeToSet('stone7_size',$allStoneSet);
			$this->addAttributeToSet('stone8_size',$allStoneSet);
			$this->addAttributeToSet('stone9_size',$allStoneSet);
			$this->addAttributeToSet('stone10_size',$allStoneSet);
			
			$this->addAttributeToSet('stone1_grade',$allStoneSet);
			$this->addAttributeToSet('stone2_grade',$allStoneSet);
			$this->addAttributeToSet('stone3_grade',$allStoneSet);
			$this->addAttributeToSet('stone4_grade',$allStoneSet);
			$this->addAttributeToSet('stone5_grade',$allStoneSet);
			$this->addAttributeToSet('stone6_grade',$allStoneSet);
			$this->addAttributeToSet('stone7_grade',$allStoneSet);
			$this->addAttributeToSet('stone8_grade',$allStoneSet);
			$this->addAttributeToSet('stone9_grade',$allStoneSet);
			$this->addAttributeToSet('stone10_grade',$allStoneSet);
			
			$this->addAttributeToSet('stone1_type',$allStoneSet);
			$this->addAttributeToSet('stone2_type',$allStoneSet);
			$this->addAttributeToSet('stone3_type',$allStoneSet);
			$this->addAttributeToSet('stone4_type',$allStoneSet);
			$this->addAttributeToSet('stone5_type',$allStoneSet);
			$this->addAttributeToSet('stone6_type',$allStoneSet);
			$this->addAttributeToSet('stone7_type',$allStoneSet);
			$this->addAttributeToSet('stone8_type',$allStoneSet);
			$this->addAttributeToSet('stone9_type',$allStoneSet);
			$this->addAttributeToSet('stone10_type',$allStoneSet);
			
			$this->addAttributeToSet('stone1_cut',$allStoneSet);
			$this->addAttributeToSet('stone2_cut',$allStoneSet);
			$this->addAttributeToSet('stone3_cut',$allStoneSet);
			$this->addAttributeToSet('stone4_cut',$allStoneSet);
			$this->addAttributeToSet('stone5_cut',$allStoneSet);
			$this->addAttributeToSet('stone6_cut',$allStoneSet);
			$this->addAttributeToSet('stone7_cut',$allStoneSet);
			$this->addAttributeToSet('stone8_cut',$allStoneSet);
			$this->addAttributeToSet('stone9_cut',$allStoneSet);
			$this->addAttributeToSet('stone10_cut',$allStoneSet);
			
			$this->addAttributeToSet('stone1_setting',$allStoneSet);
			$this->addAttributeToSet('stone2_setting',$allStoneSet);
			$this->addAttributeToSet('stone3_setting',$allStoneSet);
			$this->addAttributeToSet('stone4_setting',$allStoneSet);
			$this->addAttributeToSet('stone5_setting',$allStoneSet);
			$this->addAttributeToSet('stone6_setting',$allStoneSet);
			$this->addAttributeToSet('stone7_setting',$allStoneSet);
			$this->addAttributeToSet('stone8_setting',$allStoneSet);
			$this->addAttributeToSet('stone9_setting',$allStoneSet);
			$this->addAttributeToSet('stone10_setting',$allStoneSet);
			
			$this->addAttributeToSet('stone1_weight',$allStoneSet);
			$this->addAttributeToSet('stone2_weight',$allStoneSet);
			$this->addAttributeToSet('stone3_weight',$allStoneSet);
			$this->addAttributeToSet('stone4_weight',$allStoneSet);
			$this->addAttributeToSet('stone5_weight',$allStoneSet);
			$this->addAttributeToSet('stone6_weight',$allStoneSet);
			$this->addAttributeToSet('stone7_weight',$allStoneSet);
			$this->addAttributeToSet('stone8_weight',$allStoneSet);
			$this->addAttributeToSet('stone9_weight',$allStoneSet);
			$this->addAttributeToSet('stone10_weight',$allStoneSet);
			
			$this->addAttributeToSet('stone1_count',$allStoneSet);
			$this->addAttributeToSet('stone2_count',$allStoneSet);
			$this->addAttributeToSet('stone3_count',$allStoneSet);
			$this->addAttributeToSet('stone4_count',$allStoneSet);
			$this->addAttributeToSet('stone5_count',$allStoneSet);
			$this->addAttributeToSet('stone6_count',$allStoneSet);
			$this->addAttributeToSet('stone7_count',$allStoneSet);
			$this->addAttributeToSet('stone8_count',$allStoneSet);
			$this->addAttributeToSet('stone9_count',$allStoneSet);
			$this->addAttributeToSet('stone10_count',$allStoneSet);
			
			$this->addAttributeToSet('metal1_type',$allStoneSet);
			$this->addAttributeToSet('filterable_stone_shapes',$allStoneSet);
			$this->addAttributeToSet('filterable_stone_names',$allStoneSet);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$allStoneSet);
			$this->addAttributeToSet('filterable_metal_types',$allStoneSet);
			$this->addAttributeToSet('jewelry_styles',$allStoneSet);
			$this->addAttributeToSet('filterable_stone_grades',$allStoneSet);
			$this->addAttributeToSet('default_stone1_size',$allStoneSet);
			$this->addAttributeToSet('default_stone1_grade',$allStoneSet);
			$this->addAttributeToSet('default_metal1_type',$allStoneSet);
			$this->addAttributeToSet('stone_variation_count',$allStoneSet);
			$this->addAttributeToSet('metal_variation_count',$allStoneSet);
			$this->addAttributeToSet('jewelry_type',$allStoneSet);
			$this->addAttributeToSet('master_sku',$allStoneSet);
			$this->addAttributeToSet('yellow_gold_image',$allStoneSet);;
			$this->addAttributeToSet('white_gold_image',$allStoneSet);
		}
		foreach($allPendantSets as $allPendantSet){
			$this->addAttributeToSet('chain1_type',$allPendantSet);
			$this->addAttributeToSet('chain1_length',$allPendantSet);
		}
		foreach($allEarringsSets as $allEarringsSet){
			$this->addAttributeToSet('post1_type',$allEarringsSet);
			$this->addAttributeToSet('butterfly1_type',$allEarringsSet);
			$this->addAttributeToSet('stopper1_type',$allEarringsSet);
		}
		foreach($twoMetal6s as $twoMetal6){
			$this->addAttributeToSet('stone1_name',$twoMetal6);
			$this->addAttributeToSet('stone1_name',$twoMetal6);
			$this->addAttributeToSet('stone2_name',$twoMetal6);
			$this->addAttributeToSet('stone3_name',$twoMetal6);
			$this->addAttributeToSet('stone4_name',$twoMetal6);
			$this->addAttributeToSet('stone5_name',$twoMetal6);
			$this->addAttributeToSet('stone6_name',$twoMetal6);
		
			$this->addAttributeToSet('stone1_shape',$twoMetal6);
			$this->addAttributeToSet('stone2_shape',$twoMetal6);
			$this->addAttributeToSet('stone3_shape',$twoMetal6);
			$this->addAttributeToSet('stone4_shape',$twoMetal6);
			$this->addAttributeToSet('stone5_shape',$twoMetal6);
			$this->addAttributeToSet('stone6_shape',$twoMetal6);
			
			$this->addAttributeToSet('stone1_size',$twoMetal6);
			$this->addAttributeToSet('stone2_size',$twoMetal6);
			$this->addAttributeToSet('stone3_size',$twoMetal6);
			$this->addAttributeToSet('stone4_size',$twoMetal6);
			$this->addAttributeToSet('stone5_size',$twoMetal6);
			$this->addAttributeToSet('stone6_size',$twoMetal6);
			
			$this->addAttributeToSet('stone1_grade',$twoMetal6);
			$this->addAttributeToSet('stone2_grade',$twoMetal6);
			$this->addAttributeToSet('stone3_grade',$twoMetal6);
			$this->addAttributeToSet('stone4_grade',$twoMetal6);
			$this->addAttributeToSet('stone5_grade',$twoMetal6);
			$this->addAttributeToSet('stone6_grade',$twoMetal6);
			
			$this->addAttributeToSet('stone1_type',$twoMetal6);
			$this->addAttributeToSet('stone2_type',$twoMetal6);
			$this->addAttributeToSet('stone3_type',$twoMetal6);
			$this->addAttributeToSet('stone4_type',$twoMetal6);
			$this->addAttributeToSet('stone5_type',$twoMetal6);
			$this->addAttributeToSet('stone6_type',$twoMetal6);
			
			$this->addAttributeToSet('stone1_cut',$twoMetal6);
			$this->addAttributeToSet('stone2_cut',$twoMetal6);
			$this->addAttributeToSet('stone3_cut',$twoMetal6);
			$this->addAttributeToSet('stone4_cut',$twoMetal6);
			$this->addAttributeToSet('stone5_cut',$twoMetal6);
			$this->addAttributeToSet('stone6_cut',$twoMetal6);
			
			$this->addAttributeToSet('stone1_setting',$twoMetal6);
			$this->addAttributeToSet('stone2_setting',$twoMetal6);
			$this->addAttributeToSet('stone3_setting',$twoMetal6);
			$this->addAttributeToSet('stone4_setting',$twoMetal6);
			$this->addAttributeToSet('stone5_setting',$twoMetal6);
			$this->addAttributeToSet('stone6_setting',$twoMetal6);
			
			$this->addAttributeToSet('stone1_weight',$twoMetal6);
			$this->addAttributeToSet('stone2_weight',$twoMetal6);
			$this->addAttributeToSet('stone3_weight',$twoMetal6);
			$this->addAttributeToSet('stone4_weight',$twoMetal6);
			$this->addAttributeToSet('stone5_weight',$twoMetal6);
			$this->addAttributeToSet('stone6_weight',$twoMetal6);
			
			$this->addAttributeToSet('stone1_count',$twoMetal6);
			$this->addAttributeToSet('stone2_count',$twoMetal6);
			$this->addAttributeToSet('stone3_count',$twoMetal6);
			$this->addAttributeToSet('stone4_count',$twoMetal6);
			$this->addAttributeToSet('stone5_count',$twoMetal6);
			$this->addAttributeToSet('stone6_count',$twoMetal6);
			
			$this->addAttributeToSet('metal1_type',$twoMetal6);
			$this->addAttributeToSet('metal2_type',$twoMetal6);
			$this->addAttributeToSet('filterable_stone_shapes',$twoMetal6);
			$this->addAttributeToSet('filterable_stone_names',$twoMetal6);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$twoMetal6);
			$this->addAttributeToSet('filterable_metal_types',$twoMetal6);
			$this->addAttributeToSet('jewelry_styles',$twoMetal6);
			$this->addAttributeToSet('filterable_stone_grades',$twoMetal6);
			$this->addAttributeToSet('default_stone1_size',$twoMetal6);
			$this->addAttributeToSet('default_stone1_grade',$twoMetal6);
			$this->addAttributeToSet('default_metal1_type',$twoMetal6);
			$this->addAttributeToSet('stone_variation_count',$twoMetal6);
			$this->addAttributeToSet('metal_variation_count',$twoMetal6);
			$this->addAttributeToSet('jewelry_type',$twoMetal6);
			$this->addAttributeToSet('master_sku',$twoMetal6);
			$this->addAttributeToSet('yellow_gold_image',$twoMetal6);;
			$this->addAttributeToSet('white_gold_image',$twoMetal6);
		}
		foreach($twoMetal7s as $twoMetal7){
			$this->addAttributeToSet('stone1_name',$twoMetal7);
			$this->addAttributeToSet('stone1_name',$twoMetal7);
			$this->addAttributeToSet('stone2_name',$twoMetal7);
			$this->addAttributeToSet('stone3_name',$twoMetal7);
			$this->addAttributeToSet('stone4_name',$twoMetal7);
			$this->addAttributeToSet('stone5_name',$twoMetal7);
			$this->addAttributeToSet('stone6_name',$twoMetal7);
			$this->addAttributeToSet('stone7_name',$twoMetal7);
		
			$this->addAttributeToSet('stone1_shape',$twoMetal7);
			$this->addAttributeToSet('stone2_shape',$twoMetal7);
			$this->addAttributeToSet('stone3_shape',$twoMetal7);
			$this->addAttributeToSet('stone4_shape',$twoMetal7);
			$this->addAttributeToSet('stone5_shape',$twoMetal7);
			$this->addAttributeToSet('stone6_shape',$twoMetal7);
			$this->addAttributeToSet('stone7_shape',$twoMetal7);
			
			$this->addAttributeToSet('stone1_size',$twoMetal7);
			$this->addAttributeToSet('stone2_size',$twoMetal7);
			$this->addAttributeToSet('stone3_size',$twoMetal7);
			$this->addAttributeToSet('stone4_size',$twoMetal7);
			$this->addAttributeToSet('stone5_size',$twoMetal7);
			$this->addAttributeToSet('stone6_size',$twoMetal7);
			$this->addAttributeToSet('stone7_size',$twoMetal7);
			
			$this->addAttributeToSet('stone1_grade',$twoMetal7);
			$this->addAttributeToSet('stone2_grade',$twoMetal7);
			$this->addAttributeToSet('stone3_grade',$twoMetal7);
			$this->addAttributeToSet('stone4_grade',$twoMetal7);
			$this->addAttributeToSet('stone5_grade',$twoMetal7);
			$this->addAttributeToSet('stone6_grade',$twoMetal7);
			$this->addAttributeToSet('stone7_grade',$twoMetal7);
			
			$this->addAttributeToSet('stone1_type',$twoMetal7);
			$this->addAttributeToSet('stone2_type',$twoMetal7);
			$this->addAttributeToSet('stone3_type',$twoMetal7);
			$this->addAttributeToSet('stone4_type',$twoMetal7);
			$this->addAttributeToSet('stone5_type',$twoMetal7);
			$this->addAttributeToSet('stone6_type',$twoMetal7);
			$this->addAttributeToSet('stone7_type',$twoMetal7);
			
			$this->addAttributeToSet('stone1_cut',$twoMetal7);
			$this->addAttributeToSet('stone2_cut',$twoMetal7);
			$this->addAttributeToSet('stone3_cut',$twoMetal7);
			$this->addAttributeToSet('stone4_cut',$twoMetal7);
			$this->addAttributeToSet('stone5_cut',$twoMetal7);
			$this->addAttributeToSet('stone6_cut',$twoMetal7);
			$this->addAttributeToSet('stone7_cut',$twoMetal7);
			
			$this->addAttributeToSet('stone1_setting',$twoMetal7);
			$this->addAttributeToSet('stone2_setting',$twoMetal7);
			$this->addAttributeToSet('stone3_setting',$twoMetal7);
			$this->addAttributeToSet('stone4_setting',$twoMetal7);
			$this->addAttributeToSet('stone5_setting',$twoMetal7);
			$this->addAttributeToSet('stone6_setting',$twoMetal7);
			$this->addAttributeToSet('stone7_setting',$twoMetal7);
			
			$this->addAttributeToSet('stone1_weight',$twoMetal7);
			$this->addAttributeToSet('stone2_weight',$twoMetal7);
			$this->addAttributeToSet('stone3_weight',$twoMetal7);
			$this->addAttributeToSet('stone4_weight',$twoMetal7);
			$this->addAttributeToSet('stone5_weight',$twoMetal7);
			$this->addAttributeToSet('stone6_weight',$twoMetal7);
			$this->addAttributeToSet('stone7_weight',$twoMetal7);
			
			$this->addAttributeToSet('stone1_count',$twoMetal7);
			$this->addAttributeToSet('stone2_count',$twoMetal7);
			$this->addAttributeToSet('stone3_count',$twoMetal7);
			$this->addAttributeToSet('stone4_count',$twoMetal7);
			$this->addAttributeToSet('stone5_count',$twoMetal7);
			$this->addAttributeToSet('stone6_count',$twoMetal7);
			$this->addAttributeToSet('stone7_count',$twoMetal7);
			
			$this->addAttributeToSet('metal1_type',$twoMetal7);
			$this->addAttributeToSet('metal2_type',$twoMetal7);
			$this->addAttributeToSet('filterable_stone_shapes',$twoMetal7);
			$this->addAttributeToSet('filterable_stone_names',$twoMetal7);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$twoMetal7);
			$this->addAttributeToSet('filterable_metal_types',$twoMetal7);
			$this->addAttributeToSet('jewelry_styles',$twoMetal7);
			$this->addAttributeToSet('filterable_stone_grades',$twoMetal7);
			$this->addAttributeToSet('default_stone1_size',$twoMetal7);
			$this->addAttributeToSet('default_stone1_grade',$twoMetal7);
			$this->addAttributeToSet('default_metal1_type',$twoMetal7);
			$this->addAttributeToSet('stone_variation_count',$twoMetal7);
			$this->addAttributeToSet('metal_variation_count',$twoMetal7);
			$this->addAttributeToSet('jewelry_type',$twoMetal7);
			$this->addAttributeToSet('master_sku',$twoMetal7);
			$this->addAttributeToSet('yellow_gold_image',$twoMetal7);;
			$this->addAttributeToSet('white_gold_image',$twoMetal7);
		}
		foreach($twoMetal8s as $twoMetal8){
			$this->addAttributeToSet('stone1_name',$twoMetal8);
			$this->addAttributeToSet('stone1_name',$twoMetal8);
			$this->addAttributeToSet('stone2_name',$twoMetal8);
			$this->addAttributeToSet('stone3_name',$twoMetal8);
			$this->addAttributeToSet('stone4_name',$twoMetal8);
			$this->addAttributeToSet('stone5_name',$twoMetal8);
			$this->addAttributeToSet('stone6_name',$twoMetal8);
			$this->addAttributeToSet('stone7_name',$twoMetal8);
			$this->addAttributeToSet('stone8_name',$twoMetal8);
		
			$this->addAttributeToSet('stone1_shape',$twoMetal8);
			$this->addAttributeToSet('stone2_shape',$twoMetal8);
			$this->addAttributeToSet('stone3_shape',$twoMetal8);
			$this->addAttributeToSet('stone4_shape',$twoMetal8);
			$this->addAttributeToSet('stone5_shape',$twoMetal8);
			$this->addAttributeToSet('stone6_shape',$twoMetal8);
			$this->addAttributeToSet('stone7_shape',$twoMetal8);
			$this->addAttributeToSet('stone8_shape',$twoMetal8);
			
			$this->addAttributeToSet('stone1_size',$twoMetal8);
			$this->addAttributeToSet('stone2_size',$twoMetal8);
			$this->addAttributeToSet('stone3_size',$twoMetal8);
			$this->addAttributeToSet('stone4_size',$twoMetal8);
			$this->addAttributeToSet('stone5_size',$twoMetal8);
			$this->addAttributeToSet('stone6_size',$twoMetal8);
			$this->addAttributeToSet('stone7_size',$twoMetal8);
			$this->addAttributeToSet('stone8_size',$twoMetal8);
			
			$this->addAttributeToSet('stone1_grade',$twoMetal8);
			$this->addAttributeToSet('stone2_grade',$twoMetal8);
			$this->addAttributeToSet('stone3_grade',$twoMetal8);
			$this->addAttributeToSet('stone4_grade',$twoMetal8);
			$this->addAttributeToSet('stone5_grade',$twoMetal8);
			$this->addAttributeToSet('stone6_grade',$twoMetal8);
			$this->addAttributeToSet('stone7_grade',$twoMetal8);
			$this->addAttributeToSet('stone8_grade',$twoMetal8);
			
			$this->addAttributeToSet('stone1_type',$twoMetal8);
			$this->addAttributeToSet('stone2_type',$twoMetal8);
			$this->addAttributeToSet('stone3_type',$twoMetal8);
			$this->addAttributeToSet('stone4_type',$twoMetal8);
			$this->addAttributeToSet('stone5_type',$twoMetal8);
			$this->addAttributeToSet('stone6_type',$twoMetal8);
			$this->addAttributeToSet('stone7_type',$twoMetal8);
			$this->addAttributeToSet('stone8_type',$twoMetal8);
			
			$this->addAttributeToSet('stone1_cut',$twoMetal8);
			$this->addAttributeToSet('stone2_cut',$twoMetal8);
			$this->addAttributeToSet('stone3_cut',$twoMetal8);
			$this->addAttributeToSet('stone4_cut',$twoMetal8);
			$this->addAttributeToSet('stone5_cut',$twoMetal8);
			$this->addAttributeToSet('stone6_cut',$twoMetal8);
			$this->addAttributeToSet('stone7_cut',$twoMetal8);
			$this->addAttributeToSet('stone8_cut',$twoMetal8);
			
			$this->addAttributeToSet('stone1_setting',$twoMetal8);
			$this->addAttributeToSet('stone2_setting',$twoMetal8);
			$this->addAttributeToSet('stone3_setting',$twoMetal8);
			$this->addAttributeToSet('stone4_setting',$twoMetal8);
			$this->addAttributeToSet('stone5_setting',$twoMetal8);
			$this->addAttributeToSet('stone6_setting',$twoMetal8);
			$this->addAttributeToSet('stone8_setting',$twoMetal8);
			$this->addAttributeToSet('stone9_setting',$twoMetal8);
			
			$this->addAttributeToSet('stone1_weight',$twoMetal8);
			$this->addAttributeToSet('stone2_weight',$twoMetal8);
			$this->addAttributeToSet('stone3_weight',$twoMetal8);
			$this->addAttributeToSet('stone4_weight',$twoMetal8);
			$this->addAttributeToSet('stone5_weight',$twoMetal8);
			$this->addAttributeToSet('stone6_weight',$twoMetal8);
			$this->addAttributeToSet('stone7_weight',$twoMetal8);
			$this->addAttributeToSet('stone8_weight',$twoMetal8);
			
			$this->addAttributeToSet('stone1_count',$twoMetal8);
			$this->addAttributeToSet('stone2_count',$twoMetal8);
			$this->addAttributeToSet('stone3_count',$twoMetal8);
			$this->addAttributeToSet('stone4_count',$twoMetal8);
			$this->addAttributeToSet('stone5_count',$twoMetal8);
			$this->addAttributeToSet('stone6_count',$twoMetal8);
			$this->addAttributeToSet('stone7_count',$twoMetal8);
			$this->addAttributeToSet('stone8_count',$twoMetal8);
			
			$this->addAttributeToSet('metal1_type',$twoMetal8);
			$this->addAttributeToSet('metal2_type',$twoMetal8);
			$this->addAttributeToSet('filterable_stone_shapes',$twoMetal8);
			$this->addAttributeToSet('filterable_stone_names',$twoMetal8);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$twoMetal8);
			$this->addAttributeToSet('filterable_metal_types',$twoMetal8);
			$this->addAttributeToSet('jewelry_styles',$twoMetal8);
			$this->addAttributeToSet('filterable_stone_grades',$twoMetal8);
			$this->addAttributeToSet('default_stone1_size',$twoMetal8);
			$this->addAttributeToSet('default_stone1_grade',$twoMetal8);
			$this->addAttributeToSet('default_metal1_type',$twoMetal8);
			$this->addAttributeToSet('stone_variation_count',$twoMetal8);
			$this->addAttributeToSet('metal_variation_count',$twoMetal8);
			$this->addAttributeToSet('jewelry_type',$twoMetal8);
			$this->addAttributeToSet('master_sku',$twoMetal8);
			$this->addAttributeToSet('yellow_gold_image',$twoMetal8);;
			$this->addAttributeToSet('white_gold_image',$twoMetal8);
		}
		foreach($twoMetal9s as $twoMetal9){
			$this->addAttributeToSet('stone1_name',$twoMetal9);
			$this->addAttributeToSet('stone1_name',$twoMetal9);
			$this->addAttributeToSet('stone2_name',$twoMetal9);
			$this->addAttributeToSet('stone3_name',$twoMetal9);
			$this->addAttributeToSet('stone4_name',$twoMetal9);
			$this->addAttributeToSet('stone5_name',$twoMetal9);
			$this->addAttributeToSet('stone6_name',$twoMetal9);
			$this->addAttributeToSet('stone7_name',$twoMetal9);
			$this->addAttributeToSet('stone8_name',$twoMetal9);
			$this->addAttributeToSet('stone9_name',$twoMetal9);
		
			$this->addAttributeToSet('stone1_shape',$twoMetal9);
			$this->addAttributeToSet('stone2_shape',$twoMetal9);
			$this->addAttributeToSet('stone3_shape',$twoMetal9);
			$this->addAttributeToSet('stone4_shape',$twoMetal9);
			$this->addAttributeToSet('stone5_shape',$twoMetal9);
			$this->addAttributeToSet('stone6_shape',$twoMetal9);
			$this->addAttributeToSet('stone7_shape',$twoMetal9);
			$this->addAttributeToSet('stone8_shape',$twoMetal9);
			$this->addAttributeToSet('stone9_shape',$twoMetal9);
			
			$this->addAttributeToSet('stone1_size',$twoMetal9);
			$this->addAttributeToSet('stone2_size',$twoMetal9);
			$this->addAttributeToSet('stone3_size',$twoMetal9);
			$this->addAttributeToSet('stone4_size',$twoMetal9);
			$this->addAttributeToSet('stone5_size',$twoMetal9);
			$this->addAttributeToSet('stone6_size',$twoMetal9);
			$this->addAttributeToSet('stone7_size',$twoMetal9);
			$this->addAttributeToSet('stone8_size',$twoMetal9);
			$this->addAttributeToSet('stone9_size',$twoMetal9);
			
			$this->addAttributeToSet('stone1_grade',$twoMetal9);
			$this->addAttributeToSet('stone2_grade',$twoMetal9);
			$this->addAttributeToSet('stone3_grade',$twoMetal9);
			$this->addAttributeToSet('stone4_grade',$twoMetal9);
			$this->addAttributeToSet('stone5_grade',$twoMetal9);
			$this->addAttributeToSet('stone6_grade',$twoMetal9);
			$this->addAttributeToSet('stone7_grade',$twoMetal9);
			$this->addAttributeToSet('stone8_grade',$twoMetal9);
			$this->addAttributeToSet('stone9_grade',$twoMetal9);
			
			$this->addAttributeToSet('stone1_type',$twoMetal9);
			$this->addAttributeToSet('stone2_type',$twoMetal9);
			$this->addAttributeToSet('stone3_type',$twoMetal9);
			$this->addAttributeToSet('stone4_type',$twoMetal9);
			$this->addAttributeToSet('stone5_type',$twoMetal9);
			$this->addAttributeToSet('stone6_type',$twoMetal9);
			$this->addAttributeToSet('stone7_type',$twoMetal9);
			$this->addAttributeToSet('stone8_type',$twoMetal9);
			$this->addAttributeToSet('stone9_type',$twoMetal9);
			
			$this->addAttributeToSet('stone1_cut',$twoMetal9);
			$this->addAttributeToSet('stone2_cut',$twoMetal9);
			$this->addAttributeToSet('stone3_cut',$twoMetal9);
			$this->addAttributeToSet('stone4_cut',$twoMetal9);
			$this->addAttributeToSet('stone5_cut',$twoMetal9);
			$this->addAttributeToSet('stone6_cut',$twoMetal9);
			$this->addAttributeToSet('stone7_cut',$twoMetal9);
			$this->addAttributeToSet('stone8_cut',$twoMetal9);
			$this->addAttributeToSet('stone9_cut',$twoMetal9);
			
			$this->addAttributeToSet('stone1_setting',$twoMetal9);
			$this->addAttributeToSet('stone2_setting',$twoMetal9);
			$this->addAttributeToSet('stone3_setting',$twoMetal9);
			$this->addAttributeToSet('stone4_setting',$twoMetal9);
			$this->addAttributeToSet('stone5_setting',$twoMetal9);
			$this->addAttributeToSet('stone6_setting',$twoMetal9);
			$this->addAttributeToSet('stone7_setting',$twoMetal9);
			$this->addAttributeToSet('stone8_setting',$twoMetal9);
			$this->addAttributeToSet('stone9_setting',$twoMetal9);
			
			$this->addAttributeToSet('stone1_weight',$twoMetal9);
			$this->addAttributeToSet('stone2_weight',$twoMetal9);
			$this->addAttributeToSet('stone3_weight',$twoMetal9);
			$this->addAttributeToSet('stone4_weight',$twoMetal9);
			$this->addAttributeToSet('stone5_weight',$twoMetal9);
			$this->addAttributeToSet('stone6_weight',$twoMetal9);
			$this->addAttributeToSet('stone7_weight',$twoMetal9);
			$this->addAttributeToSet('stone8_weight',$twoMetal9);
			$this->addAttributeToSet('stone9_weight',$twoMetal9);
			
			$this->addAttributeToSet('stone1_count',$twoMetal9);
			$this->addAttributeToSet('stone2_count',$twoMetal9);
			$this->addAttributeToSet('stone3_count',$twoMetal9);
			$this->addAttributeToSet('stone4_count',$twoMetal9);
			$this->addAttributeToSet('stone5_count',$twoMetal9);
			$this->addAttributeToSet('stone6_count',$twoMetal9);
			$this->addAttributeToSet('stone7_count',$twoMetal9);
			$this->addAttributeToSet('stone8_count',$twoMetal9);
			$this->addAttributeToSet('stone9_count',$twoMetal9);
			
			$this->addAttributeToSet('metal1_type',$twoMetal9);
			$this->addAttributeToSet('metal2_type',$twoMetal9);
			$this->addAttributeToSet('filterable_stone_shapes',$twoMetal9);
			$this->addAttributeToSet('filterable_stone_names',$twoMetal9);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$twoMetal9);
			$this->addAttributeToSet('filterable_metal_types',$twoMetal9);
			$this->addAttributeToSet('jewelry_styles',$twoMetal9);
			$this->addAttributeToSet('filterable_stone_grades',$twoMetal9);
			$this->addAttributeToSet('default_stone1_size',$twoMetal9);
			$this->addAttributeToSet('default_stone1_grade',$twoMetal9);
			$this->addAttributeToSet('default_metal1_type',$twoMetal9);
			$this->addAttributeToSet('stone_variation_count',$twoMetal9);
			$this->addAttributeToSet('metal_variation_count',$twoMetal9);
			$this->addAttributeToSet('jewelry_type',$twoMetal9);
			$this->addAttributeToSet('master_sku',$twoMetal9);
			$this->addAttributeToSet('yellow_gold_image',$twoMetal9);;
			$this->addAttributeToSet('white_gold_image',$twoMetal9);
		}
		foreach($twoMetal10s as $twoMetal10){
			$this->addAttributeToSet('stone1_name',$twoMetal10);
			$this->addAttributeToSet('stone1_name',$twoMetal10);
			$this->addAttributeToSet('stone2_name',$twoMetal10);
			$this->addAttributeToSet('stone3_name',$twoMetal10);
			$this->addAttributeToSet('stone4_name',$twoMetal10);
			$this->addAttributeToSet('stone5_name',$twoMetal10);
			$this->addAttributeToSet('stone6_name',$twoMetal10);
			$this->addAttributeToSet('stone7_name',$twoMetal10);
			$this->addAttributeToSet('stone8_name',$twoMetal10);
			$this->addAttributeToSet('stone9_name',$twoMetal10);
			$this->addAttributeToSet('stone10_name',$twoMetal10);
		
			$this->addAttributeToSet('stone1_shape',$twoMetal10);
			$this->addAttributeToSet('stone2_shape',$twoMetal10);
			$this->addAttributeToSet('stone3_shape',$twoMetal10);
			$this->addAttributeToSet('stone4_shape',$twoMetal10);
			$this->addAttributeToSet('stone5_shape',$twoMetal10);
			$this->addAttributeToSet('stone6_shape',$twoMetal10);
			$this->addAttributeToSet('stone7_shape',$twoMetal10);
			$this->addAttributeToSet('stone8_shape',$twoMetal10);
			$this->addAttributeToSet('stone9_shape',$twoMetal10);
			$this->addAttributeToSet('stone10_shape',$twoMetal10);
			
			$this->addAttributeToSet('stone1_size',$twoMetal10);
			$this->addAttributeToSet('stone2_size',$twoMetal10);
			$this->addAttributeToSet('stone3_size',$twoMetal10);
			$this->addAttributeToSet('stone4_size',$twoMetal10);
			$this->addAttributeToSet('stone5_size',$twoMetal10);
			$this->addAttributeToSet('stone6_size',$twoMetal10);
			$this->addAttributeToSet('stone7_size',$twoMetal10);
			$this->addAttributeToSet('stone8_size',$twoMetal10);
			$this->addAttributeToSet('stone9_size',$twoMetal10);
			$this->addAttributeToSet('stone10_size',$twoMetal10);
			
			$this->addAttributeToSet('stone1_grade',$twoMetal10);
			$this->addAttributeToSet('stone2_grade',$twoMetal10);
			$this->addAttributeToSet('stone3_grade',$twoMetal10);
			$this->addAttributeToSet('stone4_grade',$twoMetal10);
			$this->addAttributeToSet('stone5_grade',$twoMetal10);
			$this->addAttributeToSet('stone6_grade',$twoMetal10);
			$this->addAttributeToSet('stone7_grade',$twoMetal10);
			$this->addAttributeToSet('stone8_grade',$twoMetal10);
			$this->addAttributeToSet('stone9_grade',$twoMetal10);
			$this->addAttributeToSet('stone10_grade',$twoMetal10);
			
			$this->addAttributeToSet('stone1_type',$twoMetal10);
			$this->addAttributeToSet('stone2_type',$twoMetal10);
			$this->addAttributeToSet('stone3_type',$twoMetal10);
			$this->addAttributeToSet('stone4_type',$twoMetal10);
			$this->addAttributeToSet('stone5_type',$twoMetal10);
			$this->addAttributeToSet('stone6_type',$twoMetal10);
			$this->addAttributeToSet('stone7_type',$twoMetal10);
			$this->addAttributeToSet('stone8_type',$twoMetal10);
			$this->addAttributeToSet('stone9_type',$twoMetal10);
			$this->addAttributeToSet('stone10_type',$twoMetal10);
			
			$this->addAttributeToSet('stone1_cut',$twoMetal10);
			$this->addAttributeToSet('stone2_cut',$twoMetal10);
			$this->addAttributeToSet('stone3_cut',$twoMetal10);
			$this->addAttributeToSet('stone4_cut',$twoMetal10);
			$this->addAttributeToSet('stone5_cut',$twoMetal10);
			$this->addAttributeToSet('stone6_cut',$twoMetal10);
			$this->addAttributeToSet('stone7_cut',$twoMetal10);
			$this->addAttributeToSet('stone8_cut',$twoMetal10);
			$this->addAttributeToSet('stone9_cut',$twoMetal10);
			$this->addAttributeToSet('stone10_cut',$twoMetal10);
			
			$this->addAttributeToSet('stone1_setting',$twoMetal10);
			$this->addAttributeToSet('stone2_setting',$twoMetal10);
			$this->addAttributeToSet('stone3_setting',$twoMetal10);
			$this->addAttributeToSet('stone4_setting',$twoMetal10);
			$this->addAttributeToSet('stone5_setting',$twoMetal10);
			$this->addAttributeToSet('stone6_setting',$twoMetal10);
			$this->addAttributeToSet('stone7_setting',$twoMetal10);
			$this->addAttributeToSet('stone8_setting',$twoMetal10);
			$this->addAttributeToSet('stone9_setting',$twoMetal10);
			$this->addAttributeToSet('stone10_setting',$twoMetal10);
			
			$this->addAttributeToSet('stone1_weight',$twoMetal10);
			$this->addAttributeToSet('stone2_weight',$twoMetal10);
			$this->addAttributeToSet('stone3_weight',$twoMetal10);
			$this->addAttributeToSet('stone4_weight',$twoMetal10);
			$this->addAttributeToSet('stone5_weight',$twoMetal10);
			$this->addAttributeToSet('stone6_weight',$twoMetal10);
			$this->addAttributeToSet('stone7_weight',$twoMetal10);
			$this->addAttributeToSet('stone8_weight',$twoMetal10);
			$this->addAttributeToSet('stone9_weight',$twoMetal10);
			$this->addAttributeToSet('stone10_weight',$twoMetal10);
			
			$this->addAttributeToSet('stone1_count',$twoMetal10);
			$this->addAttributeToSet('stone2_count',$twoMetal10);
			$this->addAttributeToSet('stone3_count',$twoMetal10);
			$this->addAttributeToSet('stone4_count',$twoMetal10);
			$this->addAttributeToSet('stone5_count',$twoMetal10);
			$this->addAttributeToSet('stone6_count',$twoMetal10);
			$this->addAttributeToSet('stone7_count',$twoMetal10);
			$this->addAttributeToSet('stone8_count',$twoMetal10);
			$this->addAttributeToSet('stone9_count',$twoMetal10);
			$this->addAttributeToSet('stone10_count',$twoMetal10);
			
			$this->addAttributeToSet('metal1_type',$twoMetal10);
			$this->addAttributeToSet('metal2_type',$twoMetal10);
			$this->addAttributeToSet('filterable_stone_shapes',$twoMetal10);
			$this->addAttributeToSet('filterable_stone_names',$twoMetal10);
			$this->addAttributeToSet('filterable_carat_weight_ranges',$twoMetal10);
			$this->addAttributeToSet('filterable_metal_types',$twoMetal10);
			$this->addAttributeToSet('jewelry_styles',$twoMetal10);
			$this->addAttributeToSet('filterable_stone_grades',$twoMetal10);
			$this->addAttributeToSet('default_stone1_size',$twoMetal10);
			$this->addAttributeToSet('default_stone1_grade',$twoMetal10);
			$this->addAttributeToSet('default_metal1_type',$twoMetal10);
			$this->addAttributeToSet('stone_variation_count',$twoMetal10);
			$this->addAttributeToSet('metal_variation_count',$twoMetal10);
			$this->addAttributeToSet('jewelry_type',$twoMetal10);
			$this->addAttributeToSet('master_sku',$twoMetal10);
			$this->addAttributeToSet('yellow_gold_image',$twoMetal10);;
			$this->addAttributeToSet('white_gold_image',$twoMetal10);
		}
		foreach($twoMetalForMetal as $twoMetal){
			$this->addAttributeToSet('metal2_type',$twoMetal);
		}
		
		echo "done";exit;
		
		
	}
	
	public function setupAction(){
		echo "remove exit";exit;
		
		/*$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', 'stone1_size');
		
		var_dump($attr->getData());
		$attr = $attr_model->loadByCode('catalog_product', 'test_conf');
		
		var_dump($attr->getData());
		//exit;
		*/
		
		// delete attributes
		/*
		$this->deleteAttribute('bracelet_chain_type');
		$this->deleteAttribute('bracelet_clasp_type');
		$this->deleteAttribute('bracelet_length');
		$this->deleteAttribute('butterfly1_type');
		$this->deleteAttribute('chain1_length');
		$this->deleteAttribute('chain1_type');
		$this->deleteAttribute('color');
		$this->deleteAttribute('cost');
		$this->deleteAttribute('customj');
		$this->deleteAttribute('default_metal1_type');
		$this->deleteAttribute('default_stone1_grade');
		$this->deleteAttribute('default_stone1_size');
		$this->deleteAttribute('de_rhodium');
		$this->deleteAttribute('de_stone_shape');
		$this->deleteAttribute('de_stone_type');
		$this->deleteAttribute('de_style');
		$this->deleteAttribute('discount_price');
		$this->deleteAttribute('earring_backing_type');
		$this->deleteAttribute('emb_carat_weight1');
		$this->deleteAttribute('emb_carat_weight2');
		$this->deleteAttribute('emb_carat_weight3');
		$this->deleteAttribute('emb_clarity1');
		$this->deleteAttribute('emb_clarity2');
		$this->deleteAttribute('emb_clarity3');
		$this->deleteAttribute('emb_color1');
		$this->deleteAttribute('emb_color2');
		$this->deleteAttribute('emb_color3');
		$this->deleteAttribute('emb_dimension1');
		$this->deleteAttribute('emb_dimension2');
		$this->deleteAttribute('emb_dimension3');
		$this->deleteAttribute('emb_extra_cost');
		$this->deleteAttribute('emb_fourteen_wg_weight');
		$this->deleteAttribute('emb_fourteen_yg_weight');
		$this->deleteAttribute('emb_number_of_stones1');
		$this->deleteAttribute('emb_number_of_stones2');
		$this->deleteAttribute('emb_number_of_stones3');
		$this->deleteAttribute('emb_pl_weight');
		$this->deleteAttribute('emb_quality_grade1');
		$this->deleteAttribute('emb_quality_grade2');
		$this->deleteAttribute('emb_quality_grade3');
		$this->deleteAttribute('emb_setting_type1');
		$this->deleteAttribute('emb_setting_type2');
		$this->deleteAttribute('emb_setting_type3');
		$this->deleteAttribute('emb_shape1');
		$this->deleteAttribute('emb_shape2');
		$this->deleteAttribute('emb_shape3');
		$this->deleteAttribute('emb_sl_weight');
		$this->deleteAttribute('emb_stone_name');
		$this->deleteAttribute('emb_stone_name2');
		$this->deleteAttribute('emb_stone_name3');
		$this->deleteAttribute('filterable_carat_weight_ranges');
		$this->deleteAttribute('filterable_metal_types');
		$this->deleteAttribute('filterable_stone_grades');
		$this->deleteAttribute('filterable_stone_names');
		$this->deleteAttribute('filterable_stone_shapes');
		$this->deleteAttribute('gemstone_brilliance');
		$this->deleteAttribute('gemstone_carat_weight');
		$this->deleteAttribute('gemstone_clarity');
		$this->deleteAttribute('gemstone_color');
		$this->deleteAttribute('gemstone_enhancement');
		$this->deleteAttribute('gemstone_type');
		$this->deleteAttribute('imagesku');
		$this->deleteAttribute('jewelry_styles');
		$this->deleteAttribute('manufacturer');
		$this->deleteAttribute('max_price');
		$this->deleteAttribute('metal');
		$this->deleteAttribute('metal1_type');
		$this->deleteAttribute('metal2_type');
		$this->deleteAttribute('metal3_type');
		$this->deleteAttribute('metal_type');
		$this->deleteAttribute('metal_type_multi');
		$this->deleteAttribute('metal_variation_count');
		$this->deleteAttribute('min_price');
		$this->deleteAttribute('newmedia');
		$this->deleteAttribute('new_arrival');
		$this->deleteAttribute('pendant_chain_length');
		$this->deleteAttribute('pendant_chain_metal');
		$this->deleteAttribute('pendant_chain_type');
		$this->deleteAttribute('pendant_clasp_type');
		$this->deleteAttribute('post1_type');
		$this->deleteAttribute('productfacts');
		$this->deleteAttribute('quality_description');
		$this->deleteAttribute('quality_type_multi');
		$this->deleteAttribute('ring_size');
		$this->deleteAttribute('settingstyle');
		$this->deleteAttribute('stone1_count');
		$this->deleteAttribute('stone1_cut');
		$this->deleteAttribute('stone1_grade');
		$this->deleteAttribute('stone1_name');
		$this->deleteAttribute('stone1_setting');
		$this->deleteAttribute('stone1_shape');
		$this->deleteAttribute('stone1_size');
		$this->deleteAttribute('stone1_type');
		$this->deleteAttribute('stone1_weight');
		$this->deleteAttribute('stone2_count');
		$this->deleteAttribute('stone2_cut');
		$this->deleteAttribute('stone2_grade');
		$this->deleteAttribute('stone2_name');
		$this->deleteAttribute('stone2_setting');
		$this->deleteAttribute('stone2_shape');
		$this->deleteAttribute('stone2_size');
		$this->deleteAttribute('stone2_type');
		$this->deleteAttribute('stone2_weight');
		$this->deleteAttribute('stone3_count');
		$this->deleteAttribute('stone3_cut');
		$this->deleteAttribute('stone3_grade');
		$this->deleteAttribute('stone3_name');
		$this->deleteAttribute('stone3_setting');
		$this->deleteAttribute('stone3_shape');
		$this->deleteAttribute('stone3_size');
		$this->deleteAttribute('stone3_type');
		$this->deleteAttribute('stone3_weight');
		$this->deleteAttribute('stone4_count');
		$this->deleteAttribute('stone4_cut');
		$this->deleteAttribute('stone4_grade');
		$this->deleteAttribute('stone4_name');
		$this->deleteAttribute('stone4_setting');
		$this->deleteAttribute('stone4_shape');
		$this->deleteAttribute('stone4_size');
		$this->deleteAttribute('stone4_type');
		$this->deleteAttribute('stone4_weight');
		$this->deleteAttribute('stone5_count');
		$this->deleteAttribute('stone5_cut');
		$this->deleteAttribute('stone5_grade');
		$this->deleteAttribute('stone5_name');
		$this->deleteAttribute('stone5_setting');
		$this->deleteAttribute('stone5_shape');
		$this->deleteAttribute('stone5_size');
		$this->deleteAttribute('stone5_type');
		$this->deleteAttribute('stone5_weight');
		$this->deleteAttribute('stone6_count');
		$this->deleteAttribute('stone6_cut');
		$this->deleteAttribute('stone6_grade');
		$this->deleteAttribute('stone6_name');
		$this->deleteAttribute('stone6_setting');
		$this->deleteAttribute('stone6_shape');
		$this->deleteAttribute('stone6_size');
		$this->deleteAttribute('stone6_type');
		$this->deleteAttribute('stone6_weight');
		$this->deleteAttribute('stone7_count');
		$this->deleteAttribute('stone7_cut');
		$this->deleteAttribute('stone7_grade');
		$this->deleteAttribute('stone7_name');
		$this->deleteAttribute('stone7_setting');
		$this->deleteAttribute('stone7_shape');
		$this->deleteAttribute('stone7_size');
		$this->deleteAttribute('stone7_type');
		$this->deleteAttribute('stone7_weight');
		$this->deleteAttribute('stone8_count');
		$this->deleteAttribute('stone8_cut');
		$this->deleteAttribute('stone8_grade');
		$this->deleteAttribute('stone8_name');
		$this->deleteAttribute('stone8_setting');
		$this->deleteAttribute('stone8_shape');
		$this->deleteAttribute('stone8_size');
		$this->deleteAttribute('stone8_type');
		$this->deleteAttribute('stone8_weight');
		$this->deleteAttribute('stone9_count');
		$this->deleteAttribute('stone9_cut');
		$this->deleteAttribute('stone9_grade');
		$this->deleteAttribute('stone9_name');
		$this->deleteAttribute('stone9_setting');
		$this->deleteAttribute('stone9_shape');
		$this->deleteAttribute('stone9_size');
		$this->deleteAttribute('stone9_type');
		$this->deleteAttribute('stone9_weight');
		$this->deleteAttribute('stone10_count');
		$this->deleteAttribute('stone10_cut');
		$this->deleteAttribute('stone10_grade');
		$this->deleteAttribute('stone10_name');
		$this->deleteAttribute('stone10_setting');
		$this->deleteAttribute('stone10_shape');
		$this->deleteAttribute('stone10_size');
		$this->deleteAttribute('stone10_type');
		$this->deleteAttribute('stone10_weight');
		$this->deleteAttribute('stone_variation_count');
		$this->deleteAttribute('stopper1_type');
		$this->deleteAttribute('total_carat_weight');
		$this->deleteAttribute('twct_range');
		$this->deleteAttribute('white_best_image');
		$this->deleteAttribute('white_better_image');
		$this->deleteAttribute('white_good_image');
		$this->deleteAttribute('white_heirloom_image');
		$this->deleteAttribute('white_image');
		$this->deleteAttribute('yellow_best_image');
		$this->deleteAttribute('yellow_better_image');
		$this->deleteAttribute('yellow_good_image');
		$this->deleteAttribute('yellow_heirloom_image');
		$this->deleteAttribute('yellow_image');
		echo "done";exit;*/
		
		// deleting attribute sets
		/*$this->deleteAttributeSet('Rings');
		$this->deleteAttributeSet('Earrings');
		$this->deleteAttributeSet('Pendants');
		$this->deleteAttributeSet('Gemstones');*/
		/*
		$this->deleteAttributeSet('Ring with 0 variation of stone');
		$this->deleteAttributeSet('Ring with 1 variation of stone');
		$this->deleteAttributeSet('Ring with 2 variation of stone');
		$this->deleteAttributeSet('Ring with 3 variation of stone');
		$this->deleteAttributeSet('Ring with 4 variation of stone');
		$this->deleteAttributeSet('Ring with 5 variation of stone');
		
		$this->deleteAttributeSet('Pendant with 0 variation of stone');
		$this->deleteAttributeSet('Pendant with 1 variation of stone');
		$this->deleteAttributeSet('Pendant with 2 variation of stone');
		$this->deleteAttributeSet('Pendant with 3 variation of stone');
		$this->deleteAttributeSet('Pendant with 4 variation of stone');
		$this->deleteAttributeSet('Pendant with 5 variation of stone');
		
		$this->deleteAttributeSet('Earrings with 0 variation of stone');
		$this->deleteAttributeSet('Earrings with 1 variation of stone');
		$this->deleteAttributeSet('Earrings with 2 variation of stone');
		$this->deleteAttributeSet('Earrings with 3 variation of stone');
		$this->deleteAttributeSet('Earrings with 4 variation of stone');
		$this->deleteAttributeSet('Earrings with 5 variation of stone');
		
		$this->deleteAttributeSet('Bracelet with 0 variation of stone');
		$this->deleteAttributeSet('Bracelet with 1 variation of stone');
		//echo "done";exit;
		$this->deleteAttributeSet('Ring with 6 variation of stone');
		$this->deleteAttributeSet('Ring with 7 variation of stone');
		$this->deleteAttributeSet('Ring with 8 variation of stone');
		$this->deleteAttributeSet('Ring with 9 variation of stone');
		$this->deleteAttributeSet('Ring with 10 variation of stone');
		
		$this->deleteAttributeSet('Pendant with 6 variation of stone');
		$this->deleteAttributeSet('Pendant with 7 variation of stone');
		$this->deleteAttributeSet('Pendant with 8 variation of stone');
		$this->deleteAttributeSet('Pendant with 9 variation of stone');
		$this->deleteAttributeSet('Pendant with 10 variation of stone');
		
		$this->deleteAttributeSet('Earrings with 6 variation of stone');
		$this->deleteAttributeSet('Earrings with 7 variation of stone');
		$this->deleteAttributeSet('Earrings with 8 variation of stone');
		$this->deleteAttributeSet('Earrings with 9 variation of stone');
		$this->deleteAttributeSet('Earrings with 10 variation of stone');
		
		$this->deleteAttributeSet('Two Tone Ring with 0 variation of stone');
		$this->deleteAttributeSet('Two Tone Ring with 1 variation of stone');
		$this->deleteAttributeSet('Two Tone Ring with 2 variation of stone');
		$this->deleteAttributeSet('Two Tone Ring with 3 variation of stone');
		$this->deleteAttributeSet('Two Tone Ring with 4 variation of stone');
		$this->deleteAttributeSet('Two Tone Ring with 5 variation of stone');
		
		$this->deleteAttributeSet('Two Tone Pendant with 0 variation of stone');
		$this->deleteAttributeSet('Two Tone Pendant with 1 variation of stone');
		$this->deleteAttributeSet('Two Tone Pendant with 2 variation of stone');
		$this->deleteAttributeSet('Two Tone Pendant with 3 variation of stone');
		$this->deleteAttributeSet('Two Tone Pendant with 4 variation of stone');
		$this->deleteAttributeSet('Two Tone Pendant with 5 variation of stone');
		
		$this->deleteAttributeSet('Two Tone Earrings with 0 variation of stone');
		$this->deleteAttributeSet('Two Tone Earrings with 1 variation of stone');
		$this->deleteAttributeSet('Two Tone Earrings with 2 variation of stone');
		$this->deleteAttributeSet('Two Tone Earrings with 3 variation of stone');
		$this->deleteAttributeSet('Two Tone Earrings with 4 variation of stone');
		$this->deleteAttributeSet('Two Tone Earrings with 5 variation of stone');
		
		
		$this->deleteAttributeSet('Three Tone Ring with 0 variation of stone');
		$this->deleteAttributeSet('Three Tone Ring with 1 variation of stone');
		$this->deleteAttributeSet('Three Tone Ring with 2 variation of stone');
		$this->deleteAttributeSet('Three Tone Ring with 3 variation of stone');
		$this->deleteAttributeSet('Three Tone Ring with 4 variation of stone');
		$this->deleteAttributeSet('Three Tone Ring with 5 variation of stone');
		
		$this->deleteAttributeSet('Three Tone Pendant with 0 variation of stone');
		$this->deleteAttributeSet('Three Tone Pendant with 1 variation of stone');
		$this->deleteAttributeSet('Three Tone Pendant with 2 variation of stone');
		$this->deleteAttributeSet('Three Tone Pendant with 3 variation of stone');
		$this->deleteAttributeSet('Three Tone Pendant with 4 variation of stone');
		$this->deleteAttributeSet('Three Tone Pendant with 5 variation of stone');
		
		$this->deleteAttributeSet('Three Tone Earrings with 0 variation of stone');
		$this->deleteAttributeSet('Three Tone Earrings with 1 variation of stone');
		$this->deleteAttributeSet('Three Tone Earrings with 2 variation of stone');
		$this->deleteAttributeSet('Three Tone Earrings with 3 variation of stone');
		$this->deleteAttributeSet('Three Tone Earrings with 4 variation of stone');
		$this->deleteAttributeSet('Three Tone Earrings with 5 variation of stone');
		echo "done";exit;*/
		// managing set array
		$allStoneSet = array(
			'Ring with 1 variation of stone', 
			'Ring with 2 variation of stone', 
			'Ring with 3 variation of stone', 
			'Ring with 4 variation of stone',
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 1 variation of stone', 
			'Pendant with 2 variation of stone', 
			'Pendant with 3 variation of stone', 
			'Pendant with 4 variation of stone',
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 1 variation of stone', 
			'Earrings with 2 variation of stone', 
			'Earrings with 3 variation of stone', 
			'Earrings with 4 variation of stone',
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
			'Bracelet with 1 variation of stone', 
		
		);
		
		$twoMetalAllStoneSet = array(
			'Two Tone Ring with 1 variation of stone', 
			'Two Tone Ring with 2 variation of stone', 
			'Two Tone Ring with 3 variation of stone', 
			'Two Tone Ring with 4 variation of stone',
			'Two Tone Ring with 5 variation of stone',
			
			'Two Tone Pendant with 1 variation of stone', 
			'Two Tone Pendant with 2 variation of stone', 
			'Two Tone Pendant with 3 variation of stone', 
			'Two Tone Pendant with 4 variation of stone',
			'Two Tone Pendant with 5 variation of stone',
			
			'Two Tone Earrings with 1 variation of stone', 
			'Two Tone Earrings with 2 variation of stone', 
			'Two Tone Earrings with 3 variation of stone', 
			'Two Tone Earrings with 4 variation of stone',
			'Two Tone Earrings with 5 variation of stone',
		);
		
		$threeMetalAllStoneSet = array(
			'Three Tone Ring with 1 variation of stone', 
			'Three Tone Ring with 2 variation of stone', 
			'Three Tone Ring with 3 variation of stone', 
			'Three Tone Ring with 4 variation of stone',
			'Three Tone Ring with 5 variation of stone',
			
			'Three Tone Pendant with 1 variation of stone', 
			'Three Tone Pendant with 2 variation of stone', 
			'Three Tone Pendant with 3 variation of stone', 
			'Three Tone Pendant with 4 variation of stone',
			'Three Tone Pendant with 5 variation of stone',
			
			'Three Tone Earrings with 1 variation of stone', 
			'Three Tone Earrings with 2 variation of stone', 
			'Three Tone Earrings with 3 variation of stone', 
			'Three Tone Earrings with 4 variation of stone',
			'Three Tone Earrings with 5 variation of stone',
		);
		
		$twoStoneSet = array(
			'Ring with 2 variation of stone', 
			'Ring with 3 variation of stone', 
			'Ring with 4 variation of stone',
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 2 variation of stone', 
			'Pendant with 3 variation of stone', 
			'Pendant with 4 variation of stone',
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 2 variation of stone', 
			'Earrings with 3 variation of stone', 
			'Earrings with 4 variation of stone',
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
		);
		
		$threeStoneSet = array(
			'Ring with 3 variation of stone', 
			'Ring with 4 variation of stone',
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 3 variation of stone', 
			'Pendant with 4 variation of stone',
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 3 variation of stone', 
			'Earrings with 4 variation of stone',
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
		);
		
		$fourStoneSet = array(
			'Ring with 4 variation of stone',
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 4 variation of stone',
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 4 variation of stone',
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
		);
		
		$fiveStoneSet = array(
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
		);
		
		$sixStoneSet = array(
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
		);
		
		$sevenStoneSet = array(
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
		);
		
		$eightStoneSet = array(
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
		);
		
		$nineStoneSet = array(
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
			
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
			
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
			
		);
		
		$tenStoneSet = array(
			'Ring with 10 variation of stone',
			'Pendant with 10 variation of stone',
			'Earrings with 10 variation of stone',	
		);
		
		$zeroStoneSet = array(
			'Ring with 0 variation of stone',
			'Pendant with 0 variation of stone',
			'Earrings with 0 variation of stone',
			'Bracelet with 0 variation of stone',
		);
		
		$twoMetalZeroStoneSet = array(
			'Two Tone Ring with 0 variation of stone',
			'Two Tone Pendant with 0 variation of stone',
			'Two Tone Earrings with 0 variation of stone',
		);
		
		$threeMetalZeroStoneSet = array(
			'Three Tone Ring with 0 variation of stone',
			'Three Tone Pendant with 0 variation of stone',
			'Three Tone Earrings with 0 variation of stone',
		);
		
		$allRingSet = array(
			'Ring with 0 variation of stone',
			'Ring with 1 variation of stone', 
			'Ring with 2 variation of stone', 
			'Ring with 3 variation of stone', 
			'Ring with 4 variation of stone',
			'Ring with 5 variation of stone',
			'Ring with 6 variation of stone',
			'Ring with 7 variation of stone',
			'Ring with 8 variation of stone',
			'Ring with 9 variation of stone',
			'Ring with 10 variation of stone',
		);
		
		$allPendantSet = array(
			'Pendant with 0 variation of stone',
			'Pendant with 1 variation of stone', 
			'Pendant with 2 variation of stone', 
			'Pendant with 3 variation of stone', 
			'Pendant with 4 variation of stone',
			'Pendant with 5 variation of stone',
			'Pendant with 6 variation of stone',
			'Pendant with 7 variation of stone',
			'Pendant with 8 variation of stone',
			'Pendant with 9 variation of stone',
			'Pendant with 10 variation of stone',
		);
		
		$allEarringsSet = array(
			'Earrings with 0 variation of stone',
			'Earrings with 1 variation of stone', 
			'Earrings with 2 variation of stone', 
			'Earrings with 3 variation of stone', 
			'Earrings with 4 variation of stone',
			'Earrings with 5 variation of stone',
			'Earrings with 6 variation of stone',
			'Earrings with 7 variation of stone',
			'Earrings with 8 variation of stone',
			'Earrings with 9 variation of stone',
			'Earrings with 10 variation of stone',
		);
		
		$allBresletSet = array(
			'Bracelet with 0 variation of stone',
			'Bracelet with 1 variation of stone'
		);
		// creating attribute sets
		$this->createAttributeSet('Ring with 0 variation of stone');
		$this->createAttributeSet('Ring with 1 variation of stone');
		$this->createAttributeSet('Ring with 2 variation of stone');
		$this->createAttributeSet('Ring with 3 variation of stone');
		$this->createAttributeSet('Ring with 4 variation of stone');
		$this->createAttributeSet('Ring with 5 variation of stone');
		$this->createAttributeSet('Ring with 6 variation of stone');
		$this->createAttributeSet('Ring with 7 variation of stone');
		$this->createAttributeSet('Ring with 8 variation of stone');
		$this->createAttributeSet('Ring with 9 variation of stone');
		$this->createAttributeSet('Ring with 10 variation of stone');
		
		$this->createAttributeSet('Pendant with 0 variation of stone');
		$this->createAttributeSet('Pendant with 1 variation of stone');
		$this->createAttributeSet('Pendant with 2 variation of stone');
		$this->createAttributeSet('Pendant with 3 variation of stone');
		$this->createAttributeSet('Pendant with 4 variation of stone');
		$this->createAttributeSet('Pendant with 5 variation of stone');
		$this->createAttributeSet('Pendant with 6 variation of stone');
		$this->createAttributeSet('Pendant with 7 variation of stone');
		$this->createAttributeSet('Pendant with 8 variation of stone');
		$this->createAttributeSet('Pendant with 9 variation of stone');
		$this->createAttributeSet('Pendant with 10 variation of stone');
		
		$this->createAttributeSet('Earrings with 0 variation of stone');
		$this->createAttributeSet('Earrings with 1 variation of stone');
		$this->createAttributeSet('Earrings with 2 variation of stone');
		$this->createAttributeSet('Earrings with 3 variation of stone');
		$this->createAttributeSet('Earrings with 4 variation of stone');
		$this->createAttributeSet('Earrings with 5 variation of stone');
		$this->createAttributeSet('Earrings with 6 variation of stone');
		$this->createAttributeSet('Earrings with 7 variation of stone');
		$this->createAttributeSet('Earrings with 8 variation of stone');
		$this->createAttributeSet('Earrings with 9 variation of stone');
		$this->createAttributeSet('Earrings with 10 variation of stone');
		
		$this->createAttributeSet('Bracelet with 0 variation of stone');
		$this->createAttributeSet('Bracelet with 1 variation of stone');
		
		$this->createAttributeSet('Two Tone Ring with 0 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 1 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 2 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 3 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 4 variation of stone');
		$this->createAttributeSet('Two Tone Ring with 5 variation of stone');
		
		$this->createAttributeSet('Two Tone Pendant with 0 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 1 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 2 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 3 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 4 variation of stone');
		$this->createAttributeSet('Two Tone Pendant with 5 variation of stone');
		
		$this->createAttributeSet('Two Tone Earrings with 0 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 1 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 2 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 3 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 4 variation of stone');
		$this->createAttributeSet('Two Tone Earrings with 5 variation of stone');
		
		
		$this->createAttributeSet('Three Tone Ring with 0 variation of stone');
		$this->createAttributeSet('Three Tone Ring with 1 variation of stone');
		$this->createAttributeSet('Three Tone Ring with 2 variation of stone');
		$this->createAttributeSet('Three Tone Ring with 3 variation of stone');
		$this->createAttributeSet('Three Tone Ring with 4 variation of stone');
		$this->createAttributeSet('Three Tone Ring with 5 variation of stone');
		
		$this->createAttributeSet('Three Tone Pendant with 0 variation of stone');
		$this->createAttributeSet('Three Tone Pendant with 1 variation of stone');
		$this->createAttributeSet('Three Tone Pendant with 2 variation of stone');
		$this->createAttributeSet('Three Tone Pendant with 3 variation of stone');
		$this->createAttributeSet('Three Tone Pendant with 4 variation of stone');
		$this->createAttributeSet('Three Tone Pendant with 5 variation of stone');
		
		$this->createAttributeSet('Three Tone Earrings with 0 variation of stone');
		$this->createAttributeSet('Three Tone Earrings with 1 variation of stone');
		$this->createAttributeSet('Three Tone Earrings with 2 variation of stone');
		$this->createAttributeSet('Three Tone Earrings with 3 variation of stone');
		$this->createAttributeSet('Three Tone Earrings with 4 variation of stone');
		$this->createAttributeSet('Three Tone Earrings with 5 variation of stone');
		//echo "done";exit;*/
		
		# @todo add more options in array
		# @todo remove X.0 size if X is there (e.g. 4.0 and 4 both are present)
		$stonesArray = array('Amazonite', 'Amethyst', 'Aquamarine', 'Blue Topaz', 'Swiss Blue Topaz', 'Carnelian', 'Citrine', 'Colored Diamond', 'Diamond', 'Emerald', 'Garnet', 'Green Amethyst', 'Lapis Lazuli', 'Lemon Quartz', 'Opal', 'Pearl', 'Peridot', 'Pink Amethyst', 'Pink Sapphire', 'Pink Tourmaline', 'Rose Quartz', 'Ruby', 'Sapphire', 'Smoky Quartz', 'Tanzanite', 'Turquoise', 'White Sapphire', 'Yellow Sapphire', 'Natural Pink Diamond', 'Akoya Cultured Pearl', 'Black Tahitian Cultured Pearl', 'Freshwater Cultured Pearl');
		$stoneShapesArray = array('Baguette', 'Ball', 'Cushion', 'Drop', 'Emerald', 'Half Moon', 'Heart', 'Marquise', 'Oval', 'Pear', 'Round', 'Square', 'Trillion', 'Asscher', 'Radiant', 'Trapezoid', 'Rectangle' ,'Princess');
		$stoneSizesArray = array('0.6mm', '0.7mm', '0.8mm', '0.9mm','0.95mm', '1mm', '1.1mm', '1.15mm', '1.15x0.8mm', '1.1x0.8mm', '1.2mm', '1.25mm', '1.25x1x0.75mm', '1.2x0.8mm', '1.3mm', '1.3x0.8mm', '1.4mm', '1.4x0.8mm', '1.5mm', '1.5x1mm', '1.5x1.25x1mm', '1.5x1x0.75mm', '1.5x3mm', '1.6mm', '1.7mm', '1.75x0.75x0.5mm', '1.75x1.25x1mm', '1.75x1.5x1mm', '1.75x1.5x1.25mm', '1.75x1x0.5mm', '1.75x1x0.75mm', '1.8mm', '1.9mm', '10mm', '10.5mm', '10x5mm', '10x7mm', '10x8mm', '11mm', '11x9mm', '12mm', '12x10mm', '12x6mm', '12x8mm', '13mm', '14mm', '14x10mm', '14x12mm', '14x9mm', '15mm', '15x10mm', '16mm', '16x12mm', '16x8mm', '17mm', '18mm', '19mm', '1x0.6mm', '1x0.8mm', '2mm', '2.1mm', '2.2mm', '2.25x1.25x0.75mm', '2.25x1x0.5mm', '2.3mm', '2.4mm', '2.5mm', '2.6mm', '2.7mm', '2.8mm', '2.9mm', '20mm', '2x1.25x1mm', '2x1.5x1mm', '2x1.5x1.25mm', '2x1x0.5mm', '2x1x0.75mm', '2x1.5mm', '3.5x2x2.5mm', '4x2.5x3mm', '4.5x3x3.5mm', '5.5x3.5mm', '3mm', '3.1mm', '3.2mm', '3.3mm', '3.4mm', '3.5mm', '3.6mm', '3.7mm', '3.8mm', '3.9mm', '3x1.5mm', '3x2mm', '4mm', '4.1mm', '4.2mm', '4.3mm', '4.4mm', '4.5mm', '4.6mm', '4.7mm', '4.8mm', '4.9mm', '4x2mm', '4x3mm', '5mm', '5.1mm', '5.2mm', '5.3mm', '5.4mm', '5.5mm', '5.6mm', '5.7mm', '5.8mm', '5.9mm', '5x2.5mm', '5x3mm', '5x4mm', '6mm', '6.1mm', '6.2mm', '6.3mm', '6.4mm', '6.5mm', '6.5x4.5mm', '6.6mm', '6.7mm', '6.8mm', '6.9mm', '6x3mm', '6x4mm', '7mm', '7.5mm', '7.5x5.5mm', '7x3.5mm', '7x5mm', '8mm', '8.5mm', '8.5x6.5mm', '8x4mm', '8x5mm', '8x6mm', '9mm', '9.5mm', '9.5x6mm', '9x6mm', '9x6.5mm', '9x7mm', '10x6mm', '14x8mm');
		$stoneGradesArray = array('A', 'AA', 'AAA', 'AAAA', 'Lab Created', 'J I2', 'I I1', 'H SI2', 'G-H VS', 'J-M I2-I3', 'H-I I1', 'H SI1-SI2', 'G-H VS1-VS2', 'JK I2-I3', 'GH I1', 'H I4', 'GH I2-I3', 'GH Blue I2-I3', 'I1-I4', 'JK I1-I3', 'GH Black I1-I3', 'GH I2,I3', 'GH Blue I1,I2', 'GH I1,I3', 'GH I4', 'JK I1', 'Blue I1', 'GH Blue I1', 'Blue (Color Enhanced) I2', 'Black (Color Enhanced) I2', 'Black I1', 'VS1-VS2', 'SI1-SI2', 'JM I2-I3', 'H I I1', 'I3', 'Blue I1', 'GH Black (Color Enhanced) I1-I3', 'JK I2 I3', 'I I2', 'Yellow I1', 'GH, Blue (Color Enhanced) I2-I3', 'GH, Black (Color Enhanced) I1-I3', 'GH, Blue (Color Enhanced) I1-I2', 'GH, Blue (Color Enhanced) I1');
		$stoneTypesArray = array('Gemstone', 'Diamond');
		$stoneCutsArray = array('Asscher', 'Brilliant', 'Cabochon', 'Emerald', 'Faceted', 'Princess', 'Radiant', 'Step Cut', 'Sugarloaf');
		$metalTypesArray = array('Silver', '18K Yellow Gold', '18K White Gold','10K White Gold', '10K Yellow Gold', '14K White Gold', '14K Yellow Gold', '14K Rose Gold', 'Platinum' , '14K Two Tone Gold', '10K Two Tone Gold','18K Two Tone Gold', '14K Tricolor Gold', '10K Tricolor Gold', '18K Tricolor Gold', 'Two Tone Gold', '18k White & 14k Rose Gold');
		$filterableMetalTypesArray = array('Silver', 'White Gold', 'Yellow Gold', 'Platinum','Rose Gold' , 'Two Tone Gold', 'Tricolor Gold');
		$filterableStoneGradesArray = array('A - Good', 'AA - Better', 'AAA - Best', 'AAAA - Heirloom', 'Lab Created', 'J I2', 'I I1', 'H SI2', 'G-H VS', 'J-M I2-I3', 'H-I I1', 'H SI1-SI2', 'G-H VS1-VS2', 'JK I2-I3', 'GH I1', 'H I4', 'GH I2-I3', 'GH Blue I2-I3', 'I1-I4', 'JK I1-I3', 'GH Black I1-I3', 'GH I2,I3', 'GH I4', 'JK I1', 'Blue I1', 'GH Blue I1', 'Blue (Color Enhanced) I2', 'Black (Color Enhanced) I2', 'Black I1', 'VS1-VS2', 'SI1-SI2', 'JM I2-I3', 'H I I1', 'I3', 'Blue I1', 'GH Black (Color Enhanced) I1-I3', 'JK I2 I3', 'I I2', 'Yellow I1', 'GH, Blue (Color Enhanced) I2-I3', 'GH, Black (Color Enhanced) I1-I3', 'GH, Blue (Color Enhanced) I1-I2', 'GH, Blue (Color Enhanced) I1');
		$chainTypesArray = array('Box', 'Curb', 'Cable', 'Rope', 'Link', 'Wheat');
		$postTypesArray = array('Single Notch', 'Double Notch', 'Threaded', 'V-cut Post');
		$butterflyTypesArray = array('Push Back', 'Screw Back', 'Hinged Clip', 'Shepherd Hook', 'Lever Back', 'Fishhook');
		$stopperTypesArray = array('Plastic Round', 'Rubber Round');
		$filterableCaratWeightRangesArray = array('0.01 - 0.50', '0.51 - 1.00', '1.01 - 1.50', '1.51 - 3.00', 'Over 3.01');
		$jewelryStylesArray = array('Antique','Art Deco', 'Bridal Sets', 'Cathedral','Celtic','Charm','Cluster','Cocktail','Critters','Cross','Dangle','Designer','Engagement', 'Eternity', 'Fashion','Five Stone','Floral','Halo','Heart','Hoop','Journey','Key','Nine Stone','Seven Stone','Snow Flakes','Solitaire','Split Shank','Stackable','Studs','Three Stone','V-Bale','Vintage', 'Wedding Bands');
		$stoneSettingsArray = array('Bar','Bezel','Channel','Flush','Gypsy','Invisible','Pave','Peg','Pressure','Prong','Semi Bezel','Strung on Silk Thread','Tension','Top Drill');
		
		$jewelryType = array('Ring', 'Pendant', 'Earrings', 'Bracelet');
		
		echo "Creating attributes";

		// Crating attributes
		// Stones
		$this->createAttribute('Center Stone', 'stone1_name', array(
							'is_used_for_promo_rules'       => '1',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_name', $stonesArray);
		//echo "done-test";exit;
		$this->createAttribute('Accent Stone 1', 'stone2_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 2', 'stone3_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 3', 'stone4_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 4', 'stone5_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_name', $stonesArray);
		
		//Multiple Attribute Set upload
		$this->createAttribute('Accent Stone 5', 'stone6_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 6', 'stone7_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 7', 'stone8_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 8', 'stone9_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_name', $stonesArray);
		
		$this->createAttribute('Accent Stone 9', 'stone10_name', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_name', $stonesArray);
		
		// Shapes
		$this->createAttribute('Center Stone Shape', 'stone1_shape', array(
						'is_required'                   => '0',
						'backend_type'                => 'int',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 1 Shape', 'stone2_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 2 Shape', 'stone3_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 3 Shape', 'stone4_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 4 Shape', 'stone5_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_shape', $stoneShapesArray);
		
		//Multiple Attribute Set upload
		$this->createAttribute('Accent Stone 5 Shape', 'stone6_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 6 Shape', 'stone7_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 7 Shape', 'stone8_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 8 Shape', 'stone9_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_shape', $stoneShapesArray);
		
		$this->createAttribute('Accent Stone 9 Shape', 'stone10_shape', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_shape', $stoneShapesArray);
		
		// Sizes
		$this->createAttribute('Gemstone Size', 'stone1_size', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 1 Size', 'stone2_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 2 Size', 'stone3_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 3 Size', 'stone4_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 4 Size', 'stone5_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_size', $stoneSizesArray);
		
		//Multiple Attribute Set upload
		
		$this->createAttribute('Accent Stone 5 Size', 'stone6_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 6 Size', 'stone7_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 7 Size', 'stone8_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 8 Size', 'stone9_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_size', $stoneSizesArray);
		
		$this->createAttribute('Accent Stone 9 Size', 'stone10_size', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_size', $stoneSizesArray);
		
		
		// Grades
		$this->createAttribute('Gemstone Quality', 'stone1_grade', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
							'is_required'                   => '0',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 1 Grade', 'stone2_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 2 Grade', 'stone3_grade', array(
                        'is_required'                   => '0',
						'backend_type'                => 'int',
						), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 3 Grade', 'stone4_grade', array(
						'is_required'                   => '0',
						'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 4 Grade', 'stone5_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_grade', $stoneGradesArray);
		
		// Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Grade', 'stone6_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 6 Grade', 'stone7_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 7 Grade', 'stone8_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 8 Grade', 'stone9_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_grade', $stoneGradesArray);
		
		$this->createAttribute('Accent Stone 9 Grade', 'stone10_grade', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_grade', $stoneGradesArray);
		
		// Types
		$this->createAttribute('Center Stone Type', 'stone1_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 1 Type', 'stone2_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 2 Type', 'stone3_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 3 Type', 'stone4_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 4 Type', 'stone5_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_type', $stoneTypesArray);
		
		//Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Type', 'stone6_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 6 Type', 'stone7_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 7 Type', 'stone8_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 8 Type', 'stone9_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_type', $stoneTypesArray);
		
		$this->createAttribute('Accent Stone 9 Type', 'stone10_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_type', $stoneTypesArray);
		
		// Cuts
		$this->createAttribute('Center Stone Cut', 'stone1_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 1 Cut', 'stone2_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 2 Cut', 'stone3_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 3 Cut', 'stone4_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 4 Cut', 'stone5_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_cut', $stoneCutsArray);
		
		//Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Cut', 'stone6_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 6 Cut', 'stone7_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 7 Cut', 'stone8_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 8 Cut', 'stone9_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_cut', $stoneCutsArray);
		
		$this->createAttribute('Accent Stone 9 Cut', 'stone10_cut', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_cut', $stoneCutsArray);
		
		
		// Settings
		$this->createAttribute('Center Stone Setting', 'stone1_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('stone1_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 1 Setting', 'stone2_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $twoStoneSet);
		$this->addAttributeOption('stone2_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 2 Setting', 'stone3_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $threeStoneSet);
		$this->addAttributeOption('stone3_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 3 Setting', 'stone4_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fourStoneSet);
		$this->addAttributeOption('stone4_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 4 Setting', 'stone5_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $fiveStoneSet);
		$this->addAttributeOption('stone5_setting', $stoneSettingsArray);
		
		//Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Setting', 'stone6_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sixStoneSet);
		$this->addAttributeOption('stone6_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 6 Setting', 'stone7_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->addAttributeOption('stone7_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 7 Setting', 'stone8_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $eightStoneSet);
		$this->addAttributeOption('stone8_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 8 Setting', 'stone9_setting', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $nineStoneSet);
		$this->addAttributeOption('stone9_setting', $stoneSettingsArray);
		
		$this->createAttribute('Accent Stone 9 Setting', 'stone10_setting', array(
							'is_required'                  => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $tenStoneSet);
		$this->addAttributeOption('stone10_setting', $stoneSettingsArray);
		
		// Weights
		$this->createAttribute('Center Stone Weight', 'stone1_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $allStoneSet);
		$this->createAttribute('Accent Stone 1 Weight', 'stone2_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $twoStoneSet);
		$this->createAttribute('Accent Stone 2 Weight', 'stone3_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $threeStoneSet);
		$this->createAttribute('Accent Stone 3 Weight', 'stone4_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $fourStoneSet);
		$this->createAttribute('Accent Stone 4 Weight', 'stone5_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $fiveStoneSet);
		
		//Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Weight', 'stone6_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $sixStoneSet);
		$this->createAttribute('Accent Stone 6 Weight', 'stone7_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->createAttribute('Accent Stone 7 Weight', 'stone8_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $eightStoneSet);
		$this->createAttribute('Accent Stone 8 Weight', 'stone9_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $nineStoneSet);
		$this->createAttribute('Accent Stone 9 Weight', 'stone10_weight', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-number',
							'is_required'                   => '0',
                        ), $productTypes = -1, $tenStoneSet);
						
		// Counts
		$this->createAttribute('Center Stone Count', 'stone1_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $allStoneSet);
		$this->createAttribute('Accent Stone 1 Count', 'stone2_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $twoStoneSet);
		$this->createAttribute('Accent Stone 2 Count', 'stone3_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $threeStoneSet);
		$this->createAttribute('Accent Stone 3 Count', 'stone4_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $fourStoneSet);
		$this->createAttribute('Accent Stone 4 Count', 'stone5_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $fiveStoneSet);
						
		//Multiple Sets Add
		
		$this->createAttribute('Accent Stone 5 Count', 'stone6_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $sixStoneSet);
		$this->createAttribute('Accent Stone 6 Count', 'stone7_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $sevenStoneSet);
		$this->createAttribute('Accent Stone 7 Count', 'stone8_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $eightStoneSet);
		$this->createAttribute('Accent Stone 8 Count', 'stone9_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $nineStoneSet);
		$this->createAttribute('Accent Stone 9 Count', 'stone10_count', array(
							'is_required'                   => '0',
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, $tenStoneSet);
										
						
		// Metals
		$this->createAttribute('Metal Type', 'metal1_type', array(
							'is_configurable'               => '1',
							'backend_type'                  => 'int',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		$this->addAttributeOption('metal1_type', $metalTypesArray);
		
		// 2, 3 metals
		$this->createAttribute('Metal 2 Type', 'metal2_type', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, array_merge($twoMetalAllStoneSet, $twoMetalZeroStoneSet));
		$this->addAttributeOption('metal2_type', $metalTypesArray);
		
		$this->createAttribute('Metal 3 Type', 'metal3_type', array(
							'backend_type'                => 'int',
                        ), $productTypes = -1, array_merge($threeMetalAllStoneSet, $threeMetalZeroStoneSet));
		$this->addAttributeOption('metal3_type', $metalTypesArray);
		
		// Pendant
		$this->createAttribute('Chain Type', 'chain1_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allPendantSet);
		$this->addAttributeOption('chain1_type', $chainTypesArray);
		
		$this->createAttribute('Chain Length', 'chain1_length', array(
							'is_required'                   => '0',
							'frontend_input'                			=> 'text',
							'frontend_class'                => 'validate-number',
                        ), $productTypes = -1, $allPendantSet);
						
		// Earrings
		$this->createAttribute('Post Type', 'post1_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allEarringsSet);
		$this->addAttributeOption('post1_type', $postTypesArray);
		
		$this->createAttribute('Butterfly Type', 'butterfly1_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allEarringsSet);
		$this->addAttributeOption('butterfly1_type', $butterflyTypesArray);
		
		$this->createAttribute('Stopper Type', 'stopper1_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, $allEarringsSet);
		$this->addAttributeOption('stopper1_type', $stopperTypesArray);
		
		
		// Filterable Attributes
		$this->createAttribute('Stone Shape', 'filterable_stone_shapes', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('filterable_stone_shapes', $stoneShapesArray);
		
		$this->createAttribute('Gemstone Type', 'filterable_stone_names', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('filterable_stone_names', $stonesArray);
		
		$this->createAttribute('Total Carat Weight', 'filterable_carat_weight_ranges', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('filterable_carat_weight_ranges', $filterableCaratWeightRangesArray);
		
		$this->createAttribute('Metal Type', 'filterable_metal_types', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		$this->addAttributeOption('filterable_metal_types', $filterableMetalTypesArray);
		
		$this->createAttribute('Style', 'jewelry_styles', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		$this->addAttributeOption('jewelry_styles', $jewelryStylesArray);
		
		$this->createAttribute('Quality', 'filterable_stone_grades', array(
							'backend_model' 				=> 'eav/entity_attribute_backend_array',
							'frontend_input'                => 'multiselect',
							'is_filterable'                 => '1',
                            'is_filterable_in_search'       => '1',
                        ), $productTypes = -1, $allStoneSet);
		$this->addAttributeOption('filterable_stone_grades', $filterableStoneGradesArray);
		
		// default attributes for configurable products
		$this->createAttribute('Default Stone Size', 'default_stone1_size', array(
							'backend_type'                => 'int',
                        ), $productTypes = array('configurable'), $allStoneSet);
		$this->addAttributeOption('default_stone1_size', $stoneSizesArray);

		$this->createAttribute('Default Stone Grade', 'default_stone1_grade', array(
							'backend_type'                => 'int',
                        ), $productTypes = array('configurable'), $allStoneSet);
		$this->addAttributeOption('default_stone1_grade', $stoneGradesArray);

		$this->createAttribute('Default Metal Type', 'default_metal1_type', array(
							'backend_type'                => 'int',
                        ), $productTypes = array('configurable'), array_merge($allStoneSet, $zeroStoneSet));
		$this->addAttributeOption('default_metal1_type', $metalTypesArray);
		
		// common attributes
		$this->createAttribute('Stone Variation Count', 'stone_variation_count', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		$this->createAttribute('Metal Variation Count', 'metal_variation_count', array(
							'frontend_input'                => 'text',
							'frontend_class'                => 'validate-digits',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
						
		$this->createAttribute('Video', 'video', array(
							'frontend_input'                => 'text',
							'is_required'                   => '0',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		
		$this->createAttribute('Jewelry Type', 'jewelry_type', array(
							'is_required'                   => '0',
							'backend_type'                => 'int',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));
		$this->addAttributeOption('jewelry_type', $jewelryType);
		
		$this->createAttribute('Master Sku', 'master_sku', array(
							'frontend_input'                => 'text',
							'is_required'                   => '0',
                        ), $productTypes = -1, array_merge($allStoneSet, $zeroStoneSet));	
		
		/*$this->createAttribute('Minimum Variation Price', 'min_variation_price', array(
							'frontend_input'                => 'price',
							'is_required'                   => '0',
							'backend_type'                  => 'decimal',
                        ), $productTypes = array('configurable'), array_merge($allStoneSet, $zeroStoneSet));
						
		$this->createAttribute('Maximum Variation Price', 'max_variation_price', array(
							'frontend_input'                => 'price',
							'is_required'                   => '0',
							'backend_type'                  => 'decimal',
                        ), $productTypes = array('configurable'), array_merge($allStoneSet, $zeroStoneSet));*/
						
		$this->createAttribute('Yellow Gold Image', 'yellow_gold_image', array(
							'frontend_input'                => 'text',
							'is_required'                   => '0',
                        ), $productTypes = array('configurable'), array_merge($allStoneSet, $zeroStoneSet));
			
		$this->createAttribute('White Gold Image', 'white_gold_image', array(
							'frontend_input'                => 'text',
							'is_required'                   => '0',
                        ), $productTypes = array('configurable'), array_merge($allStoneSet, $zeroStoneSet));												
													
	}
	
	    
        public function createAttributeSet($setName, $copyGroupsFromID = -1)
        {
     
            $setName = trim($setName);
			
     
            $this->logInfo("Creating attribute-set with name [$setName].");
     
            if($setName == '')
            {
                $this->logError("Could not create attribute set with an empty name.");
                return false;
            }
     
            //>>>> Create an incomplete version of the desired set.
     
            $model = Mage::getModel('eav/entity_attribute_set');
     
            // Set the entity type.
     
            $entityTypeID = Mage::getModel('catalog/product')->getResource()->getTypeId();
            $this->logInfo("Using entity-type-ID ($entityTypeID).");
     
            $model->setEntityTypeId($entityTypeID);
     
            // We don't currently support groups, or more than one level. See
            // Mage_Adminhtml_Catalog_Product_SetController::saveAction().
     
            $this->logInfo("Creating vanilla attribute-set with name [$setName].");
     
            $model->setAttributeSetName($setName);
     
            // We suspect that this isn't really necessary since we're just
            // initializing new sets with a name and nothing else, but we do
            // this for the purpose of completeness, and of prevention if we
            // should expand in the future.
            $model->validate();
     
            // Create the record.
     
            try
            {
                $model->save();
            }
            catch(Exception $ex)
            {
                $this->logError("Initial attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
                return false;
            }
     
            if(($id = $model->getId()) == false)
            {
                $this->logError("Could not get ID from new vanilla attribute-set with name [$setName].");
                return false;
            }
     
            $this->logInfo("Set ($id) created.");
     
            //<<<<
     
            //>>>> Load the new set with groups (mandatory).
     
            // Attach the same groups from the given set-ID to the new set.
            if($copyGroupsFromID !== -1)
            {
                $this->logInfo("Cloning group configuration from existing set with ID ($copyGroupsFromID).");
               
                $model->initFromSkeleton($copyGroupsFromID);
            }
     
            // Just add a default group.
            else
            {
				$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                $this->logInfo("Creating default group [{$this->groupName}] for set.");
				
				$copyGroupsFromID = $setup->getAttributeSetId('catalog_product', 'Default');
				$model->initFromSkeleton($copyGroupsFromID);
                /*$modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setAttributeGroupName($this->groupName);
                $modelGroup->setAttributeSetId($id);
     
                // This is optional, and just a sorting index in the case of
                // multiple groups.
                // $modelGroup->setSortOrder(1);
     
                $model->setGroups(array($modelGroup));
				*/
            }
     
            //<<<<
     
            // Save the final version of our set.
     
            try
            {
                $model->save();
            }
            catch(Exception $ex)
            {
                $this->logError("Final attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
                return false;
            }
     
            $this->logInfo("Created attribute-set with ID ($id).");
     
            return array(
                            'SetID'     => $id,
                            'GroupID'   => $groupID,
                        );
        }
     
	 
	 public function addAttributeOption($attributeCode, $options){

		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
		$attr = $attr_model->loadByCode('catalog_product', $attributeCode);
		$attr_id = $attr->getAttributeId();
		
		$oldOptions = $attr->getSource()->getAllOptions(false);

		$option['attribute_id'] = $attr_id;
		foreach ($options as $key => $value) {
			foreach($oldOptions as $oldOption){
				if($oldOption['label'] == $value){
					continue 2;
				}
			}
			$option['value']['option_'.$key][0] = $value;
		}
		
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);
		
	}
	 
        /**
         * Create an attribute.
         *
         * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
         *
         * @return int|false
         */
        public function createAttribute($labelText, $attributeCode, $values = -1, $productTypes = -1, $setNames)
        {
     		$this->deleteAttribute($attributeCode);
            $labelText = trim($labelText);
            $attributeCode = trim($attributeCode);
     
            if($labelText == '' || $attributeCode == '')
            {
                $this->logError("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
                return false;
            }
     
            if($values === -1)
                $values = array();
     
            if($productTypes === -1)
                $productTypes = array();
     
            $this->logInfo("Creating attribute [$labelText] with code [$attributeCode].");
     
            //>>>> Build the data structure that will define the attribute. See
            //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     
            $data = array(
                            'is_global'                     => '1',
                            'frontend_input'       			=> 'select',
							'backend_model' 				=> '',
                            'default_value_text'            => '',
                            'default_value_yesno'           => '0',
                            'default_value_date'            => '',
                            'default_value_textarea'        => '',
                            'is_unique'                     => '0',
                            'is_required'                   => '1',
                            'frontend_class'                => '',
                            'is_searchable'                 => '0',
                            'is_visible_in_advanced_search' => '0',
                            'is_comparable'                 => '0',
                            'is_used_for_promo_rules'       => '0',
                            'is_html_allowed_on_front'      => '0',
                            'is_visible_on_front'           => '0',
                            'used_in_product_listing'       => '0',
                            'used_for_sort_by'              => '0',
                            'is_configurable'               => '0',
                            'is_filterable'                 => '0',
                            'is_filterable_in_search'       => '0',
                            'backend_type'                  => 'varchar',
                            'default_value'                 => '',
                        );
     
            // Now, overlay the incoming values on to the defaults.
            foreach($values as $key => $newValue)
                if(isset($data[$key]) == false)
                {
                    $this->logError("Attribute feature [$key] is not valid.");
                    return false;
                }
           
                else
                    $data[$key] = $newValue;
     
            // Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
            $data['apply_to']       = $productTypes;
            $data['attribute_code'] = $attributeCode;
            $data['frontend_label'] = array(
                                                0 => $labelText,
                                                1 => '',
                                                3 => '',
                                                2 => '',
                                                4 => '',
                                            );
     
            //<<<<
     
            //>>>> Build the model.
     
            $model = Mage::getModel('catalog/resource_eav_attribute');
     
            $model->addData($data);
     
            $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
            $model->setEntityTypeId($entityTypeID);
     
            $model->setIsUserDefined(1);
     
            //<<<<
     
            // Save.
     
            try
            {
                $model->save();
            }
            catch(Exception $ex)
            {
                $this->logError("Attribute [$labelText] could not be saved: " . $ex->getMessage());
                return false;
            }
     
            $id = $model->getId();
			
			if(!empty($setNames[0])){
				foreach($setNames as $setName){
					$this->addAttributeToSet($attributeCode, $setName);
				}
            }
     
            $this->logInfo("Attribute [$labelText] has been saved as ID ($id).");
     
            return $id;
        }
		
		public function deleteAttribute($attributeCode){
			try{
				$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
				$attributeType = 'catalog_product';
				$attributeId = $installer->getAttributeId($attributeType, $attributeCode);
				$model = Mage::getModel('catalog/resource_eav_attribute');
      			$model->load($attributeId);
      			$model->delete();
			}
			catch(Exception $e){
				var_dump($e);
			}
		}
		
		public function deleteAttributeSet($attributeSetName){
			try{
				$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
				$attributeSetId = $installer->getAttributeSetId('catalog_product', $attributeSetName);
				$model = Mage::getModel('eav/entity_attribute_set');
      			$model->load($attributeSetId);
      			$model->delete();
			}
			catch(Exception $e){
				var_dump($e);
			}
		}
		
		public function addAttributeToSet($attributeCode, $attributeSetName, $groupName = 'Jewelry Details'){
			try {
				$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
				//-------------- add attribute to set and group
				
				
				$attributeSetId = $setup->getAttributeSetId('catalog_product', $attributeSetName);
				$attributeGroupId = $setup->getAttributeGroupId('catalog_product', $attributeSetId, $groupName);
				$attributeId = $setup->getAttributeId('catalog_product', $attributeCode);
				
				$setup->addAttributeToSet($entityTypeId='catalog_product',$attributeSetId, $attributeGroupId, $attributeId);
				
			} 
			catch (Exception $e) { 
				echo '<p>Sorry, error occured while trying to save the attribute to attribute set. Error: '.$e->getMessage().'</p>'; 
			}
		}
		
		public function addAttributeToSetById($attributeCode, $attributeSetId, $groupName = 'Jewelry Details'){
			try {
				$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
				//-------------- add attribute to set and group
				
				
				//$attributeSetId = $setup->getAttributeSetId('catalog_product', $attributeSetName);
				$attributeGroupId = $setup->getAttributeGroupId('catalog_product', $attributeSetId, $groupName);
				$attributeId = $setup->getAttributeId('catalog_product', $attributeCode);
				
				$setup->addAttributeToSet($entityTypeId='catalog_product',$attributeSetId, $attributeGroupId, $attributeId);
				
			} 
			catch (Exception $e) { 
				echo "<p>Sorry, Attribute $attributeCode Not Exists</p>"; 
			}
		}
		
		public function logInfo($str){
			echo $str."<br>";
		}
		
		public function logError($str){
			echo '<h3>'.$str."</h3><br>";
		}

}
?>
