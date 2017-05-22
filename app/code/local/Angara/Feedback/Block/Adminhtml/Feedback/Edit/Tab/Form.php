<?php
class Angara_Feedback_Block_Adminhtml_Feedback_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("feedback_form", array("legend"=>Mage::helper("feedback")->__("Item information")));

		
		$fieldset->addField("email", "text", array(
			"label" 	=> Mage::helper("feedback")->__("Email"),					
			"class" 	=> "required-entry",
			"required" 	=> true,
			"name" 		=> "email",
		));
		
		$fieldset->addField("contact_number", "text", array(
			"label" 	=> Mage::helper("feedback")->__("Contact Number"),					
			//"class" 	=> "required-entry",
			//"required" 	=> true,
			"name" 		=> "contact_number",
		));
					
		$field = 	$fieldset->addField('category_id', 'select', array(
			'label'     => Mage::helper('feedback')->__('Category'),
			'values'   	=> Angara_Feedback_Block_Adminhtml_Feedback_Grid::getValueArray1(),
			'name' 		=> 'category_id',					
			"class" 	=> "required-entry",
			"required" 	=> true,
			//'onchange'	=>	'fireAjax(this)',
		));	
		
		/*$field->setAfterElementHtml('<script>
            function fireAjax(checkboxElem){
				//alert("test");
				console.log("fire");
                if(checkboxElem.checked){
                    $("target_element").disabled=true;
                }
                else{
                    $("target_element").disabled=false;
                }
            }
        </script>');*/
		
		
		$fieldset->addField('sub_category_id', 'select', array(
			'label'     => Mage::helper('feedback')->__('Sub Category'),
			'values'   	=> Angara_Feedback_Block_Adminhtml_Feedback_Grid::getValueArray2(),
			'name' 		=> 'sub_category_id',
		));
		
		$fieldset->addField("message", "textarea", array(
			"label" 	=> Mage::helper("feedback")->__("Message"),					
			"class" 	=> "required-entry",
			"required" 	=> true,
			"name" 		=> "message",
		));
					
		 $fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('feedback')->__('Status'),
			'values'   	=> Angara_Feedback_Block_Adminhtml_Feedback_Grid::getValueArray5(),
			'name' 		=> 'status',					
			"class" 	=> "required-entry",
			"required" 	=> true,
		));

		if (Mage::getSingleton("adminhtml/session")->getFeedbackData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getFeedbackData());
			Mage::getSingleton("adminhtml/session")->setFeedbackData(null);
		} 
		elseif(Mage::registry("feedback_data")) {
			$form->setValues(Mage::registry("feedback_data")->getData());
		}
		return parent::_prepareForm();
	}
}
