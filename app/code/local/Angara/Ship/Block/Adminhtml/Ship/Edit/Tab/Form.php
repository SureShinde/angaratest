<?php
class Angara_Ship_Block_Adminhtml_Ship_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("ship_form", array("legend"=>Mage::helper("ship")->__("Item information")));

		$fieldset->addField("name", "text", array(
			"label" => Mage::helper("ship")->__("Name"),					
			"class" => "required-entry",
			"required" => true,
			"name" => "name",
		));
	
		$fieldset->addField("sort_order", "text", array(
			"label" => Mage::helper("ship")->__("Sort Order"),					
			"class" => "required-entry",
			"required" => true,
			"name" => "sort_order",
		));
					
		 $fieldset->addField('enabled', 'select', array(
			'label'     => Mage::helper('ship')->__('Enabled'),
			'values'   => Angara_Ship_Block_Adminhtml_Ship_Grid::getValueArray3(),
			'name' => 'enabled',					
			"class" => "required-entry",
			"required" => true,
		));

		if (Mage::getSingleton("adminhtml/session")->getShipData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getShipData());
			Mage::getSingleton("adminhtml/session")->setShipData(null);
		} 
		elseif(Mage::registry("ship_data")) {
			$form->setValues(Mage::registry("ship_data")->getData());
		}
		return parent::_prepareForm();
	}
}
