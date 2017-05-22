<?php

class Runa_Promotions_Model_Totals_Runa_Discount_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Add grand total information to adderess
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Grand
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {

        if ($address->getSubtotal() > 0 && $address->getQuote()->runa_discount_total < 0)
        {
             $discountText = Mage::helper('runapromotions')->getDiscountLabel(); 

            if (!$discountText)
            {
               $discountText = "Special Discount";
            }
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => "<b id='runa_special_discount_total' class=\"runa_special_discount_total\" style=\"color:green;\">" . Mage::helper('sales')->__("( $discountText )") . "</b>",
                'value' => $address->getQuote()->runa_discount_total
            ));
        }

        return $this;
    }

}
