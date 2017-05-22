<?php
class Angara_Promotions_Block_Adminhtml_Coupon_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("promotions_form", array("legend"=>Mage::helper("promotions")->__("Item information")));
				
				$registryCouponData = Mage::registry("coupon_data")->getData();
				if(!$registryCouponData) $registryCouponData['rule_id'] = NULL;

						//	S:VA	
						$fieldset->addField('rule_valid_for', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Rule Valid For'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray10(),
							'name' 			=> 	'rule_valid_for',					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
						));
						//	E:VA
						
						 $fieldset->addField('rule_id', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Rule'),
							'values'  		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray3($registryCouponData['rule_id']),
							'name' 			=> 	'rule_id',					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							'note' 			=> 	'Coupon Codes already used are not available here. please go back and edit them if required.'
						));	

						$fieldset->addField('valid_shipping', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Shipping'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray16(),
							'name' 			=> 	'valid_shipping',					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
						));
						
						 $fieldset->addField('channel_id', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Channel'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray4(),
							'name' 			=> 	'channel_id',					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
						));
						$fieldset->addField("criteria_message", "textarea", array(
							"label" 		=> 	Mage::helper("promotions")->__("Criteria"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name"			=> 	"criteria_message",
						));
					
						$fieldset->addField("criteria_error_message", "textarea", array(
							"label" 		=> 	Mage::helper("promotions")->__("Criteria Error"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name" 			=> 	"criteria_error_message",
						));
					
						$fieldset->addField("terms", "textarea", array(
							"label" 		=> 	Mage::helper("promotions")->__("Terms & Conditions"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name"			=> 	"terms",
						));
					
						$fieldset->addField("priority", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Priority"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name" 			=> 	"priority",
						));
									
						 $fieldset->addField('display_on_frontend', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Allow On Frontend'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray9(),
							'name' 			=> 	'display_on_frontend',					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
						));
						$fieldset->addField("short_description", "textarea", array(
							"label" 		=> 	Mage::helper("promotions")->__("Short Description"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name" 			=> 	"short_description",
						));
					
						$fieldset->addField("long_description", "textarea", array(
							"label" 		=> 	Mage::helper("promotions")->__("Long Description"),					
							"class" 		=> 	"required-entry",
							"required" 		=> 	true,
							"name" 			=> 	"long_description",
						));
							
						$fieldset->addField("min_price_fp1", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Min Price for Free Product 1"),					
							"required" 		=> 	false,
							"name" 			=> 	"min_price_fp1",
						));	
						$fieldset->addField('free_product1_id', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Free Product 1'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray12(),
							'name' 			=> 	'free_product1_id',
						));	
						$fieldset->addField("max_price_fp1", "text", array(
							"label"		 	=> 	Mage::helper("promotions")->__("Max Price for Free Product 1"),					
							"required" 		=> 	false,
							"name" 			=> 	"max_price_fp1",
						));	
							
						$fieldset->addField("min_price_fp2", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Min Price for Free Product 2"),					
							"required" 		=> 	false,
							"name" 			=> 	"min_price_fp2",
						));
						$fieldset->addField('free_product2_id', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Free Product 2'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray13(),
							'name' 			=> 	'free_product2_id',
						));	
						$fieldset->addField("max_price_fp2", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Max Price for Free Product 2"),					
							"required" 		=> 	false,
							"name" 			=> 	"max_price_fp2",
						));
						
						$fieldset->addField("min_price_fp3", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Min Price for Free Product 3"),					
							"required" 		=> 	false,
							"name" 			=> 	"min_price_fp3",
						));	
						$fieldset->addField('free_product3_id', 'select', array(
							'label'    		=> 	Mage::helper('promotions')->__('Free Product 3'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray14(),
							'name' 			=> 	'free_product3_id',
						));	
						$fieldset->addField("max_price_fp3", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Max Price for Free Product 3"),					
							"required" 		=> 	false,
							"name" 			=> 	"max_price_fp3",
						));							
						
						$fieldset->addField("min_price_fp4", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Min Price for Free Product 4"),					
							"required" 		=> 	false,
							"name" 			=> 	"min_price_fp4",
						));	
						$fieldset->addField('free_product4_id', 'select', array(
							'label'     	=> 	Mage::helper('promotions')->__('Free Product 4'),
							'values'   		=> 	Angara_Promotions_Block_Adminhtml_Coupon_Grid::getValueArray15(),
							'name' 			=> 	'free_product4_id',
						));	
						$fieldset->addField("max_price_fp4", "text", array(
							"label" 		=> 	Mage::helper("promotions")->__("Max Price for Free Product 4"),					
							"required" 		=> 	false,
							"name" 			=> 	"max_price_fp4",
						));		

				if (Mage::getSingleton("adminhtml/session")->getCouponData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCouponData());
					Mage::getSingleton("adminhtml/session")->setCouponData(null);
				} 
				elseif(Mage::registry("coupon_data")) {
				    $form->setValues(Mage::registry("coupon_data")->getData());
				}
				return parent::_prepareForm();
		}
}
