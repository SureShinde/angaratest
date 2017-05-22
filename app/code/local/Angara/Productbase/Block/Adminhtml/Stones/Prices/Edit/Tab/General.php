<?php
class Angara_Productbase_Block_Adminhtml_Stones_Prices_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() 
    {
        $data = Mage::registry('item_data');
		
		if (is_object($data)) $data = $data->getData();

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend' => $this->__('Stone Price Information')));
		
		# sku field
        $fieldset->addField('title', 'text', array(
                'label'    => $this->__('Title'),
                'name'     => 'title',
                'note'   => $this->__('Stone name goes here e.g. Ruby, Sapphire.'),
				'required' => true,
            ));
			
		$fieldset->addField('alias', 'text', array(
                'label'    => $this->__('Alias'),
                'name'     => 'alias',
                'note'   => $this->__('Stone alias used in site e.g. R for Ruby, D for Diamond.'),
				'required' => true,
            ));
			
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}