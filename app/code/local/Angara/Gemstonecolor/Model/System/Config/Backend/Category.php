<?php  

class Angara_Gemstonecolor_Model_System_Config_Backend_Category extends Mage_Core_Model_Config_Data
{
    public function save(){
    	
		if($value=$this->getValue()) 
		{
    		$value=implode(',',array_unique(explode(',',$this->getValue())));
			$this->setValue($value);
		}
    	return parent::save();
    }
}