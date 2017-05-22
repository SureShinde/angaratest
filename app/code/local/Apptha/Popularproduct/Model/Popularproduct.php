<?php

class Apptha_Popularproduct_Model_Popularproduct extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('popularproduct/popularproduct');
    }
}