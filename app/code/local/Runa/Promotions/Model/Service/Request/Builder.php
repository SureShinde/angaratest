<?php

class Runa_Promotions_Model_Service_Request_Builder {
    const CONTEXT_ORDER = 'ORDER';

    public function getRequestXmlForItem($item, $overRideAttribs = null) {

        return $this->_buildRequest($item, $overRideAttribs);
    }

    public function buildOrderRequest($order) {

        $_quoteId = $order->getQuoteId();

        $_quote = mage::getModel("sales/quote")->load($_quoteId);

        $_requestXml = "";

        $_itemsTotal = 0;
        $catalogDiscount = 0;
        foreach ($_quote->getAllItems() as $_item) {
            if ($_item->getParentItemId()) {
                continue;
            }
            $attribsToIgnore =
                    array('basket-id' => 'ignore',
                        'ip-address' => 'ignore');

            $_itemXml = '<item>' . $this->getRequestXmlForItem($_item, $attribsToIgnore) . '</item>';
            $catalogDiscount += ( $_item->getProduct()->getPrice() - $_item->getPrice()) * $_item->getQty();

            $_requestXml .= $_itemXml;
        }

        $_requestXml = "<items>" . $_requestXml . "</items>";

        $_helper = mage::helper('runapromotions');
        /* @var $_helper Runa_Promotions_Helper_Data */
        $_cartHeaderXml = "";
        $_cartHeaderXml .= "<page-type>" . $_helper->getRouteKey() . "</page-type>";
        $_cartHeaderXml .= "<basket-id>" . $_quote->getId() . "</basket-id>";
        $_cartHeaderXml .= "<order-id>" . $_quote->reserved_order_id . "</order-id>";
        $_cartHeaderXml .= "<ip-address>" . $_quote->getRemoteIp() . "</ip-address>";
        $_cartHeaderXml .= "<total>" . $order->getGrandTotal() . "</total>";

        $_discountsTotal = $order->getDiscountAmount() + $catalogDiscount;
        $_itemsTotal = $order->getSubtotal() + $catalogDiscount;

        $_itemsTotalXml = "<items>" . $_itemsTotal . "</items>";

        $_chargesTotal = $order->getShippingAmount() + $order->getTaxAmount();
        $_chargesTotalXml = "<charges>$_chargesTotal</charges>";

        $_discountsTotalXml = "<discounts>$_discountsTotal</discounts>";

        $_runaDiscountXml = "<runa-discounts>" . (($order->getRunaDiscountTotal()) * -1) . "</runa-discounts>";
        $_discountsTotalXml = $_discountsTotalXml . $_runaDiscountXml;

        $_totalsXml = $_itemsTotalXml . $_chargesTotalXml . $_discountsTotalXml;

        $_cartHeaderXml .= "<totals>" . $_totalsXml . "</totals>";

        $_discountCharge = $this->_getCartDicountCharge($order, self::CONTEXT_ORDER);

        $_shippingCharge = $this->_getCartShippingChange($order);

        $_runaCharge = $this->_getRunaDiscount($order);

        $_taxCharge = $this->_getTaxCharge($order);

        $_chargesXml = $_discountCharge . $_shippingCharge . $_runaCharge . $_taxCharge;

        if ($_chargesXml) {
            $_chargesXml = "<charges>" . $_chargesXml . '</charges>';
        }

        $_shippingMethodXml = $this->_getCartShippingXml($order);

        $_taxXml = "<tax>" . $order->getTaxAmount() . "</tax>";



        $_requestXml = "<cart>" . $_cartHeaderXml . $_chargesXml . $_shippingMethodXml . $_taxXml . $_requestXml . "</cart>";


        return $_requestXml;
    }

    private function _getRunaDiscount($order) {
        if ($order->getRunaDiscountTotal() == 0) {
            return "";
        }
        $_runaChargeXml = "<charge><module><code>Runa_Promotions</code></module><type>Runa Discount</type>";
        $_runaChargeXml .= "<amount>" . $order->getRunaDiscountTotal() . "</amount></charge>";

        return $_runaChargeXml;
    }

