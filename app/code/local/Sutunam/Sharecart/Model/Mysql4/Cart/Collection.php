<?php
class Sutunam_Sharecart_Model_Mysql4_Cart_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sharecart/cart');
    }
}