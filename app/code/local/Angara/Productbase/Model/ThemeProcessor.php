<?php
class Angara_Productbase_Model_ThemeProcessor extends Mage_Core_Model_Abstract
{
	protected $_storeId = 0;  # todo get store id without hardcoding
	
	protected $_productMetals;
	protected $_productStones;
	protected $_productGrades;
	protected $_ringAttributeSetId;
	
	/*
     * Class constructor
     */
    public function _construct()
    {
        $this->_productMetals = Mage::getModel('productbase/productMetal')->getCollection();
		$this->_productStones = Mage::getModel('productbase/productStone')->getCollection();
		$this->_productGrades = Mage::getModel('productbase/productGrade')->getCollection();
		
		$this->_ringAttributeSetId = Mage::getModel("eav/entity_attribute_set")->getCollection()->getItemByColumnValue('attribute_set_name','Rings')->getAttributeSetId();
    }
	
	public function resetProduct($product, $debug = false){
		if($product->getTheme() == "angara/customj"){
			if($debug){
				$this->_createCustomjPriceFactor($product, $debug);
			}
			else{
				$this->_setCustomjTheme($product);
			}
		}
		else if($product->getTheme() == "angara/ADVRP"){
			# todo manage debug for ADVRP
			if(!$debug){
				$this->_setADVRPTheme($product);
			}
		}
		else{
			# todo manage debug for default
			if(!$debug){
				$this->_setDefaultTheme($product);
			}
		}
		if(!$debug && $product->getAltered()){
			$product->save();
		}
	}
	
	
	protected function _setRingSize($product, $options){
		if($product->getAttributeSetId() == $this->_ringAttributeSetId){
			$ringSizeOptions = $options->getItemByColumnValue('title', "Ring Size");
			if($ringSizeOptions){
				if($ringSizeOptions->getType() !="drop_down"){
					$ringSizeOptions->delete();
					$this->_createRingSize($product);
					$product->setAltered(true);
				}
				else{
					
				}
			}else{
				$this->_createRingSize($product);
				$product->setAltered(true);
			}
		}
	}
	
