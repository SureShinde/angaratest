<?php

class Angara_Abandoncart_Model_Mysql4_Abandoncart extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
		// Note that the abandoncart_id refers to the key field in your database table.
		
        $this->_init('abandoncart/abandoncart', 'abandoncart_id');
    }
}