<?php

class Angara_Angaracart_Model_Mysql4_Insurance extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
		// Note that the insurance_id refers to the key field in your database table.
		
        $this->_init('angaracart/insurance', 'insurance_id');
    }
}