	protected function _createRingSize($product){
		$product->setHasOptions(1);
		$values = array();
		$size = 3;
		$sort_order = 1;
		for($i = 0; $i<21; $i++){
			$values[] = array(
				'title'		=> $size,
				'price'		=> 0,
				'price_type'	=> 'fixed',
				'sku' => $size,
				'sort_order' => $sort_order++
			);
			$size+=.5;
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 1,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 0
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Ring Size", $options, $values);
	}
	
	
	/*
	*
	* --- customj Theme ------------
	*
	*/
	
	protected function _setCustomjTheme($product){
		$this->_setCustomjCustomOptions($product);
		//if($product->getCustomDesign() != 'angara/customj' || ($product->getCustomj() != 1)){
			$product->setCustomDesign('angara/customj');
			$product->setCustomj(1);
			$product->setHasOptions(1);
			$product->setAltered(true);
		//}
		
		# todo apply theme on mage product
	}
	
	protected function _setCustomjCustomOptions($product){
		$options = $product->getProductOptionsCollection();
		
		$this->_setRingSize($product, $options);
		$this->_setCustomjStoneQuality($product, $options);
		$this->_setCustomjMetalType($product, $options);
		$this->_setCustomjStoneSize($product, $options);
		$this->_setCustomjCoordinates($product, $options);
		$this->_setCustomjSizeVariation($product, $options);
		$this->_setCustomjCountVariation($product, $options);
		$this->_setCustomjWeightVariation($product, $options);
		$this->_setCustomjPriceFactor($product, $options);
		$this->_setCustomjVariationInfoToOrder($product, $options);
		# todo check if unwanted custom option is present
		
	}
	
	protected function _setCustomjStoneQuality($product, $options){
		$stoneQualityOptions = $options->getItemByColumnValue('title', "Stone Quality");
		if($stoneQualityOptions){
			if($stoneQualityOptions->getType() !="drop_down"){
				$stoneQualityOptions->delete();
				$this->_createCustomjStoneQuality($product);
				//$product->setAltered(true);
			}
			else{
				//$this->_updateCustomjStoneQuality($product, $options);
			}
		}else{
			$this->_createCustomjStoneQuality($product);
			$product->setAltered(true);
		}
	}
	
	protected function _createCustomjStoneQuality($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		foreach($stones as $stone){
			foreach($grades as $grade){
				$values[] = array(
					'title'		=> $grade->getGrade()."|".$stone->getCenterStoneSize(),
					'price'		=> 0,
					'price_type'	=> 'fixed',
					'sku' => 'SQ-'.$grade->getGrade(),
					'sort_order' => ($stone->getIsDefault()&&$grade->getIsDefault())?0:1
				);
			}
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 1
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Stone Quality", $options, $values);
	}
	
	protected function _setCustomjMetalType($product, $options){
		$metalTypeOptions = $options->getItemByColumnValue('title', "Metal Type");
		if($metalTypeOptions){
			if($metalTypeOptions->getType() !="drop_down"){
				$metalTypeOptions->delete();
				$this->_createCustomjMetalType($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjMetalType($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjMetalType($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metals = $this->_productMetals->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		$sort_order = 1;
		foreach($stones as $stone){
			foreach($metals as $metal){
				
				if($metal->getAlias() == 'SL'){
					$sort_order = 1;
				}else if($metal->getAlias() == 'WG'){
					$sort_order = 2;
				}else if($metal->getAlias() == 'YG'){
					$sort_order = 3;
				}else if($metal->getAlias() == 'RG'){
					$sort_order = 3;
				}else if($metal->getAlias() == 'PT'){
					$sort_order = 4;
				}
				
				$values[] = array(
					'title'		=> $metal->getMetal()."|".$stone->getCenterStoneSize(),
					'price'		=> 0,
					'price_type'	=> 'fixed',
					'sku' => 'MT-'.$metal->getAlias(),
					'sort_order' => $sort_order
				);
			}
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 2
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Metal Type", $options, $values);
	}
	
	protected function _setCustomjStoneSize($product, $options){
		$stoneSizeOptions = $options->getItemByColumnValue('title', "Stone Size");
		if($stoneSizeOptions){
			if($stoneSizeOptions->getType() !="drop_down"){
				$stoneSizeOptions->delete();
				$this->_createCustomjStoneSize($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjStoneSize($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjStoneSize($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		$sort_order = 1;
		foreach($stones as $stone){
			$values[] = array(
				'title'		=> $stone->getCenterStoneSize(),
				'price'		=> 0,
				'price_type'	=> 'fixed',
				'sku' => 'SS-'.$stone->getCenterStoneSize(),
				'sort_order' => $sort_order++
			);
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 3
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Stone Size", $options, $values);
	}
	
	protected function _setCustomjCoordinates($product, $options){
		$coordinatesOptions = $options->getItemByColumnValue('title', "coordinates");
		if($coordinatesOptions){
			if($coordinatesOptions->getType() !="drop_down"){
				$coordinatesOptions->delete();
				$this->_createCustomjCoordinates($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjCoordinates($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjCoordinates($product){
		
		$coordinates = explode(';',$product->getCoordinates());
		
		$values = array();
		foreach($coordinates as $coordinate){
			$values[] = array(
				'title'		=> $coordinate,
				'price'		=> 0,
				'price_type'	=> 'fixed'
			);
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 4
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "coordinates", $options, $values);
	}
	
	protected function _setCustomjSizeVariation($product, $options){
		$SizeVariationOptions = $options->getItemByColumnValue('title', "size-variation");
		if($SizeVariationOptions){
			if($SizeVariationOptions->getType() !="drop_down"){
				$SizeVariationOptions->delete();
				$this->_createCustomjSizeVariation($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjSizeVariation($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjSizeVariation($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		
		foreach($stones as $stone){
			if($stone->getStone1Count() != 0){
				$values[] = array(
					'title'		=> $stone->getCenterStoneSize()."|Emb1-".$stone->getStone1Size(),
					'price'		=> 0,
					'price_type'	=> 'fixed'
				);
			}
			$values[] = array(
				'title'		=> $stone->getCenterStoneSize()."|Emb2-".$stone->getCenterStoneSize(),
				'price'		=> 0,
				'price_type'	=> 'fixed'
			);
			if($stone->getStone3Count() != 0){
				$values[] = array(
					'title'		=> $stone->getCenterStoneSize()."|Emb3-".$stone->getStone3Size(),
					'price'		=> 0,
					'price_type'	=> 'fixed'
				);
			}
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 5
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "size-variation", $options, $values);
	}
	
	protected function _setCustomjCountVariation($product, $options){
		$CountVariationOptions = $options->getItemByColumnValue('title', "count-variation");
		if($CountVariationOptions){
			if($CountVariationOptions->getType() !="drop_down"){
				$CountVariationOptions->delete();
				$this->_createCustomjCountVariation($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjCountVariation($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjCountVariation($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		
		foreach($stones as $stone){
			if($stone->getStone1Count() != 0){
				$values[] = array(
					'title'		=> $stone->getCenterStoneSize()."|Emb1-".array_sum(explode(', ',$stone->getStone1Count())),
					'price'		=> 0,
					'price_type'	=> 'fixed'
				);
			}
			$values[] = array(
				'title'		=> $stone->getCenterStoneSize()."|Emb2-".array_sum(explode(', ',$stone->getCenterStoneCount())),
				'price'		=> 0,
				'price_type'	=> 'fixed'
			);
			if($stone->getStone3Count() != 0){
				$values[] = array(
					'title'		=> $stone->getCenterStoneSize()."|Emb3-".array_sum(explode(', ',$stone->getStone3Count())),
					'price'		=> 0,
					'price_type'	=> 'fixed'
				);
			}
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 6
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "count-variation", $options, $values);
	}
	
	protected function _setCustomjWeightVariation($product, $options){
		$WeightVariationOptions = $options->getItemByColumnValue('title', "weight-variation");
		if($WeightVariationOptions){
			if($WeightVariationOptions->getType() !="drop_down"){
				$WeightVariationOptions->delete();
				$this->_createCustomjWeightVariation($product);
				//$product->setAltered(true);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createCustomjWeightVariation($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjWeightVariation($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		//if(count($stones)>1){
			foreach($stones as $stone){
				if($stone->getStone1Count() != 0){
					$weight = 0;
					$stoneCount = explode(', ', $stone->getStone1Count());
					$stoneWeight = explode(', ', $stone->getStone1Weight());
					for($i = 0; $i < count($stoneCount); $i++){
						$weight += ($stoneWeight[$i] * $stoneCount[$i]);
					}
					
					$values[] = array(
						'title'		=> $stone->getCenterStoneSize()."|Emb1-".round($weight,2),
						'price'		=> 0,
						'price_type'	=> 'fixed'
					);
				}
				$weight = 0;
				$stoneCount = explode(', ', $stone->getCenterStoneCount());
				$stoneWeight = explode(', ', $stone->getCenterStoneWeight());
				for($i = 0; $i < count($stoneCount); $i++){
					$weight += $stoneWeight[$i] * $stoneCount[$i];
				}
				$values[] = array(
					'title'		=> $stone->getCenterStoneSize()."|Emb2-".round($weight,2),
					'price'		=> 0,
					'price_type'	=> 'fixed'
				);
				if($stone->getStone3Count() != 0){
					$weight = 0;
					$stoneCount = explode(', ', $stone->getStone3Count());
					$stoneWeight = explode(', ', $stone->getStone3Weight());
					for($i = 0; $i < count($stoneCount); $i++){
						$weight += ($stoneWeight[$i] * $stoneCount[$i]);
					}
					$values[] = array(
						'title'		=> $stone->getCenterStoneSize()."|Emb3-".round($weight,2),
						'price'		=> 0,
						'price_type'	=> 'fixed'
					);
				}
			}
			
			$options = 	array(
				'type'		=> 'drop_down',
				'is_require'	=> 0,
				'price'		=> 0,
				'price_type'	=> 'fixed',
				'sort_order' => 7
			  );
			
			Mage::helper('productbase/productbase')->saveCustomOption($product, "weight-variation", $options, $values);
		//}
	}
	
	protected function _setCustomjPriceFactor($product, $options){
		$PriceFactorOptions = $options->getItemByColumnValue('title', "Price Factor");
		if($PriceFactorOptions){
			if($PriceFactorOptions->getType() !="drop_down"){
				$PriceFactorOptions->delete();
				$this->_createCustomjPriceFactor($product);
				//$product->setAltered(true);
			}
			else{
				$this->_updateCustomjPriceFactor($product, $PriceFactorOptions);
			}
		}else{
			$this->_createCustomjPriceFactor($product);
			//$product->setAltered(true);
		}
	}
	
	protected function _createCustomjPriceFactor($product, $debug = false){
		
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metals = $this->_productMetals->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$values = array();
		
		foreach($metals as $metal){
			foreach($stones as $stone){
				foreach($grades as $grade){
					if($metal->getAlias()!="YG" && $metal->getAlias()!="RG"){
						$values[] = array(
							'title'		=> $metal->getAlias()."-".$grade->getGrade()."-".$stone->getCenterStoneSize(),
							'price'		=> $this->_getPriceFactor($product, $metal, $stone, $grade, $debug),
							'price_type'	=> 'fixed'
						);
					}
				}
			}
		}
		
		$options = 	array(
			'type'		=> 'drop_down',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 8
		  );
		if(!$debug){
			Mage::helper('productbase/productbase')->saveCustomOption($product, "Price Factor", $options, $values);
		}
	}
	
	protected function _updateCustomjPriceFactor($product, $PriceFactorOptions){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metals = $this->_productMetals->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$product->setHasOptions(1);
		$values = $PriceFactorOptions->getValuesCollection();
		$newValues = array();
		
		$altered = false;
		
		foreach($metals as $metal){
			foreach($stones as $stone){
				foreach($grades as $grade){
					if($metal->getAlias()!="YG" && $metal->getAlias()!="RG"){
						$value = $values->getItemByColumnValue('title',$metal->getAlias()."-".$grade->getGrade()."-".$stone->getCenterStoneSize());
						if($value){
							$price = floatval($this->_getPriceFactor($product, $metal, $stone, $grade, $debug));
							if($value->getPrice() != $price){
								$value->setPrice($price);
								$altered = true;
							}
							$newValues[]=$value->toArray();
						}else{
							$value = array(
								'title'		=> $metal->getAlias()."-".$grade->getGrade()."-".$stone->getCenterStoneSize(),
								'price'		=> $this->_getPriceFactor($product, $metal, $stone, $grade, $debug),
								'price_type'	=> 'fixed'
							);
							$newValues[] = $value;
							$altered = true;
						}
					}
				}
			}
		}
		
		if($altered){
			$PriceFactorOptions->setStoreId(0);
			$PriceFactorOptions->setData('values',$newValues);
			$PriceFactorOptions->save();
		}
	}
	
	protected function _setCustomjVariationInfoToOrder($product, $options){
		$VariationInfoToOrderOption = $options->getItemByColumnValue('title', "VariationInfoToOrder");
		if($VariationInfoToOrderOption){
			if($VariationInfoToOrderOption->getType() !="field"){
				$VariationInfoToOrderOption->delete();
				$this->_createVariationInfoToOrder($product, $options);
			}
			else{
				# update options if needed
			}
		}else{
			$this->_createVariationInfoToOrder($product, $options);
		}
	}
	
	protected function _createVariationInfoToOrder($product){
		$options = 	array(
			'type'		=> 'field',
			'is_require'	=> 0,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 9,
			'max_characters' => 500
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "VariationInfoToOrder", $options, array());
	}
	
	
	private function _getPriceFactor($product, $metal, $stone, $grade, $debug = false){
		
		return $this->_getVariationPrice($product, $metal, $stone, $grade, $debug) - $product->getPrice();
	}
	
	
	private function _getVariationPrice($product, $metal, $stone, $grade, $debug = false){
		$dummyProduct = Mage::getModel('productbase/dummyProduct');
		$dummyProduct->setSku($product->getSku());
		$dummyProduct->setMetal(Mage::helper('productbase/productbase')->findMetalFullName($product->getAttributeText("metal"), $metal->getMetal()));
		$dummyProduct->setAvgMetalWeight($product->getAvgMetalWeight() + $stone->getAvgMetalWeightAdjustment());
		$dummyProduct->setfinding_14kgold($product->getfinding_14kgold());
		$dummyProduct->setFindingSilver($product->getFindingSilver());
		$dummyProduct->setFindingPlatinum($product->getFindingPlatinum());
		$dummyProduct->setAttributeSetId($product->getAttributeSetId());
		$dummyProduct->setAngaraPbCategoryId($product->getAngaraPbCategoryId());
		$dummyProduct->setGrade($grade->getGrade());
		
		$dummyStones = array();
		
		$stoneCount = explode(', ', $stone->getCenterStoneCount());
		$stoneSize = explode(', ', $stone->getCenterStoneSize());
		$stoneSetting = explode(', ', $product->getAttributeText('emb_setting_type2'));
		
		
		
		for($i = 0; $i < count($stoneCount); $i++){
			$dummyStone2 = Mage::getModel('productbase/dummyStone');
			$dummyStone2->setShape($product->getAttributeText('emb_shape2'));
			$dummyStone2->setName($product->getAttributeText("emb_stone_name2"));
			$dummyStone2->setGrade($grade->getGrade());
			$dummyStone2->setCount($stoneCount[$i]);
			$dummyStone2->setSize($stoneSize[$i]);
			$dummyStone2->setSettingType($stoneSetting[$i]);
			$dummyStones[] = $dummyStone2;
		}
		
		
		
		if($stone->getStone1Count() != 0){
			
			$stoneCount = explode(', ', $stone->getStone1Count());
			$stoneSize = explode(', ', $stone->getStone1Size());
			$stoneSetting = explode(', ', $product->getAttributeText('emb_setting_type1'));
			
			for($i = 0; $i < count($stoneCount); $i++){
				$dummyStone1 = Mage::getModel('productbase/dummyStone');
				$dummyStone1->setShape($product->getAttributeText('emb_shape1'));
				$dummyStone1->setName($product->getAttributeText("emb_stone_name"));
				$dummyStone1->setGrade($grade->getGrade());
				$dummyStone1->setCount($stoneCount[$i]);
				$dummyStone1->setSize($stoneSize[$i]);
				$dummyStone1->setSettingType($stoneSetting[$i]);
				$dummyStones[] = $dummyStone1;
			}
		}
		
		if($stone->getStone3Count() != 0){
			
			$stoneCount = explode(', ', $stone->getStone3Count());
			$stoneSize = explode(', ', $stone->getStone3Size());
			$stoneSetting = explode(', ', $product->getAttributeText('emb_setting_type3'));
			
			
			for($i = 0; $i < count($stoneCount); $i++){
				$dummyStone3 = Mage::getModel('productbase/dummyStone');
				$dummyStone3->setShape($product->getAttributeText('emb_shape3'));
				$dummyStone3->setName($product->getAttributeText("emb_stone_name3"));
				$dummyStone3->setGrade($grade->getGrade());
				$dummyStone3->setCount($stoneCount[$i]);
				$dummyStone3->setSize($stoneSize[$i]);
				$dummyStone3->setSettingType($stoneSetting[$i]);
				$dummyStones[] = $dummyStone3;
			}
		}
		$dummyProduct->setStones($dummyStones);
		$price = Mage::getModel('productbase/priceProcessor')->getProductPrice($dummyProduct, $debug);
		return $price;
	}
	
	/*
	*
	* --- ADVRP Theme ------------
	*
	*/
	
	protected function _setADVRPTheme($product){
		$this->_setADVRPCustomOptions($product);
		//if($product->getCustomDesign() != 'angara/ADVRP' || ($product->getCustomj() == 1)){
			$product->setCustomDesign('angara/ADVRP');
			$product->setCustomj(0);
			$product->setHasOptions(1);
			$product->setAltered(true);
		//}
		# todo apply theme on mage product
	}
	
	protected function _setADVRPCustomOptions($product){
		$options = $product->getProductOptionsCollection();
		
		$this->_setRingSize($product, $options);
		$this->_setADVRPStoneQuality($product, $options);
		$this->_setADVRPMetalType($product, $options);
		if($product->getCustomj() == 1){
			$this->_unsetCustomjOption($product, $options, 'Stone Size');
			$this->_unsetCustomjOption($product, $options, 'size-variation');
			$this->_unsetCustomjOption($product, $options, 'count-variation');
			$this->_unsetCustomjOption($product, $options, 'weight-variation');
			$this->_unsetCustomjOption($product, $options, 'Price Factor');
			$this->_unsetCustomjOption($product, $options, 'VariationInfoToOrder');
			$this->_unsetCustomjOption($product, $options, 'coordinates');
		}
		
		
	}
	
	protected function _setADVRPStoneQuality($product, $options){
		$stoneQualityOptions = $options->getItemByColumnValue('title', "Stone Quality");
		if($stoneQualityOptions){
			if($stoneQualityOptions->getType() !="radio"){
				$stoneQualityOptions->delete();
				$this->_createADVRPStoneQuality($product);
			}
			else{
				$this->_updateADVRPStoneQuality($product, $stoneQualityOptions);
			}
		}else{
			$this->_createADVRPStoneQuality($product);
		}
	}
	
	protected function _createADVRPStoneQuality($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		$metal = Mage::getModel('productbase/metal')->getCollection()->getItemByColumnValue('metal', $product->getAttributeText("metal"));
		
		$values = array();
		$sort_order = 1;
		foreach($stones as $stone){
			if($stone->getIsDefault()){
				foreach($grades as $grade){
					if($grade->getGrade() == 'A') $sort_order = 1;
					elseif($grade->getGrade() == 'AA') $sort_order = 2;
					elseif($grade->getGrade() == 'AAA') $sort_order = 3;
					elseif($grade->getGrade() == 'AAAA') $sort_order = 4;
					
					$values[] = array(
						'title'		=> $grade->getGrade(),
						'price'		=> $this->_getPriceFactor($product, $metal, $stone, $grade),
						'price_type'	=> 'fixed',
						'sku' => 'SQ-'.$grade->getGrade(),
						'sort_order' => $sort_order
					);
				}
			}
		}
		
		$options = 	array(
			'type'		=> 'radio',
			'is_require'	=> 1,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 1
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Stone Quality", $options, $values);
	}
	
	protected function _updateADVRPStoneQuality($product, $stoneQualityOptions){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metal = Mage::getModel('productbase/metal')->getCollection()->getItemByColumnValue('metal', $product->getAttributeText("metal"));
		
		$product->setHasOptions(1);
		$values = $stoneQualityOptions->getValuesCollection();
		$altered = false;
		
		$newValues = array();
		# todo find best sort order by checking available sort orders
		foreach($stones as $stone){
			if($stone->getIsDefault()){
				foreach($grades as $grade){
					
					$value = $values->getItemByColumnValue('title', $grade->getGrade());
					if($value){
						$price = floatval($this->_getPriceFactor($product, $metal, $stone, $grade));
						if($price != $value->getPrice()){
							$value->setPrice($price);
							$altered = true;
						}
						$newValues[]=$value->toArray();
					}else{
						$value = array(
							'title'		=> $grade->getGrade(),
							'price'		=> $this->_getPriceFactor($product, $metal, $stone, $grade),
							'price_type'	=> 'fixed',
							'sku' => 'SQ-'.$grade->getGrade()
						);
						$newValues[] = $value;
						$altered = true;
					}
				}
			}
		}
		
		if($altered){
			$stoneQualityOptions->setStoreId(0);
			$stoneQualityOptions->setData('values',$newValues);
			$stoneQualityOptions->save();
		}
	}
	
	protected function _setADVRPMetalType($product, $options){
		$MetalTypeOptions = $options->getItemByColumnValue('title', "Metal Type");
		if($MetalTypeOptions){
			if($MetalTypeOptions->getType() !="radio"){
				$MetalTypeOptions->delete();
				$this->_createADVRPMetalType($product);
			}
			else{
				$this->_updateADVRPMetalType($product, $MetalTypeOptions);
			}
		}else{
			$this->_createADVRPMetalType($product);
		}
	}
	
	protected function _createADVRPMetalType($product){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metals = $this->_productMetals->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		if($product->getEmbQualityGrade1()){
			$default_grade = $product->getAttributeText('emb_quality_grade1');
		}elseif($product->getEmbQualityGrade2()){
			$default_grade = $product->getAttributeText('emb_quality_grade2');
		}elseif($product->getEmbQualityGrade3()){
			$default_grade = $product->getAttributeText('emb_quality_grade3');
		}
		
		/* { This part need improvement */
		foreach($grades as $tmpGrade){
			//if($tmpGrade->getGrade() == $default_grade)
			
			//standard grade hardcoded for calculating metal offset
			if($tmpGrade->getGrade() == 'AA')
				$grade = $tmpGrade;
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'AAA')
					$grade = $tmpGrade;
			}
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'AAAA')
					$grade = $tmpGrade;
			}
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'A')
					$grade = $tmpGrade;
			}
		}
		/* This part need improvement } */
		
		$values = array();
		$sort_order = 1;
		
		foreach($metals as $metal){
			if($metal->getAlias() == 'WG'){
				$standardMetal = $metal;
			}
		}
		
		foreach($stones as $stone){
			if($stone->getIsDefault()){
				foreach($metals as $metal){
					if($metal->getAlias() == 'PT'){
						$metalAlias = "PL";
					}else{
						$metalAlias = $metal->getAlias();
					}
					if($metal->getAlias() 		== 'SL') $sort_order = 1;
					elseif($metal->getAlias() 	== 'YG') $sort_order = 2;
					elseif($metal->getAlias() 	== 'WG') $sort_order = 3;
					elseif($metal->getAlias() 	== 'RG') $sort_order = 4;
					elseif($metal->getAlias() 	== 'PT') $sort_order = 5;
					
					$values[] = array(
						'title'		=> $metal->getMetal(),
						'price'		=> $this->_getVariationPrice($product, $metal, $stone, $grade) - $this->_getVariationPrice($product, $standardMetal, $stone, $grade),
						'price_type'	=> 'fixed',
						'sku' => 'MT-'.$metalAlias,
						'sort_order' => $sort_order
					);
				}
			}
		}
		
		$options = 	array(
			'type'		=> 'radio',
			'is_require'	=> 1,
			'price'		=> 0,
			'price_type'	=> 'fixed',
			'sort_order' => 2
		  );
		
		Mage::helper('productbase/productbase')->saveCustomOption($product, "Metal Type", $options, $values);
	}
	
	protected function _updateADVRPMetalType($product, $MetalTypeOptions){
		
		$stones = $this->_productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$metals = $this->_productMetals->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		$grades = $this->_productGrades->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		
		if($product->getEmbQualityGrade1()){
			$default_grade = $product->getAttributeText('emb_quality_grade1');
		}elseif($product->getEmbQualityGrade2()){
			$default_grade = $product->getAttributeText('emb_quality_grade2');
		}elseif($product->getEmbQualityGrade3()){
			$default_grade = $product->getAttributeText('emb_quality_grade3');
		}
		
		/* { This part need improvement */
		foreach($grades as $tmpGrade){
			//if($tmpGrade->getGrade() == $default_grade)
			
			//standard grade hardcoded for calculating metal offset
			if($tmpGrade->getGrade() == 'AA')
				$grade = $tmpGrade;
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'AAA')
					$grade = $tmpGrade;
			}
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'AAAA')
					$grade = $tmpGrade;
			}
		}
		
		if(empty($grade)){
			foreach($grades as $tmpGrade){
				if($tmpGrade->getGrade() == 'A')
					$grade = $tmpGrade;
			}
		}
		/* This part need improvement } */
		
		$product->setHasOptions(1);
		$values = $MetalTypeOptions->getValuesCollection();
		
		$altered = false;
		
		$newValues = array();
		
		foreach($metals as $metal){
			if($metal->getAlias() == 'WG'){
				$standardMetal = $metal;
			}
		}
		
		# todo find best sort order by checking available sort orders
		foreach($stones as $stone){
			if($stone->getIsDefault()){
				foreach($metals as $metal){
					if($metal->getAlias() == 'PT'){
						$metalAlias = "PL";
					}else{
						$metalAlias = $metal->getAlias();
					}
					$value = $values->getItemByColumnValue('title', $metal->getMetal());
					if($value){
						$price = floatval($this->_getVariationPrice($product, $metal, $stone, $grade) - $this->_getVariationPrice($product, $standardMetal, $stone, $grade));
						if($price != $value->getPrice()){
							$value->setPrice($price);
							$altered = true;
						}
						$newValues[]=$value->toArray();
					}else{
						$value = array(
							'title'		=> $metal->getMetal(),
							'price'		=> $this->_getVariationPrice($product, $metal, $stone, $grade) - $this->_getVariationPrice($product, $standardMetal, $stone, $grade),
							'price_type'	=> 'fixed',
							'sku' => 'MT-'.$metalAlias
						);
						$newValues[] = $value;
						$altered = true;
					}
				}
			}
		}
		
		if($altered){
			$MetalTypeOptions->setStoreId(0);
			$MetalTypeOptions->setData('values',$newValues);
			$MetalTypeOptions->save();
		}
	}
	
	protected function _unsetCustomjOption($product, $options, $title){
		$CustomOption = $options->getItemByColumnValue('title', $title);
		if($CustomOption){
			$CustomOption->delete();
		}
	}
	
	/*
	*
	* --- default Theme ------------
	*
	*/
	
	protected function _setDefaultTheme($product){
		if($product->getCustomDesign() != ''){
			$product->setCustomDesign('');
			$product->setCustomj(0);
			$product->setAltered(true);
		}
		
		$options = $product->getProductOptionsCollection();
		$this->_setRingSize($product, $options);
		# todo unset ADVRP, customj custom options
	}
}
