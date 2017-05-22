<?php

class Angara_Feeds_Block_Adminhtml_Feeds_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'feeds';
        $this->_controller = 'adminhtml_feeds';
        
        $this->_updateButton('save', 'label', Mage::helper('feeds')->__('Save Feeds'));
        $this->_updateButton('delete', 'label', Mage::helper('feeds')->__('Delete Feeds'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('feeds_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'feeds_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'feeds_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('feeds_data') && Mage::registry('feeds_data')->getId() ) {
            return Mage::helper('feeds')->__("Edit Feeds '%s'", $this->htmlEscape(Mage::registry('feeds_data')->getTitle()));
        } else {
            return Mage::helper('feeds')->__('Add Feeds');
        }
    }
}