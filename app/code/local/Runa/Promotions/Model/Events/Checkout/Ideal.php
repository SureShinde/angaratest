<?php

class Runa_Promotions_Model_Events_Checkout_Ideal extends Varien_Object {

    public function setDiscounts($event) {
        
        $idealCartFields = $event->getIdealCart()->getFields();
        $salesOrderEntiry = $event->getIdealCart()->getSalesEntity();

        $_runaDiscountTotal = $salesOrderEntiry->getRunaDiscountTotal();
        $_runaShippingDiscountTotal = $salesOrderEntiry->getRunaDiscountShipping();

        $_helper = mage::helper('runapromotions');
        $_discountLabel = $_helper->getDiscountLabel();
        $_discountShippingLabel = 'Shipping Offer';

        $totalItemIncrement = $event->getIdealCart()->getTotalItemIncrement();

        if (abs($_runaDiscountTotal) > 0) {
            $idealCartFields = array_merge($idealCartFields, array(
                "itemNumber" . $totalItemIncrement => $_discountLabel,
                "itemDescription" . $totalItemIncrement => '',
                "itemQuantity" . $totalItemIncrement => 1,
                "itemPrice" . $totalItemIncrement => $_runaDiscountTotal * 100
                    ));
            $totalItemIncrement++;
        }

        if (abs($_runaShippingDiscountTotal) > 0) {
            $idealCartFields = array_merge($idealCartFields, array(
                "itemNumber" . $totalItemIncrement => $_discountShippingLabel ,
                "itemDescription" . $totalItemIncrement => '',
                "itemQuantity" . $totalItemIncrement => 1,
                "itemPrice" . $totalItemIncrement => $_runaShippingDiscountTotal * 100
                    ));
            $totalItemIncrement++;
        }

        $event->getIdealCart()->setFields($idealCartFields);
        $event->getIdealCart()->getTotalItemIncrement($totalItemIncrement);

        return;
    }

}