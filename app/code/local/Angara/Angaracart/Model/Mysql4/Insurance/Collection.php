<?php

class Angara_Angaracart_Model_Mysql4_Insurance_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
		
		parent::_construct();
        $this->_init('angaracart/insurance');
    }
}