<?php

class Angara_Abandoncart_Model_Abandoncart extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
		//echo "hiii";exit;
		parent::_construct();
        $this->_init('abandoncart/abandoncart');
		
    }
}