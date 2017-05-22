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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product View block
 *
 * @category Mage
 * @package  Mage_Catalog
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Add meta information from product to head block
     *
     * @return Mage_Catalog_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('catalog/breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
	    else{
		$shortDescription = $product->getShortDescription();
		$headBlock->setTitle($shortDescription);
	    }
            $keyword = $product->getMetaKeyword();
            $currentCategory = Mage::registry('current_category');
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } elseif($currentCategory) {
				// Angara Modification Start
				//$headBlock->setKeywords($product->getName());
				$headBlock->setKeywords($product->getShortDescription().', '.$product->getSku()); //added by anil jain - 10-04-2012
				// Angara Modification End
            }
            $description = $product->getMetaDescription();
            if ($description) {
                $headBlock->setDescription( ($description) );
            } else {
				// Angara Modification Start
				//$headBlock->setDescription(Mage::helper('core/string')->substr($product->getDescription(), 0, 255));
				$headBlock->setDescription($product->getShortDescription().'-'.$product->getSku()); //added by anil jain - 10-04-2012
				// Angara Modification End
            }
            if ($this->helper('catalog/product')->canUseCanonicalTag()) {
                $params = array('_ignore_category'=>true);
                $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance(true)->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || Mage::app()->getRequest()->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }
	
	
	
	/*	
		S:VA
		Add Warranty Checkbox Show or Not Show
	*/
	public function canShowWarranty(){
		$_product 				= 	$this->getProduct();
		$warrantyHideCatArray	=	array('70','71','72','82','76');		
		// Diamond Rings (ID: 70), Diamond Earrings (ID: 71), Diamond Necklace Pendants (ID: 72), Diamond Engagement Rings (ID: 82), Diamond Bracelets (ID: 76)
		$result 				= 	array_intersect($warrantyHideCatArray, $_product->getCategoryIds());
		$hideWarranty			=	count($result);
		if( $hideWarranty == 0  ){
			return true;		//	show
		}else{
			return false;		//	don't show
		}
	}
	
	
	/*	
		S:VA
		Don't show middle container border if there is only single option (Ring Size) to show for Configurable Product		or only single custom option for Simple Product
	*/
	public function canShowOptionBoxBorder(){
		$_product = $this->getProduct();
		if($_product->isConfigurable()){
			
			$childIds 			= 	Mage::getModel('catalog/product_type_configurable')->getChildrenIds($_product->getId());
			//prd($childIds[0]);
			if(!empty($childIds)){
				$childProductCount	=	count($childIds[0]);
			}
			//	As there is only one child product that means in any case we are not showing the bg color
			if($childProductCount<=1){
				return $style	=	'style="background:none;"';
			}
			
			//	Check if configurable product with single configurable attribute
			$_attributes 					= 	$_product->getTypeInstance(true)->getConfigurableAttributes($_product); 
			$countConfigurableAttributes	=	count($_attributes->getData());
			//echo $countConfigurableAttributes;
			if($countConfigurableAttributes<=1){
				//	Check if there is only one configurable attribute that is ring_size
				foreach($_attributes as $attribute) { 
					$attributeCode 	 = 	$attribute->getProductAttribute()->getAttributeCode();
					if($attributeCode=='ring_size'){
						return $style	=	'style="background:none;"';
					}
				}
				//	Now check if we have a ring size as custom option if yes then we will count the attribute as +1
				$countCustomOptions	=	count($_product->getOptions());
				if($countCustomOptions>1){		//	Ring Size and Engraving
					//	now we have ring size as well so we won't hide bg color
					
				}else{
					$style	=	'style="background:none;"';
				}
			}
		}else{
			$countCustomOptions	=	count($_product->getOptions());
			if($countCustomOptions<=2){		//	Ring Size and Engraving
				$style	=	'style="background:none;"';
			}
		}
		return $style;	
	}
	
	public function getJewelProductConfig(){
		$product = $this->getProduct();
		$vendorLeadTime = $product->getVendorLeadTime();
		$config = array(
			'shippingDates' => array()
		);
		
		$config['vendorLeadTime'] = $vendorLeadTime;
		
		// add vendor lead times as per maximum shipping date difference
		$config['shippingDates'] = array(
			$vendorLeadTime => Mage::helper('function')->skipUsaHolidays($vendorLeadTime),
			$vendorLeadTime - 1 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 1),			//	Added by Vaseem
			$vendorLeadTime - 2 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 2),			//	Added by Vaseem
			$vendorLeadTime - 3 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 3),			//	Added by Vaseem
			$vendorLeadTime - 4 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 4),			
			$vendorLeadTime - 5 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 5),			
			$vendorLeadTime - 6 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 6),
			$vendorLeadTime - 7 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 7),
			$vendorLeadTime - 8 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 8),
			$vendorLeadTime - 9 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 9),
			$vendorLeadTime - 10 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime - 10),			
			$vendorLeadTime + 1 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 1),
			$vendorLeadTime + 2 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 2),
			$vendorLeadTime + 3 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 3),
			$vendorLeadTime + 4 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 4),
			$vendorLeadTime + 5 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 5),
			$vendorLeadTime + 6 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 6),
			$vendorLeadTime + 7 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 7),
			$vendorLeadTime + 8 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 8),
			$vendorLeadTime + 9 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 9),
			$vendorLeadTime + 10 => Mage::helper('function')->skipUsaHolidays($vendorLeadTime + 10)
		);
		
		return Mage::helper('core')->jsonEncode($config);
	}
	
}
