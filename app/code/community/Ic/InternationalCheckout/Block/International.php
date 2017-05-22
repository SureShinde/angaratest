<?php
class Ic_InternationalCheckout_Block_International extends Mage_Core_Block_Template {

	public $IC_URL = "https://www.internationalcheckout.com/cart.php";
	
	
	
	
	
	
	/**
	 * Returns true only if name_of_company and shipping_method have been defined and Enabled is set to Yes.  
	 *	
	 */
	 
	function isEnabled() {
		
		$isValid = false;
		$sm_config = trim(Mage::getStoreConfig("checkout/internationalcheckout/shipping_method"));
		
		if ((trim(Mage::getStoreConfig("checkout/internationalcheckout/name_of_company")) != "") && ($sm_config != "") && Mage::getStoreConfig("checkout/internationalcheckout/active") ) {
		
			$sm = explode("_",$sm_config);
			if ( Mage::getStoreConfig('carriers/'.$sm[0].'/active') || $sm[0] == 'freedomesticshippingtoic' ) {
				$isValid = true;
			}
		}
		return $isValid;
	}
	

	/**
	 * Returns name_of_company after removing spaces and converting to lower case.  
	 *	
	 */	
	
	function formatCompany($cName) {		
		$cName = str_replace(" ","",$cName);
		$cName = strtolower($cName);		
		return $cName;
	}
	
	
	
	/**
	 * Returns class name for IC button.  
	 *	
	 */	
	
	function buttonClassName() {		
		
		$className = "";
		
		$ic_button_image = trim(Mage::getStoreConfig("checkout/internationalcheckout/ic_button_image"));
		
		if ($ic_button_image != "") {
			return "";
		}
		
		$version = Mage::getVersion();
		
		if ($version >= "1.4") {
			$className = "button btn-checkout";
		}
		else {
			$className = "form-button-alt";
		}
		
		return $className;
	}
	
	
	/**
	 * Returns CSS style attribute filled with image info.  
	 *	
	 */	
	
	function buttonImagePath() {		
		
		$style = "";
		$ic_button_image = trim(Mage::getStoreConfig("checkout/internationalcheckout/ic_button_image"));
		
		if ($ic_button_image != "") {
		
			$style= "style=\"background-image:url('".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."Ic/images/" . $ic_button_image ."'); background-repeat:no-repeat; width:".trim(Mage::getStoreConfig("checkout/internationalcheckout/ic_button_width"))."px; height:".trim(Mage::getStoreConfig("checkout/internationalcheckout/ic_button_height"))."px; outline:none; border:none; cursor:pointer;\"";
		}
		
		return $style;
	}
	
	
	/**
	 * Returns text for IC button.  
	 *	
	 */		
	
