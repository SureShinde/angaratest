<?php

class Runa_Promotions_Model_Totals_Runa_Discount_Shipping extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Add grand total information to adderess
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Grand
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {

        if ($address->getSubtotal() > 0 && $address->getQuote()->runa_discount_shipping < 0)
        {

            $discountText = Mage::helper('runapromotions')->getShippingDiscountLabelPrefix() . ' ' . $address->getShippingDescription();

            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => "<b id='runa_special_shipping_discount' class=\"runa_special_shipping_discount\" style=\"color:green;\">" . Mage::helper('sales')->__("( $discountText  )") . "</b>",
                'value' => $address->getQuote()->runa_discount_shipping
            ));
        }

        return $this;
    }

}
