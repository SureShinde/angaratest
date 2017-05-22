<?php
class Angara_Digger_Block_Adminhtml_Synonym_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('synonym_form',
                                       array('legend'=>'synonym information'));
        $fieldset->addField('master_keyword', 'text',
                       array(
                          'label' => 'Master Keyword',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'master_keyword',
                    ));
        $fieldset->addField('synonym', 'text',
                         array(
                          'label' => 'Synonym',
                          'class' => 'required-entry',
                          'required' => true,
                          'name' => 'synonym',
                      ));
          
 if ( Mage::registry('synonym_data') )
 {
    $form->setValues(Mage::registry('synonym_data')->getData());
  }
  return parent::_prepareForm();
 }
}
?>