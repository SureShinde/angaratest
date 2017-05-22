<?php
class Angara_Arrivaldate_Block_Adminhtml_Leadtimerules_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('leadtimerules_form',
                                       array('legend'=>'leadtime information'));
        $fieldset->addField('lead_time', 'text',
                       array(
                          'label' => 'Lead Time',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'lead_time',
                    ));
        $fieldset->addField('shipping_method', 'text',
                         array(
                          'label' => 'Shipping Method',
                          'class' => 'required-entry',
                          'required' => true,
                          'name' => 'shipping_method',
                      ));
		$fieldset->addField('arrival_text', 'text',
                         array(
                          'label' => 'Arrival Text',
                          'class' => 'required-entry',
                          'required' => true,
                          'name' => 'arrival_text',
                      ));
          
 if ( Mage::registry('leadtimerules_data') )
 {
    $form->setValues(Mage::registry('leadtimerules_data')->getData());
  }
  return parent::_prepareForm();
 }
}
?>