<?php
class Angara_Productbase_Model_Products extends Mage_Core_Model_Abstract
{
	
	protected $_errors;
	
	public function getErrors(){
		return $this->_errors;
	}
	
	public function getProducts(){
		$mageproducts = array();
		$products = Mage::getModel('productbase/product')->getCollection();
		foreach($products as $product){
			if($product->getApproved()){
				$mageproduct = $this->_convertMageProduct($product);
				if($mageproduct){
					$mageproducts[] = $mageproduct;
				}
			}
		}
		
		return $mageproducts;
	}
	
	public function resetProduct($angaraproduct, $debug = false){
		$product = $this->_convertAngaraProduct($angaraproduct);
		if($product){
			$price = $this->_getDefaultPrice($product, $debug);
			if($product->getPrice() != $price){
				$product->setPrice($price);
				$product->setAltered(true);
			}
			$retailPrice = Mage::getModel('productbase/priceProcessor')->getRetailProductPrice($product);
			if($product->getRetailPrice() != $retailPrice){
				$product->setRetailPrice($retailPrice);
				$product->setAltered(true);
			}
			Mage::getModel('productbase/themeProcessor')->resetProduct($product, $debug);			
		}
	}
	
	private function _convertAngaraProduct($mageproduct){
		$product = Mage::getModel('productbase/product')->getCollection()->getItemByColumnValue('sku',$mageproduct->getSku());
		# throw exception if Product Sku doesn't exist in master collection.
		if($product->getApproved()){
			$mageproduct->setAvgMetalWeight($product->getAvgMetalWeight());
			$mageproduct->setfinding_14kgold($product->getfinding_14kgold());
			$mageproduct->setFindingSilver($product->getFindingSilver());
			$mageproduct->setFindingPlatinum($product->getFindingPlatinum());
			$mageproduct->setTheme($product->getTheme());
			$mageproduct->setAngaraProductId($product->getId());
			$mageproduct->setAngaraPbCategoryId($product->getAngaraPbCategoryId());
			$mageproduct->setCoordinates($product->getCoordinates());
			$mageproduct->setAltered(false);
			return $mageproduct;
		}
		else{
			$this->_errors[] = (Mage::helper('adminhtml')->__('Product '.$product->getSku().' is not approved for pricing.'));
			return false;
		}
	}
	
	private function _convertMageProduct($product){
		$mageproduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$product->getSku());
		# throw exception if Product Sku doesn't exist in master collection.
		if($mageproduct){
			$mageproduct->setAvgMetalWeight($product->getAvgMetalWeight());
			$mageproduct->setfinding_14kgold($product->getfinding_14kgold());
			$mageproduct->setFindingSilver($product->getFindingSilver());
			$mageproduct->setFindingPlatinum($product->getFindingPlatinum());
			$mageproduct->setTheme($product->getTheme());
			$mageproduct->setAngaraProductId($product->getId());
			$mageproduct->setAngaraPbCategoryId($product->getAngaraPbCategoryId());
			$mageproduct->setCoordinates($product->getCoordinates());
			return $mageproduct;
		}
		else{
			$this->_errors[] = (Mage::helper('adminhtml')->__('Product '.$product->getSku().' doesn\'t exist.'));
			return false;
		}
	}
	
	public function deleteProduct($sku){
		
		$ang_product = Mage::getModel('productbase/product')->getCollection()->getItemByColumnValue('sku',$sku);
		if($ang_product){
			$ang_product->delete();
		}
		else{
			Mage::throwException(Mage::helper('adminhtml')->__('Product data doesn\'t exist.'));
		}
	}
	
	public function save($data){
		$ang_product = Mage::getModel('productbase/product')->getCollection()->getItemByColumnValue('sku',$data['sku']);
		if($ang_product){
			$ang_product->setAvgMetalWeight($data['avg_metal_weight']);
			$ang_product->setfinding_14kgold($data['finding_14kgold']);
			$ang_product->save();
		}
		else{
			$ang_product = Mage::getModel('productbase/product');
			# todo check if sku exists in master collection
			$ang_product->setSku($data['sku']);
			$ang_product->setAvgMetalWeight($data['avg_metal_weight']);
			$ang_product->setfinding_14kgold($data['finding_14kgold']);
			$ang_product->save();
		}
	}
	
	public function resetPricing($debug = false){
		$products = $this->getProducts();
		foreach($products as $product){
			try{
				
				$price = $this->_getDefaultPrice($product, $debug);
				if($price > 0){
					if($product->getPrice() != $price){
						$product->setPrice($price);
						$product->setAltered(true);
					}
					$retailPrice = Mage::getModel('productbase/priceProcessor')->getRetailProductPrice($product);
					if($product->getRetailPrice() != $retailPrice){
						$product->setRetailPrice($retailPrice);
						$product->setAltered(true);
					}
				
					Mage::getModel('productbase/themeProcessor')->resetProduct($product, $debug);
				}
				else{
					Mage::getModel('catalog/product_status')->updateProductStatus($product->getId(), $storeId, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
					$this->_errors[] = "Product ".$product->getSku()." is disabled.";
				}
			}
			catch (Exception $e){
				$this->_errors[] = $e->getMessage();
			}
		}
		return $this->_errors;
	}
	
	private function _getDefaultPrice($product, $debug = false){
		
		if($product->getEmbQualityGrade1()){
			$default_grade = $product->getAttributeText('emb_quality_grade1');
		}elseif($product->getEmbQualityGrade2()){
			$default_grade = $product->getAttributeText('emb_quality_grade2');
		}elseif($product->getEmbQualityGrade3()){
			$default_grade = $product->getAttributeText('emb_quality_grade3');
		}
		
		$dummyProduct = Mage::getModel('productbase/dummyProduct');
		$dummyProduct->setSku($product->getSku());
		$dummyProduct->setMetal($product->getAttributeText("metal"));
		$dummyProduct->setAvgMetalWeight($product->getAvgMetalWeight());
		$dummyProduct->setfinding_14kgold($product->getfinding_14kgold());
		$dummyProduct->setFindingSilver($product->getFindingSilver());
		$dummyProduct->setFindingPlatinum($product->getFindingPlatinum());
		$dummyProduct->setAttributeSetId($product->getAttributeSetId());
		$dummyProduct->setAngaraPbCategoryId($product->getAngaraPbCategoryId());
		$dummyProduct->setGrade($default_grade);
		
		$dummyStones = array();
		
		$productStones = Mage::getModel('productbase/productStone')->getCollection();
		$stones = $productStones->getItemsByColumnValue("angara_pb_product_id",$product->getAngaraProductId());
		foreach($stones as $tmpStone){
			if($tmpStone->getIsDefault()){
				$stone = $tmpStone;
				break;
			}
		}
		
		$stoneCount = explode(', ', $stone->getCenterStoneCount());
		$stoneSize = explode(', ', $stone->getCenterStoneSize());
		$stoneSetting = explode(', ', $product->getAttributeText('emb_setting_type2'));
		
		for($i = 0; $i < count($stoneCount); $i++){
			$dummyStone2 = Mage::getModel('productbase/dummyStone');
			$dummyStone2->setShape($product->getAttributeText('emb_shape2'));
			$dummyStone2->setName($product->getAttributeText("emb_stone_name2"));
			$dummyStone2->setGrade($default_grade);
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
				$dummyStone1->setGrade($default_grade);
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
				$dummyStone3->setGrade($default_grade);
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
}
