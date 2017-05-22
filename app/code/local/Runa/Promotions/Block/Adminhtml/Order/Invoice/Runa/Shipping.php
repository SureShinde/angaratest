<?php

class Runa_Promotions_Block_Adminhtml_Order_Discount_Runa_Shipping extends Mage_Adminhtml_Block_Sales_Order_Totals_Item {

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    public function initTotals()
    {

        $_source = $this->getSource();

        $_runaDiscountAmount = $_source->getRunaDiscountShipping();

        if ($_runaDiscountAmount == 0)
        {
            return;
        }

        $discountText = Mage::helper('runapromotions')->getShippingDiscountLabelPrefix() . ' ' . $_source->getShippingDescription();

        $_runaDiscount = new Varien_Object(array(
                    'code' => $this->getNameInLayout(),
                    'area' => $this->getDisplayArea(),
                    'strong' => 0,
                    'label' => "<b id='runa_special_shipping_discount' class=\"runa_special_shipping_discount\" style=\"color:green;\">" . Mage::helper('sales')->__("( $discountText  )") . "</b>",
                    'value' => $_runaDiscountAmount
                ));


        if ($this->getBeforeCondition())
        {
            $this->getParentBlock()->addTotalBefore($_runaDiscount, $this->getBeforeCondition());
        } else
        {
            $this->getParentBlock()->addTotal($_runaDiscount, $this->getAfterCondition());
        }
        return $this;
    }

}
