<?php
class Angara_Promotions_Model_Mysql4_Coupon extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("promotions/coupon", "id");
    }
}