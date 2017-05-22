<?php

class Runa_Promotions_Model_Events_Checkout_Google extends Varien_Object {

    public function setDiscounts($event) {

        $salesOrderEntiry = $event->getQuote();
        $discountItem = $event->getDiscountItem();

        $_runaDiscountTotal = $salesOrderEntiry->getRunaDiscountTotal();
        $_runaShippingDiscountTotal = $salesOrderEntiry->getRunaDiscountShipping();

        $_helper = mage::helper('runapromotions');
        $_discountLabel = $_helper->getDiscountLabel();
        $_discountShippingLabel = 'Shipping Offer';

        if (abs($_runaDiscountTotal) > 0) {
            $discountItem->setPrice($discountItem->getPrice() + $_runaDiscountTotal);
            $discountItem->setDescription($_helper->__($_discountLabel));
        }

        if (abs($_runaShippingDiscountTotal) > 0) {
            $discountItem->setPrice($discountItem->getPrice() + $_runaShippingDiscountTotal);
            $discountItem->setDescription($_helper->__($_discountLabel));
        }


        return;
    }

}