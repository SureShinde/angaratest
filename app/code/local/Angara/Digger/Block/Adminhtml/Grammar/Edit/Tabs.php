<?php
  class Angara_Digger_Block_Adminhtml_Grammar_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('grammar_tabs');
          $this->setDestElementId('edit_form');
          $this->setTitle('Grammar');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'Grammar Information',
                   'title' => 'Grammar Information',
                   'content' => $this->getLayout()
     ->createBlock('digger/adminhtml_grammar_edit_tabs_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}
?>