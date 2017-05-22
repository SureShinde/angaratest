<?php
class Angara_Digger_Block_Adminhtml_Grammar_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('grammar_form',
                                       array('legend'=>'grammar information'));
        $fieldset->addField('grammar_rule', 'text',
                       array(
                          'label' => 'Grammar Rule',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'grammar_rule',
                    ));
					
				
       
          
 if ( Mage::registry('grammar_data') )
 {
    $form->setValues(Mage::registry('grammar_data')->getData());
  }
  return parent::_prepareForm();
 }
}
?>