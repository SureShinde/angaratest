<?php
class Angara_Digger_Model_Grammar extends Mage_Core_Model_Abstract

{
	

     public function _construct()
     {
	 
         parent::_construct();
         $this->_init('digger/grammar');
		 
     }
	  
	 
	  public function getGrammarRules()
	 {
		  $rule = array(); 		 
		 $this->_init('digger/grammar');
		 $collection = $this->getCollection();	  
		foreach($collection as $entry) 
 			{
  			 $value = $entry->getData(); 
   			array_push($rule, $value['grammar_rule']); 
			
  			}
				
				return $rule;	
		
	     }
  
   
}
?>