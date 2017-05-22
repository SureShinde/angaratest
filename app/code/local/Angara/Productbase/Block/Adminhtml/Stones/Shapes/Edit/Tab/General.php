<?php
class Angara_Productbase_Block_Adminhtml_Stones_Shapes_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() 
    {
        $data = Mage::registry('item_data');
		
		if (is_object($data)) $data = $data->getData();

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend' => $this->__('Shape Information')));
		
		# sku field
        $fieldset->addField('shape', 'text', array(
                'label'    => $this->__('Shape'),
                'name'     => 'shape',
                'note'   => $this->__('Stone shape goes here e.g. Round, Oval.'),
				'required' => true,
            ));
			
		$fieldset->addField('alias', 'text', array(
                'label'    => $this->__('Alias'),
                'name'     => 'alias',
                'note'   => $this->__('Shape alias used in site e.g. R for Round, O for Oval.'),
				'required' => true,
            ));
			
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}