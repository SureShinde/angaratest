<?php
class Angara_CustomerStory_Block_Adminhtml_Story extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = "adminhtml_story";
		$this->_blockGroup = "customerstory";
		$this->_headerText = Mage::helper("customerstory")->__("Shared Story Manager");
		//$this->_addButtonLabel = Mage::helper("customerstory")->__("Add New Item");
		parent::__construct();	
		$this->_removeButton('add');
	}
}?>