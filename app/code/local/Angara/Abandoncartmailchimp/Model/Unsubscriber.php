<?php

class Angara_Abandoncartmailchimp_Model_Unsubscriber extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
		//echo "hiii";exit;
		parent::_construct();
        $this->_init('abandoncartmailchimp/unsubscriber');
		
    }
}