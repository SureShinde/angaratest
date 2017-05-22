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
class Apptha_Facebookapp_Block_Adminhtml_Facebookapp_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('facebookapp_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('facebookapp')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('facebookapp')->__('Item Information'),
          'title'     => Mage::helper('facebookapp')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('facebookapp/adminhtml_facebookapp_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}