<?php

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Runa_Promotions_Model_Config_Source_Modulemode
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'PROD', 'label'=>Mage::helper('adminhtml')->__('Production')),
            array('value' => 'STAGE', 'label'=>Mage::helper('adminhtml')->__('Staging')),
            array('value' => 'DEV', 'label'=>Mage::helper('adminhtml')->__('Sand-Box / Development')),
            array('value' => 'CUSTOM_DOMAIN', 'label'=>Mage::helper('adminhtml')->__('Custom Domain')),
        );
    }

}
