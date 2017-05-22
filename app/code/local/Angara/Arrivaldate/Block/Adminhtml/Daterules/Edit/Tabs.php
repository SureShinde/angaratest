<?php
  class Angara_Arrivaldate_Block_Adminhtml_Daterules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('daterules_tabs');
          $this->setDestElementId('edit_form');
          $this->setTitle('Daterules');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'Date location',
                   'title' => 'Date location',
                   'content' => $this->getLayout()
     ->createBlock('arrivaldate/adminhtml_daterules_edit_tabs_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}
?>