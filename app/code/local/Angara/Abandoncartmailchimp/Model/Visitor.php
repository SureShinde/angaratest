<?php

class Angara_Abandoncartmailchimp_Model_Visitor extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
		//echo "hiii";exit;
		parent::_construct();
        $this->_init('abandoncartmailchimp/visitor');
		
    }
}