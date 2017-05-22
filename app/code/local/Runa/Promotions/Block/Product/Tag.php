<?php

class Runa_Promotions_Block_Product_Tag extends Mage_Core_Block_Abstract {

    public function _toHtml()
    {
        Mage::getSingleton('runapromotions/log_debug')->info('Runa_Promotions_Block_Product_Simple::_toHtml() called');

        $product = $this->getProduct();

        return $this->helper('runapromotions')->getProductCommandSpan($product);
    }

}