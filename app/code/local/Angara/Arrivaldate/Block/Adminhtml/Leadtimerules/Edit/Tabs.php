<?php
  class Angara_Arrivaldate_Block_Adminhtml_Leadtimerules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('leadtimerules_tabs');
          $this->setDestElementId('edit_form');
          $this->setTitle('Information Leadtime rules');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'leadtime Information',
                   'title' => 'leadtime Information',
                   'content' => $this->getLayout()
     ->createBlock('arrivaldate/adminhtml_leadtimerules_edit_tabs_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}
?>