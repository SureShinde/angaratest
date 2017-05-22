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
 * Catalog super product configurable part block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Ayasoftware_SimpleProductPricing_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{

	public function getProductJsonConfigHash(){
		$countryParam = Mage::getModel('countrymapping/country')->getCountryParameters();
		return $this->getProduct()->getId().strtolower($countryParam->getCountryCode()).strtotime($this->getProduct()->getUpdatedAt());
	}

    public function getJsonConfig()
    {
		if(Mage::registry('current_json_config2')){
			return Mage::registry('current_json_config2');
		}
		/*$productConfigHash = $this->getProductJsonConfigHash();
		$productConfigData = Angara_Cacher::get($productConfigHash);
		if($productConfigData){
			return $productConfigData;
		}
        */
		$showOutOfStock = false;
		$config = Zend_Json::decode(parent::getJsonConfig());
		if (Mage::getStoreConfig('spp/setting/show')) {
			$productsCollection = $this->canShowOutOfStockProducts();
			$showOutOfStock = true;
		} else {
			$productsCollection = $this->getAllowProducts();
		}
		
		
		// Add media gallery to collection
		$this->addMediaGalleryAttributeToCollection($productsCollection, $this->getProduct()->getTypeInstance(true)->getUsedProductIds($this->getProduct()));
		
		$placeholder = Mage::getModel('catalog/product')->getSmallImageUrl(350,350);
		$inventoryStockItemObj = Mage::getModel('cataloginventory/stock_item');
		$productObj = Mage::getModel('catalog/product');
		$catalogImageObj = Mage::helper('catalog/image');
		
		//Create the extra price and tier price data/html we need.
		//foreach ($productsCollection as $product) {

		// Added by Vaibhav
		$trio_flag=0; 
		if(Mage::registry('current_product')->getTrioSku())
		{
			$trio_skus=Mage::registry('current_product')->getTrioSku();
		    $trio_sku_array = explode('|', $trio_skus);
		    if(count($trio_sku_array)==3)
		    {
		    	$trio_product1_id = Mage::getModel('catalog/product')->getIdBySku($trio_sku_array[0]);
			    $trioProduct1 = Mage::getModel('catalog/product')->load($trio_product1_id);    
			    $trio1_all = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$trioProduct1);
			    $trio_product2_id = Mage::getModel('catalog/product')->getIdBySku($trio_sku_array[1]);
				$trioProduct2 = Mage::getModel('catalog/product')->load($trio_product2_id);
                $trio2_all = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$trioProduct2);
                $trio_product3_id = Mage::getModel('catalog/product')->getIdBySku($trio_sku_array[2]);
			    $trioProduct3 = Mage::getModel('catalog/product')->load($trio_product3_id);
				$trio3_all = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$trioProduct3);
				$trio_flag=1;
		    }
		    
		}
	    


		foreach ($productsCollection as $product) 
		{
			$productId = $product->getId();
			if($trio_flag)
			{
				$metal_type=$product->getData('metal1_type');
                $stone_grade=$product->getData('stone1_grade');
          
                $trio_array=Mage::getModel('catalog/product_type_configurable_price')->getTrioProducts($trio1_all,$trio2_all,$trio3_all,$metal_type,$stone_grade);
			
				$trio1_simple_sku=$trio_array[0];
				
				$trio2_simple_sku=$trio_array[1];
				$trio3_simple_sku=$trio_array[2];
				
				$trio1productId= Mage::getModel('catalog/product')->getIdBySku($trio1_simple_sku);
				$trio2productId= Mage::getModel('catalog/product')->getIdBySku($trio2_simple_sku);
				$trio3productId= Mage::getModel('catalog/product')->getIdBySku($trio3_simple_sku);
				
				$trioProduct1 = Mage::getModel('catalog/product')->load($trio1productId);
				
				$trioProduct2 = Mage::getModel('catalog/product')->load($trio2productId);
				$trioProduct3 = Mage::getModel('catalog/product')->load($trio3productId);

				// media images
				$trio1_images = $trioProduct1->getMediaGalleryImages();
				$trio2_images = $trioProduct2->getMediaGalleryImages();
				$trio3_images = $trioProduct3->getMediaGalleryImages();

				$images = array( 0 => array(
				"url" => $placeholder,
				"thumbUrl" => $placeholder,
				"alt" => ''
				));
				$imageIndex = 0;
				foreach($trio1_images as $mediaImage){
					if(!$mediaImage->getDisabledDefault()){
						$images[$imageIndex++] = array(
							"url" => (string)$catalogImageObj->init($trioProduct1, 'image', $mediaImage->getFile()),
							//"url" => $imageURL,
							"thumbUrl" => (string)$catalogImageObj->init($trioProduct1, 'image', $mediaImage->getFile())->resize(98),
							"alt" => $mediaImage->getLabel()
						);
						
					}
				}
				$images_trio1=$images;
				
				$productImagesTrio1[$product->getId()]['trio1_images'] = $images;
				$productImagesTrio1[$product->getId()]['trio1_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct1->getFinalPrice()));
				$productImagesTrio1[$product->getId()]['trio1_traditional_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct1->getMsrp()));
				$productImagesTrio1[$product->getId()]['trio1_name'] = $trioProduct1->getShortDescription();
				$productImagesTrio1[$product->getId()]['trio1_sku'] = $trio1_simple_sku;
				$productImagesTrio1[$product->getId()]['trio1_url'] = $trioProduct1->getProductUrl();


				//trio 2
				$images = array( 0 => array(
				"url" => $placeholder,
				"thumbUrl" => $placeholder,
				"alt" => ''
				));
				$imageIndex = 0;
				foreach($trio2_images as $mediaImage){
					if(!$mediaImage->getDisabledDefault()){
						$images[$imageIndex++] = array(
							"url" => (string)$catalogImageObj->init($trioProduct2, 'image', $mediaImage->getFile()),
							//"url" => $imageURL,
							"thumbUrl" => (string)$catalogImageObj->init($trioProduct2, 'image', $mediaImage->getFile())->resize(98),
							"alt" => $mediaImage->getLabel()
						);
						
					}
				}
				
				$images_trio2=$images;
				
				$productImagesTrio1[$product->getId()]['trio2_images'] = $images;
				$productImagesTrio1[$product->getId()]['trio2_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct2->getFinalPrice()));
				$productImagesTrio1[$product->getId()]['trio2_traditional_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct2->getMsrp()));
				$productImagesTrio1[$product->getId()]['trio2_name'] = $trioProduct2->getShortDescription();
				$productImagesTrio1[$product->getId()]['trio2_sku'] = $trio2_simple_sku;
				$productImagesTrio1[$product->getId()]['trio2_url'] = $trioProduct2->getProductUrl();
                
				//trio3
				$images = array( 0 => array(
				"url" => $placeholder,
				"thumbUrl" => $placeholder,
				"alt" => ''
				));
				$imageIndex = 0;
				foreach($trio3_images as $mediaImage){
					if(!$mediaImage->getDisabledDefault()){
						$images[$imageIndex++] = array(
							"url" => (string)$catalogImageObj->init($trioProduct3, 'image', $mediaImage->getFile()),
							//"url" => $imageURL,
							"thumbUrl" => (string)$catalogImageObj->init($trioProduct3, 'image', $mediaImage->getFile())->resize(98),
							"alt" => $mediaImage->getLabel()
						);
						
					}
				}
				$images_trio3=$images;
				$productImagesTrio1[$product->getId()]['trio3_images'] = $images;
				$productImagesTrio1[$product->getId()]['trio3_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct3->getFinalPrice()));
				$productImagesTrio1[$product->getId()]['trio3_traditional_price'] = $this->_registerJsPrice($this->_convertPrice($trioProduct3->getMsrp()));
				$productImagesTrio1[$product->getId()]['trio3_name'] = $trioProduct3->getShortDescription();
				$productImagesTrio1[$product->getId()]['trio3_sku'] = $trio3_simple_sku;
				$productImagesTrio1[$product->getId()]['trio3_url'] = $trioProduct3->getProductUrl();

				
			}
			
			$stockItem = $inventoryStockItemObj->loadByProduct($productId);
			$currentItem = $productObj->load($productId);
			if ($stockItem->getQty() <= 0) {
				$stockInfo[$productId] = array(
					"stockLabel" => $this->__('Out of stock'),
					"stockQty" => intval($stockItem->getQty()),
					"is_in_stock" => false,
				);
			} else {
				$stockInfo[$productId] = array(
					"stockLabel" => $this->__('In stock'),
					"stockQty" => intval($stockItem->getQty()),
					"is_in_stock" => true,
				);
			}
			//	S:VA
			$offerData	=	Mage::helper('function')->getCurrentOfferCouponCode();
			if(is_array($offerData)){
				$couponPerc	=	(int)$offerData['coupon_perc'];
				if ($couponPerc) {
					$config['coupon_percent'] = $couponPerc;
				}
			}
			//	E:VA
		    
			$childProducts[$productId] = array(
				"price" 			=> ($trio_flag==0 ? $this->_registerJsPrice($this->_convertPrice($product->getPrice())) : $this->_registerJsPrice($this->_convertPrice($product->getPrice()+$trioProduct1->getPrice()+$trioProduct2->getPrice()+$trioProduct3->getPrice()))),
				"finalPrice" 		=> ($trio_flag==0 ? $this->_registerJsPrice($this->_convertPrice($product->getFinalPrice())) : $this->_registerJsPrice($this->_convertPrice($product->getFinalPrice()+$trioProduct1->getFinalPrice()+$trioProduct2->getFinalPrice()+$trioProduct3->getFinalPrice()))),
				"msrp"				=> ($trio_flag==0 ? $this->_convertPrice($product->getMsrp()) : $this->_convertPrice($product->getMsrp()+$trioProduct1->getMsrp()+$trioProduct2->getMsrp()+$trioProduct3->getMsrp())),
				"stones"			=>  $this->getStones($product) ,
				"metals"			=> $this->getMetals($product),
				"vendorLeadTime"	=> $product->getVendorLeadTime(),
				"categoryIds"		=> $this->getProduct()->getCategoryIds(),
				"jewelryStyles"		=> ((is_array($product->getAttributeText('jewelry_styles')))?array_values($product->getAttributeText('jewelry_styles')):array($product->getAttributeText('jewelry_styles')))
				//"band_width"		=> $product->getAttributeText('band_width'),
			);
			
			$childProducts[$productId]['total_weight'] = $childProducts[$productId]['stones']['total_weight'];
			$childProducts[$productId]['weight_description'] = $childProducts[$productId]['stones']['weight_description'];
			//	Added by Saurabh
			$childProducts[$productId]['band_width'] = $product->getAttributeText('band_width');
			//$childProducts[$productId]['prod_name'] = $product->getShortDescription();		//	S:VA			
			//$childProducts[$productId]['shortDescription'] = $product->getShortDescription();
			//$childProducts[$productId]['description'] = $product->getDescription();
			$childProducts[$productId]['band_height'] = $product->getBandHeight();
			$childProducts[$productId]['approximate_metal_weight'] = $product->getApproximateMetalWeight();
			// S:Added by Pankaj
			$childProducts[$productId]['clasp_type'] = $product->getAttributeText('clasp_type');
			$childProducts[$productId]['length'] = $product->getAttributeText('length');
			$childProducts[$productId]['width'] = $product->getAttributeText('width');
			$childProducts[$productId]['butterfly_type'] = $product->getAttributeText('butterfly1_type');
			// E: Ended by Pankaj
			unset($childProducts[$productId]['stones']['total_weight']);
			unset($childProducts[$productId]['stones']['weight_description']);
			$childProducts[$productId]['stones'] = array_values($childProducts[$productId]['stones']);
			
			//grab product media


			$mediaImages = $product->getMediaGalleryImages();

			$images = array( 0 => array(
				"url" => $placeholder,
				"thumbUrl" => $placeholder,
				"alt" => ''
			));
			$imageIndex = 0;
			foreach($mediaImages as $mediaImage){
				if(!$mediaImage->getDisabledDefault()){
					$images[$imageIndex++] = array(
						"url" => (string)$catalogImageObj->init($product, 'image', $mediaImage->getFile()),
						//"url" => $imageURL,
						"thumbUrl" => (string)$catalogImageObj->init($product, 'image', $mediaImage->getFile())->resize(98),
						"alt" => $mediaImage->getLabel()
					);
					
				}
			}
			
			if($trio_flag)
			{
				$images_all=array_merge($images,$images_trio1,$images_trio2,$images_trio3);
                $productImages[$productId] = $images_all;
			}
			else
				$productImages[$productId] = $images;
			
			
			# @todo grab videos and certificates
		}

		if (Mage::getStoreConfig('spp/setting/customstockdisplay')) {
			$config['customStockDisplay'] = true;
		} else {
			$config['customStockDisplay'] = false;
		}
		if($trio_flag) {
			$selectedSimple = Mage::registry('current_selected_product');
			if($selectedSimple && $selectedSimple->getId() &&  isset($childProducts[$selectedSimple->getId()]['price'])){
				$selectedSimple->setPrice($childProducts[$selectedSimple->getId()]['price']);
				$selectedSimple->setFinalPrice($childProducts[$selectedSimple->getId()]['finalPrice']);
				$selectedSimple->setMsrp($childProducts[$selectedSimple->getId()]['msrp']);
				Mage::unregister('current_selected_product');
				Mage::register('current_selected_product',$selectedSimple);
			}
		}
		$config['showOutOfStock'] = $showOutOfStock;
		$config['stockInfo'] = $stockInfo;
		$config['childProducts'] = $childProducts;
		
		//$config['productName'] = $this->getProduct()->getShortDescription();			//	S:VA
		$config['description'] = $this->getProduct()->getDescription();
		$config['shortDescription'] = $this->getProduct()->getShortDescription();
		$config['isBuildYourOwn'] = $this->getProduct()->getIsBuildYourOwn();
		if($product->getJewelryType())
			$config['jewelry_type'] = $product->getAttributeText('jewelry_type');
		if($product->getStone1Name())
			$config['stone1_name'] = $product->getAttributeText('stone1_name');
		if($product->getStone1Shape())
			$config['stone1_shape'] = $product->getAttributeText('stone1_shape');
		if($product->getIsBuildYourOwn()){
			if($product->getStone2Name())
				$config['stone2_name'] = $product->getAttributeText('stone2_name');
			if($product->getStone2Shape())
				$config['stone2_shape'] = $product->getAttributeText('stone2_shape');
		}	
		$config['showPriceRangesInOptions'] = true;
		$config['rangeToLabel'] = $this->__('-');
		if (Mage::getStoreConfig('spp/setting/hideprices')) {
			$config['hideprices'] = true;
		} else {
			$config['hideprices'] = false;
		}
		
		$config['productImages'] = $productImages;

		if($trio_flag)
		{
			$config['productImagesTrio'] = $productImagesTrio1;
			
			$config['isTrio']=1;
		}
		
		
		//$config['prod_name'] = $this->getProduct()->getShortDescription();		//	S:VA
		$config['band_width'] = $this->getProduct()->getAttributeText('band_width');
		// S: Added by Pankaj
		$config['clasp_type'] = $this->getProduct()->getAttributeText('clasp_type');
		$config['length'] = $this->getProduct()->getAttributeText('length');
		$config['width'] = $this->getProduct()->getAttributeText('width');
		$config['butterfly_type'] = $this->getProduct()->getAttributeText('butterfly1_type');
		// E: Ended by Pankaj
		$config['band_height'] = $this->getProduct()->getBandHeight();
		$config['approximate_metal_weight'] = $this->getProduct()->getApproximateMetalWeight();
		//$config['short_description'] = $this->getProduct()->getShortDescription();
		//$config['long_description'] = $this->getProduct()->getDescription();
		
		$jsonConfig = Zend_Json::encode($config);
		//Angara_Cacher::set($productConfigHash, $jsonConfig);
		Mage::register('current_json_config2',$jsonConfig);
		return $jsonConfig;
    }

    public function getCustomJsonConfig()
    {
        $attributes = array();
        $options = array();
        $store = Mage::app()->getStore();
        if (Mage::getStoreConfig('spp/setting/show')) {
            $productsCollection = $this->canShowOutOfStockProducts();
        } else {
            $productsCollection = $this->getAllowProducts();
        }
		
        foreach ($productsCollection as $product) {
            $productId = $product->getId();
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($this->getProduct()->getFinalPrice())
        );
        $optionsLabel = array();
        $configurablePrice = $this->getProduct()->getFinalPrice();
        $configurableOldPrice = $this->getProduct()->getPrice();
        foreach ($this->getAllowAttributes() as $attribute) {
            array_push($optionsLabel, $attribute->getLabel());
        }
        $numberOfOptions = count($optionsLabel);
        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
                'id' => $productAttribute->getId(),
                'code' => $productAttribute->getAttributeCode(),
                'label' => $attribute->getLabel(),
                'options' => array()
            );
            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    if ($attribute->getLabel() == $optionsLabel[$numberOfOptions - 1]) {
                        $products = $options[$attributeId][$value['value_index']];
                        $numItems = count($products);
                        for ($i = 0; $i < $numItems; $i++) {
                            $backoder_date = null;
                            $a = array(0 => $products[$i]);
                            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($products[$i]);
                            $currentItem = Mage::getModel('catalog/product')->load($products[$i]);
                            $__manStock = $currentItem->getStockItem()->getManageStock();
                            if ($__manStock > 0) {
                                $outStockLabel = "(out of stock)";
                            } else {
                                $outStockLabel = "";
                            }
                            $simplePrice = $currentItem->getPrice();
                            $finalPrice = $currentItem->getFinalPrice();
                            $info['options'][] = array(
                                'id' => $value['value_index'],
                                'oldPrice' => $simplePrice - $configurableOldPrice,
                                'label' => ($stockItem->getQty() <= 0) ? $value['label'] . $outStockLabel : $value['label'],
                                'price' => $this->_registerJsPrice($this->_convertPrice($finalPrice)) - $this->_registerJsPrice($this->_convertPrice($configurablePrice)),
                                'products' => isset($options[$attributeId][$value['value_index']]) ? $a : array(),
                            );
                        }
                    } else {
                        $info['options'][] = array(
                            'id' => $value['value_index'],
                            'oldPrice' => 0,
                            'label' => $value['label'],
                            'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                            'products' => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                        );
                    }
                    $optionPrices[] = $this->_preparePrice($value['pricing_value'], $value['is_percent']);
                }

            }

            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional - $optionPrice));
                }
            }
            if ($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $taxConfig = array(
            'includeTax' => Mage::helper('tax')->priceIncludesTax(),
            'showIncludeTax' => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices' => Mage::helper('tax')->displayBothPrices(),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'inclTaxTitle' => Mage::helper('catalog')->__('Incl. Tax'),
        );

        $config = array(
            'attributes' => $attributes,
            'template' => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice' => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getFinalPrice())),
            'oldPrice' => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getPrice())),
            'productId' => $this->getProduct()->getId(),
            'chooseText' => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig' => $taxConfig,
        );

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function canShowOutOfStockProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                $products[] = $product;
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
	
	public function addMediaGalleryAttributeToCollection($_productCollection, $allIds) {
		$_mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();
		$_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
		
		$_mediaGalleryData = $_read->fetchAll('
			SELECT
				main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
				`value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
				`default_value`.`position` AS `position_default`,
				`default_value`.`disabled` AS `disabled_default`
			FROM `catalog_product_entity_media_gallery` AS `main`
				LEFT JOIN `catalog_product_entity_media_gallery_value` AS `value`
					ON main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId() . '
				LEFT JOIN `catalog_product_entity_media_gallery_value` AS `default_value`
					ON main.value_id=default_value.value_id AND default_value.store_id=0
			WHERE (
				main.attribute_id = ' . $_read->quote($_mediaGalleryAttributeId) . ') 
				AND (main.entity_id IN (' . $_read->quote($allIds) . '))
			ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC    
		');
		
		$_mediaGalleryByProductId = array();
		foreach ($_mediaGalleryData as $_galleryImage) {
			$k = $_galleryImage['entity_id'];
			unset($_galleryImage['entity_id']);
			if (!isset($_mediaGalleryByProductId[$k])) {
				$_mediaGalleryByProductId[$k] = array();
			}
			$_mediaGalleryByProductId[$k][] = $_galleryImage;
		}
		unset($_mediaGalleryData);
		foreach ($_productCollection as &$_product) {
			$_productId = $_product->getData('entity_id');
			if (isset($_mediaGalleryByProductId[$_productId])) {
				$_product->setData('media_gallery', array('images' => $_mediaGalleryByProductId[$_productId]));
			}
		}
		unset($_mediaGalleryByProductId);
		
		return $_productCollection;
	}
	
	function getMetals($product){
		$metals = array();
		for($i = 1; $i <= $product->getMetalVariationCount(); $i++){
			$metals[$i] = array(
				'type' => $product->getAttributeText('metal'.$i.'_type'),
			);
		}
		
		return array_values($metals);
	}
	
	function getStoneUniqueName($product, $stoneIndex){
		if($product->getData('stone'.$stoneIndex.'_weight') / $product->getData('stone'.$stoneIndex.'_count') > .24)
			return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade').$stoneIndex;
		return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade');
	}
	
	function getStones($product){
		$stones = array();
		$weightDetails = array();
		for($i = ($product && $product->getIsBuildYourOwn()?'2':'1'); $i <= $product->getStoneVariationCount(); $i++){
			$stoneName = $this->getStoneUniqueName($product, $i);
			
			if(isset($weightDetails[$product->getAttributeText('stone'.$i.'_name')])){
				$weightDetails[$product->getAttributeText('stone'.$i.'_name')] += $product->getData('stone'.$i.'_weight');
			}
			else{
				$weightDetails[$product->getAttributeText('stone'.$i.'_name')] = $product->getData('stone'.$i.'_weight');
			}
			
			if(!isset($stones[$stoneName])){
				$stones[$stoneName] = array(
					'name'		=> $product->getAttributeText('stone'.$i.'_name'),
					'shape'		=> $product->getAttributeText('stone'.$i.'_shape'),
					'size'		=> $product->getAttributeText('stone'.$i.'_size'),
					'grade'		=> $product->getAttributeText('stone'.$i.'_grade'),
					'type'		=> $product->getAttributeText('stone'.$i.'_type'),
					'cut'		=> $product->getAttributeText('stone'.$i.'_cut'),
					'weight'	=> $product->getData('stone'.$i.'_weight'),
					'count'		=> $product->getData('stone'.$i.'_count'),
					'setting'  => $product->getAttributeText('stone'.$i.'_setting'), //	Added by Saurabh
					'color'   => $product->getAttributeText('stone'.$i.'_color'),
					'clarity'   => $product->getAttributeText('stone'.$i.'_clarity')
				);
			}
			else{
				if(strpos(' '.$stones[$stoneName]['size'], ' '.$product->getAttributeText('stone'.$i.'_size')) === false){
					$stones[$stoneName]['size'] .= ', '.$product->getAttributeText('stone'.$i.'_size');
				}
				if(strpos(' '.$stones[$stoneName]['setting'], ' '.$product->getAttributeText('stone'.$i.'_setting')) === false){
					$stones[$stoneName]['setting'] .= ', '.$product->getAttributeText('stone'.$i.'_setting');
				}
				$stones[$stoneName]['weight'] += $product->getData('stone'.$i.'_weight');
				$stones[$stoneName]['count'] += $product->getData('stone'.$i.'_count');
			}
		}
		$stones['total_weight'] = 0;
		foreach($stones as &$stone){
			// add 's' or 'ies' in case stone count > 1
			if($stone['count'] > 1){
				// replace 'y' with 'ies'
				if(substr($stone['name'], -1) == 'y'){
					$stone['name'] = substr_replace($stone['name'], "ies", -1);
				}
				else if(substr($stone['name'], -1) == 'z'){
					$stone['name'] = substr_replace($stone['name'], "zes", -1);
				}
				else if(substr($stone['name'], -1) == 'x'){
					$stone['name'] = substr_replace($stone['name'], "xes", -1);
				}
				else{
					$stone['name'] .= "s";
				}
			}
			$stones['total_weight'] += $stone['weight'];
			if($stone['weight'] > 0){
				if($stone['weight'] == 1){
					$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carat';
				}
				else{
					$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carats';
				}
			}
			
			if($stone['type'] == 'Gemstone' && $stone['grade'] != 'Lab Created' && $stone['grade'] != 'Classic Moissanite' && $stone['grade'] != 'Forever Brilliant'){
				$stone['grade'] = 'Natural - '.$stone['grade'];
			}
		}
		if($stones['total_weight'] > 0){
			if($stones['total_weight'] == 1){
				$stones['total_weight'] = number_format(round((float)$stones['total_weight'], 2), 2, '.', '') . ' carat';
			}
			else{
				$stones['total_weight'] = number_format(round((float)$stones['total_weight'], 2), 2, '.', '') . ' carats';
			}
		}
		
		$weightDescription = array();
		foreach($weightDetails as $stoneTitle => $weight){
			if($weight == 1){
				$weight = number_format(round((float)$weight, 2), 2, '.', '') . ' carat';
			}
			else{
				$weight = number_format(round((float)$weight, 2), 2, '.', '') . ' carats';
			}
			
			if($stoneTitle == 'Moissanite'){
				$weightDescription[] = $weight . ' diamond equivalent weight of ' . strtolower($stoneTitle);
			}
			else{
				$weightDescription[] = $weight . ' of ' . strtolower($stoneTitle);
			}
		}
		$stones['weight_description'] = implode(', ',$weightDescription);
		
		// converting associative array to numeric array
		return $stones;
	}
}