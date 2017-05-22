<?php
class Angara_FlashSale_Block_Adminhtml_Flashsale_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form 		= 	new Varien_Data_Form();
		$this->setForm($form);
		$fieldset 	= 	$form->addFieldset("flashsale_form", array("legend"=>Mage::helper("flashsale")->__("Item information")));

		$fieldset->addField("flashsale_name", "text", array(
			"label" 		=> 	Mage::helper("flashsale")->__("Name"),					
			"class" 		=> 	"required-entry",
			"required" 		=> 	true,
			"name" 			=> 	"flashsale_name",
		));
	
		$fieldset->addField("description", "textarea", array(
			"label" 		=> 	Mage::helper("flashsale")->__("Description"),
			"name" 			=> 	"description",
		));
					
		 $fieldset->addField('is_active', 'select', array(
			'label'     	=> 	Mage::helper('flashsale')->__('Enabled'),
			'values'   		=> 	Angara_FlashSale_Block_Adminhtml_Flashsale_Grid::getValueArray2(),
			'name' 			=> 	'is_active',
		));
		
		$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$fieldset->addField('from_date', 'date', array(
			'label'       	=> 	Mage::helper('flashsale')->__('From Date'),
			'name'         	=> 	'from_date',					
			"class" 		=> 	"required-entry",
			"required" 		=> 	true,
			'time' 			=> 	true,
			'image'        	=> 	$this->getSkinUrl('images/grid-cal.gif'),
			'format'       	=> 	$dateFormatIso
		));
		
		/*$fieldset->addField("from_hour", "text", array(
			"label" => Mage::helper("flashsale")->__("From Hour"),					
			"class" => "required-entry",
			"required" => true,
			"name" => "from_hour",
		));*/
	
		$fieldset->addField('to_date', 'date', array(
			'label'        	=> 	Mage::helper('flashsale')->__('To Date'),
			'name'         	=> 	'to_date',					
			"class" 		=> 	"required-entry",
			"required" 		=> 	true,
			'time' 			=> 	true,
			'image'        	=> 	$this->getSkinUrl('images/grid-cal.gif'),
			'format'      	=> 	$dateFormatIso
		));
		
		/*$fieldset->addField("to_hour", "text", array(
			"label" => Mage::helper("flashsale")->__("To Hour"),					
			"class" => "required-entry",
			"required" => true,
			"name" => "to_hour",
		));*/
	
		$fieldset->addField("product_id", "text", array(
			"label" 		=> 	Mage::helper("flashsale")->__("SKU"),					
			"class" 		=> 	"required-entry",
			"required" 		=> 	true,
			"name" 			=> 	"product_id",
		));
			

		if (Mage::getSingleton("adminhtml/session")->getFlashsaleData()) {
			$form->setValues(Mage::getSingleton("adminhtml/session")->getFlashsaleData());
			Mage::getSingleton("adminhtml/session")->setFlashsaleData(null);
		} elseif(Mage::registry("flashsale_data")) {
			$form->setValues(Mage::registry("flashsale_data")->getData());
		}
		return parent::_prepareForm();
	}
}
