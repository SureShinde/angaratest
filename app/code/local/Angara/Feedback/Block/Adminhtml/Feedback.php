<?php


class Angara_Feedback_Block_Adminhtml_Feedback extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_feedback";
	$this->_blockGroup = "feedback";
	$this->_headerText = Mage::helper("feedback")->__("Feedback Manager");
	$this->_addButtonLabel = Mage::helper("feedback")->__("Add New Item");
	parent::__construct();
	
	}

}