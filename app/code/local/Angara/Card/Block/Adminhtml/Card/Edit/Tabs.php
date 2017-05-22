<?php
class Angara_Card_Block_Adminhtml_Card_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("card_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("card")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("card")->__("Item Information"),
				"title" => Mage::helper("card")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("card/adminhtml_card_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
