<?php
class Sutunam_Sharecart_Model_Cart extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('sharecart/cart');
    }
}