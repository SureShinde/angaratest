<?php
class Angara_Productbase_Block_Adminhtml_Products_Edit extends Mage_Adminhtml_Block_Widget_Form_Container 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'productbase';
        $this->_controller = 'adminhtml_products';
        
        $this->_updateButton('save', 'label', $this->__('Save Data'));
        $this->_updateButton('save', 'onclick', 'save(this)');
        $this->_updateButton('delete', 'label', $this->__('Delete Product Pricing Data'));
        
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
        $data = Mage::registry('product_data');
        if ( $data && isset($data['sku']) && $data['sku'] ) 
            return $this->__("Edit pricing data for '%s'", $this->htmlEscape($data['sku']));
        else return $this->__('Add Product Pricing Data');
    }
}
