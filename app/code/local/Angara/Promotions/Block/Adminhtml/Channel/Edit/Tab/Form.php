<?php
class Angara_Promotions_Block_Adminhtml_Channel_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("promotions_form", array("legend"=>Mage::helper("promotions")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("promotions")->__("Channel Title"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "name",
						));
					
						$fieldset->addField("url_identifier", "text", array(
						"label" => Mage::helper("promotions")->__("Url Identifier String"),
						"name" => "url_identifier",
						'note' => 'A blank url identifier with ID:1 must be present to serve as the default channel.'
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('promotions')->__('Status'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getValueArray2(),
						'name' => 'status',
						'note' => 'This doesn\'t disable the coupons binded to this channel. Those coupons are still applicable on cart.',
						));
						
						$fieldset->addField('home_hero_banner_desktop', 'select', array(
						'label' => Mage::helper('promotions')->__('Home Hero Banner for Desktop'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'home_hero_banner_desktop',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('home_hero_banner_tablet', 'select', array(
						'label' => Mage::helper('promotions')->__('Home Hero Banner for Tablet'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'home_hero_banner_tablet',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('home_hero_banner_mobile', 'select', array(
						'label' => Mage::helper('promotions')->__('Home Hero Banner for Mobile'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'home_hero_banner_mobile',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('catalog_banner_desktop', 'select', array(
						'label' => Mage::helper('promotions')->__('Catalog Banner for Desktop'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'catalog_banner_desktop',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('catalog_banner_tablet', 'select', array(
						'label' => Mage::helper('promotions')->__('Catalog Banner for Tablet'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'catalog_banner_tablet',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('catalog_banner_mobile', 'select', array(
						'label' => Mage::helper('promotions')->__('Catalog Banner for Mobile'),
						'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
						'name' => 'catalog_banner_mobile',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						
						//	S:VA
						$fieldset->addField('offer_banner_desktop', 'select', array(
							'label' => Mage::helper('promotions')->__('Offer Popup Banner for Desktop/Tablet'),
							'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
							'name' => 'offer_banner_desktop',
							'note' => '(*.jpg, *.png, *.gif)',
						));
						
						$fieldset->addField('offer_banner_mobile', 'select', array(
							'label' => Mage::helper('promotions')->__('Offer Popup Banner for Mobile'),
							'values'   => Angara_Promotions_Block_Adminhtml_Channel_Grid::getBanners(true),
							'name' => 'offer_banner_mobile',
							'note' => '(*.jpg, *.png, *.gif)',
						));
						//	E:VA

				if (Mage::getSingleton("adminhtml/session")->getChannelData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getChannelData());
					Mage::getSingleton("adminhtml/session")->setChannelData(null);
				} 
				elseif(Mage::registry("channel_data")) {
				    $form->setValues(Mage::registry("channel_data")->getData());
				}
				return parent::_prepareForm();
		}
}
