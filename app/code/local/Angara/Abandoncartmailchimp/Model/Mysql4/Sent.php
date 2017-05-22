<?php

class Angara_Abandoncartmailchimp_Model_Mysql4_Sent extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
		// Note that the abandoncartmailchimp_id refers to the key field in your database table.
		
        $this->_init('abandoncartmailchimp/sent', 'sent_id');
    }
}