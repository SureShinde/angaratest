<?php

class Angara_Abandoncartmailchimp_Model_Mysql4_Unsubscriber extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
		
		
        $this->_init('abandoncartmailchimp/unsubscriber', 'mailchimp_id');
    }
}