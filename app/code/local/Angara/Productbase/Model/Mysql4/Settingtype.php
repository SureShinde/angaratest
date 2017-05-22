<?php
class Angara_Productbase_Model_Mysql4_Settingtype extends Mage_Core_Model_Mysql4_Abstract 
{
    public function _construct()
    {
       $this->_init('productbase/settingtype', 'id');
    }
}