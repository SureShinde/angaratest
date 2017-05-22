<?php

class Angara_Feeds_Model_Mysql4_Amazon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
	    parent::_construct();
        $this->_init('feeds/amazon');
    }
}