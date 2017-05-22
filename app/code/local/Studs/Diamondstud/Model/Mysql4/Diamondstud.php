<?php

class Studs_Diamondstud_Model_Mysql4_Diamondstud extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the diamondstud_id refers to the key field in your database table.
        $this->_init('diamondstud/diamondstud', 'diamondstud_id');
    }
}