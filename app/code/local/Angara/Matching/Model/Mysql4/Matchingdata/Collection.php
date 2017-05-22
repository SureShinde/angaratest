<?php

class Angara_Matching_Model_Mysql4_Matchingdata_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
		
		parent::_construct();
        $this->_init('matching/matchingdata');
    }
}