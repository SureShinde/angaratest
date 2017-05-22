<?php
  class Angara_Digger_Block_Adminhtml_Synonym_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('synonym_tabs');
          $this->setDestElementId('edit_form');
          $this->setTitle('Information synonym');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'synonym Information',
                   'title' => 'synonym Information',
                   'content' => $this->getLayout()
     ->createBlock('digger/adminhtml_synonym_edit_tabs_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}
?>