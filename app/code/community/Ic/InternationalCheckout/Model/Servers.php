<?php 
class Ic_InternationalCheckout_Model_Servers {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Sandbox')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Live')),
        );
    }
}