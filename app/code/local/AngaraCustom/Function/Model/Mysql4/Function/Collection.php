<?php

class AngaraCustom_Function_Model_Mysql4_Function_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('function/cms');
    }
}
