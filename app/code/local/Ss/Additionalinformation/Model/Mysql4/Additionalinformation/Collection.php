<?php

class Ss_Additionalinformation_Model_Mysql4_Additionalinformation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('additionalinformation/additionalinformation');
    }
}