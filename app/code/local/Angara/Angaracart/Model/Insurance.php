<?php

class Angara_Angaracart_Model_Insurance extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
		//echo "hiii";exit;
		parent::_construct();
        $this->_init('angaracart/insurance');
		
    }
}