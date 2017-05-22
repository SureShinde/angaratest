<?php

class Runa_Promotions_Model_Events_Cart_View extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * webservice client for tuna
     * @var Runa_Promotions_Model_Events_Processor
     */
    private $_runaClient;

    /**
     *
     * @var Mage_Sales_Model_Quote
     */
    private $_quote;

    /**
     * Collect grand total address amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Grand
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {

        if ($address->getSubtotal() > 0) {
            $_discountOfCart = 0;
            $_discountOnShipping = 0;
            $this->_quote = $address->getQuote();
            $this->_runaClient = mage::getModel('runapromotions/events_processor');

            //checking to see if we need to send the cart now
            if (!$this->_needToSendCart()) {
                return $this;
            }

            //checking to see if the cart send was successfull
            if (!$this->_sendCart($address)) {
                //keeeping the discount for failed requests and if the cart was not updated
                if (!Mage::getSingleton('checkout/session')->getCartWasUpdated()) {
                    $address->setGrandTotal($address->getGrandTotal() + $this->_quote->getRunaDiscountTotal() + $this->_quote->getRunaDiscountShipping());
                }
                return $this;
            }

            //set the needed cart messages
            $this->_setRunaCartMessages();

            //extractiting the discounts
            $_discountOfCart = $this->_getCartRunaTotalDiscount();
            $_discountOnShipping = $this->_getCartRunaShippingDiscount();

            //saving the discounts on the quote
            $this->_quote->setRunaDiscountTotal($_discountOfCart)->save();
            $this->_quote->setRunaDiscountShipping($_discountOnShipping)->save();
            $this->_setRunaItemDiscounts();


            $discountedGrandTotal = $address->getGrandTotal() + $_discountOfCart + $_discountOnShipping;
            //adjusting the grand total based on discount
            $address->setGrandTotal($discountedGrandTotal);
            $address->setBaseGrandTotal($discountedGrandTotal);
            $_totalCartDiscount = abs($address->getDiscountAmount()) + abs($_discountOfCart + $_discountOnShipping);

            $_totalCartDiscount = -$_totalCartDiscount;
            //for magento 1.3.x discounts are positive value
            if (mage::helper('runapromotions')->isMage13X()) {
                $_totalCartDiscount = abs($_totalCartDiscount);
            }

            $address->setDiscountAmount($_totalCartDiscount);
            $address->setBaseDiscountAmount($_totalCartDiscount);
        }
        return $this;
    }

    private function _sendCart(Mage_Sales_Model_Quote_Address $address) {
        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_requestXml = $_requestBuilder->buildCartRequest(
                $address
        );

        //checking for issues with service
        if (!$this->_runaClient->sendCart($_requestXml)) {
            $_discountOfCart = 0;
            $_discountOnShipping = 0;
            $this->_quote->setRunaDiscountTotal($_discountOfCart)->save();
            $this->_quote->setRunaDiscountShipping($_discountOnShipping)->save();

            if (mage::getSingleton('checkout/session')->getCartHasRunaDicount()) {
                mage::getSingleton('checkout/session')->addError(
                        mage::helper('checkout')->__("Sorry, we could not process your discount at this time!")
                );
            }
            return false;
        }

        return true;
    }

    private function _needToSendCart() {
        $routeKey = mage::helper('runapromotions')->getRouteKey();

        $_routsNotToCalculateTotals = array('checkout_cart_delete', 'checkout_cart_updatePost', 'checkout_cart_add');

        //for these routes it not needed to send cart to runa
        if (in_array($routeKey, $_routsNotToCalculateTotals)) {
            return false;
        }

        return true;
    }

    /**
     * sets the cart messages when discount is recieved
     */
    private function _setRunaCartMessages() {
        $_runCartXml = $this->_runaClient->getRunaCartXml();
        //extractiting the discounts
        $_discountOfCart = $this->_getCartRunaTotalDiscount();
        $_discountOnShipping = $this->_getCartRunaShippingDiscount();

        $routeKey = mage::helper('runapromotions')->getRouteKey();

        $_routsToShowMessage = array('checkout_cart_index');

        //show discount message for shipping
        if ($_discountOnShipping && $this->_quote->getRunaDiscountShipping() != $_discountOnShipping) {

            if (in_array($routeKey, $_routsToShowMessage)) {
                mage::getSingleton('checkout/session')->addSuccess(
                        mage::helper('checkout')->__("You have recieved a special discount of %s", '<span id="runa_discount_applied">$' . number_format(-$_discountOnShipping, 2) . '</span>')
                );
            }

            mage::getSingleton('checkout/session')->setCartHasRunaDicount(true);
        }

        //show discount message for cart total
        if ($_discountOfCart && $this->_quote->getRunaDiscountTotal() != $_discountOfCart) {
            if (in_array($routeKey, $_routsToShowMessage)) {
                mage::getSingleton('checkout/session')->addSuccess(
                        mage::helper('checkout')->__("You have recieved a special discount of %s", '<span id="runa_discount_applied">$' . number_format(-$_discountOfCart, 2) . '</span>')
                );
            }

            mage::getSingleton('checkout/session')->setCartHasRunaDicount(true);
        }

        //set hasdiscont flag on the cart
        if (!$_discountOfCart && !$_discountOnShipping) {
            mage::getSingleton('checkout/session')->setCartHasRunaDicount(false);
        }
    }

    /**
     * applies the item level discounts
     */
    private function _setRunaItemDiscounts() {
        $_runCartXml = $this->_runaClient->getRunaCartXml();

        //fetching item level discounts
        $_itemRunaDiscountList = $_runCartXml->items->item;
        $_itemDiscounts = array();
        foreach ($_itemRunaDiscountList as $_itemDiscout) {
            $_itemDiscounts[(int) $_itemDiscout->{'item-id'}] = (float) $_itemDiscout->{'runa-discount'};
        }

        $_settings = mage::getModel('runapromotions/config_settings');
        /* @var $_settings  Runa_Promotions_Model_Config_Settings */

        //applying the discounts on the line items
        foreach ($this->_quote->getAllItems() as $_item) {
            if (array_key_exists($_item->getId(), $_itemDiscounts)) {
                if ($_itemDiscounts[$_item->getId()]) {
                    $_itemDiscountTotal = $_itemDiscounts[$_item->getId()] * $_item->getQty();
                    if ($_settings->showLineItemsDiscounts()) {
                        $_item->addMessage(
                                mage::helper('checkout')->__("Special savings of %s on this item", '$' . $_itemDiscountTotal)
                        );
                    }
                    $_item->setRunaItemDiscountTotal(-$_itemDiscountTotal)->save();
                }
            }
        }
    }

    /**
     * gets the total discount of the cart
     * @return float
     */
    private function _getCartRunaTotalDiscount() {
        $_runCartXml = $this->_runaClient->getRunaCartXml();
        //extractiting the total discount
        $_discountOfCart = -1 * (float) $_runCartXml->{'items'}->{'runa-total-discount'};
        return $_discountOfCart;
    }

    /**
     * get the shipping discount
     * @return float
     */
    private function _getCartRunaShippingDiscount() {
        $_runCartXml = $this->_runaClient->getRunaCartXml();
        //extractiting the total shipping discount
        $_discountOnShipping = -1 * (float) $_runCartXml->{'shipping'}->{'promotion'}->discount;

        return $_discountOnShipping;
    }

}
