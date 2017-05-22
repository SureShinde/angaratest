<?php
class Angara_Productbase_Model_Mysql4_Metal extends Mage_Core_Model_Mysql4_Abstract 
{
    public function _construct()
    {
       $this->_init('productbase/metal', 'id');
    }
}