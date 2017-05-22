<?php
class Angara_Ship_Model_Mysql4_Ship extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("ship/ship", "ship_id");
    }
}