	function buttonText() {
	
		if (trim(Mage::getStoreConfig("checkout/internationalcheckout/ic_button_image")) != "") {
			return "";
		}
		else {
			
			return $this->__('International Checkout');
		}
	}
	
	
	/*
	 * getBundleOptions - Mage_Bundle_Block_Checkout_Cart_Item_Renderer
	 *
	 */	
	protected function _getBundleOptions($item, $useCache = true)
    {
        $options = array();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $item->getProduct()->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption =  $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = unserialize($optionsQuoteItemOption->getValue());
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $item->getProduct());

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $item->getProduct()
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array('label' => $bundleOption->getTitle(), "value" => array());
                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $option['value'][] = $this->_getSelectionQty($item, $bundleSelection->getSelectionId()).' x '. $this->htmlEscape($bundleSelection->getName()). ' ' .Mage::helper('core')->currency($this->_getSelectionFinalPrice($item, $bundleSelection));
                    }

                    $options[] = $option;
                }
            }
        }
        return $options;
    }
	
	
	function _getSelectionFinalPrice($item, $selectionProduct)
    {
        $bundleProduct = $item->getProduct();
        return $bundleProduct->getPriceModel()->getSelectionFinalPrice(
            $bundleProduct, $selectionProduct,
            $item->getQty(),
            $this->_getSelectionQty($item, $selectionProduct->getSelectionId())
        );
    }

    /**
     * Get selection quantity
     *    
     */
    function _getSelectionQty($item, $selectionId)
    {
        if ($selectionQty = $item->getProduct()->getCustomOption('selection_qty_' . $selectionId)) {
            return $selectionQty->getValue();
        }
        return 0;
    }
	
	
	
	/**
	 * get product attributes
	 *	
	 */
	
	function getProductAttributes($item)
    {
        $attributes = $item->getProduct()->getTypeInstance(true)
            ->getSelectedAttributesInfo($item->getProduct());
        return $attributes;
    }
	
	
	
	 function getLinks($item)
    {
        $itemLinks = array();
        if ($linkIds = $item->getOptionByCode('downloadable_link_ids')) {
            $productLinks = $item->getProduct()->getTypeInstance(true)
                ->getLinks($item->getProduct());
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }
        }
        return $itemLinks;
    }
	
	
	function getLinksTitle($item)
    {
        if ($item->getProduct()->getLinksTitle()) {
            return $item->getProduct()->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
	
	
	function getChildProduct($item)
    {
        if ($option = $item->getOptionByCode('simple_product')) {
            return $option->getProduct();
        }
        return $item->getProduct();
    }
	
	function getGroupedProduct($item)
    {
        $option = $item->getOptionByCode('product_type');
        if ($option) {
            return $option->getProduct();
        }
        return $item->getProduct();
    }
	
	
	
	function getProductThumbnail($item)
    {
        
		// begin configurable
		 if ($item->getProduct()->isConfigurable()) {
			$product = $this->getChildProduct($item);
			if (!$product || !$product->getData('thumbnail')
				|| ($product->getData('thumbnail') == 'no_selection')
				|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Configurable::CONFIGURABLE_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Configurable::USE_PARENT_IMAGE)) {
				$product = $item->getProduct();
			}
			return Mage::helper('catalog/image')->init($product, 'thumbnail');
		}
		// end configurable
		
		// begin grouped
		if ($item->getProduct()->isGrouped()) {
			$product = $item->getProduct();
			if (!$product->getData('thumbnail')
				||($product->getData('thumbnail') == 'no_selection')
				|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Grouped::GROUPED_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Grouped::USE_PARENT_IMAGE)) {
				$product = $this->getGroupedProduct($item);
			}
			return Mage::helper('catalog/image')->init($product, 'thumbnail');
		}
		// end grouped
		
		
		return Mage::helper('catalog/image')->init($item->getProduct(), 'thumbnail');
		
    }
	
	
	
	
	/**
	 * Returns product options and additional attrubutes.  
	 *	
	 */
	
	function getProductOptions($item)
    {
      $options = array();
      if ($optionIds = $item->getOptionByCode('option_ids')) {
          $options = array();
          foreach (explode(',', $optionIds->getValue()) as $optionId) {
              if ($option = $item->getProduct()->getOptionById($optionId)) {

                  $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());

                  $group = $option->groupFactory($option->getType())
                      ->setOption($option)
                      ->setQuoteItemOption($quoteItemOption);

                  $options[] = array(
                      'label' => $option->getTitle(),
                      'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                      'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                      'option_id' => $option->getId(),
                      'option_type' => $option->getType(),
                      'custom_view' => $group->isCustomizedView()
                  );
              }
          }
      }
      if ($addOptions = $item->getOptionByCode('additional_options')) {
          $options = array_merge($options, unserialize($addOptions->getValue()));
      }
      
	  
	  if ($item->getProduct()->isConfigurable()) {
	  	$options = array_merge($this->getProductAttributes($item), $options); // configurable products
       }
	   
	   
	   if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
	   	$options = array_merge($this->_getBundleOptions($item), $options); // bundle products
	   }
	  
	  
	  return $options;
    }
	

	/**
	 * Returns formatted string of item description and attributes.  
	 *	
	 */	

	function buildItemDescription($item) {
	
		$crlf = "\n";		
		$valueSeperator = " - ";
		$output = "";
		$output .= $this->htmlEscape($item->getName()).$crlf;
		
		$options = $this->getProductOptions($item);
		if (count($options)) {
			for ($c=0; $c<count($options); $c++) {
				
				if (is_array($options[$c]["value"])) {
					$output .= " [ ". $options[$c]["label"].": ".strip_tags(implode($valueSeperator,$options[$c]["value"]))." ] ";				
				}
				else {
					$output .= " [ ".$options[$c]["label"].": ".strip_tags($options[$c]["value"])." ] ";
				}
				
				$output .= $crlf;
			}
		}
		
		// addition of links for downloadable products
		//
		if ($links = $this->getLinks($item)) {
			$output .= " [ " . strip_tags($this->getLinksTitle($item));
			foreach ($links as $link) {
				$output .= " ( " . strip_tags($link->getTitle()) . " ) ";
			}
			$output .= " ] ";
			$output .= $crlf;
		}
		
		
		return $output;
	}
	
	
	/**
	 * Calculates and returns the domestic shipping price.  
	 *	
	 */
	
	function shippingPrice () {
	
		$session = Mage::getSingleton('checkout/session');
		$address = $session->getQuote()->getShippingAddress();
			
		$bkCountryId = $address->getCountryId();
		$bkCity = $address->getCity();
		$bkPostcode = $address->getPostcode();
		$bkRegionId = $address->getRegionId();
		$bkRegion = $address->getRegion();
		
		$address->setCountryId("US")
				->setCity("California")
				->setPostcode("91406")
				->setRegionId("")
				->setRegion("")
				->setCollectShippingRates(true)->collectShippingRates();
				 
		
		
		$shippingCarrierMethodArr = explode("_",Mage::getStoreConfig('checkout/internationalcheckout/shipping_method'));
		$shippingCarrier = $shippingCarrierMethodArr[0];
		$shippingMethod = $shippingCarrierMethodArr[1];
		$carriers[$shippingCarrier] = '';
		$price = 0;
		
		if ($shippingCarrier == "freedomesticshippingtoic" && $shippingMethod == "freedomesticshippingtoic") {
			return $price;
		}
			 
		$result = Mage::getModel('shipping/shipping')
			 ->collectRatesByAddress($address, array_keys($carriers))
			 ->getResult();
 
		foreach ($result->getAllRates() as $rate) {
			if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
				$errors[$rate->getCarrierTitle()] = 1;
				$price = -1; // error flag
			} else {
			
				 if ($address->getFreeShipping()) {
					$price = 0;
				 } else {
					 if ($shippingMethod == $rate->getMethod()) {  
						$price = $rate->getPrice();
					 }
			 	 }
			
				 if ($price) {
					 $price = Mage::helper('tax')->getShippingPrice($price, false, $address);
				 }
			 
				//unset($errors[$rate->getCarrierTitle()]);
				
			}
		}
		
		
		$address->setCountryId($bkCountryId)
				->setCity($bkCity)
				->setPostcode($bkPostcode)
				->setRegionId($bkRegionId)
				->setRegion($bkRegion)
				->setCollectShippingRates(true)->collectShippingRates();
		
		
		return $price;
	
	}
	
	
	
}
