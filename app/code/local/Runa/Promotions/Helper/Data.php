<?php

class Runa_Promotions_Helper_Data extends Mage_Core_Helper_Abstract {
    const TYPE_DETAIL = 'product_detail';      // used on product detail pages
    const TYPE_AGGREGATE = 'products_aggregate';  // used on category pages

    /**
     * Return the html Runa product command span tag for the indicated product
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param string $type One of Runa_Promotions_Helper_Data::TYPE_* constant values
     * @return string
     */
    public function getProductCommandSpan($product, $type = self::TYPE_DETAIL) {
        $_product_id = $product->getId();
        $_product_name = str_replace(array('"', "'"), '', $product->getName());

        $prices = $this->getProductPrices($product);
        $_product_price_unit = $prices->getUnitPrice();
        $_product_price_list = $prices->getListPrice();

        $_product_options = ''; // optional runa_command attributes not implemented
        $_product_attributes = $this->getProductOptionsAsAttribs($product);

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            $_groupedProducts = $product->getTypeInstance()->getAssociatedProductCollection();
            $_spanXml = '';
            foreach ($_groupedProducts as $_child) {
                $_child = mage::getModel('catalog/product')->load($_child->getId());
                $_spanXml .= $this->getProductCommandSpan($_child, self::TYPE_AGGREGATE);
            }

            return $_spanXml;
        }

        $catig_Ids = $this->getProductCategories($product);

        return "<span  class='runa_command' type='$type' product_id='$_product_id' categories='$catig_Ids' name='$_product_name'" .
                " unit_price='$_product_price_unit' list_price='$_product_price_list'   product_attributes='$_product_attributes' />";
    }

    public function getProductPrices($product) {

        //   $_taxHelper = mage::helper('tax');
        //   $_minimalPriceValue = $product->getMinimalPrice();
        //   if (!$_minimalPriceValue) {
        //   }
        //  $_unitPriceExclTax = $_taxHelper->getPrice($product, $_minimalPriceValue, $includingTax = null);

        $_product_price_list = $product->getPrice();
        $_minimalPriceValue = $product->getFinalPrice();
        $priceObject = new Varien_Object();
        $priceObject->setUnitPrice($_minimalPriceValue);
        $priceObject->setListPrice($_product_price_list);
        return $priceObject;
    }

    public function getProductCategories($_product) {

        $categories = $_product->getCategoryIds();
        return implode(',', $categories);
    }

    public function getProductOptionsAsAttribs($product) {

        $_attribs = "";
        $_optVals = '';
        //handling custonm options
        foreach ($product->getProductOptionsCollection() as $_option) {

            $_attribName = $_option->getDefaultTitle();

            $_optVal = '';
            foreach ($_option->getValues() as $_value) {
                $_optVal = $_attribName . ':' . $_value->getOptionTypeId() . ':' . $_value->getPrice();
                if ($_optVals) {
                    $_optVals = $_optVals . ',' . $_optVal;
                } else {
                    $_optVals = $_optVal;
                }
            }
        }

        $_optVal = '';
        $_value = '';
        //handling simple attributes
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $_bundle = $product->getTypeInstance($product);

            foreach ($_bundle->getOptionsCollection() as $_option) {
                $_attribName = $_option->getDefaultTitle();
                $_optVal = '';
                $_selections = $_bundle->getSelectionsCollection(array($_option->getOptionId()));
                $count = $_selections->count();
                $_value = '';
                foreach ($_selections as $_value) {
                    $_optVal = $_attribName . ':' . $_value->getSelectionId() . ':' . round($_value->getFinalPrice(), 2);
                    if ($_optVals) {
                        $_optVals = $_optVals . ',' . $_optVal;
                    } else {
                        $_optVals = $_optVal;
                    }
                }
            }
        }

        $_optVal = '';
        $_value = '';
        //handling configurable attributes
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $_configurable = $product->getTypeInstance($product);
            foreach ($_configurable->getConfigurableAttributeCollection() as $_option) {
                $_attribName = $_option->getProductAttribute()->attribute_code;
                $_optVal = '';
                $_selections = $_option->getPrices();
                foreach ($_selections as $_value) {
                    if (!$_value['pricing_value']) {
                        $_price = 0;
                    } else {
                        $_price = $_value['pricing_value'];
                    }
                    $_optVal = $_attribName . ':' . $_value['default_label'] . ':' . $_price;
                    if ($_optVals) {
                        $_optVals = $_optVals . ',' . $_optVal;
                    } else {
                        $_optVals = $_optVal;
                    }
                }
            }
        }


        return $_optVals;
    }

    public function createRandomString() {
        $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789";

        $i = 0;
        $_string = '';
        while ($i <= 31) {
            $num = rand() % 60;
            $tmp = substr($chars, $num, 1);
            $_string = $_string . $tmp;
            $i++;
        }

        return $_string;
    }

    public function getRouteKey() {
        $front = mage::App()->getFrontController();
        $_moduleName = $front->getRequest()->getModuleName();
        $_controllerName = $front->getRequest()->getControllerName();
        $_actionName = $front->getRequest()->getActionName();

        $_hashRequest = $_moduleName . '_' . $_controllerName . '_' . $_actionName;

        return $_hashRequest;
    }

    public function getDiscountLabel() {

        return Mage::getStoreConfig('sales/runa_promote/default_discount_text');
    }

    public function getShippingDiscountLabelPrefix() {

        return Mage::getStoreConfig('sales/runa_promote/shipping_discount_prefix');
    }

    public function getMagentoVersion() {
        $ver = Mage::getVersion();
        $version = str_replace('.', '', $ver);
        return $version;
    }

    public function isMage13X() {

        $version = $this->getMagentoVersion();
        if ($version > 1330) {
            //if version is >1.4.X remove 1.3.X layout
            return false;
        }

        return true;
    }

}