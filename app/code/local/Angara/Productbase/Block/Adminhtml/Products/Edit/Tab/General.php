<?php
class Angara_Productbase_Block_Adminhtml_Products_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() 
    {
        $data = Mage::registry('product_data');
		
		if (is_object($data)) $data = $data->getData();

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend' => $this->__('Product Pricing Data')));
		
		# sku field
        $fieldset->addField('sku', 'text', array(
                'label'    => $this->__('SKU'),
                'name'     => 'sku',
                'note'   => $this->__('This SKU must match with the existing product\'s SKU.'),
				'required' => true,
            ));
			
			//'avg_metal_weight' => string '1.5' (length=3)
      		//'finding_14kgold' => string '45' (length=2)
			
		$fieldset->addField('avg_metal_weight', 'text', array(
                'label'    => $this->__('Avg. Metal Weight'),
                'name'     => 'avg_metal_weight',
                'note'   => $this->__('Average metal weight of product in grams.'),
				'required' => true,
            ));
			
		$fieldset->addField('finding_14kgold', 'text', array(
                'label'    => $this->__('14k Gold Finding Cost'),
                'name'     => 'finding_14kgold',
                'note'   => $this->__('Finding cost in USD if the product was 14k Gold.'),
				'required' => true,
            ));

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}