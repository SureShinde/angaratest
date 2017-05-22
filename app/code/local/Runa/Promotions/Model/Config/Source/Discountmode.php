<?php

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Runa_Promotions_Model_Config_Source_Discountmode {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        return array(
            array('value' => '0', 'label' => Mage::helper('adminhtml')->__('Consolidate Discount to Display on Single Line')),
            array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Show Individual Line Item Discounts Per Product')),
        );
    }

}