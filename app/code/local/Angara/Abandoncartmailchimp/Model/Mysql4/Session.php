<?php
class Angara_Abandoncartmailchimp_Model_Mysql4_Session extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
        //parent::_construct();
         $this->_init('abandoncartmailchimp/session','mailchimpsession_id');
		 
     }
}
?>