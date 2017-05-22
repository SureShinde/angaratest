<?php
class Angara_Promotions_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("promotions_form", array("legend"=>Mage::helper("promotions")->__("Item information")));
				
						$fieldset->addField("unique_identifier", "text", array(
						"label" => Mage::helper("promotions")->__("Unique Banner Identifier"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "unique_identifier",
						));
				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("promotions")->__("Banner Title"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "name",
						));
					
						$fieldset->addField("url", "text", array(
						"label" => Mage::helper("promotions")->__("Redirect Url"),
						"name" => "url",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('promotions')->__('Status'),
						'values'   => Angara_Promotions_Block_Adminhtml_Banner_Grid::getValueArray2(),
						'name' => 'status',					
						"class" => "required-entry",
						"required" => true,
						));
						$fieldset->addField("description", "textarea", array(
						"label" => Mage::helper("promotions")->__("Description"),
						"name" => "description",
						));
									
						$fieldset->addField('image_path', 'image', array(
						'label' => Mage::helper('promotions')->__('Banner Image'),
						'name' => 'image_path',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("image_title", "text", array(
						"label" => Mage::helper("promotions")->__("Title Text"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "image_title",
						));
					
						$fieldset->addField("image_alt_text", "text", array(
						"label" => Mage::helper("promotions")->__("Alt Text"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "image_alt_text",
						));
					
						$fieldset->addField("html_content", "textarea", array(
						"label" => Mage::helper("promotions")->__("Html Content"),
						"name" => "html_content",
						"note" => 'write additional html, css and scripts here. {{ID}} will be replaced with the banner id itself. available elements bannerImage{{ID}}, #bannerImageMap{{ID}}, bannerTicker{{ID}}.'
						));
									
						 $fieldset->addField('has_ticker', 'select', array(
						'label'     => Mage::helper('promotions')->__('Allow Ticker'),
						'values'   => Angara_Promotions_Block_Adminhtml_Banner_Grid::getValueArray8(),
						'name' => 'has_ticker',
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('ticker_time', 'date', array(
						'label'        => Mage::helper('promotions')->__('Stop Ticker At'),
						'name'         => 'ticker_time',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("ticker_left_position", "text", array(
						"label" => Mage::helper("promotions")->__("Ticker Left Position"),
						"name" => "ticker_left_position",
						));
					
						$fieldset->addField("ticker_top_position", "text", array(
						"label" => Mage::helper("promotions")->__("Ticker Top Position"),
						"name" => "ticker_top_position",
						));
						
						$fieldset->addField("created_at", "hidden", array(
						"name" => "created_at",
						));
						
						$fieldset->addField("updated_at", "hidden", array(
						"name" => "updated_at",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getBannerData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBannerData());
					Mage::getSingleton("adminhtml/session")->setBannerData(null);
				} 
				elseif(Mage::registry("banner_data")) {
				    $form->setValues(Mage::registry("banner_data")->getData());
				}
				return parent::_prepareForm();
		}
}
