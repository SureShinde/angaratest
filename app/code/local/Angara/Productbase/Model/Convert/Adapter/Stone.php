<?php
class Angara_Productbase_Model_Convert_Adapter_Stone extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
	protected $_stoneNameModel;
	protected $_stonePriceModel;
	protected $_stoneShapeModel;
	protected $_stoneWeightModel;
	
	protected function _getStoneNameModel(){
		if (is_null($this->_stoneNameModel)) {
            $model = Mage::getModel('productbase/stone_name');
            $this->_stoneNameModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_stoneNameModel);
	}
	
	protected function _getStonePriceModel(){
		if (is_null($this->_stonePriceModel)) {
            $model = Mage::getModel('productbase/stone_price');
            $this->_stonePriceModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_stonePriceModel);
	}
	
	protected function _getStoneShapeModel(){
		if (is_null($this->_stoneShapeModel)) {
            $model = Mage::getModel('productbase/stone_shape');
            $this->_stoneShapeModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_stoneShapeModel);
	}
	
	protected function _getStoneWeightModel(){
		if (is_null($this->_stoneWeightModel)) {
            $model = Mage::getModel('productbase/stone_weight');
            $this->_stoneWeightModel = Mage::objects()->save($model);
        }
        return Mage::objects()->load($this->_stoneWeightModel);
	}
	
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
		
		if(empty($importData['stone'])){
			$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'stone');
        	Mage::throwException($message);
		}
		
		if(empty($importData['grade'])){
			$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'grade');
        	Mage::throwException($message);
		}
		
		if(empty($importData['shape'])){
			$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'shape');
        	Mage::throwException($message);
		}
		
		if(empty($importData['size'])){
			$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'size');
        	Mage::throwException($message);
		}
		
		if(empty($importData['price_per_carat'])){
			$message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'price_per_carat');
        	Mage::throwException($message);
		}
		
		$stones = $this->_getStoneNameModel()->getCollection()->addFieldToFilter('title', $importData['stone']);
		if(count($stones)==0){
			$message = Mage::helper('catalog')->__('Skipping import row, required stone "%s" is not found in system.', $importData['stone']);
        	Mage::throwException($message);
		}else{
			$stone = $stones->getFirstItem();
		}
		
		$shapes = $this->_getStoneShapeModel()->getCollection()->addFieldToFilter('shape', $importData['shape']);
		if(count($shapes)==0){
			$message = Mage::helper('catalog')->__('Skipping import row, required shape "%s" is not found in system.', $importData['shape']);
        	Mage::throwException($message);
		}else{
			$shape = $shapes->getFirstItem();
		}
		
		$weights = $this->_getStoneWeightModel()->getCollection()
			->addFieldToFilter('stone_id', $stone->getId())
			->addFieldToFilter('shape_id', $shape->getId())
			->addFieldToFilter('stone_size', $importData['size']);
			
		$weight = $weights->getFirstItem();
		
		if(count($weights)==0){
			if(empty($importData['weight'])){
				$message = Mage::helper('catalog')->__('Skipping import row, stone weight for '.$importData['shape'].$importData['stone'].'-'.$importData['grade'].'-'.$importData['size'].' is not present in system or import row.');
				Mage::throwException($message);
			}
			else{
				if(empty($importData['weight_constant'])){
					$message = Mage::helper('catalog')->__('Skipping import row, weight_constant is not defined.');
					Mage::throwException($message);
				}
				else{
					$weight->setStoneId($stone->getId());
					$weight->setShapeId($shape->getId());
					$weight->setStoneSize($importData['size']);
					$weight->setWeight($importData['weight']);
					$weight->setWeightConstant($importData['weight_constant']);
					$weight->save();
				}
			}
		}
		else{
			if(!empty($importData['weight'])){
				if(empty($importData['weight_constant'])){
					$message = Mage::helper('catalog')->__('Skipping import row, weight_constant is not defined.');
					Mage::throwException($message);
				}
				$weight->setWeight($importData['weight']);
				$weight->setWeightConstant($importData['weight_constant']);
				$weight->save();
			}
		}

		$prices = $this->_getStonePriceModel()->getCollection()
			->addFieldToFilter('stone_id', $stone->getId())
			->addFieldToFilter('stone_grade', $importData['grade'])
			->addFieldToFilter('shape_id', $shape->getId())
			->addFieldToFilter('stone_size', $importData['size']);
			
			
		$price = $prices->getFirstItem();
		if(count($prices)==0){
			$price->setStoneId($stone->getId());
			$price->setStoneGrade($importData['grade']);
			$price->setShapeId($shape->getId());
			$price->setStoneSize($importData['size']);
			$price->setPricePerCarat($importData['price_per_carat']);
			$price->save();
		}
		else{
			$price->setPricePerCarat($importData['price_per_carat']);
			$price->save();
		}
        return true;
    }
	
	
}
