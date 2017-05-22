<?php
class Angara_Digger_Model_Mysql4_Grammar_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()     {
	
         parent::_construct();
         $this->_init('digger/grammar');
		 
		 
     }
}
?>