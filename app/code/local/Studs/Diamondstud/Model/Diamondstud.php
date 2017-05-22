<?php

class Studs_Diamondstud_Model_Diamondstud extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('diamondstud/diamondstud');
    }
}