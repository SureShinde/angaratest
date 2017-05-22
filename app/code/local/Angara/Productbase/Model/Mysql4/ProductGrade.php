<?php
class Angara_Productbase_Model_Mysql4_ProductGrade extends Mage_Core_Model_Mysql4_Abstract 
{
    public function _construct()
    {
       $this->_init('productbase/productgrade', 'id');
    }
}