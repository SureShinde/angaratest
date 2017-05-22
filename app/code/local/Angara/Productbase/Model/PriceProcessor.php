<?php
class Angara_Productbase_Model_PriceProcessor extends Mage_Core_Model_Abstract
{
	protected $_metals;
	protected $_stones;
	protected $_settings;
	protected $_categories;
	protected $_ringAttributeSetId;
	protected $_storeId = 0;  # todo get store id without hardcoding
	
	protected $_rhodium_cost;
	protected $_packing;
	protected $_ring_resizing;
	protected $_shipping;
	protected $_debug;
	/*
     * Class constructor
     */
    public function _construct()
    {
        $this->_metals = Mage::getModel('productbase/metal')->getCollection();
		$this->_stones = Mage::getModel('productbase/stone')->getCollection();
		$this->_settings = Mage::getModel('productbase/settingtype')->getCollection();
		$this->_categories = Mage::getModel('productbase/category')->getCollection();
		
		$this->_ringAttributeSetId = Mage::getModel("eav/entity_attribute_set")->getCollection()->getItemByColumnValue('attribute_set_name','Rings')->getAttributeSetId();
		
		$this->_rhodium_cost = Mage::getStoreConfig('productbase/general/rhodiumcost');
		$this->_packing = Mage::getStoreConfig('productbase/general/packingcost');
		$this->_ring_resizing = Mage::getStoreConfig('productbase/general/ringresizingcost');
		$this->_shipping = Mage::getStoreConfig('productbase/general/shippingcost');
		
		if(empty($this->_rhodium_cost))
			$this->_rhodium_cost = 0;
		if(empty($this->_packing))
			$this->_packing = 0;
		if(empty($this->_ring_resizing))
			$this->_ring_resizing = 0;
		if(empty($this->_shipping))
			$this->_shipping = 0;
			
		$this->_debug = false;
		
    }
	
	
	protected function debugReport($msg, $product){
		if($this->_debug){
			echo '<tr><td>'.$product->getSku().'</td> <td align="right">'.$msg.'</td></tr>';
		}
	}
	
	protected function _getStoneCost($stone, $product){
		$angaraStone = $this->_stones->getItemByColumnValue('id',$stone->getAlias());
		if($angaraStone){
			$this->debugReport('Stone '.$stone->getAlias()." Cost is ".$angaraStone->getCost(), $product);
			return $angaraStone->getCost();
		}
		else{
			$this->debugReport('Stone '.$stone->getAlias().' doesn\'t exist.', $product);
			Mage::throwException(Mage::helper('adminhtml')->__($product->getSku().': Stone '.$stone->getAlias().' doesn\'t exist.'));
			//echo $product->getSku().': Stone '.$stone->getAlias().' doesn\'t exist.'."\n";
		}
	}
	
	protected function _getSettingCost($stone, $product){
		$setting = $this->_settings->getItemByColumnValue('id',$stone->getSettingAlias());
		if($setting){
			$this->debugReport($stone->getAlias()." Setting ".$stone->getSettingAlias(). " Cost is ".$setting->getSettingCost(), $product);
			return $setting->getSettingCost();
		}
		else{
			Mage::throwException(Mage::helper('adminhtml')->__($product->getSku().': Setting '.$stone->getSettingAlias().' doesn\'t exist.'));
			//echo $product->getSku().': Setting '.$stone->getSettingAlias().' doesn\'t exist.'."\n";
		}
	}
	
	protected function _getStonePrice ($stone, $product){
		$stone_price = ($this->_getStoneCost($stone, $product) + $this->_getSettingCost($stone, $product)) * $stone->getCount();
		$this->debugReport('Stone '.$stone->getAlias().'('.$stone->getCount().') Price is '.$stone_price, $product);
		return $stone_price;
	}
	
	protected function _getProductStonesPrice ($product){		
		$stones = $product->getStones();
		$stones_price = 0;
		foreach($stones as $stone){
			$price = $this->_getStonePrice($stone, $product);
			if($price>0){
				$stones_price += $price;
			}
			else{
				$this->debugReport('Invalid stone price: '.$price." Stone: ".$stone->getAlias()." Count: ".$stone->getCount(), $product);
				Mage::throwException(Mage::helper('adminhtml')->__($product->getSku().': Invalid stone price: '.$price." Stone: ".$stone->getAlias()." Count: ".$stone->getCount()));
				//echo $product->getSku().': Invalid stone price: '.$price." Stone: ".$stone->getAlias()." Count: ".$stone->getCount()."\n";
			}
			
		}
		$this->debugReport('Total Stone Price is '.$stones_price, $product);
		return $stones_price;
	}
	
