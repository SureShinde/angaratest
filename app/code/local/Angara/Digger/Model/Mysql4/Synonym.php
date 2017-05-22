<?php
class Angara_Digger_Model_Mysql4_Synonym extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
        // parent::_construct();
         $this->_init('digger/synonym','id');
		 
     }
}
?>