    public function buildCartRequest($addressQuote) {

        $_quote = $addressQuote->getQuote();

        $_requestXml = "";
        foreach ($_quote->getAllItems() as $_item) {
            if (!$_item->getParentItemId()) {
                $attribsToIgnore =
                        array('basket-id' => 'ignore',
                            'ip-address' => 'ignore');

                $_itemXml = '<item>' . $this->getRequestXmlForItem($_item, $attribsToIgnore) . '</item>';
                $_requestXml .= $_itemXml;
            }
        }

        $_requestXml = "<items>" . $_requestXml . "</items>";
        $_cartHeaderXml = "<basket-id>" . $_quote->getId() . "</basket-id>";

        $_helper = mage::helper('runapromotions');
        /* @var $_helper Runa_Promotions_Helper_Data */
        $_cartHeaderXml .= "<page-type>" . $_helper->getRouteKey() . "</page-type>";
        $_cartHeaderXml .= "<ip-address>" . $_quote->getRemoteIp() . "</ip-address>";
        $_cartHeaderXml .= "<total>" . $addressQuote->getGrandTotal() . "</total>";

        $_discountCharge = $this->_getCartDicountCharge($addressQuote);

        $_shippingCharge = $this->_getCartShippingChange($addressQuote);
        $_taxCharge = $this->_getTaxCharge($addressQuote);

        $_chargesXml = $_discountCharge . $_shippingCharge . $_taxCharge;

        if ($_chargesXml) {
            $_chargesXml = "<charges>" . $_chargesXml . '</charges>';
        }

        $_requestXml = "<cart>" . $_cartHeaderXml
                . $_chargesXml
                . $this->_getCartShippingXml($addressQuote)
                . $this->_getCartTaxXml($addressQuote)
                . $_requestXml
                . "</cart>";

        return $_requestXml;
    }

    private function _getTaxCharge($addressQuote) {

        $_taxCharge = $addressQuote->getTaxAmount();

        if ($_taxCharge == 0) {
            return "";
        }

        $_taxChargeXml = "<charge><module><code>Tax</code></module><type>Tax</type>";
        $_taxChargeXml .= "<amount>$_taxCharge</amount></charge>";

        return $_taxChargeXml;
    }

    private function _getCartShippingXml($addressQuote) {
        $_shippingCharge = $addressQuote->getShippingAmount();
        $_method = $addressQuote->getShippingMethod();
        $_description = $addressQuote->getShippingDescription();

        if ($_shippingCharge == 0) {
            return "";
        }

        $_regionState = $addressQuote->getRegionCode();
        $_zipcode = $addressQuote->getPostcode();
        $_country_id = $addressQuote->getCountryId();
        $_locationXml = "<state>$_regionState</state><zipcode>$_zipcode</zipcode><country>$_country_id</country>";
        $_shippingXml = "<shipping>$_locationXml<method>" . $this->_wrapInCDATA($_description) . "</method><method-code>$_method</method-code><cost>$_shippingCharge</cost></shipping>";

        return $_shippingXml;
    }

    private function _getCartTaxXml($addressQuote) {
        $_taxCharge = $addressQuote->getTaxAmount();
        $_taxChargeXml = "";

        if ($_taxCharge == 0) {
            return "";
        }

        $_taxChargeXml.= "<tax>$_taxCharge</tax>";

        return $_taxChargeXml;
    }

    private function _getCartShippingChange($addressQuote) {
        $_shippingCharge = $addressQuote->getShippingAmount();

        if ($_shippingCharge == 0) {
            return "";
        }

        $_shipingChargeXml = "<charge><module><code>Shipping</code></module><type>Shipping</type>";
        $_shipingChargeXml .= "<amount>$_shippingCharge</amount></charge>";

        return $_shipingChargeXml;
    }

