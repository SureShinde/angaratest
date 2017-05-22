<?php 

class Angara_Gemstonecolor_Model_System_Config_Source_Category
{
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value' => '0', 'label'=>Mage::helper('gemstonecolor')->__('All Allowed Categories'));
		$options[] = array('value' => '1', 'label'=>Mage::helper('gemstonecolor')->__('Specific Categories'));
		return $options;
		
    }

}