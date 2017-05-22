<?php 

class Angara_Gemstonecolor_Block_Adminhtml_System_Config_Frontend_Category extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		//return 'fdgbfdghdfhghfghfghfghg';
		return $this->getLayout()->createBlock('gemstonecolor/adminhtml_system_config_frontend_categories','gemstone_allowed_categories')->setElement($element)->toHtml();
	}
	   
}