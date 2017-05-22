<?php

class Apptha_Popularproduct_Block_Adminhtml_Popularproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'popularproduct';
        $this->_controller = 'adminhtml_popularproduct';
        
        $this->_updateButton('save', 'label', Mage::helper('popularproduct')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('popularproduct')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('popularproduct_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'popularproduct_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'popularproduct_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('popularproduct_data') && Mage::registry('popularproduct_data')->getId() ) {
            return Mage::helper('popularproduct')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('popularproduct_data')->getTitle()));
        } else {
            return Mage::helper('popularproduct')->__('Add Item');
        }
    }
}