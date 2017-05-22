<?php
class Angara_Recentlysold_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract 
{
    public function _construct()
    {
       $this->_init('recentlysold/item', 'id');
    }
}