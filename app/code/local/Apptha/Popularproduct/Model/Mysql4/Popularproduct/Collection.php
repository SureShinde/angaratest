<?php

class Apptha_Popularproduct_Model_Mysql4_Popularproduct_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('popularproduct/popularproduct');
    }
}