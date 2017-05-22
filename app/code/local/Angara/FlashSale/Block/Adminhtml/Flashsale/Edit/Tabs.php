<?php
class Angara_FlashSale_Block_Adminhtml_Flashsale_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("flashsale_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("flashsale")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("flashsale")->__("Item Information"),
				"title" => Mage::helper("flashsale")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("flashsale/adminhtml_flashsale_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
