<?php

class Runa_Promotions_Model_Events_Checkout_Paypal extends Varien_Object {
    const PAYPAL_STANDARD = "paypal_standard_redirect";

    public function setDiscounts($event) {

        $paypalCart = $event->getPaypalCart();
        /* @var $paypalCart Mage_Paypal_Model_Cart */
        $salesOrderEntiry = $paypalCart->getSalesEntity();
        $_runaDiscountTotal = $salesOrderEntiry->getRunaDiscountTotal();
        $_runaShippingDiscountTotal = $salesOrderEntiry->getRunaDiscountShipping();

        $_helper = mage::helper('runapromotions');
        $_discountLabel = $_helper->getDiscountLabel();
        $_discountShippingLabel = 'Shipping Offer';

        $routeKey = mage::helper('runapromotions')->getRouteKey();

        if ($routeKey == strtolower(self::PAYPAL_STANDARD)) {
            //this is only for paypalpayments standard
            $totalRunaDiscount = abs($_runaDiscountTotal) + abs($_runaShippingDiscountTotal);
            $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, $totalRunaDiscount);
            return;
        }

        if (abs($_runaDiscountTotal) > 0) {
            $paypalCart->addItem($_discountLabel, 1, $_runaDiscountTotal, 'Special Offer');
        }

        if (abs($_runaShippingDiscountTotal) > 0) {
            $paypalCart->addItem($_discountShippingLabel, 1, $_runaShippingDiscountTotal, 'Special Offer Shipping');
        }


        return;
    }

}