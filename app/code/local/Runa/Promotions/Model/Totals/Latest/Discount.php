<?php

class Runa_Promotions_Model_Totals_Latest_Discount extends Mage_SalesRule_Model_Quote_Discount {

    /**
     * Add discount total information to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            //adjusting the discount to tot real coupon discount only
            $_runaTotalDiscount = $address->getQuote()->runa_discount_shipping + $address->getQuote()->runa_discount_total;
            $amount = abs($amount) - abs($_runaTotalDiscount);
            $amount =-$amount ;
        }
        if ($amount != 0) {
            $description = $address->getDiscountDescription();
            if ($description) {
                $title = Mage::helper('sales')->__('Discount (%s)', $description);
            } else {
                $title = Mage::helper('sales')->__('Discount');
            }
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));
        }
        return $this;
    }

}