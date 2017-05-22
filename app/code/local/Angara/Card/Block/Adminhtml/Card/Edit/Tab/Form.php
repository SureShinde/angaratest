<?php
class Angara_Card_Block_Adminhtml_Card_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("card_form", array("legend"=>Mage::helper("card")->__("Item information")));

				
						$fieldset->addField("customer_name", "text", array(
						"label" => Mage::helper("card")->__("Customer Name"),					
						"class" => "required-entry validate-name",
						"required" => true,
						"name" => "customer_name",
						));
					
						$fieldset->addField("customer_email", "text", array(
						"label" => Mage::helper("card")->__("Customer Email"),					
						"class" => "required-entry validate-email",
						"required" => true,
						"name" => "customer_email",
						));
									
						$fieldset->addField('c_type', 'select', array(
						'label' => Mage::helper('card')->__('Card Type'),
						'values' => Angara_Card_Block_Adminhtml_Card_Grid::getValueArray2(),
						'name' => 'c_type',				
						"class" => "required-entry validate-select validate-cc-type-value validate-cc-type",
						"required" => true,
						));
						
						$fieldset->addField("c_number", "text", array(
						"label" => Mage::helper("card")->__("Card Number"),					
						"class" => "required-entry validate-number validate-cc-number",
						"required" => true,
						"name" => "c_number",
						));
					
						$fieldset->addField("c_exp_year", "select", array(
						"label" => Mage::helper("card")->__("Expiry Year"),					
						"class" => "required-entry validate-select validate-year-value",
						'values'   => Angara_Card_Block_Adminhtml_Card_Grid::getValueArray4(),
						"required" => true,
						"name" => "c_exp_year",
						));
					
						$fieldset->addField("c_exp_month", "select", array(
						"label" => Mage::helper("card")->__("Expiry Month"),					
						"class" => "required-entry validate-select validate-month-value",
						'values'   => Angara_Card_Block_Adminhtml_Card_Grid::getValueArray3(),
						"required" => true,
						"name" => "c_exp_month",
						));
					
						$fieldset->addField("c_cvv", "text", array(
						"label" => Mage::helper("card")->__("CVV"),					
						"class" => "required-entry validate-number validate-length-cvv validate-cc-cvn",
						"required" => true,
						"name" => "c_cvv",
						));
						
						$fieldset->addField("order_id", "text", array(
						"label" => Mage::helper("card")->__("Order Number"),					
						"class" => "validate-number",
						"required" => false,
						"name" => "order_id",
						));
					
						$fieldset->addField("comments", "textarea", array(
						"label" => Mage::helper("card")->__("Comment"),
						"name" => "comments",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCardData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCardData());
					Mage::getSingleton("adminhtml/session")->setCardData(null);
				} 
				elseif(Mage::registry("card_data")) {
				    $form->setValues(Mage::registry("card_data")->getData());
				}
				return parent::_prepareForm();
		}
}
