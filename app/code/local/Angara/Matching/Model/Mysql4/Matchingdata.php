<?php

class Angara_Matching_Model_Mysql4_Matchingdata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
		// Note that the abandoncartmailchimp_id refers to the key field in your database table.
		
        $this->_init('matching/matchingdata', 'matching_id');
    }
}