	protected function _getProductMetalPrice ($product){
		$metal = $this->_metals->getItemByColumnValue('metal',$product->getMetal());
		if($metal){
			$metal_cost = $metal->getMetalCost($product->getAvgMetalWeight());
			if($metal_cost > 0){
				$this->debugReport('Metal '.$product->getMetal()." Price is ".$metal_cost, $product);
				return $metal_cost;
			}
			else{
				$this->debugReport('Invalid Metal Price: '.$metal_cost.' Metal:'.$product->getMetal().' ('.$product->getAvgMetalWeight().' g)', $product);
				Mage::throwException(Mage::helper('adminhtml')->__($product->getSku().': Invalid Metal Price: '.$metal_cost.' Metal:'.$product->getMetal().' ('.$product->getAvgMetalWeight().' g)'));
				//echo $product->getSku().': Invalid Metal Price: '.$metal_cost.' Metal:'.$product->getMetal().' ('.$product->getAvgMetalWeight().' g)'."\n";
			}
		}
		else{
			$this->debugReport('Metal '.$product->getMetal().' doesn\'t exist.', $product);
			Mage::throwException(Mage::helper('adminhtml')->__('Metal '.$product->getMetal().' doesn\'t exist.'));
		}
	}
	
	protected function _getFindingCost($product){
		switch( $product->getMetal() ) {
			case 'Silver': 
				$finding_cost = $product->getFindingSilver();
				break;
			case '14k White Gold': 
			case '14k Yellow Gold': 
			case '14k Rose Gold': 
				$finding_cost = $product->getfinding_14kgold();
				break;
			case 'Platinum': 
				$finding_cost = $product->getFindingPlatinum();
				break;	
		}
		
		if($finding_cost){
			$this->debugReport('Finding Cost: '.$finding_cost, $product);
			return $finding_cost;
		}
		else{
			return 0;
		}
	}
	
	public function getProductPrice ($product, $debug = false){
		
		$this->_debug = $debug;
		if($debug){
			echo '<br><table cellpadding="5" border="1" style="font-family: Lucida Console">';
		}
		
		$ringResizing = 0;
		$category = $this->_categories->getItemByColumnValue('id',$product->getAngaraPbCategoryId());
		if(!$category){
			$this->debugReport('Angara Category '.$product->getAngaraPbCategoryId().' doesn\'t exist.', $product);
			Mage::throwException(Mage::helper('adminhtml')->__('Angara Category '.$product->getAngaraPbCategoryId().' doesn\'t exist.'));
		}
		$total_stone_cost = $this->_getProductStonesPrice($product);
		
		$metal_price = $this->_getProductMetalPrice($product);
		$product_cost = $total_stone_cost + $metal_price + $this->_rhodium_cost;
		$finding = $this->_getFindingCost($product);
		// check if ring
		if($product->getAttributeSetId() == $this->_ringAttributeSetId){
			$ringResizing = $this->_ring_resizing;
		}
		
		
		// Hard coding qualitywise markup for sapphire products 
		/*if(strpos( $category->getTitle(),'Sapphire') !== false){
			if($product->getGrade() == 'AA' || $product->getGrade() == 'AAA'){
				$category->setMargin(.4651);
			}
			else if($product->getGrade() == 'AAAA'){
				$category->setMargin(.4761);
			}
		}*/
		// Hard coding ends 
		
		$total_angara_cost = $this->_shipping + $this->_packing + $finding + $ringResizing + $product_cost;
		$this->debugReport('Angara Product Cost is '.$total_angara_cost, $product);
		$calculated_sale_price = $total_angara_cost / $category->getMargin();
		$angara_price = (ceil(($calculated_sale_price)/10) * 10 ) - 0.01;
		$this->debugReport('Angara Product Sell Price is '.$angara_price, $product);
		if($debug){
			echo "</table>";
		}
		return $angara_price;
	}
	
	public function getRetailProductPrice($product){
		$category = $this->_categories->getItemByColumnValue('id',$product->getAngaraPbCategoryId());
		if(!$category){
			Mage::throwException(Mage::helper('adminhtml')->__('Angara Category '.$product->getAngaraPbCategoryId().' doesn\'t exist.'));
		}
		$retail_price = $product->getPrice() * (1 + $category->getRetailPercent());
		$retail_price = (ceil(($retail_price)/10) * 10 ) - 0.01;
		//$this->debugReport('Product Retail Price is '.$retail_price, $product);
		return $retail_price;
	}

}
