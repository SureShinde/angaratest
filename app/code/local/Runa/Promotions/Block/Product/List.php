<?php

class Runa_Promotions_Block_Product_List extends Mage_Core_Block_Abstract {

    public function _toHtml()
    {
        Mage::getSingleton('runapromotions/log_debug')->info('Runa_Promotions_Block_Product_List::_toHtml() called');

        $_productCollection = $this->getProductCollection();

        $helper = $this->helper('runapromotions');
        $html = '';
        foreach ($_productCollection as $_product)
        {
            $html .= $helper->getProductCommandSpan($_product, Runa_Promotions_Helper_Data::TYPE_AGGREGATE);
        }

        return $html;
    }

    

}