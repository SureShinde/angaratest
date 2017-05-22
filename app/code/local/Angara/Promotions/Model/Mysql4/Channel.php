<?php
class Angara_Promotions_Model_Mysql4_Channel extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("promotions/channel", "id");
    }
}