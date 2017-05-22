<?php
/**
 * @name         :  Magento Facebook App
 * @version      :  1.1
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  6 October 2011
 * 
 * */
class Apptha_Facebookapp_Block_Adminhtml_Facebookapp_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('facebookapp_form', array('legend'=>Mage::helper('facebookapp')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('facebookapp')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('facebookapp')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('facebookapp')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('facebookapp')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('facebookapp')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('facebookapp')->__('Content'),
          'title'     => Mage::helper('facebookapp')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFacebookappData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFacebookappData());
          Mage::getSingleton('adminhtml/session')->setFacebookappData(null);
      } elseif ( Mage::registry('facebookapp_data') ) {
          $form->setValues(Mage::registry('facebookapp_data')->getData());
      }
      return parent::_prepareForm();
  }
}