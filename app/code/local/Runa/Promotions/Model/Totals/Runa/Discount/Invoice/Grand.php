<?php

class Runa_Promotions_Model_Totals_Runa_Discount_Invoice_Grand  extends Mage_Sales_Model_Order_Invoice_Total_Abstract {

    /**
     * Add grand total information to adderess
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getRunaDiscountTotal() + $invoice->getRunaDiscountShipping());
        return $this;
    }

}
