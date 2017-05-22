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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Price
{
    const CACHE_TAG = 'PRODUCT_PRICE';

    static $attributeCache = array();

    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
        return $product->getData('price');
    }
	
	/**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $price = (float)$product->getPrice();
        return min($this->_applyGroupPrice($product, $price), $this->_applyTierPrice($product, $qty, $price),
            $this->_applySpecialPrice($product, $price)
        );
    }

    /**
     * Retrieve product final price
     *
     * @param float|null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty = null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);

        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }

    public function getChildFinalPrice($product, $productQty, $childProduct, $childProductQty)
    {
        return $this->getFinalPrice($childProductQty, $childProduct);
    }
	
	/**
     * Apply group price for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    protected function _applyGroupPrice($product, $finalPrice)
    {
        $groupPrice = $product->getGroupPrice();
        if (is_numeric($groupPrice)) {
            $finalPrice = min($finalPrice, $groupPrice);
        }
        return $finalPrice;
    }

    /**
     * Get product group price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getGroupPrice($product)
    {

        $groupPrices = $product->getData('group_price');

        if (is_null($groupPrices)) {
            $attribute = $product->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $groupPrices = $product->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return $product->getPrice();
        }

        $customerGroup = $this->_getCustomerGroupId($product);

        $matchedPrice = $product->getPrice();
        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup && $groupPrice['website_price'] < $matchedPrice) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }

        return $matchedPrice;
    }
	
    /**
     * Apply tier price for product if not return price that was before
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   float $qty
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice  = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }

    /**
     * Get product tier price by qty
     *
     * @param   float $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  float
     */
    public function getTierPrice($qty = null, $product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');

        if (is_null($prices)) {
            $attribute = $product->getResource()->getAttribute('tier_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
                return $product->getPrice();
            }
            return array(array(
                'price'         => $product->getPrice(),
                'website_price' => $product->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = $product->getPrice();
            $prevGroup = $allGroups;

            foreach ($prices as $price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                if ($price['website_price'] < $prevPrice) {
                    $prevPrice  = $price['website_price'];
                    $prevQty    = $price['price_qty'];
                    $prevGroup  = $price['cust_group'];
                }
            }
            return $prevPrice;
        } else {
            $qtyCache = array();
            foreach ($prices as $i => $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups) {
                    unset($prices[$i]);
                } else if (isset($qtyCache[$price['price_qty']])) {
                    $j = $qtyCache[$price['price_qty']];
                    if ($prices[$j]['website_price'] > $price['website_price']) {
                        unset($prices[$j]);
                        $qtyCache[$price['price_qty']] = $i;
                    } else {
                        unset($prices[$i]);
                    }
                } else {
                    $qtyCache[$price['price_qty']] = $i;
                }
            }
        }

        return ($prices) ? $prices : array();
    }

    protected function _getCustomerGroupId($product)
    {
        if ($product->getCustomerGroupId()) {
            return $product->getCustomerGroupId();
        }
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Apply special price for product if not return price that was before
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applySpecialPrice($product, $finalPrice)
    {
        return $this->calculateSpecialPrice($finalPrice, $product->getSpecialPrice(), $product->getSpecialFromDate(),
                        $product->getSpecialToDate(), $product->getStore()
        );
    }

    /**
     * Count how many tier prices we have for the product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  int
     */
    public function getTierPriceCount($product)
    {
        $price = $product->getTierPrice();
        return count($price);
    }

    /**
     * Get formatted by currency tier price
     *
     * @param   float $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  array || float
     */
    public function getFormatedTierPrice($qty=null, $product)
    {
        $price = $product->getTierPrice($qty);
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                $price[$index]['formated_price'] = Mage::app()->getStore()->convertPrice(
                        $price[$index]['website_price'], true
                );
            }
        }
        else {
            $price = Mage::app()->getStore()->formatPrice($price);
        }

        return $price;
    }

    /**
     * Get formatted by currency product price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  array || float
     */
    public function getFormatedPrice($product)
    {
        return Mage::app()->getStore()->formatPrice($product->getFinalPrice());
    }

    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
		//print_r($product->getCategoryIds());		
		if(in_array('277',$product->getCategoryIds())){
			$temp = 0;
			$new_finalPrice = 0;
			if ($optionIds = $product->getCustomOption('option_ids')) {
				$basePrice = $finalPrice;				
				$opt_arr = array();
				$side_stone_flag = 'no';
				$side_stone_cost = 0;
				foreach (explode(',', $optionIds->getValue()) as $optionId) {					
					if ($option = $product->getOptionById($optionId)) {					
						$confItemOption = $product->getCustomOption('option_'.$option->getId());					
						$_result = $option->getValueById($confItemOption->getValue());
						
						if($option->getTitle()){
							if($_result){								
								if($option->getTitle() != 'Ring Size'){
									$opt_arr[$option->getTitle()] = $_result->getData('title');
									if($option->getTitle() == 'Side Stone'){										
										//echo '<pre>';print_r($_result);echo '</pre>';
										$side_stone_flag = 'yes';
										$side_stone_cost = $_result->getPrice();										
									}
								}
							}
						}
						if($option->getTitle()){
							if($option->getTitle() == 'Engraving'){
								$group = $option->groupFactory($option->getType())->setOption($option)->setConfigurationItemOption($confItemOption);
								$new_finalPrice = $group->getOptionPrice($confItemOption->getValue(), $temp);
								$temp = $new_finalPrice;
							}
						}						
					}
				}
				$proMod = Mage::getModel('catalog/product');				
				$prod = $proMod->load($product->getId());				
				 
				 //var_dump($prod->getData('emb_fourteen_wg_weight')); exit();
				 
				// start seperate calculation for stone, metal and extra prices excluding engraving
				$stone = $prod->getAttributeText('emb_stone_name2'); // 'Ruby';// stone value
				$shape = $prod->getAttributeText('emb_shape2');  // 'Cushion'; // shape value
				$yg_weight = $prod->getData('emb_fourteen_yg_weight'); // '1'; yello gold weight
				$wg_weight = $prod->getData('emb_fourteen_wg_weight'); // '1'; white gold weight 
				$pl_weight = $prod->getData('emb_pl_weight'); // '1'; pletinum gold weight 
				$sl_weight = $prod->getData('emb_sl_weight');	 // '1'; silver weight		
				$extra_labor_cost = $prod->getData('emb_extra_cost'); // '300'; extra labor cost
				$number_of_stone = $prod->getData('emb_number_of_stones2'); // '1'; number of stone
				
				
				foreach($opt_arr as $key=>$val){
					switch (trim($key)) {
						case 'Stone Size':
							$size = $val;
							break;
						case 'Stone Quality':
							$grade = $val;
							break;
						case 'Metal Type':
							$metal = $val;
							break;
						case 'Side Stone':
							$side_stone_flag = 'yes';
							//$stone = $val;
							break;						
					}	
				}
				require_once ('ajaxact/ajax_byoj_price_cart.php');
				/*echo $shape.', '.$stone.', '.$size.', '.$metal.', '.$grade.', '.$side_stone_flag.', '.$yg_weight.', '.$wg_weight.', '.$pl_weight.', '.$sl_weight.', '.$side_stone_cost.', '.$extra_labor_cost.', '.$number_of_stone;*/
				$final_cost=getFinalProductAmount($shape,$stone,$size,$metal,$grade,$side_stone_flag,$yg_weight,$wg_weight,$pl_weight,$sl_weight,$side_stone_cost,$extra_labor_cost,$number_of_stone);
				
				$temp = $temp + $final_cost;						
				// end seperate calculation for stone, metal and extra prices excluding engraving											
			}else{
				$temp = $finalPrice;
			}			
			return $temp;
		}else{
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
    }	
    /**
     * Calculate product price based on special price data and price rules
     *
     * @param   float $basePrice
     * @param   float $specialPrice
     * @param   string $specialPriceFrom
     * @param   string $specialPriceTo
     * @param   float|null|false $rulePrice
     * @param   mixed $wId
     * @param   mixed $gId
     * @param   null|int $productId
     * @return  float
     */
    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
            $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        Varien_Profiler::start('__PRODUCT_CALCULATE_PRICE__');
        if ($wId instanceof Mage_Core_Model_Store) {
            $sId = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $sId = Mage::app()->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }

        $finalPrice = $basePrice;
        if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }

        $finalPrice = self::calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $sId);

        if ($rulePrice === false) {
            $storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($sId);
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($storeTimestamp, $wId, $gId, $productId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);
        Varien_Profiler::stop('__PRODUCT_CALCULATE_PRICE__');
        return $finalPrice;
    }

    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param mixed $store
     * @return float
     */
    public static function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
            $store = null)
    {
        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                $finalPrice     = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    /**
     * Check is tier price value fixed or percent of original price
     *
     * @return bool
     */
    public function isTierPriceFixed()
    {
        return $this->isGroupPriceFixed();
    }
	
	/**
     * Check is group price value fixed or percent of original price
     *
     * @return bool
     */
    public function isGroupPriceFixed()
    {
        return true;
    }
}
