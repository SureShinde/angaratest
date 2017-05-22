<?php

class Apptha_Popularproduct_Block_Adminhtml_Popularproduct_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('popularproduct_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('popularproduct')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('popularproduct')->__('Item Information'),
          'title'     => Mage::helper('popularproduct')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('popularproduct/adminhtml_popularproduct_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}