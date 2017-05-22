<?php
class Angara_Productbase_Model_Convert_Adapter_Product extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
	protected $_productModel;
	protected $_productGradeModel;
	protected $_productStoneModel;
	protected $_productMetalModel;
	protected $_categoryModel;
	
    public function load()
    {
    }

    public function save()
    {
    }
	
	

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        $productModel = $this->_getProductModel();
		$productGradeModel = $this->_getProductGradeModel();
		$productStoneModel = $this->_getProductStoneModel();
		$productMetalModel = $this->_getProductMetalModel();
		
		$products = $productModel->getCollection();

		$sku = $importData['sku'];
        if (empty($sku)) {
            
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
        	Mage::throwException($message);
        }
        else {
			$mageproduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
			if(!$mageproduct){
				$message = Mage::helper('catalog')->__('Skipping import row, required product "%s" is not found.', $sku);
	        	Mage::throwException($message);
			}
        }
		
		$default = ($importData['is_default']==1)?true:false;
		$product = $products->getItemByColumnValue('sku', $sku);
		if(!$product){
			$product = $productModel;
			$product->setSku($sku);
		}
		$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$currentDate = date('Y-m-d H:i:s', $currentTimestamp);
		$product->setModified($currentDate);
		
		if($default){
			if(!empty($importData['category'])){
				$category = $this->_getCategoryModel()->getCollection()->getItemByColumnValue('title', $importData['category']);
				if(!$category){
					$product->setApproved(0);
					$product->save();
					$message = Mage::helper('catalog')->__('Product '.$sku.' disapproved, required category "%s" is not found.', $importData['category']);
					Mage::throwException($message);
				}
				$product->setAngaraPbCategoryId($category->getId());
			}
			
			$product->setFindingSilver($importData['finding_silver']);
			$product->setFinding_14kgold($importData['finding_14kgold']);
			$product->setFindingPlatinum($importData['finding_platinum']);
			$product->setTheme($importData['theme']);
			$product->setAvgMetalWeight($importData['avg_metal_weight']);
			$product->setCoordinates($importData['coordinates']);
			$product->setApproved(1);
			$product->save();
		}
		
		$grade = $importData['grade'];
		if(!empty($grade)){
			$grades = $productGradeModel->getCollection()->addFieldToFilter('angara_pb_product_id',$product->getId())->addFieldToFilter('grade',$grade);
			if(count($grades)==0){
				$productGradeModel->setAngaraPbProductId($product->getId());
				$productGradeModel->setGrade($grade);
				$productGradeModel->setIsDefault($default);
				$productGradeModel->save();
			}
			else{
				$grades->getFirstItem()->setIsDefault($default)->save();
			}
			
		}
		
		$metal = $importData['metal'];
		if(!empty($metal)){
			$metals = $productMetalModel->getCollection()->addFieldToFilter('angara_pb_product_id',$product->getId())->addFieldToFilter('metal',$metal);
			if(count($metals)==0){
				
				$alias = '';
				if($metal == 'Silver'){ $alias = "SL"; }
				else if($metal == 'White Gold'){ $alias = "WG"; }
				else if($metal == 'Yellow Gold'){ $alias = "YG"; }
				else if($metal == 'Platinum'){ $alias = "PT"; }
				else if($metal == 'Rose Gold'){ $alias = "RG"; }
				
				
				$productMetalModel->setAngaraPbProductId($product->getId());
				$productMetalModel->setMetal($metal);
				$productMetalModel->setAlias($alias);
				$productMetalModel->setIsDefault($default);
				$productMetalModel->save();
			}
			else{
				$metals->getFirstItem()->setIsDefault($default)->save();
			}
		}
		
		if( !empty($importData['emb2_size'])){
			$angaraStones = Mage::getModel('productbase/stone')->getCollection();
			
			$productStones = $productStoneModel->getCollection()->addFieldToFilter('angara_pb_product_id',$product->getId())->addFieldToFilter('center_stone_size',$importData['emb2_size']);
			if(count($productStones)==0){
				$productStone = $productStoneModel;
			}else{
				$productStone = $productStones->getFirstItem();
			}
			
			$productStone->setAngaraPbProductId($product->getId());
			$productStone->setAvgMetalWeightAdjustment($importData['avg_metal_weight'] - $product->getAvgMetalWeight());
			
			$dummyStone = Mage::getModel('productbase/dummyStone');
			
			$dummyStone->setShape($mageproduct->getAttributeText('emb_shape2'));
			$dummyStone->setName($mageproduct->getAttributeText("emb_stone_name2"));
			$dummyStone->setGrade('A');
			
			$stoneSize = explode(', ', $importData['emb2_size']);
			$stoneWeights = array();
			
			for($i = 0; $i < count($stoneSize); $i++){
				$dummyStone->setSize($stoneSize[$i]);
				$angaraStone = $angaraStones->getItemByColumnValue('id', $dummyStone->getAlias());
				if(!$angaraStone){
					$product->setApproved(0);
					$product->save();
					$message = Mage::helper('catalog')->__('Product '.$sku.' disapproved, required stone "%s" is not found.', $dummyStone->getAlias());
					Mage::throwException($message);
				}
				
				$stoneWeights[] = $angaraStone->getWeight();
			}
			
			$productStone->setCenterStoneSize($importData['emb2_size']);
			$productStone->setCenterStoneCount($importData['emb2_count']);
			$productStone->setCenterStoneWeight(implode(', ', $stoneWeights));
			
			if( !empty($importData['emb1_size'])){
				$dummyStone->setShape($mageproduct->getAttributeText('emb_shape1'));
				$dummyStone->setName($mageproduct->getAttributeText("emb_stone_name"));
				$dummyStone->setGrade('A');
				
				$stoneSize = explode(', ', $importData['emb1_size']);
				$stoneWeights = array();
				
				for($i = 0; $i < count($stoneSize); $i++){
					$dummyStone->setSize($stoneSize[$i]);
					$angaraStone = $angaraStones->getItemByColumnValue('id', $dummyStone->getAlias());
					if(!$angaraStone){
						$product->setApproved(0);
						$product->save();
						$message = Mage::helper('catalog')->__('Product '.$sku.' disapproved, required stone "%s" is not found.', $dummyStone->getAlias());
						Mage::throwException($message);
					}
					$stoneWeights[] = $angaraStone->getWeight();
				}
				
				
				$productStone->setStone1Size($importData['emb1_size']);
				$productStone->setStone1Count($importData['emb1_count']);
				$productStone->setStone1Weight(implode(', ', $stoneWeights));
			}else{
				$productStone->setStone1Size('');
				$productStone->setStone1Count('');
				$productStone->setStone1Weight('');
			}
			
			if( !empty($importData['emb3_size'])){
				$dummyStone->setShape($mageproduct->getAttributeText('emb_shape3'));
				$dummyStone->setName($mageproduct->getAttributeText("emb_stone_name3"));
				$dummyStone->setGrade('A');
				
				
				$stoneSize = explode(', ', $importData['emb3_size']);
				$stoneWeights = array();
				
				
				for($i = 0; $i < count($stoneSize); $i++){
					$dummyStone->setSize($stoneSize[$i]);
					$angaraStone = $angaraStones->getItemByColumnValue('id', $dummyStone->getAlias());
					if(!$angaraStone){
						$product->setApproved(0);
						$product->save();
						$message = Mage::helper('catalog')->__('Product '.$sku.' disapproved, required stone "%s" is not found.', $dummyStone->getAlias());
						Mage::throwException($message);
					}
					
					$stoneWeights[] = $angaraStone->getWeight();
				}
				
				$productStone->setStone3Size($importData['emb3_size']);
				$productStone->setStone3Count($importData['emb3_count']);
				$productStone->setStone3Weight(implode(', ', $stoneWeights));
			}else{
				$productStone->setStone3Size('');
				$productStone->setStone3Count('');
				$productStone->setStone3Weight('');
			}
			
			
			$productStone->setIsDefault($default);
			$productStone->save();
			
		}
        return true;
    }
	
	protected function _getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('productbase/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }
	
	protected function _getCategoryModel(){
		if (is_null($this->_categoryModel)) {
            $model = Mage::getModel('productbase/category');
            $this->_categoryModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_categoryModel);
	}
	
	protected function _getProductGradeModel()
    {
        if (is_null($this->_productGradeModel)) {
            $model = Mage::getModel('productbase/productGrade');
            $this->_productGradeModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_productGradeModel);
    }
	
	protected function _getProductStoneModel()
    {
        if (is_null($this->_productStoneModel)) {
            $model = Mage::getModel('productbase/productStone');
            $this->_productStoneModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_productStoneModel);
    }
	
	protected function _getProductMetalModel()
    {
        if (is_null($this->_productMetalModel)) {
            $model = Mage::getModel('productbase/productMetal');
            $this->_productMetalModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_productMetalModel);
    }
}
