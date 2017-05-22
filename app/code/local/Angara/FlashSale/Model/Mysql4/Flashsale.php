<?php
class Angara_FlashSale_Model_Mysql4_Flashsale extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("flashsale/flashsale", "flashsale_id");
    }
}