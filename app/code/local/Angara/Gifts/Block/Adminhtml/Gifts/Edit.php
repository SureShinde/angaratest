<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */
 
class Angara_Gifts_Block_Adminhtml_Gifts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'gifts';
        $this->_controller = 'adminhtml_gifts';

        $this->_updateButton('save', 'label', Mage::helper('gifts')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('gifts')->__('Delete Rule'));

        $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                ), -100);


        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('gifts_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'gifts_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'gifts_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

    }

    public function getHeaderText()
    {
        if (Mage::registry('gifts_data') && Mage::registry('gifts_data')->getId()) {
            return Mage::helper('gifts')->__("Edit Rule '%s'", $this->htmlEscape(Mage::registry('gifts_data')->getTitle()));
        } else {
            return Mage::helper('gifts')->__('Add Rule');
        }
    }

}
