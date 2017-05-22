<?php
class Angara_Feedback_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form_data = new Varien_Object();
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("category_form", array("legend"=>Mage::helper("feedback")->__("Item information")));

		
		$fieldset->addField('parent_category_id', 'select', array(
			'label'     => Mage::helper('feedback')->__('Parent Category'),
			'values'   	=> Angara_Feedback_Block_Adminhtml_Category_Grid::getValueArray2(),
			'name' 		=> 'parent_category_id',
		));
		
		$fieldset->addField("name", "text", array(
			"label" 	=> Mage::helper("feedback")->__("Category Name"),					
			"class" 	=> "required-entry",
			"required" 	=> true,
			"name" 		=> "name",
		));
	
		$fieldset->addField("description", "textarea", array(
			"label" 	=> Mage::helper("feedback")->__("Description"),					
			//"class" 	=> "required-entry",
			//"required" 	=> true,
			"name" 		=> "description",
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => 	Mage::helper('feedback')->__('Status'),
			'values'   	=> 	Angara_Feedback_Block_Adminhtml_Category_Grid::getValueArray6(),
			'name' 		=> 	'status',					
			"class" 	=> 	"required-entry",
			"required" 	=> 	true,
		));

		if (Mage::getSingleton("adminhtml/session")->getFeedbackData()) {
			$form->setValues(Mage::getSingleton("adminhtml/session")->getFeedbackData());
			Mage::getSingleton("adminhtml/session")->setFeedbackData(null);
		} elseif(Mage::registry("category_data")) {
			$form->setValues(Mage::registry("category_data")->getData());
		}
		return parent::_prepareForm();
	}
}
