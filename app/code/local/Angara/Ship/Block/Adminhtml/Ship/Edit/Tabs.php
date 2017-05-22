<?php
class Angara_Ship_Block_Adminhtml_Ship_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId("ship_tabs");
		$this->setDestElementId("edit_form");
		$this->setTitle(Mage::helper("ship")->__("Item Information"));
	}
	protected function _beforeToHtml()
	{
		$this->addTab("form_section", array(
		"label" => Mage::helper("ship")->__("Item Information"),
		"title" => Mage::helper("ship")->__("Item Information"),
		"content" => $this->getLayout()->createBlock("ship/adminhtml_ship_edit_tab_form")->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}
