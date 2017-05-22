<?php
class Angara_Productbase_Block_Adminhtml_Stones_Shapes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'productbase';
        $this->_controller = 'adminhtml_stones_shapes';
        
        $this->_updateButton('save', 'label', $this->__('Save Shape'));
        $this->_updateButton('save', 'onclick', 'save(this)');
        $this->_updateButton('delete', 'label', $this->__('Delete Shape'));
        
		$this->_formScripts[] = "
function setRequired(elementId, required) {
    element = $(elementId);
    if(required)
        element.addClassName('required-entry');
    else
        element.removeClassName('required-entry');

    return element;
}

function save(saveButton) {
    if(editForm.submit())
        saveButton.disabled = true;
}
";
    }

    public function getHeaderText()
    {
        $data = Mage::registry('item_data');
        if ( $data && isset($data['id']) && $data['id'] ) 
            return $this->__("Edit Shape ID: '%s'", $this->htmlEscape($data['id']));
        else return $this->__('Add Shape');
    }
}
