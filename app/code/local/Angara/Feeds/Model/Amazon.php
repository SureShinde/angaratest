<?php

class Angara_Feeds_Model_Amazon extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
		parent::_construct();
        $this->_init('feeds/amazon');
    }
}