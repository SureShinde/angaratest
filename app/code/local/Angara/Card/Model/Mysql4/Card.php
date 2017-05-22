<?php
class Angara_Card_Model_Mysql4_Card extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("card/card", "id");
    }
}