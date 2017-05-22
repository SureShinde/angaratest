<?php


class Angara_Feedback_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_category";
	$this->_blockGroup = "feedback";
	$this->_headerText = Mage::helper("feedback")->__("Feedback Category Manager");
	$this->_addButtonLabel = Mage::helper("feedback")->__("Add New Item");
	parent::__construct();
	
	}

}