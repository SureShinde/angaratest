<?php


class Angara_Card_Block_Adminhtml_Card extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_card";
	$this->_blockGroup = "card";
	$this->_headerText = Mage::helper("card")->__("Card Manager");
	$this->_addButtonLabel = Mage::helper("card")->__("Add New Card");
	parent::__construct();
	
	}

}