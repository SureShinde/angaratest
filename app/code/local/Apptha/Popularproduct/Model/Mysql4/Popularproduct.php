<?php

class Apptha_Popularproduct_Model_Mysql4_Popularproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the popularproduct_id refers to the key field in your database table.
        $this->_init('popularproduct/popularproduct', 'popularproduct_id');
    }
}