<?php
class Angara_CustomerStory_Block_Adminhtml_Story_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("customerstory_form", array("legend"=>Mage::helper("customerstory")->__("Item information")));
		
		$fieldset->addField("order_id", "text", array(
		"label" => Mage::helper("customerstory")->__("Order Number"),					
		"class" => "required-entry",
		"required" => true,
		"disabled" => true,
		"name" => "order_id",
		));
					
		 $fieldset->addField('is_approved', 'select', array(
		'label'     => Mage::helper('customerstory')->__('Approved'),
		'values'   => Angara_CustomerStory_Block_Adminhtml_Story_Grid::getValueArray3(),
		'name' => 'is_approved',					
		"class" => "required-entry",
		"required" => true,
		));

		if (Mage::getSingleton("adminhtml/session")->getStoryData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getStoryData());
			Mage::getSingleton("adminhtml/session")->setStoryData(null);
		} 
		elseif(Mage::registry("story_data")) {
			$form->setValues(Mage::registry("story_data")->getData());
		}
		return parent::_prepareForm();
	}
}?>