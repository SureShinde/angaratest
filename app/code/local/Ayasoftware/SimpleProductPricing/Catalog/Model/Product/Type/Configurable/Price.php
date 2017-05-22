<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Price
{

	/**
	 * Check if string s1 contains string s2
	 * @param $s1
	 * @param $s2
	 * @return boolean: true is yes
	 */
	protected function strContains ($s1, $s2)
	{
		$pos = strpos(strtolower($s1), strtolower($s2));
		if ($pos !== false) {
			return true;
		}
		return false;
	}
	/**
	 * Get product final price
	 *
	 * @param   double $qty
	 * @param   Mage_Catalog_Model_Product $product
	 * @return  double
	 */
	public function getTrioProducts($trio1_all=null,$trio2_all=null,$trio3_all=null,$metal=null,$stone=null)
	{
		
					$filtered_simple_trio=array();
                    
					foreach($trio1_all as $simple)
					{
						if($simple['metal1_type']==$metal && $simple['stone1_grade']==$stone) 
						{
                            $filtered_simple_trio[]=$simple->getSku();
                            break;
						}
					}
					
					foreach($trio2_all as $simple)
					{
						if($simple['metal1_type']==$metal && $simple['stone1_grade']==$stone) 
						{
                            $filtered_simple_trio[]=$simple->getSku();
                            break;
						}
					}
					
					foreach($trio3_all as $simple)
					{
						if($simple['metal1_type']==$metal && $simple['stone1_grade']==$stone) 
						{
                            $filtered_simple_trio[]=$simple->getSku();
                            break;
						}
					}
					
					return $filtered_simple_trio;

	}

	public function getFinalPrice ($qty = null, $product)
	{
		$session 				= 	Mage::getSingleton('checkout/session');
		$currentUrl 			= 	Mage::helper('core/url')->getCurrentUrl();
		$currentUrlAjaxRequest 	= 	Mage::app()->getRequest()->getServer('HTTP_REFERER');		//	S:VA	Getting current url for ajax request
		$tierPrice = 0;
		$simplePrice = 0;

		if ($this->strContains($currentUrl, "promotions/") || $this->strContains($currentUrl, "incontext/") || $this->strContains($currentUrl, "amazon_payments/") || $this->strContains($currentUrl, "onepagecheckout/") || $this->strContains($currentUrlAjaxRequest, "onepagecheckout/") || $this->strContains($currentUrl, "fancycart/") || $this->strContains($currentUrl, "onestepcheckout/") || $this->strContains($currentUrl, "checkout/cart") || $this->strContains($currentUrl, "checkout/onepage") || $this->strContains($currentUrl, "paypal/express") || $this->strContains($currentUrl, "checkout/multishipping/") ) {

			if (is_null($qty) && ! is_null($product->getCalculatedFinalPrice())) {
				return $product->getCalculatedFinalPrice();
			}
			$catalog = Mage::getModel('catalog/product'); 
			$_config = $catalog->load($product->getId());
			if ($_config->getUseStandardConfig()) {
				$simplePrice =  parent::getFinalPrice($qty, $product);
			}
			
           $custom_array_temp	=$product->getTypeInstance(true)->getOrderOptions($product);
		   $custom_array= $custom_array_temp['options'];

           $flag=0;
           foreach($custom_array as $o)
           {
           	if($o['label']=='Trio1 Sku')
           		{
           			$trio1_simple_sku=$o['value'];
           			$flag=1;
           		}
           	else if($o['label']=='Trio2 Sku')
           		$trio2_simple_sku=$o['value'];
           	else if($o['label']=='Trio3 Sku')
           		$trio3_simple_sku=$o['value'];
           	
           }
            if($flag)
            {
				$trio1_simple = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('price','special_price')->addAttributeToFilter('sku',$trio1_simple_sku)->getFirstItem();
				
				$trio2_simple = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('price','special_price')->addAttributeToFilter('sku',$trio2_simple_sku)->getFirstItem();

				$trio3_simple = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('price','special_price')->addAttributeToFilter('sku',$trio3_simple_sku)->getFirstItem();
                
                $productOptions = $product->getTypeInstance(true)->getOrderOptions($product);
			    $simple =  $catalog->load($product->getIdBySku($productOptions['simple_sku']));

			    
			    $simplePrice	= $simple->getFinalPrice()+$trio1_simple->getFinalPrice()+$trio2_simple->getFinalPrice()+$trio3_simple->getFinalPrice();

			    if($simple->getGroupPrice())
				{
					$simplePrice = $simple->getGroupPrice()+$trio1_simple->getGroupPrice()+$trio2_simple->getGroupPrice()+$trio3_simple->getGroupPrice();
				}
				if($simple->getSpecialPrice()) 
				{
					$simplePrice =  $simple->getSpecialPrice()+$trio1_simple->getSpecialPrice()+$trio2_simple->getSpecialPrice()+$trio2_simple->getSpecialPrice();
			    }
            }
            else
            {
            	$productOptions = $product->getTypeInstance(true)->getOrderOptions($product);
			    $simple =  $catalog->load($product->getIdBySku($productOptions['simple_sku']));
			
			    $simplePrice	= $simple->getFinalPrice();			

				if($simple->getGroupPrice())
				{
					$simplePrice = $simple->getGroupPrice();
				}
				/*if ($this->canApplyTierPrice($simple, $qty)) {
					$simplePrice  = $simple->getTierPrice($qty);
				}*/
			
				if($simple->getSpecialPrice()) 
				{
					$simplePrice =  $simple->getSpecialPrice();
				}
            }
           

			
		/*	
			// BOF super attributes configuration
		$finalPrice = parent::getFinalPrice($qty, $product);
		$beforeSelections =  parent::getFinalPrice($qty, $product);
		$product->getTypeInstance(true)->setStoreFilter($product->getStore(), $product);
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
		$selectedAttributes = array();
		if ($product->getCustomOption('attributes')) {
			$selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
		}
		$basePrice = $simplePrice;
		foreach ($attributes as $attribute) {
			$attributeId = $attribute->getProductAttribute()->getId();
			$value = $this->_getValueByIndex($attribute->getPrices() ? $attribute->getPrices() : array(), isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null);
			if ($value) {
				if ($value['pricing_value'] != 0) {
					$finalPrice += $this->_calcSelectionPrice($value, $basePrice);
				}
			}
		}
		$super_attributes_price =  $finalPrice - $beforeSelections;	
		// EOF super attributes configuration
		
		 */
			if($this->applyRulesToProduct($simple)){
				$rulePrice = $this->applyRulesToProduct($simple);
				if($this->applyOptionsPrice($product,$simplePrice)){
					$rulePrice = $this->applyOptionsPrice($product,$rulePrice);
					$simplePrice = $this->applyOptionsPrice($product,$simplePrice);
				}
			 	$product->setFinalPrice(min($simplePrice,$rulePrice) );
			 	return min($simplePrice,$rulePrice);
			} else {
				if($this->applyOptionsPrice($product,$simplePrice)){
				  $simplePrice = $this->applyOptionsPrice($product,$simplePrice);
				}
			 	$product->setFinalPrice($simplePrice);
			 	return $simplePrice;
			}
		} else {
			return $this->getDefaultPrice($qty = null, $product);
		}
	}
	// Use Magento Default getFinalPrice()
	
	public function getDefaultPrice ($qty = null, $product){
			
		if (is_null($qty) && ! is_null($product->getCalculatedFinalPrice())) {
			return $product->getCalculatedFinalPrice();
		}
		$finalPrice = parent::getFinalPrice($qty, $product);
		$product->getTypeInstance(true)->setStoreFilter($product->getStore(), $product);
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
		$selectedAttributes = array();
		if ($product->getCustomOption('attributes')) {
			$selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
		}
		$basePrice = $finalPrice;
		foreach ($attributes as $attribute) {
			$attributeId = $attribute->getProductAttribute()->getId();
			$value = $this->_getValueByIndex($attribute->getPrices() ? $attribute->getPrices() : array(), isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null);
			if ($value) {
				if ($value['pricing_value'] != 0) {
					$finalPrice += $this->_calcSelectionPrice($value, $basePrice);
				}
			}
		}
		$product->setFinalPrice($finalPrice);
		return max(0, $product->getData('final_price'));
	}

	protected function _calcSelectionPrice ($priceInfo, $productPrice)
	{
		if ($priceInfo['is_percent']) {
			$ratio = $priceInfo['pricing_value'] / 100;
			$price = $productPrice * $ratio;
		} else {
			$price = $priceInfo['pricing_value'];
		}
		return $price;
	}
	protected function _getValueByIndex ($values, $index)
	{
		foreach ($values as $value) {
			if ($value['value_index'] == $index) {
				return $value;
			}
		}
		return false;
	}

	protected function applyOptionsPrice($product, $finalPrice)
	{
		if ($optionIds = $product->getCustomOption('option_ids')) {
			$basePrice = $finalPrice;
			foreach (explode(',', $optionIds->getValue()) as $optionId) {
				if ($option = $product->getOptionById($optionId)) {

					$confItemOption = $product->getCustomOption('option_'.$option->getId());
					$group = $option->groupFactory($option->getType())
					->setOption($option)
					->setConfigurationItemOption($confItemOption);

					$finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
				}
			}
		}
		return $finalPrice;
	}
	protected function canApplyTierPrice($product, $qty){
		$tierPrice  = $product->getTierPrice($qty);
		$price = $product->getPrice();
		if ($tierPrice != $price ){
			return true;
		} else {
			return false;
		}
	}
	/**
	 *
	 * Apply Catalog Rules...
	 * @param  int|Mage_Catalog_Model_Product $product
	 */

	public function applyRulesToProduct($product)
	{
		$rule = Mage::getModel("catalogrule/rule");
		return $rule->calcProductPriceRule($product,$product->getPrice());
	}
}
