<?php

class Angara_Abandoncartmailchimp_Model_Mysql4_Unsubscriber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
		
		parent::_construct();
        $this->_init('abandoncartmailchimp/unsubscriber');
    }
}