    private function _getCartDicountCharge($addressQuote, $context=null) {
        $_couponDiscount = $addressQuote->getDiscountAmount();

        if ($context == self::CONTEXT_ORDER && $_couponDiscount != 0) {
            $_couponDiscount = abs($_couponDiscount);
            $_runaTotalDiscount = abs($addressQuote->getRunaDiscountTotal() + $addressQuote->getRunaDiscountShipping());
            $_couponDiscount = $_couponDiscount - $_runaTotalDiscount;

            if ($_couponDiscount) {
                $_couponDiscount = -$_couponDiscount;
            }
        }

        if ($_couponDiscount == 0 || !$addressQuote->getCouponCode()) {
            return '';
        }
        $_discountChangeXml = "<charge><module><code>ShoppingCart_Promotions</code></module><type>COUPON</type>";
        $_discountChangeXml .= "<amount>$_couponDiscount</amount></charge>";

        return $_discountChangeXml;
    }

    private function _buildRequest($item, $overRideAttribs = null) {

        $_quote = mage::getModel('checkout/session')->getQuote();
        /* @var $_quote Mage_Sales_Model_Quote */

        $_product = $item->getProduct();

        $_unitPrice = $item->getPrice();
        $_listPrice = $_product->getPrice();
        $_attributesXml = $this->_getItemAttributeXml($item);
        $_unitPrice = $_unitPrice - $this->_itemAttributePriceTotal; //subtracting atrribute prices

        $_itemData = array();

        if (!$_quote->getId()) {
            mage::getModel('checkout/session')->setQuoteId($item->getQuoteId());
        }

        $_itemData['basket-id'] = $item->getQuoteId();
        $_itemData['item-id'] = $item->getId();
        $_itemData['ip-address'] = $_quote->getRemoteIp();
        $_itemData['product-id'] = $_product->getId();
        $_itemData['name'] = $this->_wrapInCDATA($_product->getName());
        $_itemData['unit-price'] = $_unitPrice;
        $_itemData['list-price'] = $_listPrice;
        $_itemData['quantity'] = $item->getQty();

        $_qty = $item->getQty();

        $_requestXml = '';
        foreach ($_itemData as $_tag => $_tagVal) {

            if (is_array($overRideAttribs) && array_key_exists($_tag, $overRideAttribs)) {
                $_tagVal = $overRideAttribs[$_tag];
                //ignore any attributes as needed
                if ($overRideAttribs[$_tag] == 'ignore') {
                    continue;
                }
            }


            $_requestXml = $_requestXml . "<$_tag>" . $_tagVal . "</$_tag>";
        }



        if ($_attributesXml) {
            $_requestXml = $_requestXml . "<attributes>" . $_attributesXml . "</attributes>";
        }

        $_requestXml.= $this->_getCategoriesXml($_product);

        return $_requestXml;
    }

    private function _getCategoriesXml($product) {

        $_catig_ids = $product->getCategoryIds();

        $_catig_xml = "";
        foreach ($_catig_ids as $_id) {
            $_catig_xml.="<category-code>$_id</category-code>";
        }

        if ($_catig_xml) {
            return "<categories>$_catig_xml</categories>";
        }

        return "";
    }

    private function _getItemAttributeXml(Mage_Sales_Model_Quote_Item $item) {

        $this->_itemAttributePriceTotal = 0;
        $_attributesXml = "";
        foreach ($this->_getQuoteItemAttributes($item) as $_attrib) {

            if (!$_attrib) {
                continue;
            }
            $_attributesXml.="<attribute><name>" . $_attrib->getName() . "</name><value>" . $_attrib->getValue() . "</value>";
            $_attributesXml.= "<price>" . $_attrib->getPrice() . "</price><list-price>" . $_attrib->getPrice() . "</list-price>";
            $this->_itemAttributePriceTotal = $this->_itemAttributePriceTotal + $_attrib->getPrice();
            $_quantityXml = '';
            if ($_attrib->getQuantity()) {
                $_quantityXml = "<quantity>" . $_attrib->getQuantity() . "</quantity>";
            }
            $_attributesXml.= "$_quantityXml</attribute>";
        }

        return $_attributesXml;
    }

