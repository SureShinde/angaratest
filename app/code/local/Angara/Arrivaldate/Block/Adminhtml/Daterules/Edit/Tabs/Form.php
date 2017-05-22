<?php
class Angara_Arrivaldate_Block_Adminhtml_Daterules_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('daterules_form',
                                       array('legend'=>'daterules information'));
        $fieldset->addField('location', 'text',
                       array(
                          'label' => 'Location',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'location',
                    ));
		$fieldset->addField('date', 'text',
                       array(
                          'label' => 'forward date',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'date',
                    ));
					
				
       
          
 if ( Mage::registry('daterules_data') )
 {
    $form->setValues(Mage::registry('daterules_data')->getData());
  }
  return parent::_prepareForm();
 }
}
?>