<?php

class Apptha_Popularproduct_Block_Adminhtml_Popularproduct_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('popularproduct_form', array('legend'=>Mage::helper('popularproduct')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('popularproduct')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('popularproduct')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('popularproduct')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('popularproduct')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('popularproduct')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('popularproduct')->__('Content'),
          'title'     => Mage::helper('popularproduct')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPopularproductData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPopularproductData());
          Mage::getSingleton('adminhtml/session')->setPopularproductData(null);
      } elseif ( Mage::registry('popularproduct_data') ) {
          $form->setValues(Mage::registry('popularproduct_data')->getData());
      }
      return parent::_prepareForm();
  }
}