    public function _getQuoteItemAttributes(Mage_Sales_Model_Quote_Item $item) {
        $options = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                //getting the item options
                if ($option = $item->getProduct()->getOptionById($optionId)) {

                    $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setQuoteItemOption($quoteItemOption);

                    $_optionValues = $option->getValues();
                    $_optSelected = false;
                    foreach ($_optionValues as $_value) {
                        if ($_value->getId() == $quoteItemOption->getValue()) {
                            $_optSelected = $_value;
                        }
                    }

                    //if this option has any meaning then only add it to option list
                    if ($_optSelected) {
                        $_attribute = new Varien_Object();
                        $_attribute->setName($option->getTitle());
                        $_attribute->setValue($_optSelected->getOptionTypeId());
                        $_attribute->setPrice($_optSelected->getPrice());
                        $_attribute->setValueLabel($_optSelected->getDefaultTitle());
                    }

                    $options[] = $_attribute;
                }
            }
        }
        if ($addOptions = $item->getOptionByCode('additional_options')) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $_selectedAttribs = $item->getProduct()->getTypeInstance()->getOrderOptions();
            $_configurable = new Mage_Catalog_Block_Product_View_Type_Configurable();
            $_configurable->setProduct($item->getProduct());
            $_conf = json_decode($_configurable->getJsonConfig());

            foreach ($_selectedAttribs['info_buyRequest']['super_attribute'] as $_atttribId => $_attribVal) {
                $_selectedAttribute = $_conf->attributes->$_atttribId;
                $_attribute = new Varien_Object();
                $_attribute->setName($_selectedAttribute->code);

                foreach ($_selectedAttribute->options as $_selectedOption) {
                    if ($_selectedOption->id == $_attribVal) {
                        $_attribute->setValue($_selectedOption->label);
                        $_attribute->setPrice($_selectedOption->price);
                        $_attribute->setValueLabel($_selectedOption->label);
                        $options[] = $_attribute;
                        continue;
                    }
                }
            }
        }

        if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {

            $_selectedAttribs = $item->getProduct()->getTypeInstance()->getOrderOptions();
            $_optionCollection = $item->getProduct()->getTypeInstance()->getOptionsCollection();

            foreach ($_optionCollection as $_opt) {
                $_optSelected = "";
                if (array_key_exists($_opt->getId(), $_selectedAttribs['bundle_options'])) {
                    $_optSelected = $_selectedAttribs['bundle_options'][$_opt->getId()];
                }

                if (is_array($_optSelected)) {
                    if (isset($_optSelected) && count($_optSelected['value']) == 1) {
                        $_attribute = new Varien_Object();
                        $_attribute->setName($_opt->default_title);
                        /* TODO multi select handling */
                        $_attribute->setValue($_selectedAttribs['info_buyRequest']['bundle_option'][$_opt->getId()]);
                        $_attribute->setPrice($_optSelected['value'][0]['price']);
                        $_attribute->setValueLabel($_optSelected['value'][0]['title']);
                        $_attribute->setQuantity($_optSelected['value'][0]['qty']);
                        $options[] = $_attribute;
                    } elseif (count($_optSelected['value']) > 1) {

                        foreach ($_optSelected['value'] as $_optIndex => $_multiOption) {
                            $_attribute = new Varien_Object();
                            $_attribute->setName($_opt->default_title);
                            /* TODO multi select handling */
                            $_attribute->setValue($_selectedAttribs['info_buyRequest']['bundle_option'][$_opt->getId()][$_optIndex]);
                            $_attribute->setPrice($_multiOption['price']);
                            $_attribute->setValueLabel($_multiOption['title']);
                            $_attribute->setQuantity($_multiOption['qty']);
                            $options[] = $_attribute;
                        }
                    }
                }
            }
        }


        return $options;
    }

    private function _wrapInCDATA($text) {
        return "<![CDATA[$text]]>";
    }

}