<?php
class Angara_Productbase_Block_Adminhtml_Pricing extends Mage_Adminhtml_Block_Widget_Form_Container 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'productbase';
        $this->_controller = 'adminhtml';
        
		$this->_mode = 'pricing';
		
        $this->_updateButton('save', 'label', $this->__('Create Custom Product'));
        
		$this->_formScripts[] = "
		
function setRequired(elementId, required) {
    element = $(elementId);
    if(required)
        element.addClassName('required-entry');
    else
        element.removeClassName('required-entry');

    return element;
}
";
    }

    public function getHeaderText()
    {
        return $this->__("Angara Custom Order Manager v2.0");
    }
}
