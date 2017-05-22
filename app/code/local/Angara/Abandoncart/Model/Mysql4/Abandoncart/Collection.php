<?php

class Angara_Abandoncart_Model_Mysql4_Abandoncart_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
		
		parent::_construct();
        $this->_init('abandoncart/abandoncart');
    }
}