<?php
class Angara_CustomerStory_Block_Adminhtml_Story_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId("story_tabs");
		$this->setDestElementId("edit_form");
		$this->setTitle(Mage::helper("customerstory")->__("Item Information"));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab("form_section", array(
		"label" => Mage::helper("customerstory")->__("Item Information"),
		"title" => Mage::helper("customerstory")->__("Item Information"),
		"content" => $this->getLayout()->createBlock("customerstory/adminhtml_story_edit_tab_form")->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}?>