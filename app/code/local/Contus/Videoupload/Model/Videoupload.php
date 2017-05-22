<?php

class Contus_Videoupload_Model_Videoupload extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('videoupload/videoupload');
    }
}