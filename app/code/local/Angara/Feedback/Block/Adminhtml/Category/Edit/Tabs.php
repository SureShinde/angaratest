<?php
class Angara_Feedback_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("category_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("feedback")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("feedback")->__("Item Information"),
				"title" => Mage::helper("feedback")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("feedback/adminhtml_category_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
