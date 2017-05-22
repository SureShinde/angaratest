<?php

class Angara_Feeds_Block_Adminhtml_Feeds_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('feeds_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('feeds')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('feeds')->__('General Information'),
          'title'     => Mage::helper('feeds')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('feeds/adminhtml_feeds_edit_tab_form')->toHtml(),
      ));
	  
	  $this->addTab('export_section',array(
			'label'		=> Mage::helper('feeds')->__('Export'),
			'title'     => Mage::helper('feeds')->__('Export'),
			//'url'       => $this->getUrl('*/*/exportCsv', array('_current' => true)),
            'content'   => $this->getLayout()->createBlock('feeds/adminhtml_exportfeeds')->setTemplate('feeds/form.phtml')->toHtml(),
	  ));
     
      return parent::_beforeToHtml();
  }
}